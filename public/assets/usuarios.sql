-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 17-10-2026 a las 16:36:38
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
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol_id` int(11) NOT NULL,
  `almacen_id` int(11) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `cedula` varchar(250) NOT NULL DEFAULT '0',
  `telefono` varchar(250) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `username`, `nombre`, `password`, `rol_id`, `almacen_id`, `activo`, `fecha_creacion`, `cedula`, `telefono`) VALUES
(1, 'saulenriquealbatapia252@gmail.com', 'SAUL ENRIQUE', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, NULL, 1, '2026-02-23 23:52:17', '0', '+525523789029'),
(2, 'PEDRO', 'DR. PEDRO TAPIA', '$2y$10$7wQp8D.mUvvpuRviPO6G0.8chLmuwjP.ckbkCTaFEjEMBjEgDLHVW', 9, 1, 1, '2026-02-28 14:38:54', '0', ''),
(3, 'saulenriquealbatapia253@yahoo.com', 'CASA', '$2y$10$5fSRiU9UPM6E4abIyaghde26vLaRGXbUgn2ATMcPOnfweGMz1h.fq', 2, 1, 1, '2026-03-02 22:31:17', '0', '+525511512854'),
(4, 'JavierTrabajador', 'JavierTrabajador', '$2y$10$LKFr4og.uGpUlL7PRedxHe5gAuIctxgMAbG/qkVc4Ons9XPh5eR6a', 6, 1, 1, '2026-03-25 18:27:21', '0', ''),
(5, 'vero', 'VERONICA GONZAGA TORRES', '$2y$10$uYXSSRCAtl9qn8K1s0OBMudqtmKxDXgUt4DDCbBDkkaQhwjCd93Z2', 1, NULL, 1, '2026-03-26 20:08:52', '0', ''),
(6, 'andrea', 'Andrea Chavez Rayon', '$2y$10$JFwWXOQ4KxkivGA.gra6DOIDcVP7yZLfiaMFboAPe6rw/gaWaBTVi', 2, 4, 1, '2026-03-26 20:09:14', '0', ''),
(7, 'jackeline', 'Jackeline', '$2y$10$lyWVmjxhit8jVI3GLIQASO/d8Cu/ubuB/3dnABZHmoFSVVbKR5m8u', 1, NULL, 1, '2026-04-28 18:05:33', '0', ''),
(8, 'Diana', 'DIANA HERNANDEZ APOSTOL', '$2y$10$U5T8T1rNLAK7CjU10panMer36vTtDn21iM3OrOgFn9rxWETm./J.m', 1, NULL, 1, '2026-05-25 14:59:41', '0', ''),
(9, 'Sergio@gmail.com', 'SERGIO CELIS TRUJILLO', '$2y$10$CVca.F4n0ACDZqtOUDQ5IO2xIyihKU3Q7/7bp6.1yRU.l/6syJDea', 0, 3, 1, '2026-05-25 15:00:34', '0', '+551234567890'),
(10, 'paola', 'Paola', '$2y$10$J.Aboz8GJr.XdN/1tJUVF.iYzLlcO5A8Eh64trsrhr2u9Kt2vXh36', 3, 2, 1, '2026-06-26 17:05:01', '0', ''),
(11, 'lizabeth', 'Elizabeth', '$2y$10$.hEeRPvt1zoRIQzzOAlrlu7qh9JOu9EVDLCVHBxKYR3Z/Hjz7M03a', 2, 2, 1, '2026-06-26 17:05:40', '0', ''),
(12, 'peluchin', 'Peluchin', '$2y$10$DwfaSf89gC4/CO7WLZ2Zy.8FTYr7bo6c.egs3RdiE85/fBRX7UVSS', 7, NULL, 1, '2026-06-30 16:10:45', '0', ''),
(13, 'alejandro', 'ALEJANDRO CASALES GONZAGA', '$2y$10$nI.SpHr4aRLeYJqER.CUgef3DEXQ1dCSsSdIccOyE.Xs3qiFE1Dqq', 1, NULL, 1, '2026-07-02 16:36:01', '0', ''),
(14, 'ismael', 'ISMAEL MARTINEZ OROPEZA', '$2y$10$ETzj2qLnHBKGDwyR9fjuduOggrlkc83M0lTuH0Q4oV5DF57lMkseC', 5, NULL, 1, '2026-07-02 16:39:13', '0', ''),
(15, 'ramiro', 'RAMIRO CADENA TEPEHUA', '$2y$10$sx6uiW/ERLcjbdBrlBYvVupuEScSWFFNgx/R2z/SXTJrZczebqvoO', 5, NULL, 1, '2026-07-02 16:39:55', '0', ''),
(16, 'marina', 'MARINA HERNANDEZ MUÑOS', '$2y$10$mEUjccA6d6bBWnfqEgcVA.G3MvPhykCxN51YbEi3og4gR1bKM1oja', 5, NULL, 1, '2026-07-02 16:40:54', '0', ''),
(17, 'yanin', 'YANIN  CASALES GONZAGA', '$2y$10$F6Qsz2eMl7cTnmpS3mrePeOQQpie3sHU0RsoQ6DNgyg74gqYg.pMS', 5, NULL, 1, '2026-07-02 16:42:37', '0', ''),
(18, 'oscar', 'OSCAR', '$2y$10$51uH7eYSDEu5ltP8kwy/IuFj9kf0bFQRiSb25otg0nqMR9A79YsKq', 1, NULL, 1, '2026-07-13 17:24:57', '0', ''),
(19, 'gema', 'DR. GEMA TELLEZ', '$2y$10$KU3CWEs8Qd2ydkIpC4OjEOxv.xQIwZ81OCgV76fwOq6peVKBC7Zm2', 10, 1, 1, '2026-10-07 18:58:33', '0', ''),
(20, 'juan@jimenez.com', 'JUAN JIMENEz', '$2y$10$ErpuUxvKEzqsL2L38QGrX.aP8aoQUyMn7qpgr0pPK2c9K3ydq.VH.', 10, 1, 1, '2026-10-14 23:02:03', '0', '');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `rol_id` (`rol_id`),
  ADD KEY `fk_usuario_almacen` (`almacen_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
