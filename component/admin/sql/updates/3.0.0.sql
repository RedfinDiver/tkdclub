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
UPDATE `#__tkdclub_members` SET `grade` = '0' WHERE `grade` = '';
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
    ADD `trainer_paid` tinyint(3) NOT NULL DEFAULT 0 AFTER `km_trainer`,
    ADD `assist1_paid` tinyint(3) NOT NULL DEFAULT 0 AFTER `km_assist1`,
    ADD `assist2_paid` tinyint(3) NOT NULL DEFAULT 0 AFTER `km_assist2`,
    ADD `assist3_paid` tinyint(3) NOT NULL DEFAULT 0 AFTER `km_assist3`,
    ADD `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
    ADD `created_by` INT(10) unsigned NOT NULL DEFAULT '0',
    ADD `modified` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
    ADD `modified_by` INT(10) unsigned NOT NULL DEFAULT '0',
    CHANGE `checked_out` `checked_out` int(10) NOT NULL,
    CHANGE `checked_out_time` `checked_out_time` datetime NOT NULL,
    ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `#__tkdclub_trainings`
    MODIFY `km_trainer` int(4) NOT NULL AFTER `trainer`,
    MODIFY `trainer_paid` tinyint(3) NOT NULL DEFAULT 0 AFTER `km_trainer`;

UPDATE `#__tkdclub_trainings` SET `trainer_paid` = '1' WHERE `published` = '1';
UPDATE `#__tkdclub_trainings` SET `assist1_paid` = '1' WHERE `published` = '1' AND assist1 > '0';
UPDATE `#__tkdclub_trainings` SET `assist2_paid` = '1' WHERE `published` = '1' AND assist2 > '0';
UPDATE `#__tkdclub_trainings` SET `assist3_paid` = '1' WHERE `published` = '1' AND assist3 > '0';

ALTER TABLE `#__tkdclub_trainings`
    DROP `published`;

ALTER TABLE `#__tkdclub_medals`
	CHANGE `id` `medal_id` int(11) NOT NULL AUTO_INCREMENT,
    CHANGE `date_win` `date` date NOT NULL,
    CHANGE `c_ship` `championship` varchar(50) NOT NULL,
    ADD `type` varchar(50) NOT NULL AFTER `championship`,
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

UPDATE `#__tkdclub_medals` SET `winner_ids` = CONCAT('[', `winner_ids`, ']');
UPDATE `#__tkdclub_medals` SET `type` = 'Poomsae' WHERE `class` LIKE '%Pooms%' OR `championship` LIKE '%Pooms%';
UPDATE `#__tkdclub_medals` SET `type` = 'Kyorugi' WHERE `type` = '';

RENAME TABLE `#__tkdclub_exams` TO `#__tkdclub_promotions`

ALTER TABLE `#__tkdclub_promotions`
    CHANGE `id` `promotion_id` int(10) NOT NULL AUTO_INCREMENT,
    CHANGE `date_exam` `date` date NOT NULL,
    CHANGE `city_exam` `city` varchar(20) NOT NULL,
    CHANGE `exam_type` `type` varchar(20) NOT NULL,
    CHANGE `examiner_name` `examiner_name` varchar(50),
    CHANGE `examiner_adress` `examiner_address` varchar(50) NOT NULL,
    CHANGE `examiner_email` `examiner_email` varchar(50) NOT NULL,
    CHANGE `published` `promotion_state` tinyint(4) NOT NULL,
    ADD `notes` text NOT NULL,
    ADD `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
    ADD `created_by` INT(10) unsigned NOT NULL DEFAULT '0',
    ADD `modified` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
    ADD `modified_by` INT(10) unsigned NOT NULL DEFAULT '0',
    CHANGE `checked_out` `checked_out` int(10) NOT NULL,
    CHANGE `checked_out_time` `checked_out_time` datetime NOT NULL,
    ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

UPDATE `j_tkdclub_promotions` SET `type` = 'kup' WHERE `type` = '1';
UPDATE `j_tkdclub_promotions` SET `type` = 'dan' WHERE `type` = '2';

RENAME TABLE `#__tkdclub_examparts` TO `#__tkdclub_candidates`

