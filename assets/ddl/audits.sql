CREATE TABLE IF NOT EXISTS %table_prefix%_audits (

    id                  bigint(20)   NOT NULL    AUTO_INCREMENT PRIMARY KEY  COMMENT '',
    job_id              bigint(20)   NOT NULL                                COMMENT '',
    secret_id           bigint(20)   NULL                                    COMMENT '',
    post_id             bigint(20)   NULL                                    COMMENT '',
    is_success          tinyint(1)   NOT NULL    DEFAULT 1                   COMMENT '',
    insert_timestamp    timestamp    NOT NULL    DEFAULT current_timestamp() COMMENT '',
    hash                varchar(512) NOT NULL                                COMMENT 'md5(job_id, post_id, secret_id, insert_timestamp, is_success)',
    CONSTRAINT %table_prefix%_unique UNIQUE (hash),
    CONSTRAINT %table_prefix%_map_audits_jobs FOREIGN KEY (job_id) REFERENCES %table_prefix%_jobs(id) ON DELETE RESTRICT ON UPDATE RESTRICT,
    CONSTRAINT %table_prefix%_map_audits_secrets FOREIGN KEY (secret_id) REFERENCES %table_prefix%_secrets(id) ON DELETE RESTRICT ON UPDATE RESTRICT

) %charset_collate% ;
