<?php
session_start();

// Verificar que el usuario esté autenticado
if (!isset($_SESSION['usuario'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Usuario no autenticado'
    ]);
    exit();
}

require_once 'cnt/conexion.php';

// Obtener información del usuario
$usuario = $_POST['usuario'] ?? $_SESSION['usuario'];

// Verificar que el usuario de la sesión coincida con el enviado
if ($usuario !== $_SESSION['usuario']) {
    echo json_encode([
        'success' => false,
        'message' => 'Error de autenticación'
    ]);
    exit();
}

// Obtener datos completos del usuario desde la base de datos
$sql = "SELECT nombre, email, usuario FROM usuarios WHERE usuario = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $usuario);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
$conn->close();

if (!$user) {
    echo json_encode([
        'success' => false,
        'message' => 'Usuario no encontrado'
    ]);
    exit();
}

// Webhook de Discord
$webhook_url = 'https://discord.com/api/webhooks/1447367245591220377/cWOo0BtZ69v7uMMwJ7ZnMNKcJKwI78LEJvgkjDEcwL24n_WWBr6YsPqEpY-ZfvimO79q';

// Fecha y hora actual
$fecha_hora = date('d/m/Y H:i:s');

// Obtener IP del usuario
$ip_usuario = $_SERVER['REMOTE_ADDR'] ?? 'No disponible';

// Crear el mensaje embed para Discord
$embed = [
    'title' => '💰 Notificación de Pago',
    'description' => 'Un usuario ha notificado que realizó un pago.',
    'color' => 16776960, // Color dorado/amarillo (hex: #FFFF00)
    'fields' => [
        [
            'name' => '👤 Usuario',
            'value' => $user['usuario'],
            'inline' => true
        ],
        [
            'name' => '📝 Nombre Completo',
            'value' => $user['nombre'],
            'inline' => true
        ],
        [
            'name' => '📧 Email',
            'value' => $user['email'],
            'inline' => false
        ],
        [
            'name' => '💵 Monto',
            'value' => 'S/ 40.00',
            'inline' => true
        ],
        [
            'name' => '📅 Fecha y Hora',
            'value' => $fecha_hora,
            'inline' => true
        ],
        [
            'name' => '🌐 IP del Usuario',
            'value' => $ip_usuario,
            'inline' => false
        ]
    ],
    'footer' => [
        'text' => 'Red Dragons Cup - Sistema de Pagos'
    ],
    'timestamp' => date('c') // ISO 8601 format
];

// Datos a enviar al webhook
$data = [
    'username' => 'Bot de Pagos',
    'embeds' => [$embed]
];

// Enviar la petición a Discord
$ch = curl_init($webhook_url);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_error = curl_error($ch);
curl_close($ch);

// Verificar respuesta
if ($http_code === 204 || $http_code === 200) {
    echo json_encode([
        'success' => true,
        'message' => 'Notificación enviada correctamente. El administrador revisará tu pago en las próximas 24 horas.'
    ]);
} else {
    error_log("Error al enviar notificación a Discord. HTTP Code: $http_code. Error: $curl_error. Response: $response");
    echo json_encode([
        'success' => false,
        'message' => 'Error al enviar la notificación. Por favor, contacta al administrador.'
    ]);
}
?>
