-- hari - 15 January 2026 - 10:38 AM


ALTER TABLE `permissions`
	CHANGE COLUMN `perms` `perms` SET('benefit','events','location','members','members-mod','transactions','career','advocacy','paid-plans','point-rules','messages','cms-pages','contact-enquiries','statistics','helpdesk','helpdesk/developer','helpdesk/project-coordinators','helpdesk/ncnw-team','elect','pos') NULL DEFAULT NULL COLLATE 'latin1_swedish_ci' AFTER `type`;
