USE db_p2p;

CREATE TABLE `tb_asset_0` (
  `assetId` bigint(20) NOT NULL DEFAULT '0' COMMENT '资产ID',
  `assetName` varchar(60) CHARACTER SET latin1 NOT NULL DEFAULT '' COMMENT '名称',
  `assetDesc` varchar(300) CHARACTER SET latin1 NOT NULL DEFAULT '' COMMENT '描述',
  `amount` bigint(20) NOT NULL DEFAULT '0' COMMENT '金额',
  `startYmd` bigint(20) NOT NULL DEFAULT '0' COMMENT '开始日期',
  `endYmd` bigint(20) NOT NULL DEFAULT '0' COMMENT '结束时间',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '状态',
  `createTime` bigint(20) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `iRecordVerID` int(11) NOT NULL DEFAULT '0',
  `sLockData` varchar(200) CHARACTER SET latin1 DEFAULT NULL,
  PRIMARY KEY (`assetId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



INSERT INTO db_p2p.tb_config
VALUES
	('dbsql.ver', 78) ON DUPLICATE KEY UPDATE v = 78;