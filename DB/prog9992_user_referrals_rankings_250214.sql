
--
-- Estructura de tabla para la tabla `user_referrals_rankings`
--

CREATE TABLE `user_referrals_rankings` (
  `id` bigint(20) NOT NULL,
  `codigo_referred` bigint(20) NOT NULL COMMENT 'Código de referido',
  `codigo_referrer` bigint(20) NOT NULL COMMENT 'Código de referidor',
  `referral_bonus_times` bigint(20) NOT NULL COMMENT 'Bonos que le ha otorgado el referido al referidor',
  `referral_bonus_times_max` bigint(20) NOT NULL COMMENT 'Máxima cantidad de bonos que puede otorgar el referido al referidor a través de inversión',
  `referral_ranking` bigint(20) NOT NULL COMMENT 'Ranking del referidor',
  `created_at` timestamp NOT NULL,
  `updated_at` timestamp NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `user_referrals_rankings`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `user_referrals_rankings`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;
COMMIT;

ALTER TABLE `user_referrals_rankings` CHANGE `referral_bonus_times` `referral_bonus_times` BIGINT(20) NOT NULL DEFAULT '0' COMMENT 'Bonos que le ha otorgado el referido al referidor';