CREATE DATABASE IF NOT EXISTS sistema_contable
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE sistema_contable;

-- Table structure for table `bitacora`
--
CREATE TABLE `bitacora` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `accion` varchar(50) NOT NULL,
  `tabla_afectada` varchar(50) DEFAULT NULL,
  `registro_id` int(11) DEFAULT NULL,
  `detalle` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `fecha` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bitacora`
--

INSERT INTO `bitacora` (`id`, `usuario_id`, `accion`, `tabla_afectada`, `registro_id`, `detalle`, `ip_address`, `fecha`) VALUES
(1, 1, 'login_fallido', 'usuarios', 1, 'Contraseña incorrecta', '::1', '2026-07-07 18:54:01'),
(2, 1, 'login', 'usuarios', 1, 'Inicio de sesión exitoso', '::1', '2026-07-07 18:54:37'),
(3, 1, 'login', 'usuarios', 1, 'Inicio de sesión exitoso', '::1', '2026-07-07 18:57:33'),
(4, 1, 'logout', 'usuarios', 1, 'Cierre de sesión', '::1', '2026-07-07 18:57:35'),
(5, 1, 'login', 'usuarios', 1, 'Inicio de sesión exitoso', '::1', '2026-07-07 20:14:19'),
(150, 6, 'login', 'usuarios', 6, 'Inicio de sesión exitoso', '::1', '2026-07-16 21:42:43');

-- --------------------------------------------------------

--
-- Table structure for table `cierres`
--

CREATE TABLE `cierres` (
  `id` int(11) NOT NULL,
  `tipo` varchar(30) NOT NULL,
  `periodo_inicio` date NOT NULL,
  `periodo_fin` date NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `hash_datos` varchar(255) DEFAULT NULL,
  `firma` text DEFAULT NULL,
  `estado` varchar(20) NOT NULL DEFAULT 'vigente',
  `pdf_ruta` varchar(255) DEFAULT NULL,
  `fecha_cierre` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cierres`
--

