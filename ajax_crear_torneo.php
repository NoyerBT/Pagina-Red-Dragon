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

$nombre_torneo = trim($_POST['nombre_torneo'] ?? '');
$modalidad = trim($_POST['modalidad'] ?? '');

if (empty($nombre_torneo)) {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Debes ingresar un nombre para el torneo']);
    exit();
}

if (empty($modalidad)) {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Debes seleccionar una modalidad para el torneo']);
    exit();
}

// Validar que la modalidad sea válida
$modalidades_validas = ['Single Elimination', 'Double Elimination'];
if (!in_array($modalidad, $modalidades_validas)) {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Modalidad inválida']);
    exit();
}

try {
    // Verificar que la tabla existe, si no existe, crearla
    $check_table = $conn->query("SHOW TABLES LIKE 'torneos'");
    if ($check_table->num_rows === 0) {
        // Crear la tabla si no existe
        $create_table = "CREATE TABLE IF NOT EXISTS torneos (
            id INT AUTO_INCREMENT PRIMARY KEY,
            usuario_id INT NOT NULL,
            nombre_torneo VARCHAR(200) NOT NULL,
            modalidad VARCHAR(50) NOT NULL DEFAULT 'Single Elimination',
            logo VARCHAR(255) DEFAULT NULL,
            fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_usuario (usuario_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        if (!$conn->query($create_table)) {
            throw new Exception("Error al crear la tabla: " . $conn->error);
        }
    } else {
        // Verificar si existe la columna logo, si no, agregarla
        $check_column = $conn->query("SHOW COLUMNS FROM torneos LIKE 'logo'");
        if ($check_column->num_rows === 0) {
            $conn->query("ALTER TABLE torneos ADD COLUMN logo VARCHAR(255) DEFAULT NULL AFTER nombre_torneo");
        }
        
        // Verificar si existe la columna modalidad, si no, agregarla
        $check_modalidad = $conn->query("SHOW COLUMNS FROM torneos LIKE 'modalidad'");
        if ($check_modalidad->num_rows === 0) {
            $conn->query("ALTER TABLE torneos ADD COLUMN modalidad VARCHAR(50) NOT NULL DEFAULT 'Single Elimination' AFTER nombre_torneo");
        }
    }
    
    // Procesar logo si se subió
    $logo_path = null;
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
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
        
        $new_file_name = 'torneo_' . time() . '_' . uniqid() . '.' . $file_ext;
        $logo_path = $logos_dir . $new_file_name;
        
        if (!move_uploaded_file($file['tmp_name'], $logo_path)) {
            ob_clean();
            echo json_encode(['success' => false, 'message' => 'Error al subir el logo']);
            exit();
        }
    }
    
    $stmt = $conn->prepare("INSERT INTO torneos (usuario_id, nombre_torneo, modalidad, logo) VALUES (?, ?, ?, ?)");
    if (!$stmt) {
        throw new Exception("Error al preparar la consulta: " . $conn->error);
    }
    
    $stmt->bind_param("isss", $usuario_id, $nombre_torneo, $modalidad, $logo_path);
    
    if (!$stmt->execute()) {
        throw new Exception("Error al crear el torneo: " . $conn->error);
    }
    
    $torneo_id = $conn->insert_id;
    $stmt->close();
    
    if (isset($conn)) {
        $conn->close();
    }
    
    // Asegurarse de que no hay salida antes del JSON
    ob_clean();
    
    echo json_encode([
        'success' => true, 
        'message' => 'Torneo creado exitosamente',
        'torneo_id' => $torneo_id
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
