<?php
session_start();

// Verificar que el usuario esté logueado
if (!isset($_SESSION['usuario'])) {
    $_SESSION['contacto_error'] = "Debes iniciar sesión para enviar un mensaje.";
    header("Location: contacto.php");
    exit();
}

// Verificar que la petición sea POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['contacto_error'] = "Método no permitido.";
    header("Location: contacto.php");
    exit();
}

require_once 'cnt/conexion.php';

try {
    // Obtener datos del formulario
    $nombre = trim($_POST['nombre']);
    $asunto = trim($_POST['asunto']);
    $mensaje = trim($_POST['mensaje']);
    
    // Validar campos
    if (empty($nombre) || empty($asunto) || empty($mensaje)) {
        throw new Exception("Por favor, completa todos los campos.");
    }
    
    // Obtener el correo del usuario desde la base de datos
    $usuario_sesion = $_SESSION['usuario'];
    $sql = "SELECT email, nombre FROM usuarios WHERE usuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $usuario_sesion);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception("Usuario no encontrado. Por favor, inicia sesión nuevamente.");
    }
    
    $user = $result->fetch_assoc();
    $email_usuario = $user['email'];
    
    // Mapear valores del asunto a textos legibles
    $asuntos_map = [
        'torneo' => 'Consulta sobre el torneo',
        'anticheat' => 'Soporte anticheat',
        'tecnico' => 'Problema técnico',
        'otro' => 'Otro'
    ];
    
    $asunto_texto = isset($asuntos_map[$asunto]) ? $asuntos_map[$asunto] : $asunto;
    
    // Configurar el correo
    $destinatario = "bacallatafur21@gmail.com";
    $asunto_email = $asunto_texto;
    
    // Crear el cuerpo del mensaje
    $cuerpo_mensaje = "Nuevo mensaje de contacto desde Red Dragons Cup\n\n";
    $cuerpo_mensaje .= "Tema: " . $asunto_texto . "\n\n";
    $cuerpo_mensaje .= "Nombre de Usuario (formulario): " . $nombre . "\n";
    $cuerpo_mensaje .= "Usuario registrado: " . $usuario_sesion . "\n";
    $cuerpo_mensaje .= "Correo del usuario: " . $email_usuario . "\n\n";
    $cuerpo_mensaje .= "Mensaje:\n" . $mensaje . "\n";
    
    // Configurar headers del correo
    $headers = "From: " . $email_usuario . "\r\n";
    $headers .= "Reply-To: " . $email_usuario . "\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();
    
    // Enviar el correo
    if (mail($destinatario, $asunto_email, $cuerpo_mensaje, $headers)) {
        $_SESSION['contacto_exito'] = "Tu mensaje ha sido enviado correctamente. Te responderemos pronto.";
    } else {
        throw new Exception("Error al enviar el correo. Por favor, inténtalo de nuevo más tarde.");
    }
    
    $stmt->close();
    $conn->close();
    
    // Redirigir con mensaje de éxito
    header("Location: contacto.php");
    exit();
    
} catch (Exception $e) {
    $_SESSION['contacto_error'] = $e->getMessage();
    header("Location: contacto.php");
    exit();
}
?>

