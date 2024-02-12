ALTER TABLE `#__tkdclub_members`
    ADD `image` varchar(255) DEFAULT NULL AFTER `attachments`,
    CHANGE `checked_out_time` `checked_out_time` datetime DEFAULT NULL,
    CHANGE `checked_out` `checked_out` int(10) UNSIGNED DEFAULT NULL,
    CHANGE `modified_by` `modified_by` int(10) UNSIGNED NOT NULL DEFAULT '0',
    CHANGE `modified` `modified` datetime DEFAULT NULL,
    CHANGE `created_by` `created_by` int(10) UNSIGNED NOT NULL DEFAULT '0',
    CHANGE `created`  `created` datetime DEFAULT NULL,
    CHANGE `user_id` `user_id` int(11) NOT NULL DEFAULT '0',
    CHANGE `member_state` `member_state` varchar(10) NOT NULL DEFAULT 'active',
    CHANGE `citizenship` `citizenship` varchar(10) NOT NULL DEFAULT '',
    CHANGE `street` `street` varchar(50) NOT NULL DEFAULT '',
    CHANGE `zip` `zip` varchar(10) NOT NULL DEFAULT '',
    CHANGE `city` `city` varchar(50) NOT NULL DEFAULT '',
    CHANGE `country` `country` varchar(50) NOT NULL DEFAULT '',
    CHANGE `phone` `phone` varchar(50) NOT NULL DEFAULT '',
    CHANGE `email` `email`varchar(100) NOT NULL DEFAULT '',
    CHANGE `iban` `iban` varchar(50) NOT NULL DEFAULT '',
    CHANGE `memberpass` `memberpass` varchar(30) DEFAULT NULL,
    CHANGE `grade` `grade` varchar(30) NOT NULL DEFAULT '',
    CHANGE `licenses` `licenses` text,
    CHANGE `functions` `functions` text,
    CHANGE `attachments` `attachments` text,
    CHANGE `birthdate` `birthdate` date NOT NULL,
    CHANGE `leave` `leave` date DEFAULT NULL,
    CHANGE `entry` `entry` date DEFAULT NULL,
    CHANGE `lastpromotion` `lastpromotion` date DEFAULT NULL,
    CHANGE `sex` `sex` varchar(10) NOT NULL,
    CHANGE `lastname` `lastname` varchar(50) NOT NULL,
    CHANGE `firstname` `firstname` varchar(50) NOT NULL;

 UPDATE `#__tkdclub_members` SET
	`birthdate` = CASE WHEN `birthdate` IN ('0000-00-00 00:00:00', '1000-01-01 00:00:00') THEN NULL ELSE `birthdate` END,
	`entry` = CASE WHEN `entry` IN ('0000-00-00 00:00:00', '1000-01-01 00:00:00') THEN NULL ELSE `entry` END,
    `lastpromotion` = CASE WHEN `lastpromotion` IN ('0000-00-00 00:00:00', '1000-01-01 00:00:00', '2001-03-01') THEN NULL ELSE `lastpromotion` END,
    `leave` = CASE WHEN `leave` IN ('0000-00-00 00:00:00', '1000-01-01 00:00:00') THEN NULL ELSE `leave` END,
	`checked_out_time` = CASE WHEN `checked_out_time` IN ('0000-00-00 00:00:00', '1000-01-01 00:00:00') THEN NULL ELSE `checked_out_time` END,
    `memberpass` = CASE WHEN `memberpass` = 0 THEN NULL ELSE `memberpass` END,
    `grade` = CASE WHEN `grade` = 0 OR '0' THEN '' ELSE `grade` END;

UPDATE `#__tkdclub_members` SET
	`attachments` = NULL
WHERE `member_id` > 0;

ALTER TABLE `#__tkdclub_trainings`
    CHANGE `checked_out_time` `checked_out_time` datetime DEFAULT NULL,
    CHANGE `checked_out` `checked_out` int(10) UNSIGNED DEFAULT NULL,
    CHANGE `modified_by` `modified_by` int(10) UNSIGNED NOT NULL DEFAULT '0',
    CHANGE `modified` `modified` datetime DEFAULT NULL,
    CHANGE `created_by` `created_by` int(10) UNSIGNED NOT NULL DEFAULT '0',
    CHANGE `created`  `created` datetime DEFAULT NULL,
    CHANGE `assist1` `assist1` int(5) DEFAULT NULL,
    CHANGE `km_assist1` `km_assist1` int(4) DEFAULT NULL, 
    CHANGE `assist1_paid` `assist1_paid` tinyint(3) DEFAULT NULL,
    CHANGE `assist2` `assist2` int(5) DEFAULT NULL,
    CHANGE `km_assist2` `km_assist2` int(4) DEFAULT NULL, 
    CHANGE `assist2_paid` `assist2_paid` tinyint(3) DEFAULT NULL,
    CHANGE `assist3` `assist3` int(5) DEFAULT NULL,
    CHANGE `km_assist3` `km_assist3` int(4) DEFAULT NULL, 
    CHANGE `assist3_paid` `assist3_paid` tinyint(3) DEFAULT NULL,
    CHANGE `notes` `notes` text;

ALTER TABLE `#__tkdclub_medals`
    ADD `winner_1` int(11) NOT NULL AFTER `placing`,
    ADD `winner_2` int(11) DEFAULT NULL AFTER `winner_1`,
    ADD `winner_3` int(11) DEFAULT NULL AFTER `winner_2`,
    CHANGE `checked_out_time` `checked_out_time` datetime DEFAULT NULL,
    CHANGE `checked_out` `checked_out` int(10) UNSIGNED DEFAULT NULL,
    CHANGE `modified_by` `modified_by` int(10) UNSIGNED NOT NULL DEFAULT '0',
    CHANGE `modified` `modified` datetime DEFAULT NULL,
    CHANGE `created_by` `created_by` int(10) UNSIGNED NOT NULL DEFAULT '0',
    CHANGE `created`  `created` datetime DEFAULT NULL,
    CHANGE `notes` `notes` text,
    DROP `winner_ids`;

ALTER TABLE `#__tkdclub_promotions`
    CHANGE `created` `created` datetime DEFAULT NULL,
    CHANGE `checked_out_time` `checked_out_time` datetime DEFAULT NULL,
    CHANGE `checked_out` `checked_out` int(10) UNSIGNED DEFAULT NULL;
