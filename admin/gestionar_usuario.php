<?php
session_start();

if (!isset($_SESSION['admin_usuario'])) {
    header("Location: index.php");
    exit();
}

require_once '../cnt/conexion.php';

$accion = $_REQUEST['accion'] ?? '';
$user_id = $_REQUEST['id'] ?? 0;

if ($user_id > 0) {
    switch ($accion) {
        case 'bloquear':
            // Toggle between 'activo' and 'bloqueado'
            $sql_current_state = "SELECT estado FROM usuarios WHERE id = ?";
            $stmt_current_state = $conn->prepare($sql_current_state);
            $stmt_current_state->bind_param("i", $user_id);
            $stmt_current_state->execute();
            $result = $stmt_current_state->get_result();
            $user = $result->fetch_assoc();

            $new_state = ($user['estado'] == 'activo') ? 'bloqueado' : 'activo';

            $sql = "UPDATE usuarios SET estado = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $new_state, $user_id);
            $stmt->execute();
            break;

        case 'eliminar':
            $sql = "DELETE FROM usuarios WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            break;

        case 'expiracion':
            $fecha_expiracion = $_POST['fecha_expiracion'];
            $sql = "UPDATE usuarios SET fecha_expiracion = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $fecha_expiracion, $user_id);
            $stmt->execute();
            break;
    }
}

header("Location: dashboard.php");
exit();
?>
