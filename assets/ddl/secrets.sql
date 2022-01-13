CREATE TABLE IF NOT EXISTS %table_prefix%_secrets (

    id                  bigint(20)  NOT NULL    AUTO_INCREMENT PRIMARY KEY  COMMENT '',
    name                text        NOT NULL                                COMMENT '',
    value               text        NOT NULL                                COMMENT '',
    service_id          bigint(20)  NOT NULL                                COMMENT '',
    insert_timestamp    timestamp   NOT NULL    DEFAULT current_timestamp() COMMENT '',
    disabled            tinyint(1)  NOT NULL    DEFAULT 0                   COMMENT '',
    deleted             tinyint(1)  NOT NULL    DEFAULT 0                   COMMENT '',
    hash                text        NOT NULL                                COMMENT 'md5(name, value, service_id)',
    CONSTRAINT %table_prefix%_unique UNIQUE (disabled, deleted, hash), 
    CONSTRAINT %table_prefix%_map_secrets_services FOREIGN KEY (service_id) REFERENCES %table_prefix%_services(id) ON DELETE RESTRICT ON UPDATE RESTRICT

) %charset_collate% ;
