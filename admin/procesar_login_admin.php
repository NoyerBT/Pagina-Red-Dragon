<?php
session_start();

require_once '../cnt/conexion.php';

$usuario = $_POST['usuario'];
$password_form = $_POST['password'];

// Check for an admin user
$sql = "SELECT * FROM administradores WHERE usuario = ? OR email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $usuario, $usuario);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $admin = $result->fetch_assoc();
    if (password_verify($password_form, $admin['password'])) {
        $_SESSION['admin_usuario'] = $admin['usuario'];
        $_SESSION['admin_nombre'] = $admin['nombre'];
        header("Location: dashboard.php");
        exit();
    } else {
        $_SESSION['admin_login_error'] = "Credenciales incorrectas.";
        header("Location: index.php");
        exit();
    }
} else {
    $_SESSION['admin_login_error'] = "No se encontró una cuenta de administrador.";
    header("Location: index.php");
    exit();
}

$stmt->close();
$conn->close();
?>
