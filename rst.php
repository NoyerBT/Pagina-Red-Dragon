<?php
require_once 'cnt/conexion.php';

// --- DEFINE LAS NUEVAS CONTRASEÑAS AQUÍ ---
$nueva_password_admin = 'admin123';
$nueva_password_usuarios = 'password123';
// ------------------------------------------

// Encriptar las nuevas contraseñas
$hash_admin = password_hash($nueva_password_admin, PASSWORD_DEFAULT);
$hash_usuarios = password_hash($nueva_password_usuarios, PASSWORD_DEFAULT);

// Actualizar la contraseña del administrador
$sql_admin = "UPDATE administradores SET password = ? WHERE usuario = 'admin'";
$stmt_admin = $conn->prepare($sql_admin);
$stmt_admin->bind_param('s', $hash_admin);

if ($stmt_admin->execute()) {
    echo "<h2>Contraseña del administrador 'admin' actualizada correctamente.</h2>";
    echo "<p>Nueva contraseña: <strong>" . $nueva_password_admin . "</strong></p>";
} else {
    echo "<h2>Error al actualizar la contraseña del administrador.</h2>";
}

// Actualizar la contraseña de todos los usuarios de prueba
$sql_usuarios = "UPDATE usuarios SET password = ?";
$stmt_usuarios = $conn->prepare($sql_usuarios);
$stmt_usuarios->bind_param('s', $hash_usuarios);

if ($stmt_usuarios->execute()) {
    echo "<h2>Contraseñas de los usuarios de prueba actualizadas correctamente.</h2>";
    echo "<p>Nueva contraseña para todos los usuarios de prueba: <strong>" . $nueva_password_usuarios . "</strong></p>";
} else {
    echo "<h2>Error al actualizar las contraseñas de los usuarios.</h2>";
}

$stmt_admin->close();
$stmt_usuarios->close();
$conn->close();

echo '<hr><p style="color:red; font-weight:bold;">IMPORTANTE: Por seguridad, elimina el archivo reset_passwords.php de tu servidor ahora que has terminado.</p>';

?>
