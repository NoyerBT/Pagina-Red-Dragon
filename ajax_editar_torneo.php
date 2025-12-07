<?php
// Limpiar cualquier salida previa
ob_start();

// Desactivar visualización de errores
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

session_start();

header('Content-Type: application/json');

try {
    require_once 'cnt/conexion.php';
} catch (Exception $e) {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Error de conexión: ' . $e->getMessage()]);
    exit();
}

if (!isset($_SESSION['usuario'])) {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'No estás autenticado']);
    exit();
}

// Verificar plan activo
$plan_activo = false;
$stmt = $conn->prepare("SELECT id, estado, fecha_expiracion FROM usuarios WHERE usuario = ? LIMIT 1");
$stmt->bind_param("s", $_SESSION['usuario']);
$stmt->execute();
$result = $stmt->get_result();

if ($user = $result->fetch_assoc()) {
    $usuario_id = $user['id'];
    if ($user['estado'] === 'activo' && !empty($user['fecha_expiracion'])) {
        try {
            $hoy = new DateTime('today');
            $expiracion = new DateTime($user['fecha_expiracion']);
            if ($expiracion >= $hoy) {
                $plan_activo = true;
            }
        } catch (Exception $e) {
            error_log('Error evaluando expiración de plan: ' . $e->getMessage());
        }
    }
}
$stmt->close();

if (!$plan_activo) {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'No tienes un plan activo']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit();
}

$torneo_id = intval($_POST['torneo_id'] ?? 0);
$nombre_torneo = trim($_POST['nombre_torneo'] ?? '');
$modalidad = trim($_POST['modalidad'] ?? 'Single Elimination');
$eliminar_logo = isset($_POST['eliminar_logo']) && $_POST['eliminar_logo'] === '1';

if ($torneo_id <= 0 || empty($nombre_torneo)) {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
    exit();
}

// Validar modalidad
if (!in_array($modalidad, ['Single Elimination', 'Double Elimination'])) {
    $modalidad = 'Single Elimination';
}

