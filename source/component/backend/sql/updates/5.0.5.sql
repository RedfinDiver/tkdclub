ALTER TABLE `#__tkdclub_promotions`
CHANGE `checked_out` `checked_out` int(10) UNSIGNED DEFAULT NULL,
CHANGE `checked_out_time` `checked_out_time` datetime DEFAULT NULL;

ALTER TABLE `#__tkdclub_candidates`
CHANGE `checked_out` `checked_out` int(10) UNSIGNED DEFAULT NULL,
CHANGE `checked_out_time` `checked_out_time` datetime DEFAULT NULL;

ALTER TABLE `#__tkdclub_events`
CHANGE `checked_out` `checked_out` int(10) UNSIGNED DEFAULT NULL,
CHANGE `checked_out_time` `checked_out_time` datetime DEFAULT NULL;
