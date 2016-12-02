USE `db_p2p`;

DROP TABLE IF EXISTS `tb_shortened_url_0`;
CREATE TABLE `tb_shortened_url_0` (
  `shortId` char(20) NOT NULL DEFAULT '0' COMMENT '短链ID',
  `contractId` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT 'contractId',
  `scale` smallint(4) unsigned NOT NULL COMMENT '比率',
  `copartnerName` varchar(80) NOT NULL DEFAULT '' COMMENT '短链位置',
  `instruction` varchar(255) NOT NULL DEFAULT '' COMMENT '说明',
  `createTime` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `status` tinyint(3) NOT NULL DEFAULT '1' COMMENT '状态位',
  PRIMARY KEY (`shortId`,`contractId`),
  KEY `contractId` (`contractId`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='短链表';

INSERT INTO db_p2p.tb_config
VALUES
	('dbsql.ver', 62) ON DUPLICATE KEY UPDATE v = 62;