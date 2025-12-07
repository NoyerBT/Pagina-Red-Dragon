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
$eliminar_logo = isset($_POST['eliminar_logo']) && $_POST['eliminar_logo'] === '1';

if ($torneo_id <= 0 || empty($nombre_torneo)) {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
    exit();
}

try {
    // Verificar que el torneo pertenezca al usuario
    $stmt = $conn->prepare("SELECT id, logo FROM torneos WHERE id = ? AND usuario_id = ?");
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
    $stmt->close();
    
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
    $stmt = $conn->prepare("UPDATE torneos SET nombre_torneo = ?, logo = ? WHERE id = ? AND usuario_id = ?");
    if (!$stmt) {
        throw new Exception("Error al preparar la consulta: " . $conn->error);
    }
    
    $stmt->bind_param("ssii", $nombre_torneo, $logo_path, $torneo_id, $usuario_id);
    
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
