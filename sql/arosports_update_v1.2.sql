-- Actualización para agregar campos de representante a clubes, empresas y fraccionamientos
-- Versión 1.2

-- Agregar campos de representante a la tabla clubes
ALTER TABLE `clubes` 
ADD COLUMN `representante_nombre` varchar(100) DEFAULT NULL AFTER `email`,
ADD COLUMN `representante_email` varchar(100) DEFAULT NULL AFTER `representante_nombre`,
ADD COLUMN `representante_telefono` varchar(20) DEFAULT NULL AFTER `representante_email`,
ADD COLUMN `representante_password` varchar(255) DEFAULT NULL AFTER `representante_telefono`;

-- Agregar campos de representante a la tabla empresas
ALTER TABLE `empresas` 
ADD COLUMN `representante_nombre` varchar(100) DEFAULT NULL AFTER `email`,
ADD COLUMN `representante_email` varchar(100) DEFAULT NULL AFTER `representante_nombre`,
ADD COLUMN `representante_telefono` varchar(20) DEFAULT NULL AFTER `representante_email`,
ADD COLUMN `representante_password` varchar(255) DEFAULT NULL AFTER `representante_telefono`;

-- Agregar campos de representante a la tabla fraccionamientos
ALTER TABLE `fraccionamientos` 
ADD COLUMN `representante_nombre` varchar(100) DEFAULT NULL AFTER `club_id`,
ADD COLUMN `representante_email` varchar(100) DEFAULT NULL AFTER `representante_nombre`,
ADD COLUMN `representante_telefono` varchar(20) DEFAULT NULL AFTER `representante_email`,
ADD COLUMN `representante_password` varchar(255) DEFAULT NULL AFTER `representante_telefono`;