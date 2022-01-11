INSERT INTO %table_prefix%_triggers(id, name, type, seconds, minutes, hours, days, disabled) VALUES (1, '00.00.00.30', 'every_30_seconds', 30, 0, 0, 0, 1) ON DUPLICATE KEY UPDATE id = '1';
INSERT INTO %table_prefix%_triggers(id, name, type, seconds, minutes, hours, days) VALUES (2, '00.00.01.00', 'every_1_minute', 00, 1, 0, 0) ON DUPLICATE KEY UPDATE id = '2';
INSERT INTO %table_prefix%_triggers(id, name, type, seconds, minutes, hours, days) VALUES (3, '00.00.15.00', 'every_15_minutes', 00, 15, 0, 0) ON DUPLICATE KEY UPDATE id = '3';
INSERT INTO %table_prefix%_triggers(id, name, type, seconds, minutes, hours, days) VALUES (4, '00.00.30.00', 'every_30_minutes', 00, 30, 0, 0) ON DUPLICATE KEY UPDATE id = '4';
INSERT INTO %table_prefix%_triggers(id, name, type, seconds, minutes, hours, days) VALUES (5, '00.01.00.00', 'every_1_hour', 00, 00, 1, 0) ON DUPLICATE KEY UPDATE id = '5';
INSERT INTO %table_prefix%_triggers(id, name, type, seconds, minutes, hours, days) VALUES (6, '00.06.00.00', 'every_6_hours', 00, 00, 6, 0) ON DUPLICATE KEY UPDATE id = '6';
INSERT INTO %table_prefix%_triggers(id, name, type, seconds, minutes, hours, days) VALUES (7, '00.12.00.00', 'every_12_hours', 00, 00, 12, 0) ON DUPLICATE KEY UPDATE id = '7';
INSERT INTO %table_prefix%_triggers(id, name, type, seconds, minutes, hours, days) VALUES (8, '01.00.00.00', 'every_1_day', 00, 00, 00, 1) ON DUPLICATE KEY UPDATE id = '8';