INSERT INTO `cierres` (`id`, `tipo`, `periodo_inicio`, `periodo_fin`, `usuario_id`, `hash_datos`, `firma`, `estado`, `pdf_ruta`, `fecha_cierre`) VALUES
(1, 'estado_resultados', '2026-06-15', '2026-07-15', 6, 'ceb7c86c4fb88355204268277db486a60e8d68768f02560b89a0e332e5dc920e', 'PrBj+qpjhEjR+qbS2Cf2lq1RY+g9DAjNTSFj33gWR6wbAyhS8UqGAfLSJdk7lBSzAs3EJ4tkOxoYmGmKxEWHxFkX+gd937MEAHiJdBFwBdKf3weFYGqmaICiXzPJHGnRzKUnZE3SOEBNlHNl4P+0RPmqTt8B/d6Q9FU3KiHrMBCc3FByuqndopusRHeb7XUS1O0lsW8vC/vXqvo5ZzXb0+6+zq0X8ADRr3L01g6SI2oJVesrregk++9NV4tXpEcBjqFZhIaWF9M7hpCJ45UtiQBsBwQUKa3jwRC1h1erJSmSyMAAA+GTUBgRW/FAAwFIVHGA9bz46kkyXpkvK7BiCw==', 'REVISADO', NULL, '2026-07-15 07:24:54'),
(2, 'estado_resultados', '2026-06-15', '2026-07-15', 6, 'ceb7c86c4fb88355204268277db486a60e8d68768f02560b89a0e332e5dc920e', 'PrBj+qpjhEjR+qbS2Cf2lq1RY+g9DAjNTSFj33gWR6wbAyhS8UqGAfLSJdk7lBSzAs3EJ4tkOxoYmGmKxEWHxFkX+gd937MEAHiJdBFwBdKf3weFYGqmaICiXzPJHGnRzKUnZE3SOEBNlHNl4P+0RPmqTt8B/d6Q9FU3KiHrMBCc3FByuqndopusRHeb7XUS1O0lsW8vC/vXqvo5ZzXb0+6+zq0X8ADRr3L01g6SI2oJVesrregk++9NV4tXpEcBjqFZhIaWF9M7hpCJ45UtiQBsBwQUKa3jwRC1h1erJSmSyMAAA+GTUBgRW/FAAwFIVHGA9bz46kkyXpkvK7BiCw==', 'REVISADO', NULL, '2026-07-15 07:25:37'),
(3, 'balance_general', '2026-07-15', '2026-07-15', 6, 'abc7020b56d9aa86bb0911504a3f2c3f74298f47be2b327ee6a8450f833b55ad', 'yaQ4GEiIRPKpVUPylTxqDFwUYPg5z3/H4Nve5ZTK9DoruYGh00flu38Y8z8vPn8sD2Qbx/0uCZlq4kPsHbfBUia/gMQstUvKvuldf3x1Xd1linklIfgb3rKGjMZOKZVRXXN0LQfrGEEAzUylyYoL0tj3eZeL7ms7W3ZTbI1hcgQlTMz/AGpTNcCJYOiLtTsV6gKyDNieaOL0O1XiQuG2UBTYS/rxQ700vE19PAZmaN4qDf37JG2trXX42l3/kg0ji8eICssLJdAR65v3ulpe048abKXpS2jDsRDAdaL0iv4fg7eVM/NKWVM9JLO+RsayQBjtSoj2YBM35sp72DvEJQ==', 'CERRADO', NULL, '2026-07-15 10:40:43'),
(4, 'balance_general', '2026-07-15', '2026-07-15', 6, 'abc7020b56d9aa86bb0911504a3f2c3f74298f47be2b327ee6a8450f833b55ad', 'yaQ4GEiIRPKpVUPylTxqDFwUYPg5z3/H4Nve5ZTK9DoruYGh00flu38Y8z8vPn8sD2Qbx/0uCZlq4kPsHbfBUia/gMQstUvKvuldf3x1Xd1linklIfgb3rKGjMZOKZVRXXN0LQfrGEEAzUylyYoL0tj3eZeL7ms7W3ZTbI1hcgQlTMz/AGpTNcCJYOiLtTsV6gKyDNieaOL0O1XiQuG2UBTYS/rxQ700vE19PAZmaN4qDf37JG2trXX42l3/kg0ji8eICssLJdAR65v3ulpe048abKXpS2jDsRDAdaL0iv4fg7eVM/NKWVM9JLO+RsayQBjtSoj2YBM35sp72DvEJQ==', 'CERRADO', NULL, '2026-07-15 10:43:45'),
(5, 'balance_general', '2026-07-15', '2026-07-15', 6, 'abc7020b56d9aa86bb0911504a3f2c3f74298f47be2b327ee6a8450f833b55ad', 'yaQ4GEiIRPKpVUPylTxqDFwUYPg5z3/H4Nve5ZTK9DoruYGh00flu38Y8z8vPn8sD2Qbx/0uCZlq4kPsHbfBUia/gMQstUvKvuldf3x1Xd1linklIfgb3rKGjMZOKZVRXXN0LQfrGEEAzUylyYoL0tj3eZeL7ms7W3ZTbI1hcgQlTMz/AGpTNcCJYOiLtTsV6gKyDNieaOL0O1XiQuG2UBTYS/rxQ700vE19PAZmaN4qDf37JG2trXX42l3/kg0ji8eICssLJdAR65v3ulpe048abKXpS2jDsRDAdaL0iv4fg7eVM/NKWVM9JLO+RsayQBjtSoj2YBM35sp72DvEJQ==', 'CERRADO', NULL, '2026-07-15 10:48:42'),
(6, 'balance_general', '2026-07-15', '2026-07-15', 6, 'abc7020b56d9aa86bb0911504a3f2c3f74298f47be2b327ee6a8450f833b55ad', 'yaQ4GEiIRPKpVUPylTxqDFwUYPg5z3/H4Nve5ZTK9DoruYGh00flu38Y8z8vPn8sD2Qbx/0uCZlq4kPsHbfBUia/gMQstUvKvuldf3x1Xd1linklIfgb3rKGjMZOKZVRXXN0LQfrGEEAzUylyYoL0tj3eZeL7ms7W3ZTbI1hcgQlTMz/AGpTNcCJYOiLtTsV6gKyDNieaOL0O1XiQuG2UBTYS/rxQ700vE19PAZmaN4qDf37JG2trXX42l3/kg0ji8eICssLJdAR65v3ulpe048abKXpS2jDsRDAdaL0iv4fg7eVM/NKWVM9JLO+RsayQBjtSoj2YBM35sp72DvEJQ==', 'CERRADO', NULL, '2026-07-15 10:54:25'),
(7, 'estado_resultados', '2025-01-15', '2026-07-15', 6, '6a1419d0bb884056842294922b950fa77f1eea448935e4f0f174ed4293d3bfc8', 'Fk9dMRX2cdNxCLRxb3PcOhB84YiaDH99d/nzO2lH9M3uVxud3olAv4oRI+15OKibKs4BiXHEycJqci92z+YKojHjxd7A53XfmnO4dPNxksSMjvo4qsIYCOhpjjvUxI1FPaBOIL7FaNyX//ohgqrsyeeAIyDKsX6ovgIsehGAx6eRcjHY1BZA1JoYyw+lIZr4yzw8AFqb5qvP1tZWELefQthFg58/qDsTVoOqgb2W5bevyD2bDkue7/AavKyemPXAeQKqsR8sE48eANKwx4z3w0WPzeXAFUNV+bAyTu/vXrYtfchZFURMbs59/L7RFj8V9zoT7hi+Pp1D9NNDzOpavQ==', 'CERRADO', NULL, '2026-07-15 11:04:13');

