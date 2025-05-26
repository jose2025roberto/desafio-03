-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 17-05-2025 a las 16:43:44
-- Versión del servidor: 10.4.28-MariaDB
-- Versión de PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `academia_sabatina`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asistencias`
--

CREATE TABLE `asistencias` (
  `id_asistencia` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `codigo_estudiante` varchar(20) DEFAULT NULL,
  `codigo_tutor` varchar(10) DEFAULT NULL,
  `tipo` enum('A','I','J') NOT NULL,
  `id_grupo` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `asistencias`
--

INSERT INTO `asistencias` (`id_asistencia`, `fecha`, `codigo_estudiante`, `codigo_tutor`, `tipo`, `id_grupo`) VALUES
(1, '2025-05-17', 'GA2025001', 'GA001', '', 1),
(2, '2025-05-17', 'GA2025006', 'GA001', '', 1),
(3, '2025-05-19', 'GA2025001', 'GA001', '', NULL),
(4, '2025-05-19', 'GA2025006', 'GA001', '', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `aspectos`
--

CREATE TABLE `aspectos` (
  `id_aspecto` int(11) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `tipo` enum('P','L','G','MG') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `aspectos`
--

INSERT INTO `aspectos` (`id_aspecto`, `descripcion`, `tipo`) VALUES
(1, 'Participación activa en clase', 'P'),
(2, 'Ayuda a sus compañeros', 'P'),
(3, 'Cumplimiento de tareas', 'P'),
(4, 'Puntualidad ejemplar', 'P'),
(5, 'Actitud positiva', 'P'),
(6, 'Conversaciones fuera de lugar', 'L'),
(7, 'Tarea incompleta', 'L'),
(8, 'Uso indebido del celular', 'L'),
(9, 'Llegada tarde ocasional', 'L'),
(10, 'Falta de materiales', 'L'),
(11, 'Falta de respeto al tutor', 'G'),
(12, 'Insultos a compañeros', 'G'),
(13, 'Desobediencia reiterada', 'G'),
(14, 'Dañar el material escolar', 'G'),
(15, 'Salir del aula sin permiso', 'G'),
(16, 'Agresión física', 'MG'),
(17, 'Robo de pertenencias', 'MG'),
(18, 'Amenazas a compañeros', 'MG'),
(19, 'Vandalismo', 'MG'),
(20, 'Fuga del centro educativo', 'MG');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `aspecto_estudiante`
--

CREATE TABLE `aspecto_estudiante` (
  `id` int(11) NOT NULL,
  `id_aspecto` int(11) DEFAULT NULL,
  `fecha` date NOT NULL,
  `codigo_estudiante` varchar(20) DEFAULT NULL,
  `codigo_tutor` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `aspecto_estudiante`
--

INSERT INTO `aspecto_estudiante` (`id`, `id_aspecto`, `fecha`, `codigo_estudiante`, `codigo_tutor`) VALUES
(1, 1, '2025-05-17', 'GA2025001', 'GA001'),
(2, 19, '2025-05-17', 'GA2025006', 'GA001'),
(3, 20, '2025-05-17', 'GA2025006', 'GA001'),
(4, 18, '2025-05-17', 'GA2025006', 'GA001'),
(5, 9, '2025-05-17', 'GA2025006', 'GA001'),
(6, 20, '2025-05-17', 'GA2025006', 'GA001'),
(7, 10, '2025-05-17', 'GA2025006', 'GA001'),
(8, 5, '2025-05-17', 'GA2025006', 'GA001');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estudiantes`
--

