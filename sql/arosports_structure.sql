-- Base de datos para sistema Arosports
-- Compatible con MySQL 5.7

CREATE DATABASE IF NOT EXISTS `arosports` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `arosports`;

-- Tabla de usuarios del sistema
CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) UNIQUE NOT NULL,
  `password` varchar(255) NOT NULL,
  `tipo_usuario` enum('superadmin','admin','cliente') DEFAULT 'cliente',
  `telefono` varchar(20) DEFAULT NULL,
  `direccion` text DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `fecha_registro` timestamp DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Tabla de clubes
CREATE TABLE `clubes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `direccion` text DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `fecha_registro` timestamp DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Tabla de fraccionamientos
CREATE TABLE `fraccionamientos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `direccion` text DEFAULT NULL,
  `club_id` int(11) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `fecha_registro` timestamp DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`club_id`) REFERENCES `clubes`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Tabla de empresas
CREATE TABLE `empresas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `rfc` varchar(20) DEFAULT NULL,
  `razon_social` varchar(150) DEFAULT NULL,
  `direccion` text DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `fecha_registro` timestamp DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Tabla de reservas (núcleo del sistema financiero)
CREATE TABLE `reservas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `club_id` int(11) DEFAULT NULL,
  `fraccionamiento_id` int(11) DEFAULT NULL,
  `empresa_id` int(11) DEFAULT NULL,
  `fecha_reserva` date NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL,
  `precio` decimal(10,2) NOT NULL DEFAULT 0.00,
  `estado` enum('pendiente','confirmada','cancelada','completada') DEFAULT 'pendiente',
  `observaciones` text DEFAULT NULL,
  `fecha_registro` timestamp DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`club_id`) REFERENCES `clubes`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`fraccionamiento_id`) REFERENCES `fraccionamientos`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`empresa_id`) REFERENCES `empresas`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Datos de ejemplo
INSERT INTO `usuarios` (`nombre`, `email`, `password`, `tipo_usuario`, `telefono`) VALUES
('Super Administrador', 'admin@arosports.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'superadmin', '5551234567'),
('Administrador Club', 'admin.club@arosports.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', '5551234568'),
('Cliente Demo', 'cliente@demo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'cliente', '5551234569');

INSERT INTO `clubes` (`nombre`, `descripcion`, `direccion`, `telefono`, `email`) VALUES
('Club Deportivo Arosports', 'Club deportivo principal con múltiples canchas', 'Av. Principal 123, Ciudad', '5551234567', 'info@arosports.com'),
('Club Secundario Norte', 'Sucursal norte con instalaciones modernas', 'Av. Norte 456, Ciudad', '5551234568', 'norte@arosports.com');

INSERT INTO `fraccionamientos` (`nombre`, `descripcion`, `direccion`, `club_id`) VALUES
('Fraccionamiento Los Pinos', 'Área residencial premium', 'Los Pinos 123', 1),
('Fraccionamiento Vista Hermosa', 'Zona exclusiva con vista panorámica', 'Vista Hermosa 456', 1),
('Fraccionamiento Norte', 'Desarrollo habitacional moderno', 'Av. Norte 789', 2);

INSERT INTO `empresas` (`nombre`, `rfc`, `razon_social`, `direccion`, `telefono`, `email`) VALUES
('Deportes SA de CV', 'DEP123456789', 'Deportes Sociedad Anónima de Capital Variable', 'Calle Comercio 123', '5551234567', 'contacto@deportes.com'),
('Eventos Corporativos', 'EVE987654321', 'Eventos Corporativos SA de CV', 'Av. Empresarial 456', '5551234568', 'info@eventos.com');

INSERT INTO `reservas` (`usuario_id`, `club_id`, `fraccionamiento_id`, `empresa_id`, `fecha_reserva`, `hora_inicio`, `hora_fin`, `precio`, `estado`) VALUES
(2, 1, 1, NULL, '2024-01-15', '09:00:00', '11:00:00', 500.00, 'completada'),
(3, 1, NULL, 1, '2024-01-16', '14:00:00', '16:00:00', 750.00, 'completada'),
(2, 2, 2, NULL, '2024-01-17', '10:00:00', '12:00:00', 600.00, 'confirmada'),
(3, 1, 1, NULL, '2024-01-18', '16:00:00', '18:00:00', 450.00, 'completada'),
(2, 1, NULL, 2, '2024-01-19', '08:00:00', '10:00:00', 800.00, 'pendiente');