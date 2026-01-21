-- Albert - 19 January 2026 - 03:42 PM


ALTER TABLE `members`
	ADD COLUMN `memberId` VARCHAR(25) NULL DEFAULT NULL AFTER `lastName`,
	DROP INDEX `memberId`,
	ADD UNIQUE INDEX `memberId` (`memberId`);