<?php
/**
 * INSTALADOR AUTOMÁTICO
 * Red Dragons Cup - Sistema de Brackets
 * Ejecuta este archivo UNA SOLA VEZ para configurar la base de datos
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

$installed = false;
$errors = [];
$success_messages = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['instalar'])) {
    
    // Conectar a MySQL sin seleccionar base de datos
    $conn = new mysqli('localhost', 'root', '');
    
    if ($conn->connect_error) {
        $errors[] = "Error de conexión a MySQL: " . $conn->connect_error;
    } else {
        $success_messages[] = "✓ Conectado a MySQL correctamente";
        
        // Crear la base de datos
        $sql = "CREATE DATABASE IF NOT EXISTS red_dragons_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
        if ($conn->query($sql) === TRUE) {
            $success_messages[] = "✓ Base de datos 'red_dragons_db' creada";
            
            // Seleccionar la base de datos
            $conn->select_db('red_dragons_db');
            
            // Crear tabla usuarios
            $sql_usuarios = "CREATE TABLE IF NOT EXISTS usuarios (
                id INT AUTO_INCREMENT PRIMARY KEY,
                usuario VARCHAR(50) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL,
                email VARCHAR(100) NOT NULL UNIQUE,
                rol ENUM('usuario', 'admin') DEFAULT 'usuario',
                fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
            
            if ($conn->query($sql_usuarios) === TRUE) {
                $success_messages[] = "✓ Tabla 'usuarios' creada";
            } else {
                $errors[] = "Error creando tabla usuarios: " . $conn->error;
            }
            
            // Crear tabla equipos
            $sql_equipos = "CREATE TABLE IF NOT EXISTS equipos (
                id INT AUTO_INCREMENT PRIMARY KEY,
                nombre VARCHAR(100) NOT NULL,
                seed INT NOT NULL,
                activo TINYINT(1) DEFAULT 1,
                fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY unique_seed (seed)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
            
            if ($conn->query($sql_equipos) === TRUE) {
                $success_messages[] = "✓ Tabla 'equipos' creada";
            } else {
                $errors[] = "Error creando tabla equipos: " . $conn->error;
            }
            
            // Crear tabla matches
            $sql_matches = "CREATE TABLE IF NOT EXISTS matches (
                id INT AUTO_INCREMENT PRIMARY KEY,
                bracket_tipo ENUM('winners', 'losers', 'grand_final') NOT NULL,
                ronda INT NOT NULL,
                numero_match INT NOT NULL,
                equipo1_id INT DEFAULT NULL,
                equipo2_id INT DEFAULT NULL,
                puntos_equipo1 INT DEFAULT NULL,
                puntos_equipo2 INT DEFAULT NULL,
                ganador_id INT DEFAULT NULL,
                completado TINYINT(1) DEFAULT 0,
                fecha_match TIMESTAMP NULL,
                fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (equipo1_id) REFERENCES equipos(id) ON DELETE SET NULL,
                FOREIGN KEY (equipo2_id) REFERENCES equipos(id) ON DELETE SET NULL,
                FOREIGN KEY (ganador_id) REFERENCES equipos(id) ON DELETE SET NULL,
                KEY idx_bracket_ronda (bracket_tipo, ronda),
                KEY idx_completado (completado)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
            
            if ($conn->query($sql_matches) === TRUE) {
                $success_messages[] = "✓ Tabla 'matches' creada";
            } else {
                $errors[] = "Error creando tabla matches: " . $conn->error;
            }
            
            // Insertar usuario administrador
            $password_hash = password_hash('admin123', PASSWORD_DEFAULT);
            $sql_admin = "INSERT IGNORE INTO usuarios (usuario, password, email, rol) 
                         VALUES ('admin', '$password_hash', 'admin@reddragonscup.com', 'admin')";
            
            if ($conn->query($sql_admin) === TRUE) {
                $success_messages[] = "✓ Usuario administrador creado (admin/admin123)";
                $installed = true;
            } else {
                $errors[] = "Error creando usuario admin: " . $conn->error;
            }
            
        } else {
            $errors[] = "Error creando base de datos: " . $conn->error;
        }
        
        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalador - Red Dragons Cup</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1e1e1e 0%, #2d2d2d 100%);
            color: #fff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        
        .container {
            background: rgba(30, 30, 30, 0.95);
            border: 2px solid #d4af37;
            border-radius: 15px;
            padding: 3rem;
            max-width: 600px;
            width: 100%;
            box-shadow: 0 10px 50px rgba(0, 0, 0, 0.5);
        }
        
        h1 {
            color: #d4af37;
            text-align: center;
            margin-bottom: 1rem;
            font-size: 2.5rem;
            text-shadow: 0 0 20px rgba(212, 175, 55, 0.5);
        }
        
        .subtitle {
            text-align: center;
            color: #b0b0b0;
            margin-bottom: 2rem;
            font-size: 1.1rem;
        }
        
        .warning {
            background: rgba(243, 156, 18, 0.1);
            border: 2px solid #f39c12;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .warning h3 {
            color: #f39c12;
            margin-bottom: 0.5rem;
        }
        
        .warning p {
            color: #e0e0e0;
            line-height: 1.6;
        }
        
        .btn {
            width: 100%;
            padding: 1rem 2rem;
            background: linear-gradient(135deg, #d4af37, #c09b2d);
            color: #000;
            border: none;
            border-radius: 8px;
            font-size: 1.2rem;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 1rem;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(212, 175, 55, 0.4);
        }
        
        .btn:disabled {
            background: #555;
            cursor: not-allowed;
            transform: none;
        }
        
        .message {
            padding: 1rem;
            border-radius: 8px;
            margin: 0.5rem 0;
        }
        
        .success {
            background: rgba(46, 204, 113, 0.1);
            border: 1px solid #2ecc71;
            color: #2ecc71;
        }
        
        .error {
            background: rgba(231, 76, 60, 0.1);
            border: 1px solid #e74c3c;
            color: #e74c3c;
        }
        
        .success-box {
            background: rgba(46, 204, 113, 0.1);
            border: 2px solid #2ecc71;
            border-radius: 10px;
            padding: 2rem;
            margin: 2rem 0;
            text-align: center;
        }
        
        .success-box h2 {
            color: #2ecc71;
            margin-bottom: 1rem;
            font-size: 2rem;
        }
        
        .success-box .credentials {
            background: rgba(0, 0, 0, 0.3);
            padding: 1rem;
            border-radius: 8px;
            margin: 1rem 0;
        }
        
        .success-box .credentials p {
            margin: 0.5rem 0;
            font-size: 1.1rem;
        }
        
        .success-box .credentials strong {
            color: #d4af37;
        }
        
        .btn-success {
            background: linear-gradient(135deg, #2ecc71, #27ae60);
            color: #fff;
        }
        
        .checklist {
            background: rgba(40, 40, 40, 0.6);
            padding: 1.5rem;
            border-radius: 8px;
            margin: 1rem 0;
        }
        
        .checklist h3 {
            color: #d4af37;
            margin-bottom: 1rem;
        }
        
        .checklist ul {
            list-style: none;
        }
        
        .checklist li {
            padding: 0.5rem 0;
            color: #e0e0e0;
        }
        
        .checklist li:before {
            content: "✓ ";
            color: #2ecc71;
            font-weight: bold;
            margin-right: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🏆 Instalador</h1>
        <p class="subtitle">Red Dragons Cup - Sistema de Brackets</p>
        
        <?php if (!$installed && empty($errors)): ?>
            <div class="warning">
                <h3>⚠️ Antes de continuar</h3>
                <p>Este instalador creará la base de datos <strong>red_dragons_db</strong> y todas las tablas necesarias.</p>
                <p style="margin-top: 1rem;"><strong>Asegúrate de que:</strong></p>
                <ul style="margin-left: 1.5rem; margin-top: 0.5rem;">
                    <li>XAMPP esté ejecutándose</li>
                    <li>MySQL esté activo</li>
                    <li>No hayas ejecutado esto antes</li>
                </ul>
            </div>
            
            <form method="POST">
                <button type="submit" name="instalar" class="btn">
                    🚀 Instalar Base de Datos
                </button>
            </form>
        <?php endif; ?>
        
        <?php if (!empty($success_messages)): ?>
            <div style="margin-top: 2rem;">
                <?php foreach ($success_messages as $msg): ?>
                    <div class="message success"><?php echo $msg; ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($errors)): ?>
            <div style="margin-top: 2rem;">
                <?php foreach ($errors as $error): ?>
                    <div class="message error"><?php echo $error; ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($installed && empty($errors)): ?>
            <div class="success-box">
                <h2>✅ ¡Instalación Completada!</h2>
                <p>La base de datos se ha configurado correctamente.</p>
                
                <div class="credentials">
                    <p><strong>Usuario Administrador:</strong></p>
                    <p>👤 Usuario: <strong>admin</strong></p>
                    <p>🔑 Contraseña: <strong>admin123</strong></p>
                </div>
                
                <div class="checklist">
                    <h3>Próximos Pasos:</h3>
                    <ul>
                        <li>Inicia sesión con las credenciales de arriba</li>
                        <li>Ve al panel de administración</li>
                        <li>Agrega los equipos del torneo</li>
                        <li>Genera los matches</li>
                        <li>¡Empieza el torneo!</li>
                    </ul>
                </div>
                
                <a href="login.php" style="text-decoration: none;">
                    <button class="btn btn-success">
                        🔑 Ir a Iniciar Sesión
                    </button>
                </a>
                
                <p style="margin-top: 1rem; color: #b0b0b0; font-size: 0.9rem;">
                    Puedes eliminar este archivo (instalar.php) después de usarlo.
                </p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
