<?php
session_start();
require_once 'cnt/conexion.php';

// Verificar que el usuario esté logueado
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

// Verificar que el usuario tenga plan activo
$plan_activo = false;
$stmt = $conn->prepare("SELECT id, estado, fecha_expiracion FROM usuarios WHERE usuario = ? LIMIT 1");
$stmt->bind_param("s", $_SESSION['usuario']);
$stmt->execute();
$result = $stmt->get_result();

if ($user = $result->fetch_assoc()) {
    $usuario_id = $user['id'];
    if ($user['estado'] === 'activo' && !empty($user['fecha_expiracion'])) {
        try {
            $hoy = new DateTime('today');
            $expiracion = new DateTime($user['fecha_expiracion']);
            if ($expiracion >= $hoy) {
                $plan_activo = true;
            }
        } catch (Exception $e) {
            error_log('Error evaluando expiración de plan: ' . $e->getMessage());
        }
    }
}

$stmt->close();

if (!$plan_activo) {
    $_SESSION['error'] = "No tienes un plan activo para crear torneos";
    header("Location: torneo.php");
    exit();
}

// Validar que se haya enviado el nombre del torneo
if (empty($_POST['nombre_torneo']) || !isset($_POST['equipos']) || count($_POST['equipos']) < 2) {
    $_SESSION['error'] = "Debes ingresar un nombre de torneo y al menos 2 equipos";
    header("Location: crear_torneo.php");
    exit();
}

// Validar máximo de equipos
if (count($_POST['equipos']) > 16) {
    $_SESSION['error'] = "El máximo de equipos permitidos es 16";
    header("Location: crear_torneo.php");
    exit();
}

try {
    // Crear directorio para logos si no existe
    $logos_dir = 'logos_equipos/';
    if (!file_exists($logos_dir)) {
        mkdir($logos_dir, 0777, true);
    }

    // Iniciar transacción
    $conn->begin_transaction();

    // Insertar el torneo
    $nombre_torneo = trim($_POST['nombre_torneo']);
    $stmt = $conn->prepare("INSERT INTO torneos (usuario_id, nombre_torneo) VALUES (?, ?)");
    $stmt->bind_param("is", $usuario_id, $nombre_torneo);
    
    if (!$stmt->execute()) {
        throw new Exception("Error al crear el torneo: " . $conn->error);
    }
    
    $torneo_id = $conn->insert_id;
    $stmt->close();

    // Procesar equipos
    $equipos = $_POST['equipos'];
    $orden = 1;

    foreach ($equipos as $index => $equipo) {
        $nombre_equipo = trim($equipo['nombre']);
        $tag = !empty($equipo['tag']) ? trim($equipo['tag']) : null;
        $logo_path = null;

        // Procesar logo si se subió
        if (isset($_FILES['equipos']['name'][$index]['logo']) && 
            $_FILES['equipos']['error'][$index]['logo'] === UPLOAD_ERR_OK) {
            
            $file = $_FILES['equipos'];
            $file_name = $file['name'][$index]['logo'];
            $file_tmp = $file['tmp_name'][$index]['logo'];
            $file_size = $file['size'][$index]['logo'];
            $file_error = $file['error'][$index]['logo'];

            // Validar tipo de archivo
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $allowed_ext = ['png', 'jpg', 'jpeg'];

            if (!in_array($file_ext, $allowed_ext)) {
                throw new Exception("El logo del equipo '$nombre_equipo' debe ser PNG o JPG");
            }

            // Validar tamaño (máximo 5MB)
            if ($file_size > 5242880) {
                throw new Exception("El logo del equipo '$nombre_equipo' es muy grande (máximo 5MB)");
            }

            // Generar nombre único para el logo
            $new_file_name = 'equipo_' . $torneo_id . '_' . $orden . '_' . time() . '.' . $file_ext;
            $logo_path = $logos_dir . $new_file_name;

            // Redimensionar y guardar imagen
            if (!redimensionarImagen($file_tmp, $logo_path, 200, 200)) {
                throw new Exception("Error al procesar el logo del equipo '$nombre_equipo'");
            }
        }

        // Insertar equipo
        $stmt = $conn->prepare("INSERT INTO equipos_torneo (torneo_id, nombre_equipo, tag, logo, orden) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("isssi", $torneo_id, $nombre_equipo, $tag, $logo_path, $orden);
        
        if (!$stmt->execute()) {
            throw new Exception("Error al agregar el equipo '$nombre_equipo': " . $conn->error);
        }
        
        $stmt->close();
        $orden++;
    }

    // Confirmar transacción
    $conn->commit();
    
    $_SESSION['success'] = "Torneo creado exitosamente";
    header("Location: torneo.php");
    exit();

} catch (Exception $e) {
    // Rollback en caso de error
    $conn->rollback();
    $_SESSION['error'] = $e->getMessage();
    header("Location: crear_torneo.php");
    exit();
}

// Función para redimensionar imágenes
function redimensionarImagen($source, $destination, $width, $height) {
    $image_info = getimagesize($source);
    
    if ($image_info === false) {
        return false;
    }

    $source_width = $image_info[0];
    $source_height = $image_info[1];
    $mime_type = $image_info['mime'];

    // Calcular dimensiones manteniendo aspecto
    $ratio = min($width / $source_width, $height / $source_height);
    $new_width = round($source_width * $ratio);
    $new_height = round($source_height * $ratio);

    // Crear imagen desde fuente
    switch ($mime_type) {
        case 'image/jpeg':
            $source_image = imagecreatefromjpeg($source);
            break;
        case 'image/png':
            $source_image = imagecreatefrompng($source);
            break;
        default:
            return false;
    }

    if ($source_image === false) {
        return false;
    }

    // Crear imagen destino
    $destination_image = imagecreatetruecolor($width, $height);
    
    // Mantener transparencia para PNG
    if ($mime_type === 'image/png') {
        imagealphablending($destination_image, false);
        imagesavealpha($destination_image, true);
        $transparent = imagecolorallocatealpha($destination_image, 0, 0, 0, 127);
        imagefill($destination_image, 0, 0, $transparent);
    } else {
        $white = imagecolorallocate($destination_image, 255, 255, 255);
        imagefill($destination_image, 0, 0, $white);
    }

    // Centrar la imagen
    $x_offset = ($width - $new_width) / 2;
    $y_offset = ($height - $new_height) / 2;

    // Redimensionar
    imagecopyresampled(
        $destination_image, $source_image,
        $x_offset, $y_offset, 0, 0,
        $new_width, $new_height, $source_width, $source_height
    );

    // Guardar imagen
    $result = false;
    switch ($mime_type) {
        case 'image/jpeg':
            $result = imagejpeg($destination_image, $destination, 85);
            break;
        case 'image/png':
            $result = imagepng($destination_image, $destination, 9);
            break;
    }

    // Liberar memoria
    imagedestroy($source_image);
    imagedestroy($destination_image);

    return $result;
}

$conn->close();
?>
