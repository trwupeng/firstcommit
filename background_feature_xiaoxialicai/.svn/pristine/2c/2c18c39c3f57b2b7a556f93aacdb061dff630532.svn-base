-- -----------------------------
-- db_p2prpt  报表系统中的管理员表
-- -----------------------------
CREATE DATABASE `db_p2prpt` if not exists DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `db_p2prpt`;
SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for db_p2prpt.tb_managers_0
-- ----------------------------
DROP TABLE IF EXISTS `tb_managers_0`;
CREATE TABLE `tb_managers_0` (
  `cameFrom` varchar(36) NOT NULL,
  `loginName` varchar(36) NOT NULL,
  `nickname` varchar(36) DEFAULT NULL,
  `passwd` varchar(36) DEFAULT NULL,
  `passwdSalt` varchar(36) DEFAULT NULL,
  `regYmd` int(255) NOT NULL DEFAULT '0',
  `regIP` varchar(16) NOT NULL DEFAULT '0.0.0.0',
  `rights` varchar(2000) NOT NULL DEFAULT '' COMMENT '权限',
  `lastIP` varchar(16) NOT NULL DEFAULT '0.0.0.0',
  `lastYmd` int(11) NOT NULL DEFAULT '0',
  `lastHis` int(11) NOT NULL DEFAULT '0',
  `iRecordVerID` int(20) NOT NULL DEFAULT '0',
  `sLockData` varchar(100) NOT NULL DEFAULT '',
  `dtForbidden` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`cameFrom`,`loginName`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8  COMMENT='后台管理员';

insert into db_p2p.tb_config values('dbsql.ver',33) ON DUPLICATE KEY UPDATE v=33;