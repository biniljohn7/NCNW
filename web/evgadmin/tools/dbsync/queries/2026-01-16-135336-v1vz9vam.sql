-- Deepa Regi - 16 January 2026 - 01:53 PM


ALTER TABLE `transactions`
	CHANGE COLUMN `method` `method` ENUM('stripe','manual','check','moneyorder') NOT NULL DEFAULT 'stripe' COLLATE 'latin1_swedish_ci' AFTER `type`;
