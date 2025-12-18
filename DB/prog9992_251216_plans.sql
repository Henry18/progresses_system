-- Agregar campos de imagen, PDF y descripción a la tabla plans
-- Fecha: 2025-12-16
-- Descripción: Se agregan los campos image (obligatorio), pdf (opcional) y description (obligatorio) a la tabla plans

-- Agregar campo description después de name
ALTER TABLE `plans` ADD `description` TEXT NOT NULL AFTER `name`;

-- Agregar campo image después de name (será el primero después de description)
ALTER TABLE `plans` ADD `image` VARCHAR(255) NOT NULL AFTER `name`;

-- Agregar campo pdf después de image (opcional)
ALTER TABLE `plans` ADD `pdf` VARCHAR(255) NULL AFTER `image`;
