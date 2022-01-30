CREATE TABLE IF NOT EXISTS %table_prefix%_meta (

    id                  bigint(20)      NOT NULL    AUTO_INCREMENT PRIMARY KEY  COMMENT '',
    name                varchar(255)    NOT NULL                                COMMENT 'Name of the Service Meta',
    data                varchar(255)    NOT NULL                                COMMENT 'JSON String',
    service_id          bigint(20)      NOT NULL                                COMMENT '',
    secret_id           bigint(20)      NULL                                    COMMENT '',
    key_required        tinyint(1)      NOT NULL                                COMMENT '',
    disabled            tinyint(1)      NOT NULL    DEFAULT 0                   COMMENT '',
    deleted             tinyint(1)      NOT NULL    DEFAULT 0                   COMMENT '',
    hash                varchar(512)    NOT NULL                                COMMENT 'md5(name, service_id, meta, secret_id)',
    CONSTRAINT %table_prefix%_unique UNIQUE (disabled, deleted, hash), 
    CONSTRAINT %table_prefix%_map_meta_services FOREIGN KEY (service_id) REFERENCES %table_prefix%_services(id) ON DELETE RESTRICT ON UPDATE RESTRICT,
    CONSTRAINT %table_prefix%_map_meta_secrets FOREIGN KEY (secret_id) REFERENCES %table_prefix%_secrets(id) ON DELETE RESTRICT ON UPDATE RESTRICT

) %charset_collate% ;
