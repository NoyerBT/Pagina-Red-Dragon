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
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $usuario = trim($_POST['usuario']);
    $password_form = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Basic validation
    if (empty($nombre) || empty($email) || empty($usuario) || empty($password_form) || empty($confirm_password)) {
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

    // Check if user already exists
    $sql = "SELECT * FROM usuarios WHERE usuario = ? OR email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $usuario, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        throw new Exception("El nombre de usuario o correo electrónico ya existe.");
    }

    // Insert user into database with 'bloqueado' state
    $sql = "INSERT INTO usuarios (nombre, email, usuario, password, estado) VALUES (?, ?, ?, ?, 'bloqueado')";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Error al preparar la consulta: " . $conn->error);
    }
    $stmt->bind_param("ssss", $nombre, $email, $usuario, $hashed_password);

    if ($stmt->execute()) {
        // Redirect to a success page
        header("Location: registro_exitoso.php");
        exit();
    } else {
        throw new Exception("Error al registrar el usuario: " . $conn->error);
    }
} catch (Exception $e) {
    // Log the error
    error_log('Error en procesar_registro.php: ' . $e->getMessage());
    
    // Show a user-friendly error message
    die('Lo sentimos, ha ocurrido un error al procesar tu registro. Por favor, inténtalo de nuevo más tarde.');
}

$stmt->close();
$conn->close();
?>