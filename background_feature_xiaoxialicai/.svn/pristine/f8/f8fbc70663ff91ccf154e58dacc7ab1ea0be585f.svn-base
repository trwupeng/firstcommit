USE db_p2p;

CREATE TABLE `tb_sns_wechat_0` (
  `openId` varchar(50) NOT NULL DEFAULT '' COMMENT 'openId',
  `userId` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT 'userId',
  `loginName` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '登录名',
  `expiresIn` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '上一次绑定的时间',
  `iRecordVerID` int(11) NOT NULL DEFAULT '0' COMMENT 'KVOBJ-row-version',
  PRIMARY KEY (`openId`),
  KEY `userId` (`userId`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='微信绑定用户表';

INSERT INTO db_p2p.tb_config
VALUES
	('dbsql.ver', 75) ON DUPLICATE KEY UPDATE v = 75;