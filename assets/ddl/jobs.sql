CREATE TABLE IF NOT EXISTS %table_prefix%_jobs (

    id                  bigint(20)      NOT NULL    AUTO_INCREMENT PRIMARY KEY  COMMENT '',
    name                varchar(255)    NOT NULL                                COMMENT 'Name of the Job',
    meta_id             bigint(20)      NOT NULL                                COMMENT '',
    trigger_id          bigint(20)      NOT NULL                                COMMENT '',
    insert_timestamp    timestamp       NOT NULL    DEFAULT current_timestamp() COMMENT '',
    update_timestamp    timestamp       NOT NULL    DEFAULT current_timestamp() COMMENT '',
    disabled            tinyint(1)      NOT NULL    DEFAULT 0                   COMMENT '',
    deleted             tinyint(1)      NOT NULL    DEFAULT 0                   COMMENT '',
    hash                varchar(512)    NOT NULL                                COMMENT 'md5(name, meta_id, trigger_id)',
    CONSTRAINT %table_prefix%_unique UNIQUE (disabled, deleted, hash),
    CONSTRAINT %table_prefix%_map_jobs_metas FOREIGN KEY (meta_id) REFERENCES %table_prefix%_metas(id) ON DELETE RESTRICT ON UPDATE RESTRICT,
    CONSTRAINT %table_prefix%_map_jobs_triggers FOREIGN KEY (trigger_id) REFERENCES %table_prefix%_triggers(id) ON DELETE RESTRICT ON UPDATE RESTRICT

) %charset_collate% ;
