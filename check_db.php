<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Diagnóstico de Base de Datos</h1>";

try {
    require_once 'cnt/conexion.php';
    echo "<p style='color: green;'>✅ Conexión a la base de datos exitosa.</p>";
    
    // Verificar base de datos seleccionada
    $result = $conn->query("SELECT DATABASE()");
    $row = $result->fetch_row();
    echo "<p>Base de datos actual: <strong>" . $row[0] . "</strong></p>";

    // Listar tablas
    echo "<h2>Tablas en la base de datos:</h2>";
    $result = $conn->query("SHOW TABLES");
    if ($result->num_rows > 0) {
        echo "<ul>";
        $found_usuarios = false;
        while($row = $result->fetch_row()) {
            echo "<li>" . $row[0] . "</li>";
            if ($row[0] === 'usuarios') $found_usuarios = true;
        }
        echo "</ul>";
        
        if ($found_usuarios) {
            echo "<p style='color: green;'>✅ Tabla 'usuarios' encontrada.</p>";
            
            // Mostrar estructura de usuarios
            echo "<h2>Estructura de 'usuarios':</h2>";
            $result = $conn->query("DESCRIBE usuarios");
            echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>
                    <tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                foreach($row as $cell) {
                    echo "<td>" . htmlspecialchars($cell ?? 'NULL') . "</td>";
                }
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p style='color: red;'>❌ Tabla 'usuarios' NO encontrada.</p>";
            echo "<p>Intentando crear tabla usuarios...</p>";
            
            $sql = "CREATE TABLE IF NOT EXISTS usuarios (
                id INT AUTO_INCREMENT PRIMARY KEY,
                nombre VARCHAR(100) NOT NULL,
                email VARCHAR(100) NOT NULL UNIQUE,
                usuario VARCHAR(50) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL,
                estado VARCHAR(20) DEFAULT 'activo',
                fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )";
            
            if ($conn->query($sql) === TRUE) {
                echo "<p style='color: green;'>✅ Tabla 'usuarios' creada correctamente.</p>";
            } else {
                echo "<p style='color: red;'>❌ Error al crear tabla: " . $conn->error . "</p>";
            }
        }
    } else {
        echo "<p>No hay tablas en la base de datos.</p>";
    }

} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error Fatal: " . $e->getMessage() . "</p>";
}
?>
