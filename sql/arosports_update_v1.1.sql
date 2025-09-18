-- Script de actualización para soporte de gastos y categorías
-- Versión: 1.1.0
-- Fecha: 2024

USE `arosports`;

-- Tabla de categorías para gastos y ingresos
CREATE TABLE IF NOT EXISTS `categorias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `tipo` enum('ingreso','gasto') NOT NULL DEFAULT 'gasto',
  `color` varchar(7) DEFAULT '#007bff', -- Color hex para visualización en gráficos
  `activo` tinyint(1) DEFAULT 1,
  `fecha_registro` timestamp DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Tabla de transacciones financieras (gastos y retiros)
CREATE TABLE IF NOT EXISTS `transacciones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `categoria_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL, -- Usuario que registra la transacción
  `club_id` int(11) DEFAULT NULL, -- Club asociado (opcional)
  `tipo` enum('ingreso','gasto','retiro') NOT NULL DEFAULT 'gasto',
  `concepto` varchar(200) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `monto` decimal(10,2) NOT NULL,
  `fecha_transaccion` date NOT NULL,
  `metodo_pago` enum('efectivo','transferencia','cheque','tarjeta') DEFAULT 'efectivo',
  `referencia` varchar(100) DEFAULT NULL, -- Número de cheque, referencia de transferencia, etc.
  `comprobante` varchar(255) DEFAULT NULL, -- Ruta del archivo de comprobante
  `estado` enum('pendiente','autorizada','cancelada') DEFAULT 'pendiente',
  `autorizada_por` int(11) DEFAULT NULL, -- Usuario que autoriza (para control)
  `fecha_autorizacion` timestamp NULL DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `fecha_registro` timestamp DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`categoria_id`) REFERENCES `categorias`(`id`) ON DELETE RESTRICT,
  FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`club_id`) REFERENCES `clubes`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`autorizada_por`) REFERENCES `usuarios`(`id`) ON DELETE SET NULL,
  INDEX idx_fecha_transaccion (`fecha_transaccion`),
  INDEX idx_tipo (`tipo`),
  INDEX idx_estado (`estado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Insertar categorías por defecto
INSERT INTO `categorias` (`nombre`, `descripcion`, `tipo`, `color`) VALUES
-- Categorías de ingresos
('Reservas Deportivas', 'Ingresos por reservas de canchas y espacios deportivos', 'ingreso', '#28a745'),
('Membresías', 'Ingresos por membresías anuales y mensuales', 'ingreso', '#17a2b8'),
('Eventos Especiales', 'Ingresos por organización de eventos y torneos', 'ingreso', '#6f42c1'),
('Servicios Adicionales', 'Ingresos por servicios complementarios', 'ingreso', '#20c997'),

-- Categorías de gastos
('Mantenimiento', 'Gastos de mantenimiento de instalaciones y equipos', 'gasto', '#dc3545'),
('Servicios Públicos', 'Luz, agua, gas, internet y otros servicios', 'gasto', '#fd7e14'),
('Personal', 'Salarios, prestaciones y gastos de personal', 'gasto', '#6c757d'),
('Materiales y Suministros', 'Compra de materiales, equipos y suministros', 'gasto', '#e83e8c'),
('Marketing y Publicidad', 'Gastos en promoción y marketing', 'gasto', '#ffc107'),
('Administración', 'Gastos administrativos y de oficina', 'gasto', '#6610f2'),
('Seguros', 'Pólizas de seguros y coberturas', 'gasto', '#dc3545'),
('Impuestos y Tasas', 'Pago de impuestos y tasas gubernamentales', 'gasto', '#495057');

-- Actualizar tabla de reservas para mejor integración
ALTER TABLE `reservas` 
ADD COLUMN `categoria_id` int(11) DEFAULT NULL AFTER `precio`,
ADD FOREIGN KEY (`categoria_id`) REFERENCES `categorias`(`id`) ON DELETE SET NULL;

-- Actualizar reservas existentes con la categoría de "Reservas Deportivas"
UPDATE `reservas` SET `categoria_id` = 1 WHERE `categoria_id` IS NULL;

-- Vista para reporte financiero consolidado
CREATE OR REPLACE VIEW `vista_movimientos_financieros` AS
SELECT 
    'reserva' as origen,
    r.id as origen_id,
    r.fecha_reserva as fecha,
    COALESCE(c.nombre, 'Reservas Deportivas') as categoria,
    CONCAT('Reserva - ', cl.nombre) as concepto,
    r.precio as monto,
    'ingreso' as tipo,
    r.estado as estado_movimiento,
    r.usuario_id,
    r.club_id,
    u.nombre as usuario_nombre,
    cl.nombre as club_nombre
FROM reservas r
LEFT JOIN categorias c ON r.categoria_id = c.id
LEFT JOIN usuarios u ON r.usuario_id = u.id
LEFT JOIN clubes cl ON r.club_id = cl.id
WHERE r.estado = 'completada'

UNION ALL

SELECT 
    'transaccion' as origen,
    t.id as origen_id,
    t.fecha_transaccion as fecha,
    c.nombre as categoria,
    t.concepto,
    CASE 
        WHEN t.tipo = 'ingreso' THEN t.monto
        ELSE -t.monto 
    END as monto,
    t.tipo,
    t.estado as estado_movimiento,
    t.usuario_id,
    t.club_id,
    u.nombre as usuario_nombre,
    cl.nombre as club_nombre
FROM transacciones t
LEFT JOIN categorias c ON t.categoria_id = c.id
LEFT JOIN usuarios u ON t.usuario_id = u.id
LEFT JOIN clubes cl ON t.club_id = cl.id
WHERE t.estado = 'autorizada';