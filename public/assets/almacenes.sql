-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 07-10-2026 a las 00:18:54
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
-- Base de datos: `myvet`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `almacenes`
--

CREATE TABLE `almacenes` (
  `id` int(11) NOT NULL,
  `codigo` varchar(20) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `hora_cierre_programada` time DEFAULT '18:00:00',
  `ubicacion` varchar(255) NOT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `logo` varchar(250) NOT NULL,
  `ico` varchar(250) NOT NULL,
  `tipo_plan` int(11) NOT NULL DEFAULT 4,
  `pago` int(11) NOT NULL DEFAULT 0,
  `telefono` int(250) NOT NULL DEFAULT 0,
  `correo` varchar(250) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `almacenes`
--

INSERT INTO `almacenes` (`id`, `codigo`, `nombre`, `hora_cierre_programada`, `ubicacion`, `activo`, `fecha_creacion`, `logo`, `ico`, `tipo_plan`, `pago`, `telefono`, `correo`) VALUES
(1, 'ALM-CM', 'Consultorio Ayotzingo', '18:00:00', 'LA cima 11', 1, '2026-02-26 20:34:03', 'uploads/compras/logos/logo_almacen_1_1787601264.png', 'uploads/compras/logos/ico_almacen_1_1787604529.ico', 1, 1, 0, ''),
(3, 'ALM-TEN', 'Tenango', '18:00:00', 'Sucursal Tenango', 1, '2026-02-26 20:34:03', '', '', 4, 1, 0, ''),
(4, 'ALM-VC', 'Valle de Chalco', '18:00:00', 'Sucursal Valle de Chalco', 1, '2026-02-26 20:34:03', '', '', 4, 1, 0, ''),
(5, 'ALM-FF', 'Almacen de Paso', '18:00:00', 'Casa de MAteriales', 0, '2026-05-23 01:00:09', '', '', 4, 0, 0, ''),
(6, '2', 'ALMACEN_2', '22:00:00', 'LA CIMA', 0, '2026-09-02 23:53:59', '', '', 1, 1, 0, '');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `almacenes`
--
ALTER TABLE `almacenes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo` (`codigo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `almacenes`
--
ALTER TABLE `almacenes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