ALTER TABLE `#__tkdclub_candidates`
    CHANGE `id` `id` int(10) NOT NULL AUTO_INCREMENT,
    CHANGE `exam_id` `id_promotion` int(10) NOT NULL,
    CHANGE `id_participant` `id_candidate` int(10) NOT NULL,
    CHANGE `grade_achieve` `grade_achieve` varchar(20) NOT NULL,
    CHANGE `published` `test_state` tinyint(4) NOT NULL,
    CHANGE `checked_out` `checked_out` int(10) NOT NULL,
    CHANGE `checked_out_time` `checked_out_time` datetime NOT NULL,
    ADD `notes` text NOT NULL AFTER `test_state`,
    ADD `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `notes`,
    ADD `created_by` INT(10) unsigned NOT NULL DEFAULT '0' AFTER `created`,
    ADD `modified` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `created_by`,
    ADD `modified_by` INT(10) unsigned NOT NULL DEFAULT '0' AFTER `modified`,
    ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

UPDATE `#__tkdclub_candidates` SET `grade_achieve` = '9. Kup' WHERE `grade_achieve` = '09. Kup';
UPDATE `#__tkdclub_candidates` SET `grade_achieve` = '8. Kup' WHERE `grade_achieve` = '08. Kup';
UPDATE `#__tkdclub_candidates` SET `grade_achieve` = '7. Kup' WHERE `grade_achieve` = '07. Kup';
UPDATE `#__tkdclub_candidates` SET `grade_achieve` = '6. Kup' WHERE `grade_achieve` = '06. Kup';
UPDATE `#__tkdclub_candidates` SET `grade_achieve` = '5. Kup' WHERE `grade_achieve` = '05. Kup';
UPDATE `#__tkdclub_candidates` SET `grade_achieve` = '4. Kup' WHERE `grade_achieve` = '04. Kup';
UPDATE `#__tkdclub_candidates` SET `grade_achieve` = '3. Kup' WHERE `grade_achieve` = '03. Kup';
UPDATE `#__tkdclub_candidates` SET `grade_achieve` = '2. Kup' WHERE `grade_achieve` = '02. Kup';
UPDATE `#__tkdclub_candidates` SET `grade_achieve` = '1. Kup' WHERE `grade_achieve` = '01. Kup';
UPDATE `#__tkdclub_candidates` SET `grade_achieve` = '1. Poom' WHERE `grade_achieve` = '01. Poom';
UPDATE `#__tkdclub_candidates` SET `grade_achieve` = '2. Poom' WHERE `grade_achieve` = '02. Poom';
UPDATE `#__tkdclub_candidates` SET `grade_achieve` = '3. Poom' WHERE `grade_achieve` = '03. Poom';
UPDATE `#__tkdclub_candidates` SET `grade_achieve` = '1. Dan' WHERE `grade_achieve` = '01. Dan';
UPDATE `#__tkdclub_candidates` SET `grade_achieve` = '2. Dan' WHERE `grade_achieve` = '02. Dan';
UPDATE `#__tkdclub_candidates` SET `grade_achieve` = '3. Dan' WHERE `grade_achieve` = '03. Dan';
UPDATE `#__tkdclub_candidates` SET `grade_achieve` = '4. Dan' WHERE `grade_achieve` = '04. Dan';
UPDATE `#__tkdclub_candidates` SET `grade_achieve` = '5. Dan' WHERE `grade_achieve` = '05. Dan';
UPDATE `#__tkdclub_candidates` SET `grade_achieve` = '6. Dan' WHERE `grade_achieve` = '06. Dan';
UPDATE `#__tkdclub_candidates` SET `grade_achieve` = '7. Dan' WHERE `grade_achieve` = '07. Dan';
UPDATE `#__tkdclub_candidates` SET `grade_achieve` = '8. Dan' WHERE `grade_achieve` = '08. Dan';
UPDATE `#__tkdclub_candidates` SET `grade_achieve` = '9. Dan' WHERE `grade_achieve` = '09. Dan';

ALTER TABLE `#__tkdclub_events`
    CHANGE `id` `event_id` int(10) NOT NULL AUTO_INCREMENT,
    CHANGE `title` `title` text NOT NULL,
    CHANGE `date` `date` date NOT NULL DEFAULT '0000-00-00',
    CHANGE `deadline` `deadline` date NOT NULL DEFAULT '0000-00-00',
    CHANGE `min_parts` `min` int(11) NOT NULL,
    CHANGE `max_parts` `max` int(11) NOT NULL,
    CHANGE `published` `published` tinyint(1) NOT NULL,
    CHANGE `checked_out` `checked_out` int(10) NOT NULL,
    CHANGE `checked_out_time` `checked_out_time` datetime NOT NULL,
    ADD `notes` text NOT NULL AFTER `published`,
    ADD `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `notes`,
    ADD `created_by` INT(10) unsigned NOT NULL DEFAULT '0' AFTER `created`,
    ADD `modified` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `created_by`,
    ADD `modified_by` INT(10) unsigned NOT NULL DEFAULT '0' AFTER `modified`,
    ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

RENAME TABLE `#__tkdclub_eventparts` TO `#__tkdclub_event_participants`;

ALTER TABLE `#__tkdclub_event_participants`
    CHANGE `id` `id` int(10) NOT NULL AUTO_INCREMENT,
    CHANGE `event_id` `event_id` int(11) NOT NULL,
    CHANGE `firstname` `firstname` varchar(50) NOT NULL,
    CHANGE `lastname` `lastname` varchar(50) NOT NULL,
    CHANGE `clubname` `clubname` varchar(50) NOT NULL,
    CHANGE `email` `email` varchar(50) NOT NULL,
    CHANGE `participants` `registered` INT(3) NOT NULL,
    CHANGE `grade` `grade` varchar(30) NOT NULL,
    CHANGE `age` `age` TINYTEXT NOT NULL,
    CHANGE `notes` `notes` text NOT NULL,
    CHANGE `user1` `user1` varchar(50) NOT NULL,
    CHANGE `user2` `user2` varchar(50) NOT NULL,
    CHANGE `user3` `user3` varchar(50) NOT NULL,
    CHANGE `user4` `user4`varchar(50) NOT NULL,  
    CHANGE `published` `published` tinyint(4) NOT NULL,
    CHANGE `checked_out` `checked_out` int(10) NOT NULL,
    CHANGE `checked_out_time` `checked_out_time` datetime NOT NULL,
    ADD `store_data` tinyint(4) NOT NULL AFTER `user4`,
    ADD `privacy_agreed` tinyint(4) NOT NULL AFTER `store_data`,
    ADD `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `published`,
    ADD `created_by` INT(10) unsigned NOT NULL DEFAULT '0' AFTER `created`,
    ADD `modified` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `created_by`,
    ADD `modified_by` INT(10) unsigned NOT NULL DEFAULT '0' AFTER `modified`,
    ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;