-- runs on install --


-- set language to german --
UPDATE `dev_extensions` 
SET `params` = '{"administrator":"de-DE","site":"de-DE"}' 
WHERE `name` = 'com_languages';

-- give admin user password "admin" --
UPDATE `dev_users` 
SET `password`= '433903e0a9d6a712e00251e44d29bf87:UJ0b9J5fufL3FKfCc0TLsYJBh2PFULvT' 
WHERE `username` = 'admin';

-- enable installed plugins --
UPDATE `dev_extensions` 
SET `enabled` = 1 
WHERE `name` = 'plg_console_bdreminder';

UPDATE `dev_extensions` 
SET `enabled` = 1 
WHERE `name` = 'plg_task_bdreminder';

UPDATE `dev_extensions` 
SET `enabled` = 1 
WHERE `name` = 'plg_content_gradeupdate';

UPDATE `dev_extensions` 
SET `enabled` = 1 
WHERE `name` = 'plg_user_tkdclubmember';

UPDATE `dev_extensions` 
SET `enabled` = 1 
WHERE `name` = 'plg_webservices_tkdclub';

-- enable basic api auth for simple api testing --
UPDATE `dev_extensions` 
SET `enabled` = 1 
WHERE `name` = 'plg_api-authentication_basic';

-- mock data

-- configuration parameters
UPDATE `dev_extensions`
SET `params` = '{\"club_name\":\"TKD Club\",\"currency\":\"\",\"nations\":\"\",\"functions\":\"\",\"licenses\":\"\",\"attachments_path\":\"\",\"training_types\":\"adult,child\",\"training_years\":\"\",\"training_salary\":5,\"assistent_salary\":5,\"distance_salary\":0.1,\"training_email\":\"0\",\"championship_types\":\"\",\"medal_email\":\"0\",\"badge_cost\":8,\"examiner_cost\":1,\"club_cost\":1,\"fed_cost_under_15\":15,\"fed_cost_from_15\":25,\"dan_1\":100,\"dan_2\":150,\"dan_3\":200,\"dan_4\":300,\"dan_5\":350,\"dan_6\":400,\"captcha\":\"\",\"days\":365,\"mail_prefix\":\"Update from TKD Club: \",\"mail_signature\":\"\\r\\n===============================\\r\\nThis email was sent from the TKD-Club site\\r\\nSee you in class!\",\"email_test\":\"tkdclub@tkdclub.test\",\"allowed_extensions\":\"pdf,jpeg,jpg\"}'
WHERE `name` = 'com_tkdclub'
