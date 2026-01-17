-- Add terms_accepted and project_terms_accepted columns to invests table
-- Created: 2026-01-14
-- Purpose: Store user acceptance of terms and conditions for investments and project conditions

ALTER TABLE `invests`
ADD COLUMN `terms_accepted` TINYINT(1) NOT NULL DEFAULT 0 AFTER `fractional_capital`,
ADD COLUMN `terms_accepted_at` TIMESTAMP NULL DEFAULT NULL AFTER `terms_accepted`,
ADD COLUMN `project_terms_accepted` TINYINT(1) NOT NULL DEFAULT 0 AFTER `terms_accepted_at`,
ADD COLUMN `project_terms_accepted_at` TIMESTAMP NULL DEFAULT NULL AFTER `project_terms_accepted`;

-- Update existing records to mark them as accepted (for backward compatibility)
UPDATE `invests` SET `terms_accepted` = 1, `terms_accepted_at` = `created_at`, `project_terms_accepted` = 1, `project_terms_accepted_at` = `created_at` WHERE `terms_accepted` = 0;
