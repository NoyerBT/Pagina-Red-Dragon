<?php
// Desactivar visualización de errores para evitar que se muestren antes del JSON
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

session_start();

// Establecer el header JSON primero
header('Content-Type: application/json');

try {
    require_once 'cnt/conexion.php';
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error de conexión: ' . $e->getMessage()]);
    exit();
}

// Verificar que el usuario esté logueado
if (!isset($_SESSION['usuario'])) {
    echo json_encode(['success' => false, 'message' => 'No estás autenticado']);
    exit();
}

// Verificar que el usuario tenga plan activo
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
    echo json_encode(['success' => false, 'message' => 'No tienes un plan activo']);
    exit();
}

$action = $_POST['action'] ?? '';

if ($action === 'agregar' || $action === 'editar') {
    $torneo_id = intval($_POST['torneo_id'] ?? 0);
    $nombre_equipo = trim($_POST['nombre_equipo'] ?? '');
    $tag = !empty($_POST['tag']) ? trim($_POST['tag']) : null;
    $equipo_id = isset($_POST['equipo_id']) ? intval($_POST['equipo_id']) : null;
    
    // Verificar que el torneo pertenezca al usuario
    $stmt = $conn->prepare("SELECT id FROM torneos WHERE id = ? AND usuario_id = ?");
    $stmt->bind_param("ii", $torneo_id, $usuario_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        $stmt->close();
        $conn->close();
        echo json_encode(['success' => false, 'message' => 'Torneo no encontrado']);
        exit();
    }
    $stmt->close();
    
    if (empty($nombre_equipo)) {
        $conn->close();
        echo json_encode(['success' => false, 'message' => 'Debes ingresar un nombre para el equipo']);
        exit();
    }
    
    // Procesar logo si se subió
    $logo_path = null;
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['logo'];
        $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed_ext = ['png', 'jpg', 'jpeg'];
        
        if (!in_array($file_ext, $allowed_ext)) {
            $conn->close();
            echo json_encode(['success' => false, 'message' => 'El logo debe ser PNG o JPG']);
            exit();
        }
        
        if ($file['size'] > 5242880) {
            $conn->close();
            echo json_encode(['success' => false, 'message' => 'El logo es muy grande (máximo 5MB)']);
            exit();
        }
        
        $logos_dir = 'logos_equipos/';
        if (!file_exists($logos_dir)) {
            mkdir($logos_dir, 0777, true);
        }
        
        $new_file_name = 'equipo_' . $torneo_id . '_' . ($equipo_id ?? 'new') . '_' . time() . '.' . $file_ext;
        $logo_path = $logos_dir . $new_file_name;
        
        if (!move_uploaded_file($file['tmp_name'], $logo_path)) {
            $conn->close();
            echo json_encode(['success' => false, 'message' => 'Error al subir el logo']);
            exit();
        }
    }
    
    try {
        if ($action === 'editar' && $equipo_id) {
            // Actualizar equipo existente
            if ($logo_path) {
                // Eliminar logo anterior si existe
                $stmt = $conn->prepare("SELECT logo FROM equipos_torneo WHERE id = ?");
                $stmt->bind_param("i", $equipo_id);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($old_equipo = $result->fetch_assoc() && $old_equipo['logo']) {
                    if (file_exists($old_equipo['logo'])) {
                        unlink($old_equipo['logo']);
                    }
                }
                $stmt->close();
                
                $stmt = $conn->prepare("UPDATE equipos_torneo SET nombre_equipo = ?, tag = ?, logo = ? WHERE id = ? AND torneo_id = ?");
                $stmt->bind_param("sssii", $nombre_equipo, $tag, $logo_path, $equipo_id, $torneo_id);
            } else {
                $stmt = $conn->prepare("UPDATE equipos_torneo SET nombre_equipo = ?, tag = ? WHERE id = ? AND torneo_id = ?");
                $stmt->bind_param("ssii", $nombre_equipo, $tag, $equipo_id, $torneo_id);
            }
            
            $stmt->execute();
            $stmt->close();
            
            echo json_encode(['success' => true, 'message' => 'Equipo actualizado exitosamente']);
        } else {
            // Agregar nuevo equipo
            // Obtener el siguiente orden
            $stmt = $conn->prepare("SELECT COALESCE(MAX(orden), 0) + 1 as next_orden FROM equipos_torneo WHERE torneo_id = ?");
            $stmt->bind_param("i", $torneo_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $next_orden = $result->fetch_assoc()['next_orden'];
            $stmt->close();
            
            $stmt = $conn->prepare("INSERT INTO equipos_torneo (torneo_id, nombre_equipo, tag, logo, orden) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("isssi", $torneo_id, $nombre_equipo, $tag, $logo_path, $next_orden);
            $stmt->execute();
            $equipo_id = $conn->insert_id;
            $stmt->close();
            
            echo json_encode([
                'success' => true, 
                'message' => 'Equipo agregado exitosamente',
                'equipo_id' => $equipo_id
            ]);
        }
    } catch (Exception $e) {
        if ($logo_path && file_exists($logo_path)) {
            unlink($logo_path);
        }
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    
} elseif ($action === 'eliminar') {
    $equipo_id = intval($_POST['equipo_id'] ?? 0);
    $torneo_id = intval($_POST['torneo_id'] ?? 0);
    
    // Verificar que el torneo pertenezca al usuario
    $stmt = $conn->prepare("SELECT id FROM torneos WHERE id = ? AND usuario_id = ?");
    $stmt->bind_param("ii", $torneo_id, $usuario_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        $stmt->close();
        $conn->close();
        echo json_encode(['success' => false, 'message' => 'Torneo no encontrado']);
        exit();
    }
    $stmt->close();
    
    // Obtener logo antes de eliminar
    $stmt = $conn->prepare("SELECT logo FROM equipos_torneo WHERE id = ? AND torneo_id = ?");
    $stmt->bind_param("ii", $equipo_id, $torneo_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $equipo = $result->fetch_assoc();
    $stmt->close();
    
    // Eliminar equipo
    $stmt = $conn->prepare("DELETE FROM equipos_torneo WHERE id = ? AND torneo_id = ?");
    $stmt->bind_param("ii", $equipo_id, $torneo_id);
    $stmt->execute();
    $stmt->close();
    
    // Eliminar logo si existe
    if ($equipo && $equipo['logo'] && file_exists($equipo['logo'])) {
        unlink($equipo['logo']);
    }
    
    echo json_encode(['success' => true, 'message' => 'Equipo eliminado exitosamente']);
    
} else {
    echo json_encode(['success' => false, 'message' => 'Acción no válida']);
}

$conn->close();
?>