-- --------------------------------------------------------

--
-- Table structure for table `cuentas`
--

CREATE TABLE `cuentas` (
  `id` int(11) NOT NULL,
  `codigo` varchar(20) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `clase` tinyint(4) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `fecha_registro` datetime DEFAULT current_timestamp(),
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `actualizado_por` int(11) DEFAULT NULL,
  `fecha_actualizacion` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cuentas`
--

INSERT INTO `cuentas` (`id`, `codigo`, `nombre`, `clase`, `usuario_id`, `fecha_registro`, `activo`, `actualizado_por`, `fecha_actualizacion`) VALUES
(1, '1.01', 'Caja general', 1, 2, '2026-07-05 11:50:39', 1, NULL, NULL),
(2, '2.01', 'Cuentas por pagar', 2, 2, '2026-07-05 11:50:39', 1, NULL, NULL),
(3, '3.01', 'Capital social', 3, 2, '2026-07-05 11:50:39', 1, NULL, NULL),
(4, '4.01', 'Ventas', 4, 2, '2026-07-05 11:50:39', 1, NULL, NULL),
(5, '5.01', 'Gastos de administracion', 5, 2, '2026-07-05 11:50:39', 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `diario`
--

CREATE TABLE `diario` (
  `id` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `usuario_id` int(11) NOT NULL,
  `estado` varchar(20) NOT NULL DEFAULT 'abierto',
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `actualizado_por` int(11) DEFAULT NULL,
  `fecha_actualizacion` datetime DEFAULT NULL,
  `creado_en` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `diario`
--

INSERT INTO `diario` (`id`, `fecha`, `descripcion`, `usuario_id`, `estado`, `activo`, `actualizado_por`, `fecha_actualizacion`, `creado_en`) VALUES
(1, '2026-06-01', 'Aporte inicial de capital', 2, 'abierto', 1, NULL, NULL, '2026-07-05 11:50:39'),
(2, '2026-06-05', 'Venta de servicios al contado', 2, 'abierto', 1, NULL, NULL, '2026-07-05 11:50:39'),
(3, '2026-06-10', 'Pago de gastos administrativos', 2, 'abierto', 1, NULL, NULL, '2026-07-05 11:50:39'),
(4, '2026-06-15', 'Compra a credito', 2, 'abierto', 1, NULL, NULL, '2026-07-05 11:50:39'),
(5, '2026-06-20', 'Venta de servicios al contado', 2, 'abierto', 1, NULL, NULL, '2026-07-05 11:50:39'),
(6, '2026-06-03', 'Carros', 2, 'abierto', 1, NULL, NULL, '2026-07-14 21:59:04'),
(7, '2026-07-15', 'Casa de campo', 6, 'abierto', 1, NULL, NULL, '2026-07-15 00:28:15');

-- --------------------------------------------------------

--
-- Table structure for table `diario_detalle`
--

CREATE TABLE `diario_detalle` (
  `id` int(11) NOT NULL,
  `diario_id` int(11) NOT NULL,
  `cuenta_id` int(11) NOT NULL,
  `debito` decimal(12,2) NOT NULL DEFAULT 0.00,
  `credito` decimal(12,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `diario_detalle`
--

INSERT INTO `diario_detalle` (`id`, `diario_id`, `cuenta_id`, `debito`, `credito`) VALUES
(1, 1, 1, 5000.00, 0.00),
(2, 1, 3, 0.00, 5000.00),
(3, 2, 1, 1200.00, 0.00),
(4, 2, 4, 0.00, 1200.00),
(5, 3, 5, 300.00, 0.00),
(6, 3, 1, 0.00, 300.00),
(7, 4, 5, 800.00, 0.00),
(8, 4, 2, 0.00, 800.00),
(9, 5, 1, 950.00, 0.00),
(10, 5, 4, 0.00, 950.00),
(11, 6, 4, 0.00, 100000.00),
(12, 6, 1, 100000.00, 0.00),
(13, 7, 1, 200.00, 0.00),
(14, 7, 4, 0.00, 200.00);

-- --------------------------------------------------------

--
-- Table structure for table `permisos`
--

CREATE TABLE `permisos` (
  `id` int(11) NOT NULL,
  `rol_id` int(11) NOT NULL,
  `modulo` varchar(50) NOT NULL,
  `ver` tinyint(1) NOT NULL DEFAULT 0,
  `editar` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permisos`
--

INSERT INTO `permisos` (`id`, `rol_id`, `modulo`, `ver`, `editar`) VALUES
(1, 1, 'usuarios', 1, 1),
(2, 1, 'cuentas', 1, 0),
(3, 1, 'diario', 1, 0),
(4, 1, 'informes', 1, 0),
(5, 2, 'cuentas', 1, 1),
(6, 2, 'diario', 1, 1),
(7, 2, 'informes', 1, 0),
(8, 3, 'cuentas', 1, 1),
(9, 3, 'diario', 1, 1),
(10, 3, 'informes', 1, 1),
(11, 1, 'roles', 1, 1),
(12, 1, 'bitacora', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `nombre`) VALUES
(1, 'Administrador'),
(2, 'Contador'),
(3, 'Gerente Financiero');

-- --------------------------------------------------------

--
-- Table structure for table `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `clave_publica` text DEFAULT NULL,
  `rol_id` int(11) NOT NULL,
  `estado_actividad` tinyint(1) NOT NULL DEFAULT 1,
  `actualizado_por` int(11) DEFAULT NULL,
  `fecha_actualizacion` datetime DEFAULT NULL,
  `creado_en` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `correo`, `contrasena`, `clave_publica`, `rol_id`, `estado_actividad`, `actualizado_por`, `fecha_actualizacion`, `creado_en`) VALUES
(1, 'Dario Admin', 'admin@itech.com', '$2b$10$cg9yZ7jEam3fKOQ9NwqQQu8WNyLxgySI/N1XnBi20vN5KzOXUbEVK', NULL, 1, 0, 6, '2026-07-16 21:41:59', '2026-07-05 11:50:39'),
(2, 'Ana Contadora', 'ana.contadora@itech.com', '$2b$10$cg9yZ7jEam3fKOQ9NwqQQu8WNyLxgySI/N1XnBi20vN5KzOXUbEVK', NULL, 2, 0, 1, '2026-07-14 20:55:53', '2026-07-05 11:50:39'),
(3, 'Luis Gerente', 'luis.gerente@itech.com', '$2b$10$cg9yZ7jEam3fKOQ9NwqQQu8WNyLxgySI/N1XnBi20vN5KzOXUbEVK', NULL, 3, 1, NULL, NULL, '2026-07-05 11:50:39'),
(4, 'Maria Contadora', 'maria.contadora@itech.com', '$2b$10$cg9yZ7jEam3fKOQ9NwqQQu8WNyLxgySI/N1XnBi20vN5KzOXUbEVK', NULL, 2, 1, NULL, NULL, '2026-07-05 11:50:39'),
(5, 'Jose Auxiliar', 'jose.auxiliar@itech.com', '$2b$10$cg9yZ7jEam3fKOQ9NwqQQu8WNyLxgySI/N1XnBi20vN5KzOXUbEVK', NULL, 2, 1, NULL, NULL, '2026-07-05 11:50:39'),
(6, 'Javier', 'javier@gmail.com', '$2y$10$msGh5k9k23A./erPRv7TLOxt4Post3aA9EgjiM.16awGqenanJFB2', NULL, 1, 1, 1, '2026-07-14 21:25:58', '2026-07-14 21:22:39');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bitacora`
--
ALTER TABLE `bitacora`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indexes for table `cierres`
--
ALTER TABLE `cierres`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indexes for table `cuentas`
--
ALTER TABLE `cuentas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo` (`codigo`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `fk_cuentas_actualizado_por` (`actualizado_por`);

--
-- Indexes for table `diario`
--
ALTER TABLE `diario`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `fk_diario_actualizado_por` (`actualizado_por`);

--
-- Indexes for table `diario_detalle`
--
ALTER TABLE `diario_detalle`
  ADD PRIMARY KEY (`id`),
  ADD KEY `diario_id` (`diario_id`),
  ADD KEY `cuenta_id` (`cuenta_id`);

--
-- Indexes for table `permisos`
--
ALTER TABLE `permisos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `rol_id` (`rol_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indexes for table `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `correo` (`correo`),
  ADD KEY `rol_id` (`rol_id`),
  ADD KEY `fk_usuarios_actualizado_por` (`actualizado_por`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bitacora`
--
ALTER TABLE `bitacora`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=151;

--
-- AUTO_INCREMENT for table `cierres`
--
ALTER TABLE `cierres`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `cuentas`
--
ALTER TABLE `cuentas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `diario`
--
ALTER TABLE `diario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `diario_detalle`
--
ALTER TABLE `diario_detalle`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `permisos`
--
ALTER TABLE `permisos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bitacora`
--
ALTER TABLE `bitacora`
  ADD CONSTRAINT `bitacora_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Constraints for table `cierres`
--
ALTER TABLE `cierres`
  ADD CONSTRAINT `cierres_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Constraints for table `cuentas`
--
ALTER TABLE `cuentas`
  ADD CONSTRAINT `cuentas_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `fk_cuentas_actualizado_por` FOREIGN KEY (`actualizado_por`) REFERENCES `usuarios` (`id`);

--
-- Constraints for table `diario`
--
ALTER TABLE `diario`
  ADD CONSTRAINT `diario_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `fk_diario_actualizado_por` FOREIGN KEY (`actualizado_por`) REFERENCES `usuarios` (`id`);

--
-- Constraints for table `diario_detalle`
--
ALTER TABLE `diario_detalle`
  ADD CONSTRAINT `diario_detalle_ibfk_1` FOREIGN KEY (`diario_id`) REFERENCES `diario` (`id`),
  ADD CONSTRAINT `diario_detalle_ibfk_2` FOREIGN KEY (`cuenta_id`) REFERENCES `cuentas` (`id`);

--
-- Constraints for table `permisos`
--
ALTER TABLE `permisos`
  ADD CONSTRAINT `permisos_ibfk_1` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`);

--
-- Constraints for table `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `fk_usuarios_actualizado_por` FOREIGN KEY (`actualizado_por`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
