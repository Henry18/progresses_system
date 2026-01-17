ALTER TABLE
  `withdraw_methods`
ADD
  `fixed_charge_special` DECIMAL(28, 8) NOT NULL DEFAULT '0.00000'
AFTER
  `percent_charge_bonus`,
ADD
  `percent_charge_special` DECIMAL(5, 2) NOT NULL DEFAULT '0.00'
AFTER
  `fixed_charge_special`,
ADD
  `fixed_charge_special_out` DECIMAL(28, 8) NOT NULL DEFAULT '0.00000'
AFTER
  `percent_charge_special`,
ADD
  `percent_charge_special_out` DECIMAL(5, 2) NOT NULL DEFAULT '0.00'
AFTER
  `fixed_charge_special_out`;