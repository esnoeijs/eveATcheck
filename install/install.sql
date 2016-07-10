CREATE DATABASE IF NOT EXISTS eveATcheck;
use eveATcheck;

CREATE TABLE IF NOT EXISTS `fit` (
`id`          int(11) NOT NULL AUTO_INCREMENT,
`setupId`     int(11) NOT NULL,
`qty`         int(11) NOT NULL,
`name`        varchar(255) NOT NULL,
`description` text NOT NULL,
`deleted`     datetime DEFAULT NULL,
`publishDate` datetime NOT NULL,
`updateDate`  datetime DEFAULT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `fitData` (
`id`          int(11) NOT NULL AUTO_INCREMENT,
`fitId`       int(11) NOT NULL,
`shiptypeId`  int(11) NOT NULL,
`EFTData`     text NOT NULL,
`publishDate` datetime NOT NULL,
`userId`      int(11) NOT NULL,
PRIMARY KEY (`id`),
KEY `fitId` (`fitId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `setup` (
`id`          int(11) NOT NULL AUTO_INCREMENT,
`name`        varchar(255) NOT NULL,
`description` text NOT NULL,
`publishDate` datetime NOT NULL,
`updateDate`  datetime NOT NULL,
`userId`      int(11) NOT NULL,
`deleted`     datetime DEFAULT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `user` (
`id`       int(11) NOT NULL AUTO_INCREMENT,
`username` varchar(64) NOT NULL,
`password` varchar(64) NOT NULL,
`valid`    BOOL DEFAULT 0,
`admin`    BOOL DEFAULT 0,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8

