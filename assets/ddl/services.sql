CREATE TABLE IF NOT EXISTS %table_prefix%services (

    id                  bigint(20)  NOT NULL    AUTO_INCREMENT PRIMARY KEY  COMMENT '',
    name                text        NOT NULL    UNIQUE KEY                  COMMENT 'Name of the Service'

) %charset_collate% ;
