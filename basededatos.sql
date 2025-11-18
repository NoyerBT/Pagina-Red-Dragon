-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Servidor: sql201.infinityfree.com
-- Tiempo de generación: 18-11-2025 a las 00:19:35
-- Versión del servidor: 11.4.7-MariaDB
-- Versión de PHP: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `if0_40411348_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `administradores`
--

CREATE TABLE `administradores` (
  `id` int(11) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `administradores`
--

INSERT INTO `administradores` (`id`, `usuario`, `nombre`, `email`, `password`, `fecha_registro`) VALUES
(1, 'admin', 'Administrador Principal', 'admin@reddragons.com', '$2y$10$A0h770dqQr2schVb/oGRFu31nRfH3SRdVwwQFW3ZcYilL6EJ3DuaC', '2025-11-16 19:24:26');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `estado` varchar(20) NOT NULL DEFAULT 'activo',
  `fecha_expiracion` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `usuario`, `nombre`, `email`, `password`, `fecha_registro`, `estado`, `fecha_expiracion`) VALUES
(1, 'playerone', 'Juan Pérez', 'juan.perez@email.com', '$2y$10$pZLg8MbeNtggf85NbRXEkejieM0dbbXaGmQdIfY/HWXLIRBRVs56K', '2025-11-16 19:24:26', 'activo', '2025-11-28'),
(2, 'gamemaster', 'Ana García', 'ana.garcia@email.com', '$2y$10$pZLg8MbeNtggf85NbRXEkejieM0dbbXaGmQdIfY/HWXLIRBRVs56K', '2025-11-16 19:24:26', 'activo', '2025-12-31'),
(3, 'pro_player', 'Carlos Sánchez', 'carlos.sanchez@email.com', '$2y$10$pZLg8MbeNtggf85NbRXEkejieM0dbbXaGmQdIfY/HWXLIRBRVs56K', '2025-11-16 19:24:26', 'bloqueado', '0000-00-00'),
(4, 'newbie', 'Laura Martínez', 'laura.martinez@email.com', '$2y$10$pZLg8MbeNtggf85NbRXEkejieM0dbbXaGmQdIfY/HWXLIRBRVs56K', '2025-11-16 19:24:26', 'activo', '2025-11-30'),
(5, 'NelsonH', 'Nelson', 'setacew456@chaineor.com', '$2y$10$FkBd3dk6dNHQ.PSCA9fCAOPjdtUMXZq.2WzKbCpSEU5pOFQc749Cy', '2025-11-16 20:43:35', 'bloqueado', NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `administradores`
--
ALTER TABLE `administradores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario` (`usuario`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario` (`usuario`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `administradores`
--
ALTER TABLE `administradores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
