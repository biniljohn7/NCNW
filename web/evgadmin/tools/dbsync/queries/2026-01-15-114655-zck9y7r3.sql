-- hari - 15 January 2026 - 11:46 AM


ALTER TABLE `admins`
	CHANGE COLUMN `perms` `perms` SET('benefit','events','location','members','members-mod','transactions','career','advocacy','paid-plans','point-rules','messages','cms-pages','contact-enquiries','statistics','helpdesk','helpdesk/developer','helpdesk/project-coordinators','helpdesk/ncnw-team','elect','pos') NULL DEFAULT NULL COLLATE 'latin1_swedish_ci' AFTER `password`;
