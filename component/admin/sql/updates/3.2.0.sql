ALTER TABLE `#__tkdclub_medals`
    ADD `state` tinyint(3) NOT NULL DEFAULT 0 AFTER `notes`;

UPDATE `#__tkdclub_medals` SET `state` = 1;