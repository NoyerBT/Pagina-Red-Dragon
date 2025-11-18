<?php
// Detectar si estamos en localhost o producción
$is_localhost = ($_SERVER['SERVER_NAME'] === 'localhost' || $_SERVER['SERVER_ADDR'] === '127.0.0.1');

if ($is_localhost) {
    // Configuración para XAMPP Local
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "red_dragons_db";
} else {
    // Configuración para Producción (InfinityFree)
    $servername = "sql201.infinityfree.com";
    $username = "if0_40411348";
    $password = "GgA9hzZdvjbg8";
    $dbname = "if0_40411348_db";
}

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Configurar charset
$conn->set_charset("utf8mb4");
?>
