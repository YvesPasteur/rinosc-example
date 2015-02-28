CREATE DATABASE IF NOT EXISTS vdm;
ALTER DATABASE vdm CHARACTER SET UTF8 COLLATE utf8_general_ci;

USE vdm

CREATE TABLE IF NOT EXISTS posts (
    id int auto_increment,
    author varchar(200),
    date DATETIME,
    content TEXT,
    PRIMARY KEY (id)
)
ENGINE = InnoDB;
