ALTER TABLE `#__tkdclub_members`
    CHANGE `email` `email` varchar(100) NOT NULL,
    ADD `user_id` int(11) NOT NULL AFTER `attachments`;