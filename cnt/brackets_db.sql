-- Tabla para equipos del torneo
CREATE TABLE IF NOT EXISTS equipos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    seed INT NOT NULL,
    activo TINYINT(1) DEFAULT 1,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla para matches del bracket
CREATE TABLE IF NOT EXISTS matches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    bracket_tipo ENUM('winners', 'losers', 'grand_final') NOT NULL,
    ronda INT NOT NULL,
    numero_match INT NOT NULL,
    equipo1_id INT,
    equipo2_id INT,
    puntos_equipo1 INT DEFAULT NULL,
    puntos_equipo2 INT DEFAULT NULL,
    ganador_id INT DEFAULT NULL,
    completado TINYINT(1) DEFAULT 0,
    fecha_match TIMESTAMP NULL,
    FOREIGN KEY (equipo1_id) REFERENCES equipos(id) ON DELETE SET NULL,
    FOREIGN KEY (equipo2_id) REFERENCES equipos(id) ON DELETE SET NULL,
    FOREIGN KEY (ganador_id) REFERENCES equipos(id) ON DELETE SET NULL
);

-- Índices para optimizar búsquedas
CREATE INDEX idx_bracket_ronda ON matches(bracket_tipo, ronda);
CREATE INDEX idx_equipo_seed ON equipos(seed);
