INSERT INTO %table_prefix%_services(id, name) VALUES ('1', 'YouTube') ON DUPLICATE KEY UPDATE name = VALUES(name);
INSERT INTO %table_prefix%_services(id, name, disabled) VALUES ('2', 'Twitch', 1) ON DUPLICATE KEY UPDATE name = VALUES(name), disabled = VALUES(disabled);
INSERT INTO %table_prefix%_services(id, name, disabled) VALUES ('3', 'Instagram', 1) ON DUPLICATE KEY UPDATE name = VALUES(name), disabled = VALUES(disabled);
INSERT INTO %table_prefix%_services(id, name, disabled) VALUES ('4', 'RSS', 1) ON DUPLICATE KEY UPDATE name = VALUES(name), disabled = VALUES(disabled);