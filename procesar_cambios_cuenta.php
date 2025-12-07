<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

require_once 'cnt/conexion.php';

$accion = $_POST['accion'] ?? '';
$mensaje = '';
$tipo_mensaje = '';

try {
    // Verificar si existe la columna pais
    $check_pais = $conn->query("SHOW COLUMNS FROM usuarios LIKE 'pais'");
    if ($check_pais && $check_pais->num_rows === 0) {
        $conn->query("ALTER TABLE usuarios ADD COLUMN pais VARCHAR(10) DEFAULT NULL");
    }

    if ($accion === 'cambiar_password') {
        $password_actual = $_POST['password_actual'] ?? '';
        $password_nueva = $_POST['password_nueva'] ?? '';
        $password_confirmar = $_POST['password_confirmar'] ?? '';

        if (empty($password_actual) || empty($password_nueva) || empty($password_confirmar)) {
            throw new Exception("Por favor, completa todos los campos.");
        }

        if ($password_nueva !== $password_confirmar) {
            throw new Exception("Las nuevas contraseñas no coinciden.");
        }

        if (strlen($password_nueva) < 8) {
            throw new Exception("La nueva contraseña debe tener al menos 8 caracteres.");
        }

        // Verificar contraseña actual
        $sql = "SELECT password FROM usuarios WHERE usuario = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $_SESSION['usuario']);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        if (!$user || !password_verify($password_actual, $user['password'])) {
            throw new Exception("La contraseña actual es incorrecta.");
        }

        // Actualizar contraseña
        $hashed_password = password_hash($password_nueva, PASSWORD_DEFAULT);
        $sql = "UPDATE usuarios SET password = ? WHERE usuario = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $hashed_password, $_SESSION['usuario']);
        
        if ($stmt->execute()) {
            $mensaje = "Contraseña actualizada exitosamente.";
            $tipo_mensaje = 'success';
        } else {
            throw new Exception("Error al actualizar la contraseña.");
        }
        $stmt->close();

    } elseif ($accion === 'cambiar_pais') {
        $pais = $_POST['pais'] ?? '';

        if (empty($pais)) {
            throw new Exception("Por favor, selecciona un país.");
        }

        // Actualizar país
        $sql = "UPDATE usuarios SET pais = ? WHERE usuario = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $pais, $_SESSION['usuario']);
        
        if ($stmt->execute()) {
            $mensaje = "País actualizado exitosamente.";
            $tipo_mensaje = 'success';
        } else {
            throw new Exception("Error al actualizar el país.");
        }
        $stmt->close();
    } else {
        throw new Exception("Acción no válida.");
    }

} catch (Exception $e) {
    $mensaje = $e->getMessage();
    $tipo_mensaje = 'error';
}

$conn->close();

// Guardar mensaje en sesión y redirigir
$_SESSION['mensaje_cuenta'] = $mensaje;
$_SESSION['tipo_mensaje_cuenta'] = $tipo_mensaje;
header("Location: dashboard.php");
exit();
?>
