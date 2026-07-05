CREATE DATABASE IF NOT EXISTS sistema_contable
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE sistema_contable;

-- ---------------------------------------------------------
-- Tabla: roles
-- ---------------------------------------------------------
CREATE TABLE roles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Tabla: usuarios
-- ---------------------------------------------------------
CREATE TABLE usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  correo VARCHAR(100) NOT NULL UNIQUE,
  contrasena VARCHAR(255) NOT NULL,
  rol_id INT NOT NULL,
  estado_actividad TINYINT(1) NOT NULL DEFAULT 1,
  creado_en DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (rol_id) REFERENCES roles(id)
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Tabla: permisos (qué módulo puede ver/editar cada rol)
-- ---------------------------------------------------------
CREATE TABLE permisos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  rol_id INT NOT NULL,
  modulo VARCHAR(50) NOT NULL,
  ver TINYINT(1) NOT NULL DEFAULT 0,
  editar TINYINT(1) NOT NULL DEFAULT 0,
  FOREIGN KEY (rol_id) REFERENCES roles(id)
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Tabla: cuentas (catálogo de cuentas)
-- clase: 1 Activo, 2 Pasivo, 3 Patrimonio, 4 Ingresos, 5 Gastos
-- ---------------------------------------------------------
CREATE TABLE cuentas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  codigo VARCHAR(20) NOT NULL UNIQUE,
  nombre VARCHAR(100) NOT NULL,
  clase TINYINT NOT NULL,
  usuario_id INT NOT NULL,
  fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
  activo TINYINT(1) NOT NULL DEFAULT 1,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Tabla: diario (cabecera de cada asiento contable)
-- ---------------------------------------------------------
CREATE TABLE diario (
  id INT AUTO_INCREMENT PRIMARY KEY,
  fecha DATE NOT NULL,
  descripcion VARCHAR(255),
  usuario_id INT NOT NULL,
  estado VARCHAR(20) NOT NULL DEFAULT 'abierto',
  creado_en DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Tabla: diario_detalle (líneas de cada asiento, partida doble)
-- ---------------------------------------------------------
CREATE TABLE diario_detalle (
  id INT AUTO_INCREMENT PRIMARY KEY,
  diario_id INT NOT NULL,
  cuenta_id INT NOT NULL,
  debito DECIMAL(12,2) NOT NULL DEFAULT 0,
  credito DECIMAL(12,2) NOT NULL DEFAULT 0,
  FOREIGN KEY (diario_id) REFERENCES diario(id),
  FOREIGN KEY (cuenta_id) REFERENCES cuentas(id)
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Tabla: cierres (cierre y firma digital de informes)
-- ---------------------------------------------------------
CREATE TABLE cierres (
  id INT AUTO_INCREMENT PRIMARY KEY,
  tipo VARCHAR(30) NOT NULL,
  periodo_inicio DATE NOT NULL,
  periodo_fin DATE NOT NULL,
  usuario_id INT NOT NULL,
  hash_datos VARCHAR(255),
  firma TEXT,
  estado VARCHAR(20) NOT NULL DEFAULT 'vigente',
  pdf_ruta VARCHAR(255),
  fecha_cierre DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
) ENGINE=InnoDB;

-- =========================================================
-- DATOS DE PRUEBA
-- =========================================================

-- Roles
INSERT INTO roles (nombre) VALUES
('Administrador'),
('Contador'),
('Gerente Financiero');

-- Permisos por rol (ver / editar por módulo)
INSERT INTO permisos (rol_id, modulo, ver, editar) VALUES
(1, 'usuarios', 1, 1),
(1, 'cuentas', 1, 0),
(1, 'diario', 1, 0),
(1, 'informes', 1, 0),
(2, 'cuentas', 1, 1),
(2, 'diario', 1, 1),
(2, 'informes', 1, 0),
(3, 'cuentas', 1, 1),
(3, 'diario', 1, 1),
(3, 'informes', 1, 1);

-- Usuarios de prueba (contraseña real para todos: 123456)
INSERT INTO usuarios (nombre, correo, contrasena, rol_id) VALUES
('Dario Admin', 'admin@itech.com', '$2b$10$cg9yZ7jEam3fKOQ9NwqQQu8WNyLxgySI/N1XnBi20vN5KzOXUbEVK', 1),
('Ana Contadora', 'ana.contadora@itech.com', '$2b$10$cg9yZ7jEam3fKOQ9NwqQQu8WNyLxgySI/N1XnBi20vN5KzOXUbEVK', 2),
('Luis Gerente', 'luis.gerente@itech.com', '$2b$10$cg9yZ7jEam3fKOQ9NwqQQu8WNyLxgySI/N1XnBi20vN5KzOXUbEVK', 3),
('Maria Contadora', 'maria.contadora@itech.com', '$2b$10$cg9yZ7jEam3fKOQ9NwqQQu8WNyLxgySI/N1XnBi20vN5KzOXUbEVK', 2),
('Jose Auxiliar', 'jose.auxiliar@itech.com', '$2b$10$cg9yZ7jEam3fKOQ9NwqQQu8WNyLxgySI/N1XnBi20vN5KzOXUbEVK', 2);

-- Cuentas de catálogo (una por clase contable principal)
INSERT INTO cuentas (codigo, nombre, clase, usuario_id) VALUES
('1.01', 'Caja general', 1, 2),
('2.01', 'Cuentas por pagar', 2, 2),
('3.01', 'Capital social', 3, 2),
('4.01', 'Ventas', 4, 2),
('5.01', 'Gastos de administracion', 5, 2);

-- Asientos de diario
INSERT INTO diario (fecha, descripcion, usuario_id, estado) VALUES
('2026-06-01', 'Aporte inicial de capital', 2, 'abierto'),
('2026-06-05', 'Venta de servicios al contado', 2, 'abierto'),
('2026-06-10', 'Pago de gastos administrativos', 2, 'abierto'),
('2026-06-15', 'Compra a credito', 2, 'abierto'),
('2026-06-20', 'Venta de servicios al contado', 2, 'abierto');

-- Detalle de cada asiento (partida doble: debito = credito por asiento)
INSERT INTO diario_detalle (diario_id, cuenta_id, debito, credito) VALUES
(1, 1, 5000.00, 0),
(1, 3, 0, 5000.00),
(2, 1, 1200.00, 0),
(2, 4, 0, 1200.00),
(3, 5, 300.00, 0),
(3, 1, 0, 300.00),
(4, 5, 800.00, 0),
(4, 2, 0, 800.00),
(5, 1, 950.00, 0),
(5, 4, 0, 950.00);