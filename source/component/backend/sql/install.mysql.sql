CREATE TABLE IF NOT EXISTS `#__tkdclub_members` (
  `id` int(11) NOT NULL,
  `firstname` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `lastname` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `birthdate` date NOT NULL DEFAULT '0000-00-00',
  `sex` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `citizenship` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `street` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `zip` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `city` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `country` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `phone` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `iban` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `notes_personel` text COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `memberpass` int(10) NOT NULL DEFAULT 0,
  `lastpromotion` date NOT NULL DEFAULT '0000-00-00',
  `grade` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `licenses` text COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `notes_taekwondo` text COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `functions` text COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `entry` date NOT NULL DEFAULT '0000-00-00',
  `leave` date DEFAULT '0000-00-00',
  `member_state` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `notes_clubdata` text COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `attachments` text COLLATE utf8mb4_unicode_ci DEFAULT '',
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `user_id` int(11) NOT NULL DEFAULT 0,
  `created` datetime DEFAULT '0000-00-00 00:00:00',
  `created_by` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `checked_out` int(10) UNSIGNED DEFAULT NULL,
  `checked_out_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__tkdclub_trainings` (
  `id` int(11) NOT NULL,
  `date` date NOT NULL DEFAULT '0000-00-00',
  `trainer` int(5) NOT NULL DEFAULT 0,
  `km_trainer` int(4) NOT NULL DEFAULT 0,
  `trainer_paid` tinyint(3) NOT NULL DEFAULT 0,
  `assist1` int(5) DEFAULT 0,
  `km_assist1` int(4) DEFAULT 0,
  `assist1_paid` tinyint(3) NOT NULL DEFAULT 0,
  `assist2` int(5) DEFAULT 0,
  `km_assist2` int(4) DEFAULT 0,
  `assist2_paid` tinyint(3) NOT NULL DEFAULT 0,
  `assist3` int(5) DEFAULT 0,
  `km_assist3` int(4) DEFAULT 0,
  `assist3_paid` tinyint(3) NOT NULL DEFAULT 0,
  `type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `participants` int(5) NOT NULL DEFAULT 0,
  `participant_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`participant_ids`)),
  `notes` text COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `created` datetime DEFAULT '0000-00-00 00:00:00',
  `created_by` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `checked_out` int(10) UNSIGNED DEFAULT NULL,
  `checked_out_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `#__tkdclub_medals` (
  `id` int(11) NOT NULL,
  `date` date NOT NULL,
  `championship` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `class` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `placing` tinyint(11) NOT NULL,
  `winner_1` int(11) NOT NULL,
  `winner_2` int(11) DEFAULT NULL,
  `winner_3` int(11) DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` tinyint(3) NOT NULL DEFAULT 0,
  `checked_out` int(10) UNSIGNED DEFAULT NULL,
  `checked_out_time` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `#__tkdclub_promotions` (
  `id` int(10) NOT NULL,
  `date` date NOT NULL,
  `city` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `examiner_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `examiner_address` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `examiner_email` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `promotion_state` tinyint(4) NOT NULL,
  `checked_out` int(10) NOT NULL,
  `checked_out_time` datetime NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__tkdclub_candidates` (
  `id` int(10) NOT NULL,
  `id_promotion` int(10) NOT NULL,
  `id_candidate` int(10) NOT NULL,
  `grade_achieve` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `test_state` tinyint(4) NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `checked_out` int(10) NOT NULL,
  `checked_out_time` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__tkdclub_events` (
  `id` int(10) NOT NULL,
  `title` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` date NOT NULL DEFAULT '0000-00-00',
  `deadline` date NOT NULL DEFAULT '0000-00-00',
  `min` int(11) NOT NULL,
  `max` int(11) NOT NULL,
  `published` tinyint(1) NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `checked_out` int(10) NOT NULL,
  `checked_out_time` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__tkdclub_event_participants` (
  `id` int(10) NOT NULL,
  `event_id` int(11) NOT NULL DEFAULT 0,
  `firstname` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `lastname` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `clubname` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `email` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `registered` int(3) NOT NULL DEFAULT 0,
  `grade` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `age` tinytext COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `notes` text COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `user1` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `user2` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `user3` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `user4` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `store_data` tinyint(4) NOT NULL DEFAULT 0,
  `privacy_agreed` tinyint(4) NOT NULL DEFAULT 0,
  `published` tinyint(4) NOT NULL DEFAULT 0,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `checked_out` int(10) NOT NULL DEFAULT 0,
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__tkdclub_newsletter_subscribers` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `firstname` varchar(50) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `origin` varchar(10) NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `checked_out` int(10) NOT NULL DEFAULT 0,
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
 ) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;