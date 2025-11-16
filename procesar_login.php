<?php
session_start();

require_once 'cnt/conexion.php';

$usuario = $_POST['usuario'];
$password_form = $_POST['password'];

$sql = "SELECT * FROM usuarios WHERE usuario = ? OR email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $usuario, $usuario);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    if (password_verify($password_form, $user['password'])) {
        // Check if the user account is active
        if ($user['estado'] == 'activo') {
            $_SESSION['usuario'] = $user['usuario'];
            $_SESSION['nombre'] = $user['nombre'];
            header("Location: dashboard.php");
            exit();
        } else {
            $_SESSION['login_error'] = "Tu cuenta está pendiente de aprobación por un administrador.";
            header("Location: login.php");
            exit();
        }
    } else {
        $_SESSION['login_error'] = "Usuario o contraseña incorrectos.";
        header("Location: login.php");
        exit();
    }
} else {
    $_SESSION['login_error'] = "Usuario o contraseña incorrectos.";
    header("Location: login.php");
    exit();
}

$stmt->close();
$conn->close();
?>