INSERT INTO %table_prefix%_triggers(id, name, type, seconds, minutes, hours, days, hash, disabled) VALUES (1, '00.00.01.00', 'every_1_minute', 00, 1, 0, 0, '1', 1) 
    ON DUPLICATE KEY UPDATE name = VALUES(name), type = VALUES(type), seconds = VALUES(seconds), minutes = VALUES(minutes), hours = VALUES(hours), days = VALUES(days), hash = VALUES(hash), disabled = VALUES(disabled);
INSERT INTO %table_prefix%_triggers(id, name, type, seconds, minutes, hours, days, hash) VALUES (2, '00.00.15.00', 'every_15_minutes', 00, 15, 0, 0, '2') 
    ON DUPLICATE KEY UPDATE name = VALUES(name), type = VALUES(type), seconds = VALUES(seconds), minutes = VALUES(minutes), hours = VALUES(hours), days = VALUES(days), hash = VALUES(hash);
INSERT INTO %table_prefix%_triggers(id, name, type, seconds, minutes, hours, days, hash) VALUES (3, '00.00.30.00', 'every_30_minutes', 00, 30, 0, 0, '3') 
    ON DUPLICATE KEY UPDATE name = VALUES(name), type = VALUES(type), seconds = VALUES(seconds), minutes = VALUES(minutes), hours = VALUES(hours), days = VALUES(days), hash = VALUES(hash);
INSERT INTO %table_prefix%_triggers(id, name, type, seconds, minutes, hours, days, hash) VALUES (4, '00.01.00.00', 'every_1_hour', 00, 00, 1, 0, '4') 
    ON DUPLICATE KEY UPDATE name = VALUES(name), type = VALUES(type), seconds = VALUES(seconds), minutes = VALUES(minutes), hours = VALUES(hours), days = VALUES(days), hash = VALUES(hash);
INSERT INTO %table_prefix%_triggers(id, name, type, seconds, minutes, hours, days, hash) VALUES (5, '00.06.00.00', 'every_6_hours', 00, 00, 6, 0, '5') 
    ON DUPLICATE KEY UPDATE name = VALUES(name), type = VALUES(type), seconds = VALUES(seconds), minutes = VALUES(minutes), hours = VALUES(hours), days = VALUES(days), hash = VALUES(hash);
INSERT INTO %table_prefix%_triggers(id, name, type, seconds, minutes, hours, days, hash) VALUES (6, '00.12.00.00', 'every_12_hours', 00, 00, 12, 0, '6') 
    ON DUPLICATE KEY UPDATE name = VALUES(name), type = VALUES(type), seconds = VALUES(seconds), minutes = VALUES(minutes), hours = VALUES(hours), days = VALUES(days), hash = VALUES(hash);
INSERT INTO %table_prefix%_triggers(id, name, type, seconds, minutes, hours, days, hash) VALUES (7, '01.00.00.00', 'every_1_day', 00, 00, 00, 1, '7') 
    ON DUPLICATE KEY UPDATE name = VALUES(name), type = VALUES(type), seconds = VALUES(seconds), minutes = VALUES(minutes), hours = VALUES(hours), days = VALUES(days), hash = VALUES(hash);