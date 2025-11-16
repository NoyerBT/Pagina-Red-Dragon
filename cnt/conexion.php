<?php
$servername = "sql201.infinityfree.com";
$username = "if0_40411348";
$password = "GgA9hzZdvjbg8";
$dbname = "if0_40411348_db";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>
