-- ================================================
-- CONFIGURACIÓN COMPLETA DE BASE DE DATOS LOCAL
-- Red Dragons Championship - Sistema de Brackets
-- ================================================

-- Crear la base de datos si no existe
CREATE DATABASE IF NOT EXISTS red_dragons_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Usar la base de datos
USE red_dragons_db;

-- ================================================
-- TABLA: usuarios (si no existe)
-- ================================================
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    rol ENUM('usuario', 'admin') DEFAULT 'usuario',
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================
-- TABLA: equipos (para el torneo)
-- ================================================
CREATE TABLE IF NOT EXISTS equipos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    seed INT NOT NULL,
    activo TINYINT(1) DEFAULT 1,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_seed (seed)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================
-- TABLA: matches (para el bracket)
-- ================================================
CREATE TABLE IF NOT EXISTS matches (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================
-- INSERTAR USUARIO ADMINISTRADOR POR DEFECTO
-- ================================================
-- Password: admin123 (encriptado con password_hash)
INSERT IGNORE INTO usuarios (usuario, password, email, rol) 
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@reddragonscup.com', 'admin');

-- ================================================
-- DATOS DE PRUEBA (OPCIONAL)
-- ================================================
-- Descomentar las siguientes líneas si quieres datos de prueba

/*
-- Insertar 8 equipos de prueba
INSERT INTO equipos (nombre, seed) VALUES
('Equipo Alpha', 1),
('Equipo Beta', 2),
('Equipo Gamma', 3),
('Equipo Delta', 4),
('Equipo Epsilon', 5),
('Equipo Zeta', 6),
('Equipo Eta', 7),
('Equipo Theta', 8);

-- Generar 4 matches de prueba en Winners Bracket Ronda 1
INSERT INTO matches (bracket_tipo, ronda, numero_match, equipo1_id, equipo2_id) VALUES
('winners', 1, 1, 1, 2),
('winners', 1, 2, 3, 4),
('winners', 1, 3, 5, 6),
('winners', 1, 4, 7, 8);
*/

-- ================================================
-- VERIFICACIÓN
-- ================================================
SELECT 'Base de datos configurada correctamente!' AS mensaje;
SELECT COUNT(*) AS total_usuarios FROM usuarios;
SELECT COUNT(*) AS total_equipos FROM equipos;
SELECT COUNT(*) AS total_matches FROM matches;
