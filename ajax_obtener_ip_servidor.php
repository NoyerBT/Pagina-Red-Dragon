<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['usuario'])) {
    echo json_encode(['success' => false, 'message' => 'No estás autenticado']);
    exit();
}

require_once 'cnt/conexion.php';

// Verificar que el usuario sea VIP y obtener su IP
$stmt = $conn->prepare("SELECT u.id, u.usuario, u.vip, u.fecha_expiracion, vip.ip_servidor 
                        FROM usuarios u 
                        LEFT JOIN usuarios_vip_ips vip ON u.id = vip.usuario_id 
                        WHERE u.usuario = ? LIMIT 1");
$stmt->bind_param("s", $_SESSION['usuario']);
$stmt->execute();
$result = $stmt->get_result();

if ($user = $result->fetch_assoc()) {
    // Verificar que sea VIP y que no haya expirado
    $vip_activo = false;
    $vip_expirado = false;
    
    if ($user['vip'] == 1) {
        if (!empty($user['fecha_expiracion'])) {
            $fecha_expiracion = new DateTime($user['fecha_expiracion']);
            $fecha_actual = new DateTime();
            if ($fecha_expiracion >= $fecha_actual) {
                $vip_activo = true;
            } else {
                $vip_expirado = true;
                // Eliminar IP si el VIP expiró
                $stmt_delete = $conn->prepare("DELETE FROM usuarios_vip_ips WHERE usuario_id = ?");
                $stmt_delete->bind_param("i", $user['id']);
                $stmt_delete->execute();
                $stmt_delete->close();
            }
        } else {
            // Si no tiene fecha de expiración pero es VIP, considerarlo activo
            $vip_activo = true;
        }
    }
    
    if ($vip_activo) {
        if ($user['ip_servidor']) {
            echo json_encode([
                'success' => true,
                'es_vip' => true,
                'ip_servidor' => $user['ip_servidor']
            ]);
        } else {
            echo json_encode([
                'success' => true,
                'es_vip' => true,
                'ip_servidor' => null,
                'message' => 'Aún no se te ha asignado una IP de servidor. Contacta con un administrador.'
            ]);
        }
    } else if ($vip_expirado) {
        echo json_encode([
            'success' => true,
            'es_vip' => false,
            'message' => 'Tu membresía VIP ha expirado. La IP del servidor ha sido removida.'
        ]);
    } else {
        echo json_encode([
            'success' => true,
            'es_vip' => false,
            'message' => 'No eres usuario VIP'
        ]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Usuario no encontrado']);
}

$stmt->close();
$conn->close();
?>
