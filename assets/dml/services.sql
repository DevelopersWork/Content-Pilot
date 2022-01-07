INSERT INTO %table_prefix%_services(id, name) VALUES ('1', 'YouTube') ON DUPLICATE KEY UPDATE id = '1';
INSERT INTO %table_prefix%_services(id, name) VALUES ('2','Twitch') ON DUPLICATE KEY UPDATE id = '2';
INSERT INTO %table_prefix%_services(id, name) VALUES ('3','Instagram') ON DUPLICATE KEY UPDATE id = '3';
INSERT INTO %table_prefix%_services(id, name) VALUES ('4','RSS') ON DUPLICATE KEY UPDATE id = '4';