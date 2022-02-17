CREATE TABLE IF NOT EXISTS %table_prefix%_triggers (

    id                  bigint(20)      NOT NULL    AUTO_INCREMENT PRIMARY KEY  COMMENT '',
    name                varchar(255)    NOT NULL                                COMMENT 'Name of the Trigger',
    type                varchar(255)    NOT NULL                                COMMENT 'Hourly, Daily, Weekly',
    seconds             bigint(20)      NOT NULL    DEFAULT 60                  COMMENT 'seconds between each run',
    minutes             bigint(20)      NOT NULL    DEFAULT 60                  COMMENT 'minutes between each run',
    hours               bigint(20)      NOT NULL    DEFAULT 24                  COMMENT 'hours between each run',
    days                bigint(20)      NOT NULL    DEFAULT 1                   COMMENT 'days between each run',
    insert_timestamp    timestamp       NOT NULL    DEFAULT current_timestamp() COMMENT '',
    update_timestamp    timestamp       NOT NULL    DEFAULT current_timestamp() COMMENT '',
    disabled            tinyint(1)      NOT NULL    DEFAULT 0                   COMMENT '',
    deleted             tinyint(1)      NOT NULL    DEFAULT 0                   COMMENT '',
    hash                varchar(512)    NOT NULL                                COMMENT 'md5(name, type, seconds, minutes, hours, days)',
    CONSTRAINT %table_prefix%_triggers_unique UNIQUE (disabled, deleted, hash)
    
) %charset_collate% ;
