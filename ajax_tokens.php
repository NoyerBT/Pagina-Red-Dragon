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
    require_once 'TokenGenerator.php';
    require_once 'cnt/conexion.php';
} catch (Exception $e) {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Error de conexión: ' . $e->getMessage()]);
    exit();
}

// Función para obtener el nombre del torneo del usuario
function obtener_torneo_usuario($usuario) {
    global $conn;
    
    // Obtener el ID del usuario
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE usuario = ? LIMIT 1");
    if (!$stmt) {
        return null;
    }
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    
    if (!$user || !isset($user['id'])) {
        return null;
    }
    
    $usuario_id = $user['id'];
    
    // Verificar si la tabla torneos existe
    $check_table = $conn->query("SHOW TABLES LIKE 'torneos'");
    if ($check_table->num_rows == 0) {
        return null;
    }
    
    // Obtener el torneo más reciente del usuario
    $stmt = $conn->prepare("SELECT nombre_torneo FROM torneos WHERE usuario_id = ? ORDER BY fecha_creacion DESC LIMIT 1");
    if (!$stmt) {
        return null;
    }
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $torneo = $result->fetch_assoc();
    $stmt->close();
    
    return $torneo ? $torneo['nombre_torneo'] : null;
}

// Verificar que el usuario esté logueado
if (!isset($_SESSION['usuario'])) {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'No estás autenticado']);
    exit();
}

// Obtener la acción
$action = $_POST['action'] ?? '';

try {
    $generator = new TokenGenerator();
    $current_user = $_SESSION['usuario']; // Obtener el usuario actual
    
    if ($action === 'generate') {
        $player_name = trim($_POST['player_name'] ?? '');
        
        if (empty($player_name)) {
            echo json_encode(['success' => false, 'message' => 'Por favor ingrese el nombre del jugador.']);
            exit();
        }
        
        // Obtener el nombre del torneo del usuario, o null si no tiene
        $tournament_name = obtener_torneo_usuario($current_user);
        $generated_token = $generator->generate_token($player_name, $tournament_name, $current_user);
        
        if ($generated_token) {
            ob_clean();
            echo json_encode([
                'success' => true,
                'message' => 'Token generado exitosamente.',
                'token' => $generated_token
            ]);
        } else {
            ob_clean();
            echo json_encode(['success' => false, 'message' => 'Error al generar el token.']);
        }
        
    } elseif ($action === 'validate') {
        $token_to_validate = trim($_POST['token_to_validate'] ?? '');
        
        if (empty($token_to_validate)) {
            echo json_encode(['success' => false, 'message' => 'Por favor ingrese un token para validar.']);
            exit();
        }
        
        list($isValid, $result) = $generator->validate_token($token_to_validate);
        
        ob_clean();
        if ($isValid) {
            echo json_encode([
                'success' => true,
                'valid' => true,
                'message' => 'Token válido',
                'data' => $result
            ]);
        } else {
            echo json_encode([
                'success' => true,
                'valid' => false,
                'message' => $result
            ]);
        }
        
    } elseif ($action === 'deactivate') {
        $token_hash = trim($_POST['token_hash'] ?? '');
        
        if (empty($token_hash)) {
            echo json_encode(['success' => false, 'message' => 'Token hash no proporcionado.']);
            exit();
        }
        
        if ($generator->deactivate_token($token_hash, $current_user)) {
            ob_clean();
            echo json_encode([
                'success' => true,
                'message' => 'Token desactivado correctamente.'
            ]);
        } else {
            ob_clean();
            echo json_encode([
                'success' => false,
                'message' => 'Error al desactivar el token. Verifica que el token te pertenezca.'
            ]);
        }
        
    } elseif ($action === 'delete') {
        $token_hash = trim($_POST['token_hash'] ?? '');
        
        if (empty($token_hash)) {
            echo json_encode(['success' => false, 'message' => 'Token hash no proporcionado.']);
            exit();
        }
        
        if ($generator->delete_token($token_hash, $current_user)) {
            ob_clean();
            echo json_encode([
                'success' => true,
                'message' => 'Token eliminado correctamente.'
            ]);
        } else {
            ob_clean();
            echo json_encode([
                'success' => false,
                'message' => 'Error al eliminar el token. Verifica que el token te pertenezca.'
            ]);
        }
        
    } else {
        ob_clean();
        echo json_encode(['success' => false, 'message' => 'Acción no válida']);
    }
    
} catch (Exception $e) {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}

