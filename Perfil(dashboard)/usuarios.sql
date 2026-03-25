-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 24-03-2026 a las 01:31:18
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
-- Base de datos: `life_below_blog`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `user` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `fecha_de_registro` datetime NOT NULL DEFAULT current_timestamp(),
  `rol` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `user`, `password`, `email`, `fecha_de_registro`, `rol`) VALUES
(6, 'Marino', '$2y$10$vcvehfs.scKT9staxwq91.CZb.GwkQQ/iiYJ/5z6jjOtuJptbdaEy', 'salchichon@123', '2026-03-22 21:54:21', ''),
(7, 'braulio', '$2y$10$n7dWNB3Vla1BGPCGyjgKe.qn8wdGAn5AsEWSZRJeeKQBPyoLtKgI6', 'dsalgadozepeda@gmail.com', '2026-03-23 10:02:50', ''),
(8, 'Pablito', '$2y$10$CnnkA4LiOBYnqyiNsJW9EOyyp.Y8GmCKnrMYDv52sIE2iUDXdzVO6', 'dsalgadozepeda@g', '2026-03-23 10:04:29', ''),
(9, 'isis', '$2y$10$U2ithlHtuVPpX2N.Q.XcZOAt47zISyMXdYwfotc2iNF5iD8GKtNFa', 'isegura@ucol.mx', '2026-03-23 10:47:22', '');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`user`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