try {
    // Verificar que el torneo pertenezca al usuario
    $stmt = $conn->prepare("SELECT id, logo, modalidad FROM torneos WHERE id = ? AND usuario_id = ?");
    if (!$stmt) {
        throw new Exception("Error al preparar la consulta: " . $conn->error);
    }
    
    $stmt->bind_param("ii", $torneo_id, $usuario_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        $stmt->close();
        ob_clean();
        echo json_encode(['success' => false, 'message' => 'Torneo no encontrado']);
        exit();
    }
    
    $torneo_actual = $result->fetch_assoc();
    $logo_anterior = $torneo_actual['logo'];
    $modalidad_anterior = $torneo_actual['modalidad'] ?? 'Single Elimination';
    $stmt->close();
    
    // Si se cambia a Double Elimination y no hay matches completados, generar bracket B
    $cambio_a_double = ($modalidad === 'Double Elimination' && $modalidad_anterior !== 'Double Elimination');
    if ($cambio_a_double) {
        // Verificar si hay matches completados
        $check_matches = $conn->query("SELECT COUNT(*) as total FROM matches WHERE torneo_id = $torneo_id AND completado = 1");
        $matches_completados = $check_matches->fetch_assoc()['total'];
        
        if ($matches_completados == 0) {
            // No hay matches completados, podemos generar el bracket B
            // Obtener número de equipos
            $check_equipos = $conn->query("SELECT COUNT(*) as total FROM equipos_torneo WHERE torneo_id = $torneo_id");
            $total_equipos = $check_equipos->fetch_assoc()['total'];
            
            if ($total_equipos > 0) {
                // Obtener matches de ronda 1 del Winners
                $check_ronda1 = $conn->query("SELECT COUNT(*) as total FROM matches WHERE torneo_id = $torneo_id AND bracket_tipo = 'winners' AND ronda = 1");
                $matches_ronda1 = $check_ronda1->fetch_assoc()['total'];
                
                if ($matches_ronda1 > 0) {
                    // Generar bracket B (similar a la lógica en brackets_torneo.php)
                    $matches_losers_r1 = ceil($matches_ronda1 / 2);
                    for ($i = 1; $i <= $matches_losers_r1; $i++) {
                        $conn->query("INSERT INTO matches (torneo_id, bracket_tipo, ronda, numero_match, equipo1_id, equipo2_id) 
                                     VALUES ($torneo_id, 'losers', 1, $i, NULL, NULL)");
                    }
                    
                    // Generar rondas siguientes del Losers Bracket
                    $max_ronda_winners = $conn->query("SELECT MAX(ronda) as max_ronda FROM matches WHERE torneo_id = $torneo_id AND bracket_tipo = 'winners'");
                    $max_ronda = $max_ronda_winners->fetch_assoc()['max_ronda'] ?? 1;
                    
                    $ronda_losers = 2;
                    $matches_losers_actual = $matches_losers_r1;
                    $ronda_winners = 2;
                    
                    while ($ronda_winners <= $max_ronda) {
                        $matches_winners_perdedores = ceil($matches_ronda1 / pow(2, $ronda_winners - 1));
                        $matches_losers_ganadores = ceil($matches_losers_actual / 2);
                        $matches_losers_ronda = max($matches_winners_perdedores, $matches_losers_ganadores);
                        
                        if ($matches_losers_ronda > 0) {
                            for ($i = 1; $i <= $matches_losers_ronda; $i++) {
                                $conn->query("INSERT INTO matches (torneo_id, bracket_tipo, ronda, numero_match, equipo1_id, equipo2_id) 
                                             VALUES ($torneo_id, 'losers', $ronda_losers, $i, NULL, NULL)");
                            }
                            $matches_losers_actual = $matches_losers_ronda;
                        }
                        
                        $ronda_losers++;
                        $ronda_winners++;
                        
                        // Ronda impar de Losers: Solo ganadores de la ronda anterior
                        if ($ronda_losers <= ($max_ronda - 1) * 2) {
                            $matches_losers_ronda_impar = ceil($matches_losers_actual / 2);
                            if ($matches_losers_ronda_impar > 0) {
                                for ($i = 1; $i <= $matches_losers_ronda_impar; $i++) {
                                    $conn->query("INSERT INTO matches (torneo_id, bracket_tipo, ronda, numero_match, equipo1_id, equipo2_id) 
                                                 VALUES ($torneo_id, 'losers', $ronda_losers, $i, NULL, NULL)");
                                }
                                $matches_losers_actual = $matches_losers_ronda_impar;
                                $ronda_losers++;
                            }
                        }
                    }
                    
                    // Crear Gran Final vacía
                    $conn->query("INSERT INTO matches (torneo_id, bracket_tipo, ronda, numero_match, equipo1_id, equipo2_id) 
                                 VALUES ($torneo_id, 'grand_final', 1, 1, NULL, NULL)");
                }
            }
        }
    }
    
    $logo_path = $logo_anterior;
    
    // Si se debe eliminar el logo
    if ($eliminar_logo && $logo_anterior) {
        if (file_exists($logo_anterior)) {
            @unlink($logo_anterior);
        }
        $logo_path = null;
    }
    
    // Si se sube un nuevo logo
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
        // Eliminar logo anterior si existe
        if ($logo_anterior && file_exists($logo_anterior)) {
            @unlink($logo_anterior);
        }
        
        $file = $_FILES['logo'];
        $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed_ext = ['png', 'jpg', 'jpeg'];
        
        if (!in_array($file_ext, $allowed_ext)) {
            ob_clean();
            echo json_encode(['success' => false, 'message' => 'El logo debe ser PNG o JPG']);
            exit();
        }
        
        if ($file['size'] > 5242880) {
            ob_clean();
            echo json_encode(['success' => false, 'message' => 'El logo es muy grande (máximo 5MB)']);
            exit();
        }
        
        $logos_dir = 'logos_torneos/';
        if (!file_exists($logos_dir)) {
            mkdir($logos_dir, 0777, true);
        }
        
        $new_file_name = 'torneo_' . $torneo_id . '_' . time() . '.' . $file_ext;
        $logo_path = $logos_dir . $new_file_name;
        
        if (!move_uploaded_file($file['tmp_name'], $logo_path)) {
            ob_clean();
            echo json_encode(['success' => false, 'message' => 'Error al subir el logo']);
            exit();
        }
    }
    
    // Actualizar torneo
    $stmt = $conn->prepare("UPDATE torneos SET nombre_torneo = ?, logo = ?, modalidad = ? WHERE id = ? AND usuario_id = ?");
    if (!$stmt) {
        throw new Exception("Error al preparar la consulta: " . $conn->error);
    }
    
    $stmt->bind_param("sssii", $nombre_torneo, $logo_path, $modalidad, $torneo_id, $usuario_id);
    
    if (!$stmt->execute()) {
        throw new Exception("Error al actualizar el torneo: " . $conn->error);
    }
    
    $stmt->close();
    $conn->close();
    
    ob_clean();
    echo json_encode([
        'success' => true, 
        'message' => 'Torneo actualizado exitosamente'
    ]);
    exit();
    
} catch (Exception $e) {
    if (isset($conn) && $conn) {
        $conn->close();
    }
    ob_clean();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    exit();
}
?>