CREATE TABLE `estudiantes` (
  `codigo_estudiante` varchar(20) NOT NULL,
  `nombres` varchar(100) DEFAULT NULL,
  `apellidos` varchar(100) DEFAULT NULL,
  `dui` varchar(10) DEFAULT NULL,
  `correo` varchar(100) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `fotografia` varchar(255) DEFAULT NULL,
  `estado` enum('activo','inactivo') DEFAULT 'activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `estudiantes`
--

INSERT INTO `estudiantes` (`codigo_estudiante`, `nombres`, `apellidos`, `dui`, `correo`, `telefono`, `fecha_nacimiento`, `fotografia`, `estado`) VALUES
('G2025007', 'Valeria', 'Gonzalez', '12345678-9', 'valeria.gonzalez@example.com', '555-7890', '2007-07-10', 'valeria_gonzalez.jpg', 'activo'),
('GA2025001', 'Andrea', 'García', '10000001-0', 'andrea.garcia@email.com', '7100-0001', '2010-03-01', 'foto1.jpg', 'activo'),
('GA2025006', 'Fernando', 'García', '10000006-5', 'fernando.garcia@email.com', '7100-0006', '2010-11-03', 'foto6.jpg', 'activo'),
('GA2025011', 'Karen', 'García', '10000011-0', 'karen.garcia@email.com', '7100-0011', '2010-01-11', 'foto11.jpg', 'activo'),
('GA2025016', 'Pedro', 'García', '10000016-5', 'pedro.garcia@email.com', '7100-0016', '2010-09-09', 'foto16.jpg', 'activo'),
('GA2025021', 'Ulises', 'García', '10000021-0', 'ulises.garcia@email.com', '7100-0021', '2010-02-22', 'foto21.jpg', 'activo'),
('GA2025026', 'Zaira', 'García', '10000026-5', 'zaira.garcia@email.com', '7100-0026', '2010-07-17', 'foto26.jpg', 'activo'),
('GA2025031', 'Esteban', 'García', '10000031-0', 'esteban.garcia@email.com', '7100-0031', '2010-12-16', 'foto31.jpg', 'activo'),
('GA2025036', 'Julia', 'García', '10000036-5', 'julia.garcia@email.com', '7100-0036', '2010-06-13', 'foto36.jpg', 'activo'),
('LO2025010', 'Javier', 'López', '10000010-9', 'javier.lopez@email.com', '7100-0010', '2011-12-05', 'foto10.jpg', 'activo'),
('LO2025015', 'Olga', 'López', '10000015-4', 'olga.lopez@email.com', '7100-0015', '2011-10-22', 'foto15.jpg', 'activo'),
('LO2025020', 'Tatiana', 'López', '10000020-9', 'tatiana.lopez@email.com', '7100-0020', '2011-11-29', 'foto20.jpg', 'activo'),
('LO2025025', 'Yolanda', 'López', '10000025-4', 'yolanda.lopez@email.com', '7100-0025', '2011-06-04', 'foto25.jpg', 'activo'),
('LO2025030', 'Diana', 'López', '10000030-9', 'diana.lopez@email.com', '7100-0030', '2011-08-26', 'foto30.jpg', 'activo'),
('LO2025035', 'Iván', 'López', '10000035-4', 'ivan.lopez@email.com', '7100-0035', '2011-03-06', 'foto35.jpg', 'activo'),
('LO2025040', 'Zoe', 'López', '10000040-0', 'zoe.lopez@email.com', '7100-0040', '2011-10-18', 'foto40.jpg', 'activo'),
('ME2025002', 'Bruno', 'Méndez', '10000002-1', 'bruno.mendez@email.com', '7100-0002', '2011-05-02', 'foto2.jpg', 'activo'),
('ME2025007', 'Gabriela', 'Méndez', '10000007-6', 'gabriela.mendez@email.com', '7100-0007', '2011-02-17', 'foto7.jpg', 'activo'),
('ME2025012', 'Luis', 'Méndez', '10000012-1', 'luis.mendez@email.com', '7100-0012', '2011-04-14', 'foto12.jpg', 'activo'),
('ME2025017', 'Quetzal', 'Méndez', '10000017-6', 'quetzal.mendez@email.com', '7100-0017', '2011-03-03', 'foto17.jpg', 'activo'),
('ME2025022', 'Valeria', 'Méndez', '10000022-1', 'valeria.mendez@email.com', '7100-0022', '2011-01-13', 'foto22.jpg', 'activo'),
('ME2025027', 'Alan', 'Méndez', '10000027-6', 'alan.mendez@email.com', '7100-0027', '2011-09-21', 'foto27.jpg', 'activo'),
('ME2025032', 'Fátima', 'Méndez', '10000032-1', 'fatima.mendez@email.com', '7100-0032', '2011-07-08', 'foto32.jpg', 'activo'),
('ME2025037', 'Kevin', 'Méndez', '10000037-6', 'kevin.mendez@email.com', '7100-0037', '2011-10-24', 'foto37.jpg', 'activo'),
('PE2025004', 'David', 'Pérez', '10000004-3', 'david.perez@email.com', '7100-0004', '2012-01-10', 'foto4.jpg', 'activo'),
('PE2025009', 'Isabel', 'Pérez', '10000009-8', 'isabel.perez@email.com', '7100-0009', '2012-03-28', 'foto9.jpg', 'activo'),
('PE2025014', 'Nicolás', 'Pérez', '10000014-3', 'nicolas.perez@email.com', '7100-0014', '2012-06-07', 'foto14.jpg', 'activo'),
('PE2025019', 'Samuel', 'Pérez', '10000019-8', 'samuel.perez@email.com', '7100-0019', '2012-04-01', 'foto19.jpg', 'activo'),
('PE2025024', 'Xavier', 'Pérez', '10000024-3', 'xavier.perez@email.com', '7100-0024', '2012-02-19', 'foto24.jpg', 'activo'),
('PE2025029', 'Carlos', 'Pérez', '10000029-8', 'carlos.perez@email.com', '7100-0029', '2012-05-18', 'foto29.jpg', 'activo'),
('PE2025034', 'Helena', 'Pérez', '10000034-3', 'helena.perez@email.com', '7100-0034', '2012-08-10', 'foto34.jpg', 'activo'),
('PE2025039', 'Mateo', 'Pérez', '10000039-8', 'mateo.perez@email.com', '7100-0039', '2012-11-15', 'foto39.jpg', 'activo'),
('RO2025003', 'Camila', 'Rodríguez', '10000003-2', 'camila.rodriguez@email.com', '7100-0003', '2010-07-15', 'foto3.jpg', 'activo'),
('RO2025008', 'Héctor', 'Rodríguez', '10000008-7', 'hector.rodriguez@email.com', '7100-0008', '2010-06-12', 'foto8.jpg', 'activo'),
('RO2025013', 'María', 'Rodríguez', '10000013-2', 'maria.rodriguez@email.com', '7100-0013', '2010-08-19', 'foto13.jpg', 'activo'),
('RO2025018', 'Rosa', 'Rodríguez', '10000018-7', 'rosa.rodriguez@email.com', '7100-0018', '2010-05-20', 'foto18.jpg', 'activo'),
('RO2025023', 'Wendy', 'Rodríguez', '10000023-2', 'wendy.rodriguez@email.com', '7100-0023', '2010-10-30', 'foto23.jpg', 'activo'),
('RO2025028', 'Beatriz', 'Rodríguez', '10000028-7', 'beatriz.rodriguez@email.com', '7100-0028', '2010-04-23', 'foto28.jpg', 'activo'),
('RO2025033', 'Gerardo', 'Rodríguez', '10000033-2', 'gerardo.rodriguez@email.com', '7100-0033', '2010-11-01', 'foto33.jpg', 'activo'),
('RO2025038', 'Laura', 'Rodríguez', '10000038-7', 'laura.rodriguez@email.com', '7100-0038', '2010-09-07', 'foto38.jpg', 'activo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `grupos`
--

CREATE TABLE `grupos` (
  `id_grupo` int(11) NOT NULL,
  `nombre_grupo` varchar(100) DEFAULT NULL,
  `codigo_tutor` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `grupos`
--

INSERT INTO `grupos` (`id_grupo`, `nombre_grupo`, `codigo_tutor`) VALUES
(1, 'Inducción en matemática ', 'GA001'),
(2, 'Refuerzo de Física Química', 'LO005');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `grupo_estudiantes`
--

CREATE TABLE `grupo_estudiantes` (
  `id` int(11) NOT NULL,
  `id_grupo` int(11) DEFAULT NULL,
  `codigo_estudiante` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `grupo_estudiantes`
--

INSERT INTO `grupo_estudiantes` (`id`, `id_grupo`, `codigo_estudiante`) VALUES
(2, 1, 'GA2025001'),
(3, 1, 'GA2025006'),
(4, 2, 'GA2025011');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `trimestres`
--

CREATE TABLE `trimestres` (
  `id` int(11) NOT NULL,
  `nombre` varchar(20) DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `trimestres`
--

INSERT INTO `trimestres` (`id`, `nombre`, `fecha_inicio`, `fecha_fin`) VALUES
(1, 'Trimestre 1', '2025-01-01', '2025-03-31'),
(2, 'Trimestre 2', '2025-04-01', '2025-06-30'),
(3, 'Trimestre 3', '2025-07-01', '2025-09-30'),
(4, 'Trimestre 4', '2025-10-01', '2025-12-31');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tutores`
--

CREATE TABLE `tutores` (
  `codigo_tutor` varchar(10) NOT NULL,
  `nombres` varchar(100) DEFAULT NULL,
  `apellidos` varchar(100) DEFAULT NULL,
  `dui` varchar(10) DEFAULT NULL,
  `correo` varchar(100) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `fecha_contratacion` date DEFAULT NULL,
  `estado` enum('contratado','despedido','renuncia') DEFAULT 'contratado'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tutores`
--

INSERT INTO `tutores` (`codigo_tutor`, `nombres`, `apellidos`, `dui`, `correo`, `telefono`, `fecha_nacimiento`, `fecha_contratacion`, `estado`) VALUES
('GA001', 'Carlos', 'García', '12345678-0', 'carlos.garcia@email.com', '7000-0001', '1980-05-15', '2022-01-10', 'contratado'),
('LO005', 'Mario', 'López', '56789012-4', 'mario.lopez@email.com', '7000-0005', '1982-09-30', '2018-11-03', 'contratado'),
('ME002', 'Ana', 'Méndez', '23456789-1', 'ana.mendez@email.com', '7000-0002', '1985-08-22', '2021-03-20', 'contratado'),
('PE004', 'Elena', 'Pérez', '45678901-3', 'elena.perez@email.com', '7000-0004', '1975-07-07', '2019-05-10', 'contratado'),
('RO003', 'Luis', 'Rodríguez', '34567890-2', 'luis.rodriguez@email.com', '7000-0003', '1990-12-01', '2020-02-15', 'contratado');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` enum('admin','tutor') NOT NULL,
  `codigo_tutor` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `usuario`, `password`, `rol`, `codigo_tutor`) VALUES
(1, 'admin', '240be518fabd2724ddb6f04eeb1da5967448d7e831c08c8fa822809f74c720a9', 'admin', NULL),
(2, 'GA001', '537d26965fd30bf9ebe78b86a8b56ca954cd886ed6d57e43fd37924f1dc2fcbf', 'tutor', 'GA001');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `asistencias`
--
ALTER TABLE `asistencias`
  ADD PRIMARY KEY (`id_asistencia`),
  ADD KEY `codigo_estudiante` (`codigo_estudiante`),
  ADD KEY `codigo_tutor` (`codigo_tutor`),
  ADD KEY `fk_asistencias_grupos` (`id_grupo`);

--
-- Indices de la tabla `aspectos`
--
ALTER TABLE `aspectos`
  ADD PRIMARY KEY (`id_aspecto`);

--
-- Indices de la tabla `aspecto_estudiante`
--
ALTER TABLE `aspecto_estudiante`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_aspecto` (`id_aspecto`),
  ADD KEY `codigo_estudiante` (`codigo_estudiante`),
  ADD KEY `codigo_tutor` (`codigo_tutor`);

--
-- Indices de la tabla `estudiantes`
--
ALTER TABLE `estudiantes`
  ADD PRIMARY KEY (`codigo_estudiante`),
  ADD UNIQUE KEY `dui` (`dui`);

--
-- Indices de la tabla `grupos`
--
ALTER TABLE `grupos`
  ADD PRIMARY KEY (`id_grupo`),
  ADD KEY `codigo_tutor` (`codigo_tutor`);

--
-- Indices de la tabla `grupo_estudiantes`
--
ALTER TABLE `grupo_estudiantes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_grupo` (`id_grupo`),
  ADD KEY `codigo_estudiante` (`codigo_estudiante`);

--
-- Indices de la tabla `trimestres`
--
ALTER TABLE `trimestres`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tutores`
--
ALTER TABLE `tutores`
  ADD PRIMARY KEY (`codigo_tutor`),
  ADD UNIQUE KEY `dui` (`dui`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `usuario` (`usuario`),
  ADD KEY `codigo_tutor` (`codigo_tutor`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `asistencias`
--
ALTER TABLE `asistencias`
  MODIFY `id_asistencia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `aspectos`
--
ALTER TABLE `aspectos`
  MODIFY `id_aspecto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `aspecto_estudiante`
--
ALTER TABLE `aspecto_estudiante`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `grupos`
--
ALTER TABLE `grupos`
  MODIFY `id_grupo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `grupo_estudiantes`
--
ALTER TABLE `grupo_estudiantes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `trimestres`
--
ALTER TABLE `trimestres`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `asistencias`
--
ALTER TABLE `asistencias`
  ADD CONSTRAINT `asistencias_ibfk_1` FOREIGN KEY (`codigo_estudiante`) REFERENCES `estudiantes` (`codigo_estudiante`),
  ADD CONSTRAINT `asistencias_ibfk_2` FOREIGN KEY (`codigo_tutor`) REFERENCES `tutores` (`codigo_tutor`),
  ADD CONSTRAINT `fk_asistencias_grupos` FOREIGN KEY (`id_grupo`) REFERENCES `grupos` (`id_grupo`);

--
-- Filtros para la tabla `aspecto_estudiante`
--
ALTER TABLE `aspecto_estudiante`
  ADD CONSTRAINT `aspecto_estudiante_ibfk_1` FOREIGN KEY (`id_aspecto`) REFERENCES `aspectos` (`id_aspecto`),
  ADD CONSTRAINT `aspecto_estudiante_ibfk_2` FOREIGN KEY (`codigo_estudiante`) REFERENCES `estudiantes` (`codigo_estudiante`),
  ADD CONSTRAINT `aspecto_estudiante_ibfk_3` FOREIGN KEY (`codigo_tutor`) REFERENCES `tutores` (`codigo_tutor`);

--
-- Filtros para la tabla `grupos`
--
ALTER TABLE `grupos`
  ADD CONSTRAINT `grupos_ibfk_1` FOREIGN KEY (`codigo_tutor`) REFERENCES `tutores` (`codigo_tutor`);

--
-- Filtros para la tabla `grupo_estudiantes`
--
ALTER TABLE `grupo_estudiantes`
  ADD CONSTRAINT `grupo_estudiantes_ibfk_1` FOREIGN KEY (`id_grupo`) REFERENCES `grupos` (`id_grupo`),
  ADD CONSTRAINT `grupo_estudiantes_ibfk_2` FOREIGN KEY (`codigo_estudiante`) REFERENCES `estudiantes` (`codigo_estudiante`);

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`codigo_tutor`) REFERENCES `tutores` (`codigo_tutor`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
