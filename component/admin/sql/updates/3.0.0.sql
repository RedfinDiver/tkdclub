ALTER TABLE `#__tkdclub_members`
	CHANGE `id` `member_id` int(11) AUTO_INCREMENT,
    CHANGE `firstname` `firstname` varchar(50) NOT NULL,
    CHANGE `lastname` `lastname` varchar(50) NOT NULL,
    CHANGE `birthdate` `birthdate` date NOT NULL DEFAULT '0000-00-00',
    CHANGE `sex` `sex` varchar(10) NOT NULL,
    CHANGE `citizenship` `citizenship` varchar(10) NOT NULL,
    CHANGE `street` `street` varchar(50) NOT NULL,
    CHANGE `zip`  `zip` varchar(10) NOT NULL,
    CHANGE `city` `city` varchar(50) NOT NULL,
    ADD `country` varchar(50) NOT NULL AFTER `city`,
    CHANGE `phone` `phone` varchar(50) NOT NULL,
    CHANGE `email` `email` varchar(50) NOT NULL,
    CHANGE `note_p` `notes_personel` text NOT NULL,
    CHANGE `memberpass` `memberpass` int(10) NOT NULL,
    CHANGE `grade` `grade` varchar(30) NOT NULL,
    CHANGE `lastexam` `lastpromotion` date NOT NULL DEFAULT '0000-00-00',
    CHANGE `note_t` `notes_taekwondo` text NOT NULL,
    CHANGE `functions` `functions` text NOT NULL,
    CHANGE `entry` `entry` date NOT NULL DEFAULT '0000-00-00',
    CHANGE `leave` `leave` date NOT NULL DEFAULT '0000-00-00',
    CHANGE `state` `member_state` varchar(10) NOT NULL,
    CHANGE `note_c` `notes_clubdata` text NOT NULL,
    CHANGE `attachments` `attachments` TINYINT(1) NOT NULL,
    CHANGE `created` `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
    CHANGE `created_by` `created_by` INT(10) unsigned NOT NULL DEFAULT '0',
    CHANGE `modified` `modified` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
    CHANGE `modified_by` `modified_by` INT(10) unsigned NOT NULL DEFAULT '0',
    CHANGE `checked_out` `checked_out` int(10) unsigned NOT NULL,
    CHANGE `checked_out_time` `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
	DROP `fee`,
    DROP `delete_mb`,
    ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

UPDATE `#__tkdclub_members` SET `member_state` = 'inactive' WHERE `member_state` = '0';
UPDATE `#__tkdclub_members` SET `member_state` = 'active' WHERE `member_state` = '1';
UPDATE `#__tkdclub_members` SET `member_state` = 'support' WHERE `member_state` = '2';
UPDATE `#__tkdclub_members` SET `sex` = '' WHERE `sex` = '0';
UPDATE `#__tkdclub_members` SET `sex` = 'female' WHERE `sex` = '1';
UPDATE `#__tkdclub_members` SET `sex` = 'male' WHERE `sex` = '2';
UPDATE `#__tkdclub_members` SET `grade` = '9. Kup' WHERE `grade` = '09. Kup';
UPDATE `#__tkdclub_members` SET `grade` = '8. Kup' WHERE `grade` = '08. Kup';
UPDATE `#__tkdclub_members` SET `grade` = '7. Kup' WHERE `grade` = '07. Kup';
UPDATE `#__tkdclub_members` SET `grade` = '6. Kup' WHERE `grade` = '06. Kup';
UPDATE `#__tkdclub_members` SET `grade` = '5. Kup' WHERE `grade` = '05. Kup';
UPDATE `#__tkdclub_members` SET `grade` = '4. Kup' WHERE `grade` = '04. Kup';
UPDATE `#__tkdclub_members` SET `grade` = '3. Kup' WHERE `grade` = '03. Kup';
UPDATE `#__tkdclub_members` SET `grade` = '2. Kup' WHERE `grade` = '02. Kup';
UPDATE `#__tkdclub_members` SET `grade` = '1. Kup' WHERE `grade` = '01. Kup';
UPDATE `#__tkdclub_members` SET `grade` = '1. Poom' WHERE `grade` = '01. Poom';
UPDATE `#__tkdclub_members` SET `grade` = '2. Poom' WHERE `grade` = '02. Poom';
UPDATE `#__tkdclub_members` SET `grade` = '3. Poom' WHERE `grade` = '03. Poom';
UPDATE `#__tkdclub_members` SET `grade` = '1. Dan' WHERE `grade` = '01. Dan';
UPDATE `#__tkdclub_members` SET `grade` = '2. Dan' WHERE `grade` = '02. Dan';
UPDATE `#__tkdclub_members` SET `grade` = '3. Dan' WHERE `grade` = '03. Dan';
UPDATE `#__tkdclub_members` SET `grade` = '4. Dan' WHERE `grade` = '04. Dan';
UPDATE `#__tkdclub_members` SET `grade` = '5. Dan' WHERE `grade` = '05. Dan';
UPDATE `#__tkdclub_members` SET `grade` = '6. Dan' WHERE `grade` = '06. Dan';
UPDATE `#__tkdclub_members` SET `grade` = '7. Dan' WHERE `grade` = '07. Dan';
UPDATE `#__tkdclub_members` SET `grade` = '8. Dan' WHERE `grade` = '08. Dan';
UPDATE `#__tkdclub_members` SET `grade` = '9. Dan' WHERE `grade` = '09. Dan';

