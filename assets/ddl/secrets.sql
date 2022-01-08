CREATE TABLE IF NOT EXISTS %table_prefix%_secrets (

    id                  bigint(20)  NOT NULL    AUTO_INCREMENT PRIMARY KEY  COMMENT '',
    value               text        NOT NULL    UNIQUE KEY                  COMMENT '',
    service_id          bigint(20)  NOT NULL                                COMMENT '',
    insert_timestamp    timestamp   NOT NULL    DEFAULT current_timestamp() COMMENT '',
    disabled            tinyint(1)  NOT NULL    DEFAULT 0                   COMMENT '',
    hash                text        NOT NULL                                COMMENT 'md5(*columns*)'

) %charset_collate% ;
