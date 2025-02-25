ALTER TABLE `withdrawals` ADD `withdraw_wallet` VARCHAR(50) NOT NULL AFTER `final_amount`;
ALTER TABLE `user_rankings` ADD `refer_bonus_level` DECIMAL(5,2) NOT NULL AFTER `bonus`;