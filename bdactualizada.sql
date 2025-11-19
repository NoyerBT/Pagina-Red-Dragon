-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 19-11-2025 a las 08:02:08
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `rdc`
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
-- Estructura de tabla para la tabla `equipos`
--

CREATE TABLE `equipos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `seed` int(11) NOT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `equipos`
--

INSERT INTO `equipos` (`id`, `nombre`, `seed`, `activo`, `fecha_registro`) VALUES
(1, 'MONKEY GAN', 0, 1, '2025-11-19 05:57:46'),
(2, 'PITUFIALDEA', 0, 1, '2025-11-19 05:57:46'),
(3, 'PLAZA VERGANO', 0, 1, '2025-11-19 05:57:46'),
(4, 'AFTER END', 0, 1, '2025-11-19 05:57:46'),
(5, 'GLORIUOS TEAM', 0, 1, '2025-11-19 05:57:46'),
(6, 'ENJAMBRE ZERG', 0, 1, '2025-11-19 05:57:46'),
(7, 'LOS AMOS DEL CERRO', 0, 1, '2025-11-19 05:57:46'),
(8, 'SINC4', 0, 1, '2025-11-19 05:57:46'),
(9, 'FURIA ROJA', 0, 1, '2025-11-19 05:57:46'),
(10, 'UMM', 0, 1, '2025-11-19 05:57:46'),
(11, 'AKATSUKI', 0, 1, '2025-11-19 05:57:46'),
(12, 'ASSAULT', 0, 1, '2025-11-19 05:57:46'),
(13, 'YATAGARASU', 0, 1, '2025-11-19 05:57:46'),
(14, 'SIGMA MALES', 0, 1, '2025-11-19 05:57:46'),
(15, 'LOS CHUKIS DE SIBERIA', 0, 1, '2025-11-19 05:57:46'),
(16, 'UIZ', 0, 1, '2025-11-19 05:57:46'),
(17, 'THE UNTOUCHABLES', 0, 1, '2025-11-19 05:57:46'),
(18, 'BLACK DRAGON\'S', 0, 1, '2025-11-19 05:57:46'),
(19, 'LOS SAFE-Z', 0, 1, '2025-11-19 05:57:46'),
(20, 'TETONES', 0, 1, '2025-11-19 05:57:46'),
(21, 'ICE BLOOD', 0, 1, '2025-11-19 05:57:46'),
(22, 'NEW WORLD ORDER', 0, 1, '2025-11-19 05:57:46'),
(23, 'BRAHMAN', 0, 1, '2025-11-19 05:57:46'),
(24, 'ONE LAVA', 0, 1, '2025-11-19 05:57:46'),
(25, '9Z TEAM', 0, 1, '2025-11-19 05:57:46'),
(26, 'ANTRAX', 0, 1, '2025-11-19 05:57:46'),
(27, 'X FORCE', 0, 1, '2025-11-19 05:57:46'),
(28, '2 LUV', 0, 1, '2025-11-19 05:57:46'),
(29, 'LUXURY', 0, 1, '2025-11-19 05:57:46'),
(30, 'ASTRAL ACENT', 0, 1, '2025-11-19 05:57:46'),
(31, 'CUATRONEADOS', 0, 1, '2025-11-19 05:57:46'),
(32, 'SAKAMOTO FAMILY', 0, 1, '2025-11-19 05:57:46'),
(33, 'BREAKING RETURN', 0, 1, '2025-11-19 05:57:46'),
(34, 'SAINT', 0, 1, '2025-11-19 05:57:46'),
(35, 'SYNADWTS', 0, 1, '2025-11-19 05:57:46'),
(36, 'NECTAR', 0, 1, '2025-11-19 05:57:46'),
(37, 'SUICIDE BOYS', 0, 1, '2025-11-19 05:57:46'),
(38, 'HISTERIA', 0, 1, '2025-11-19 05:57:46'),
(39, 'KINYA', 0, 1, '2025-11-19 05:57:46'),
(40, 'LOS LIZAMITAS', 0, 1, '2025-11-19 05:57:46'),
(41, 'NOT ENOUGH', 0, 1, '2025-11-19 05:57:46'),
(42, 'LOS ESPIRITUS', 0, 1, '2025-11-19 05:57:46'),
(43, 'HELL REBELS', 0, 1, '2025-11-19 05:57:46'),
(44, 'INTENSA CENTER', 0, 1, '2025-11-19 05:57:46'),
(45, 'LOS VILLANOS DEL LEFT', 0, 1, '2025-11-19 05:57:46'),
(46, 'RG4LIFE', 0, 1, '2025-11-19 05:57:46'),
(47, 'LETTER´S', 0, 1, '2025-11-19 05:57:46'),
(48, 'AMATERASSU', 0, 1, '2025-11-19 05:57:46');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `matches`
--

CREATE TABLE `matches` (
  `id` int(11) NOT NULL,
  `bracket_tipo` enum('winners','losers','grand_final') NOT NULL,
  `ronda` int(11) NOT NULL,
  `numero_match` int(11) NOT NULL,
  `equipo1_id` int(11) DEFAULT NULL,
  `equipo2_id` int(11) DEFAULT NULL,
  `puntos_equipo1` int(11) DEFAULT NULL,
  `puntos_equipo2` int(11) DEFAULT NULL,
  `ganador_id` int(11) DEFAULT NULL,
  `completado` tinyint(1) DEFAULT 0,
  `fecha_match` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `matches`
--

INSERT INTO `matches` (`id`, `bracket_tipo`, `ronda`, `numero_match`, `equipo1_id`, `equipo2_id`, `puntos_equipo1`, `puntos_equipo2`, `ganador_id`, `completado`, `fecha_match`) VALUES
(49, 'winners', 1, 1, 1, 2, NULL, NULL, NULL, 0, NULL),
(50, 'winners', 1, 2, 3, 4, 2000, 1800, 3, 1, NULL),
(51, 'winners', 2, 1, NULL, 3, NULL, NULL, NULL, 0, NULL),
(52, 'losers', 1, 1, 4, NULL, NULL, NULL, NULL, 0, NULL);

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
-- Indices de la tabla `equipos`
--
ALTER TABLE `equipos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_equipo_seed` (`seed`);

--
-- Indices de la tabla `matches`
--
ALTER TABLE `matches`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_bracket_ronda` (`bracket_tipo`,`ronda`),
  ADD KEY `equipo1_id` (`equipo1_id`),
  ADD KEY `equipo2_id` (`equipo2_id`),
  ADD KEY `ganador_id` (`ganador_id`);

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
-- AUTO_INCREMENT de la tabla `equipos`
--
ALTER TABLE `equipos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT de la tabla `matches`
--
ALTER TABLE `matches`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `matches`
--
ALTER TABLE `matches`
  ADD CONSTRAINT `matches_ibfk_1` FOREIGN KEY (`equipo1_id`) REFERENCES `equipos` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `matches_ibfk_2` FOREIGN KEY (`equipo2_id`) REFERENCES `equipos` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `matches_ibfk_3` FOREIGN KEY (`ganador_id`) REFERENCES `equipos` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
