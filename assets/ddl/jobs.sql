CREATE TABLE IF NOT EXISTS %table_prefix%_jobs (

    id                  bigint(20)  NOT NULL    AUTO_INCREMENT PRIMARY KEY  COMMENT '',
    name                text        NOT NULL                                COMMENT 'Name of the Job',
    service_id          bigint(20)  NOT NULL                                COMMENT '',
    trigger_id          bigint(20)  NOT NULL                                COMMENT '',
    key_required        tinyint(1)  NOT NULL                                COMMENT '',
    insert_timestamp    timestamp   NOT NULL    DEFAULT current_timestamp() COMMENT '',
    update_timestamp    timestamp   NOT NULL    DEFAULT current_timestamp() COMMENT '',
    disabled            tinyint(1)  NOT NULL    DEFAULT 0                   COMMENT '',
    deleted             tinyint(1)  NOT NULL    DEFAULT 0                   COMMENT '',
    hash                text        NOT NULL                                COMMENT 'md5(*columns*)'

) %charset_collate% ;
