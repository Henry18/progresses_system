-- Special Wallet and Withdrawal Periods Setup
-- Created: 2026-01-16
-- Purpose: Add restricted withdrawal functionality for special wallet

-- =====================================================
-- 1. Add restricted_withdrawal field to plans table
-- =====================================================
ALTER TABLE `plans`
ADD COLUMN `restricted_withdrawal` TINYINT(1) NOT NULL DEFAULT 0 AFTER `hold_capital`;

-- =====================================================
-- 2. Create withdrawal_periods table
-- =====================================================
CREATE TABLE IF NOT EXISTS `withdrawal_periods` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `withdraw_method_id` BIGINT UNSIGNED NOT NULL,
    `start_date` DATE NOT NULL,
    `end_date` DATE NOT NULL,
    `status` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    INDEX `withdrawal_periods_withdraw_method_id_index` (`withdraw_method_id`),
    INDEX `withdrawal_periods_dates_index` (`start_date`, `end_date`),
    INDEX `withdrawal_periods_status_index` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 3. Add foreign key constraint (optional, comment if not needed)
-- =====================================================
-- ALTER TABLE `withdrawal_periods`
-- ADD CONSTRAINT `withdrawal_periods_withdraw_method_id_foreign`
-- FOREIGN KEY (`withdraw_method_id`) REFERENCES `withdraw_methods` (`id`) ON DELETE CASCADE;


ALTER TABLE `withdrawals` ADD `is_within_period` TINYINT(1) NOT NULL AFTER `trx`;