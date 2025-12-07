<?php
// Limpiar cualquier salida previa
ob_start();

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
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Error de conexión: ' . $e->getMessage()]);
    exit();
}

// Verificar que el usuario esté logueado
if (!isset($_SESSION['usuario'])) {
    ob_clean();
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

if ($torneo_id <= 0) {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'ID de torneo inválido']);
    exit();
}

try {
    // Verificar que el torneo pertenezca al usuario
    $stmt = $conn->prepare("SELECT id, nombre_torneo FROM torneos WHERE id = ? AND usuario_id = ?");
    if (!$stmt) {
        throw new Exception("Error al preparar la consulta: " . $conn->error);
    }
    
    $stmt->bind_param("ii", $torneo_id, $usuario_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        $stmt->close();
        ob_clean();
        echo json_encode(['success' => false, 'message' => 'Torneo no encontrado o no tienes permisos']);
        exit();
    }
    
    $torneo = $result->fetch_assoc();
    $stmt->close();
    
    // Obtener logos de equipos antes de eliminar
    $stmt = $conn->prepare("SELECT logo FROM equipos_torneo WHERE torneo_id = ? AND logo IS NOT NULL");
    $stmt->bind_param("i", $torneo_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $logos = [];
    while ($row = $result->fetch_assoc()) {
        if ($row['logo'] && file_exists($row['logo'])) {
            $logos[] = $row['logo'];
        }
    }
    $stmt->close();
    
    // Eliminar el torneo (esto eliminará automáticamente los equipos por CASCADE)
    $stmt = $conn->prepare("DELETE FROM torneos WHERE id = ? AND usuario_id = ?");
    if (!$stmt) {
        throw new Exception("Error al preparar la consulta de eliminación: " . $conn->error);
    }
    
    $stmt->bind_param("ii", $torneo_id, $usuario_id);
    
    if (!$stmt->execute()) {
        throw new Exception("Error al eliminar el torneo: " . $conn->error);
    }
    
    $stmt->close();
    
    // Eliminar logos físicos
    foreach ($logos as $logo) {
        if (file_exists($logo)) {
            @unlink($logo);
        }
    }
    
    if (isset($conn)) {
        $conn->close();
    }
    
    ob_clean();
    echo json_encode([
        'success' => true, 
        'message' => 'Torneo "' . htmlspecialchars($torneo['nombre_torneo']) . '" eliminado exitosamente'
    ]);
    exit();
    
} catch (Exception $e) {
    if (isset($conn) && $conn) {
        $conn->close();
    }
    ob_clean();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    exit();
} catch (Error $e) {
    if (isset($conn) && $conn) {
        $conn->close();
    }
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    exit();
}
?>
