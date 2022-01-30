CREATE TABLE IF NOT EXISTS %table_prefix%_services (

    id                  bigint(20)      NOT NULL    AUTO_INCREMENT PRIMARY KEY  COMMENT '',
    name                varchar(255)    NOT NULL    UNIQUE KEY                  COMMENT 'Name of the Service',
    disabled            tinyint(1)      NOT NULL    DEFAULT 0                   COMMENT ''

) %charset_collate% ;
