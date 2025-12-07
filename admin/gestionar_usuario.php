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
            $fecha_expiracion = !empty($_POST['fecha_expiracion']) ? trim($_POST['fecha_expiracion']) : null;
            
            // Verificar si existe la columna VIP, si no, crearla
            try {
                $check_vip = $conn->query("SHOW COLUMNS FROM usuarios LIKE 'vip'");
                if ($check_vip && $check_vip->num_rows === 0) {
                    // Verificar si existe la columna 'rol' para usar AFTER, si no, agregar al final
                    $check_rol = $conn->query("SHOW COLUMNS FROM usuarios LIKE 'rol'");
                    if ($check_rol && $check_rol->num_rows > 0) {
                        $conn->query("ALTER TABLE usuarios ADD COLUMN vip TINYINT(1) DEFAULT 0 AFTER rol");
                    } else {
                        $conn->query("ALTER TABLE usuarios ADD COLUMN vip TINYINT(1) DEFAULT 0");
                    }
                }
            } catch (Exception $e) {
                error_log("Error al verificar/crear columna VIP: " . $e->getMessage());
            }
            
            // Si se asigna una fecha, convertir al usuario en VIP automáticamente
            if (!empty($fecha_expiracion)) {
                $sql = "UPDATE usuarios SET fecha_expiracion = ?, vip = 1, estado = 'activo' WHERE id = ?";
                $stmt = $conn->prepare($sql);
                if ($stmt) {
                    $stmt->bind_param("si", $fecha_expiracion, $user_id);
                    if (!$stmt->execute()) {
                        error_log("Error al actualizar expiración/VIP: " . $stmt->error);
                        $_SESSION['admin_flash'] = "Error al actualizar la fecha de expiración: " . $stmt->error;
                    } else {
                        $_SESSION['admin_flash'] = "Usuario convertido en VIP y fecha de expiración actualizada correctamente.";
                    }
                    $stmt->close();
                } else {
                    error_log("Error al preparar consulta: " . $conn->error);
                    $_SESSION['admin_flash'] = "Error al preparar la consulta: " . $conn->error;
                }
            } else {
                // Si se elimina la fecha, quitar VIP y eliminar IP
                $sql = "UPDATE usuarios SET fecha_expiracion = NULL, vip = 0 WHERE id = ?";
                $stmt = $conn->prepare($sql);
                if ($stmt) {
                    $stmt->bind_param("i", $user_id);
                    if (!$stmt->execute()) {
                        error_log("Error al quitar expiración/VIP: " . $stmt->error);
                        $_SESSION['admin_flash'] = "Error al quitar la fecha de expiración: " . $stmt->error;
                    } else {
                        // Eliminar IP cuando se quita el VIP
                        $check_table = $conn->query("SHOW TABLES LIKE 'usuarios_vip_ips'");
                        if ($check_table && $check_table->num_rows > 0) {
                            $sql_delete_ip = "DELETE FROM usuarios_vip_ips WHERE usuario_id = ?";
                            $stmt_delete = $conn->prepare($sql_delete_ip);
                            if ($stmt_delete) {
                                $stmt_delete->bind_param("i", $user_id);
                                $stmt_delete->execute();
                                $stmt_delete->close();
                            }
                        }
                        $_SESSION['admin_flash'] = "Fecha de expiración eliminada, VIP removido e IP eliminada correctamente.";
                    }
                    $stmt->close();
                } else {
                    error_log("Error al preparar consulta: " . $conn->error);
                    $_SESSION['admin_flash'] = "Error al preparar la consulta: " . $conn->error;
                }
            }
            break;
            
        case 'asignar_ip':
            $ip_servidor = !empty($_POST['ip_servidor']) ? trim($_POST['ip_servidor']) : null;
            
            // Validar formato de IP (básico)
            if ($ip_servidor && !preg_match('/^(\d{1,3}\.){3}\d{1,3}(:\d{1,5})?$/', $ip_servidor)) {
                $_SESSION['admin_flash'] = "Formato de IP inválido. Use el formato: 192.168.1.100:27015";
                break;
            }
            
            // Verificar si existe la tabla usuarios_vip_ips
            $check_table = $conn->query("SHOW TABLES LIKE 'usuarios_vip_ips'");
            if (!$check_table || $check_table->num_rows === 0) {
                // Crear la tabla si no existe
                $create_table = "CREATE TABLE IF NOT EXISTS usuarios_vip_ips (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    usuario_id INT NOT NULL UNIQUE,
                    ip_servidor VARCHAR(45) NOT NULL,
                    fecha_asignacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
                    INDEX idx_usuario (usuario_id)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
                $conn->query($create_table);
            }
            
            if (!empty($ip_servidor)) {
                // Insertar o actualizar IP
                $sql = "INSERT INTO usuarios_vip_ips (usuario_id, ip_servidor) 
                        VALUES (?, ?) 
                        ON DUPLICATE KEY UPDATE ip_servidor = ?, fecha_actualizacion = CURRENT_TIMESTAMP";
                $stmt = $conn->prepare($sql);
                if ($stmt) {
                    $stmt->bind_param("iss", $user_id, $ip_servidor, $ip_servidor);
                    if (!$stmt->execute()) {
                        error_log("Error al asignar IP: " . $stmt->error);
                        $_SESSION['admin_flash'] = "Error al asignar la IP: " . $stmt->error;
                    } else {
                        $_SESSION['admin_flash'] = "IP del servidor asignada correctamente.";
                    }
                    $stmt->close();
                } else {
                    error_log("Error al preparar consulta de IP: " . $conn->error);
                    $_SESSION['admin_flash'] = "Error al preparar la consulta: " . $conn->error;
                }
            } else {
                // Si la IP está vacía, eliminar la IP del usuario
                $sql = "DELETE FROM usuarios_vip_ips WHERE usuario_id = ?";
                $stmt = $conn->prepare($sql);
                if ($stmt) {
                    $stmt->bind_param("i", $user_id);
                    if (!$stmt->execute()) {
                        error_log("Error al eliminar IP: " . $stmt->error);
                        $_SESSION['admin_flash'] = "Error al eliminar la IP: " . $stmt->error;
                    } else {
                        $_SESSION['admin_flash'] = "IP del servidor eliminada correctamente.";
                    }
                    $stmt->close();
                }
            }
            
            // Verificar y eliminar IPs de usuarios VIP expirados
            $sql_cleanup = "DELETE vip FROM usuarios_vip_ips vip
                           INNER JOIN usuarios u ON vip.usuario_id = u.id
                           WHERE u.fecha_expiracion IS NOT NULL 
                           AND u.fecha_expiracion < CURDATE()";
            $conn->query($sql_cleanup);
            
            break;
    }
}

header("Location: dashboard.php");
exit();
?>
