-- Script SQL para crear tablas de torneos
-- Ejecutar este script en tu base de datos

-- Tabla para almacenar los torneos creados por usuarios VIP
CREATE TABLE IF NOT EXISTS torneos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    nombre_torneo VARCHAR(200) NOT NULL,
    modalidad VARCHAR(50) NOT NULL DEFAULT 'Single Elimination',
    logo VARCHAR(255) DEFAULT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_usuario (usuario_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla para almacenar los equipos de cada torneo
CREATE TABLE IF NOT EXISTS equipos_torneo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    torneo_id INT NOT NULL,
    nombre_equipo VARCHAR(100) NOT NULL,
    tag VARCHAR(20) DEFAULT NULL,
    logo VARCHAR(255) DEFAULT NULL,
    orden INT NOT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (torneo_id) REFERENCES torneos(id) ON DELETE CASCADE,
    INDEX idx_torneo (torneo_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
