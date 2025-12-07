-- Crear tabla para almacenar IPs de usuarios VIP
CREATE TABLE IF NOT EXISTS usuarios_vip_ips (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL UNIQUE,
    ip_servidor VARCHAR(45) NOT NULL,
    fecha_asignacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_usuario (usuario_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Agregar columna VIP a la tabla usuarios (ejecutar solo si no existe)
-- Verificar primero si existe la columna antes de agregarla
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists 
FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
AND TABLE_NAME = 'usuarios' 
AND COLUMN_NAME = 'vip';

SET @query = IF(@col_exists = 0, 
    'ALTER TABLE usuarios ADD COLUMN vip TINYINT(1) DEFAULT 0 AFTER rol', 
    'SELECT "Columna vip ya existe" AS mensaje');
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
