<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('Método no permitido. Por favor, envía el formulario correctamente.');
}

try {
    require_once 'cnt/conexion.php';

    // Form data
    $nombre_pila = trim($_POST['nombre'] ?? '');
    $apellido = trim($_POST['apellido'] ?? '');
    $nombre_completo = trim($nombre_pila . ' ' . $apellido); // Concatenar nombre y apellido
    
    $email = trim($_POST['email'] ?? '');
    $usuario = trim($_POST['usuario'] ?? '');
    $password_form = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $pais = isset($_POST['pais']) ? trim($_POST['pais']) : '';

    // Basic validation
    if (empty($nombre_pila) || empty($email) || empty($usuario) || empty($password_form) || empty($confirm_password)) {
        throw new Exception("Por favor, rellena todos los campos obligatorios.");
    }
    
    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception("Por favor, ingresa un correo electrónico válido.");
    }

    if ($password_form !== $confirm_password) {
        throw new Exception("Las contraseñas no coinciden.");
    }

    // Hash password
    $hashed_password = password_hash($password_form, PASSWORD_DEFAULT);

    // Check if user already exists using bind_result (more compatible)
    $sql = "SELECT id FROM usuarios WHERE usuario = ? OR email = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Error en prepare (check user): " . $conn->error);
    }
    
    $stmt->bind_param("ss", $usuario, $email);
    
    if (!$stmt->execute()) {
        throw new Exception("Error al ejecutar verificación: " . $stmt->error);
    }
    
    $stmt->store_result(); // Necesario para num_rows
    
    if ($stmt->num_rows > 0) {
        throw new Exception("El nombre de usuario o correo electrónico ya existe.");
    }
    $stmt->close();

    // Insert user into database
    // Usamos el nombre_completo que incluye el apellido
    
    // Verificar si existe la columna pais, si no, crearla
    $check_pais = $conn->query("SHOW COLUMNS FROM usuarios LIKE 'pais'");
    if ($check_pais && $check_pais->num_rows === 0) {
        $conn->query("ALTER TABLE usuarios ADD COLUMN pais VARCHAR(10) DEFAULT NULL");
    }
    
    if (!empty($pais)) {
        $sql = "INSERT INTO usuarios (nombre, email, usuario, password, estado, pais) VALUES (?, ?, ?, ?, 'activo', ?)";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Error al preparar la consulta (insert con pais): " . $conn->error);
        }
        $stmt->bind_param("sssss", $nombre_completo, $email, $usuario, $hashed_password, $pais);
    } else {
        $sql = "INSERT INTO usuarios (nombre, email, usuario, password, estado) VALUES (?, ?, ?, ?, 'activo')";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Error al preparar la consulta (insert sin pais): " . $conn->error);
        }
        $stmt->bind_param("ssss", $nombre_completo, $email, $usuario, $hashed_password);
    }

    if ($stmt->execute()) {
        // Redirect to a success page
        header("Location: registro_exitoso.php");
        exit();
    } else {
        throw new Exception("Error al registrar el usuario en la BD: " . $stmt->error);
    }

} catch (Throwable $e) {
    // Log the error
    error_log('Error en procesar_registro.php: ' . $e->getMessage());
    
    // Show the actual error message to the user for debugging
    die('<div style="color: #721c24; background-color: #f8d7da; border-color: #f5c6cb; padding: 20px; border: 1px solid transparent; border-radius: .25rem; font-family: sans-serif; margin: 20px;">' . 
        '<h3 style="margin-top: 0;">Ocurrió un error al procesar tu registro:</h3>' .
        '<p style="margin-bottom: 0;">' . htmlspecialchars($e->getMessage()) . '</p>' .
        '</div>');
}

if (isset($stmt) && $stmt instanceof mysqli_stmt) $stmt->close();
if (isset($conn) && $conn instanceof mysqli) $conn->close();
?>