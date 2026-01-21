-- Albert - 19 January 2026 - 03:41 PM


ALTER TABLE `members`
	CHANGE COLUMN `memberId` `memberIdOld` VARCHAR(25) NULL DEFAULT NULL COLLATE 'latin1_swedish_ci' AFTER `lastName`,
	DROP INDEX `memberId`,
	ADD UNIQUE INDEX `memberId` (`memberIdOld`) USING BTREE;