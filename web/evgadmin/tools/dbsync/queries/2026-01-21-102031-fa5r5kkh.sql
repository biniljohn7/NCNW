-- Deepa Regi - 21 January 2026 - 10:20 AM


ALTER TABLE `transactions`
	ADD COLUMN `offlinePayNum` VARCHAR(10) NULL DEFAULT NULL AFTER `refNumber`;
