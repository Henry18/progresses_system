ALTER TABLE `plans` ADD `testing` TINYINT(1) NOT NULL DEFAULT '1' AFTER `status`;
ALTER TABLE `plans` ADD `days_to_init` INT NOT NULL DEFAULT '1' AFTER `testing`;