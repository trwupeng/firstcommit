USE db_p2p;
CREATE TABLE `tb_clientPatch` (
  `clientType` int(11) NOT NULL,
  `ver` varchar(16) NOT NULL,
  `enforce` tinyint(4) NOT NULL DEFAULT '0' COMMENT '强制更新（0：非强制，1：强制）',
  `info` varchar(500) NOT NULL DEFAULT '' COMMENT '更新说明',
  `url` varchar(256) NOT NULL DEFAULT 'http[s]://' COMMENT '下载地址',
  PRIMARY KEY (`clientType`,`ver`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


INSERT INTO db_p2p.tb_config VALUES ('dbsql.ver', 50) ON DUPLICATE KEY UPDATE v = 50;