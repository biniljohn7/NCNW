-- Deepa Regi - 16 January 2026 - 03:59 PM


ALTER TABLE `transactions`
	ADD COLUMN `offlineProof` VARCHAR(80) NULL DEFAULT NULL AFTER `posDoneBy`;
