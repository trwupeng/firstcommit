-- -----------------------------------------------
-- update tb_loginname
-- -----------------------------------------------
DROP TABLE IF EXISTS `tb_loginname_alias_0`;
CREATE TABLE `tb_loginname_0` (
  `loginName` varchar(36) NOT NULL COMMENT '登录名',
  `cameFrom` varchar(36) NOT NULL COMMENT '登录类型，例如：phone,local',
  `accountId` bigint(255) NOT NULL COMMENT '用户标识',
  `flgStatus` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态位',
  `iRecordVerID` int(11) NOT NULL DEFAULT '0' COMMENT 'KVOBJ-row-version',
  PRIMARY KEY (`loginName`,`cameFrom`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- -----------------------------------------------
-- update tb_accounts
-- -----------------------------------------------
DROP TABLE IF EXISTS `tb_accounts_0`;
CREATE TABLE `tb_accounts_0` (
  `accountId` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '用户标识',
  `passwd` varchar(32) DEFAULT NULL COMMENT '密码',
  `passwdSalt` varchar(4) DEFAULT NULL COMMENT '安全码',
  `regYmd` int(11) NOT NULL DEFAULT '0' COMMENT '注册日期',
  `regHHiiss` int(11) NOT NULL DEFAULT '4' COMMENT '注册时间：时分秒',
  `regClient` tinyint(4) NOT NULL DEFAULT '0' COMMENT '注册客户端',
  `regIP` varchar(16) NOT NULL DEFAULT '' COMMENT '注册客户端',
  `dtForbidden` int(11) NOT NULL DEFAULT '0' COMMENT '状态 (0 正常使用)',
  `loginFailed` bigint(36) unsigned NOT NULL DEFAULT '0' COMMENT '密码错误后的CD',
  `nickname` varchar(36) DEFAULT NULL COMMENT '昵称',
  `lastIP` varchar(16) NOT NULL DEFAULT '' COMMENT '最后访问IP',
  `lastDt` int(11) NOT NULL DEFAULT '0' COMMENT '最后访问时间',
  `contractId` bigint(20) NOT NULL DEFAULT '0',
  `phone` varchar(20) NOT NULL DEFAULT '0',
  `rights` varchar(2000) NOT NULL DEFAULT '',
  `iRecordVerID` int(20) unsigned DEFAULT '0',
  PRIMARY KEY (`accountId`),
  UNIQUE KEY `accountId` (`accountId`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

insert into db_p2p.tb_config values('dbsql.ver',3) ON DUPLICATE KEY UPDATE v=3;