ALTER TABLE `#__tkdclub_trainings`
    CHANGE `id` `training_id` int(11) NOT NULL AUTO_INCREMENT,
    CHANGE `date` `date` date NOT NULL,
    CHANGE `trainer` `trainer` int(5) NOT NULL,
    CHANGE `km_trainer` `km_trainer` int(4) NOT NULL,
    CHANGE `assist1` `assist1` int(5) NOT NULL,
    CHANGE `km_assist1` `km_assist1` int(4) NOT NULL,
    CHANGE `assist2` `assist2` int(5) NOT NULL,
    CHANGE `km_assist2` `km_assist2` int(4) NOT NULL,
    CHANGE `assist3` `assist3` int(5) NOT NULL,
    CHANGE `km_assist3` `km_assist3` int(4) NOT NULL,
    CHANGE `type` `type` varchar(50) NOT NULL,
    CHANGE `participants` `participants` int(5) NOT NULL,
    CHANGE `notes` `notes` text NOT NULL,
    CHANGE `published` `payment_state` tinyint(3) NOT NULL DEFAULT 0,
    ADD `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
    ADD `created_by` INT(10) unsigned NOT NULL DEFAULT '0',
    ADD `modified` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
    ADD `modified_by` INT(10) unsigned NOT NULL DEFAULT '0',
    CHANGE `checked_out` `checked_out` int(10) NOT NULL,
    CHANGE `checked_out_time` `checked_out_time` datetime NOT NULL,
    ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `#__tkdclub_medals`
	CHANGE `id` `medal_id` int(11) NOT NULL AUTO_INCREMENT,
    CHANGE `date_win` `date` date NOT NULL,
    CHANGE `c_ship` `championship` varchar(50) NOT NULL,
    ADD `type` varchar(50) NOT NULL AFTER `championsship`,
    CHANGE `wa_class` `class` varchar(50) NOT NULL,
    CHANGE `placing` `placing` tinyint(11) NOT NULL,
    CHANGE `id_win` `winner_ids` varchar(50) NOT NULL,
    CHANGE `notes_medals` `notes` text NOT NULL,
    ADD `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
    ADD `created_by` INT(10) unsigned NOT NULL DEFAULT '0',
    ADD `modified` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
    ADD `modified_by` INT(10) unsigned NOT NULL DEFAULT '0',
    CHANGE `checked_out` `checked_out` int(10) NOT NULL,
    CHANGE `checked_out_time` `checked_out_time` datetime NOT NULL,
    ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;