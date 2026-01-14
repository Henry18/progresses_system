-- Crear tabla projects
-- Fecha: 2025-12-18
-- Descripción: Tabla para gestionar proyectos de inversión. Los planes ahora pertenecen a proyectos.

CREATE TABLE `projects` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `image` varchar(255) NOT NULL,
  `pdf` varchar(255) DEFAULT NULL,
  `minimum_investment` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `maximum_investment` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `days_to_init` int(11) NOT NULL DEFAULT 1,
  `featured` tinyint(1) NOT NULL DEFAULT 0,
  `testing` tinyint(1) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Agregar campo project_id a tabla plans
ALTER TABLE `plans` ADD `project_id` bigint(20) UNSIGNED NULL AFTER `id`;

-- Agregar índice y foreign key
ALTER TABLE `plans` ADD INDEX `plans_project_id_foreign` (`project_id`);
ALTER TABLE `plans` ADD CONSTRAINT `plans_project_id_foreign`
  FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE;

-- Nota: project_id es nullable para mantener compatibilidad con planes existentes
-- Una vez migrados todos los planes, se puede hacer NOT NULL si es necesario
