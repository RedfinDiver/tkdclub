-- Deleting tkd club extension and tables from extension table

DELETE FROM `dev_extensions`
WHERE `name` = 'com_tkdclub';

DELETE FROM `dev_extensions`
WHERE `name` = 'plg_console_bdreminder';

DELETE FROM `dev_extensions`
WHERE `name` = 'plg_task_bdreminder';

DELETE FROM `dev_extensions`
WHERE `name` = 'plg_content_gradeupdate';

DELETE FROM `dev_extensions`
WHERE `name` = 'plg_user_tkdclubmember';

DELETE FROM `dev_extensions`
WHERE `name` = 'plg_webservices_tkdclub';

DROP TABLE IF EXISTS `dev_tkdclub_members`;
DROP TABLE IF EXISTS `dev_tkdclub_trainings`;
DROP TABLE IF EXISTS `dev_tkdclub_medals`;
DROP TABLE IF EXISTS `dev_tkdclub_promotions`;
DROP TABLE IF EXISTS `dev_tkdclub_candidates`;
DROP TABLE IF EXISTS `dev_tkdclub_events`;
DROP TABLE IF EXISTS `dev_tkdclub_event_participants`;
DROP TABLE IF EXISTS `dev_tkdclub_newsletter_subscribers`;

