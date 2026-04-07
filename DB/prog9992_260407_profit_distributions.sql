-- Create profit_distributions table
-- Created: 2026-04-07
-- Purpose: Store history of manual profit distributions made by admin per plan

CREATE TABLE `profit_distributions` (
    `id`               BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `plan_id`          BIGINT UNSIGNED NOT NULL,
    `admin_id`         INT UNSIGNED    NOT NULL,
    `type`             ENUM('equitativo','porcentaje') NOT NULL,
    `total_amount`     DECIMAL(28, 8)  NOT NULL DEFAULT 0,
    `accounts_affected` INT UNSIGNED   NOT NULL DEFAULT 0,
    `notes`            VARCHAR(500)    NULL,
    `created_at`       TIMESTAMP       NULL DEFAULT NULL,
    `updated_at`       TIMESTAMP       NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    INDEX `idx_plan_id`   (`plan_id`),
    INDEX `idx_admin_id`  (`admin_id`),
    INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add distribution_id to transactions so each credit can be traced back
ALTER TABLE `transactions`
    ADD COLUMN `distribution_id` BIGINT UNSIGNED NULL DEFAULT NULL AFTER `invest_id`,
    ADD INDEX  `idx_distribution_id` (`distribution_id`);