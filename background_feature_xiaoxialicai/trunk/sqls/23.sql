-- ----------------------------
-- 合作商表，协议表
-- ----------------------------

-- ----------------------------
-- Table structure for db_p2p.tb_contract_0
-- ----------------------------
DROP TABLE IF EXISTS db_p2p.tb_contract_0;
CREATE TABLE db_p2p.tb_contract_0 (
  `contractId` bigint(20) NOT NULL DEFAULT '0' COMMENT '议协ID',
  `copartnerAbs` varchar(36) DEFAULT NULL COMMENT '合作商英文简称',
  `ymdStart` int(255) NOT NULL DEFAULT '20000101' COMMENT '开始时间',
  `ymdEnd` int(255) NOT NULL DEFAULT '20990101' COMMENT '结束时间',
  `profitsPlan` int(255) NOT NULL DEFAULT '0' COMMENT '分成方案',
  `profitsFix` float(10,5) NOT NULL DEFAULT '1.00000' COMMENT '分成修正',
  `notes` varchar(128) NOT NULL DEFAULT '' COMMENT '备注',
  `remarks` varchar(50) NOT NULL DEFAULT '' COMMENT '专门备注用于哪个活动',
  `statusCode` int(11) NOT NULL DEFAULT '0' COMMENT '状态',
  `iRecordVerID` int(255) NOT NULL DEFAULT '0' COMMENT '[系统]',
  PRIMARY KEY (`contractId`),
  KEY `copartner` (`copartnerAbs`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT '推广协议';


-- ----------------------------
-- Table structure for db_p2p.tb_copartner_0
-- ----------------------------
DROP TABLE IF EXISTS db_p2p.tb_copartner_0;
CREATE TABLE db_p2p.tb_copartner_0 (
  `copartnerId` int(11) NOT NULL DEFAULT '0' COMMENT 'copartnerId',
  `copartnerName` varchar(36) NOT NULL COMMENT '合作方ID',
  `copartnerAbs` varchar(36) DEFAULT NULL COMMENT '合作商英文简称',
  `contractorBiz` varchar(60) DEFAULT NULL COMMENT '联系人1(业务)',
  `contractorDev` varchar(60) DEFAULT NULl COMMENT '联系人2(技术)',
  `flgDisable` tinyint(255) NOT NULL DEFAULT '0' COMMENT '渠道使用状态',
  `iRecordVerID` int(255) NOT NULL DEFAULT '0',
  PRIMARY KEY (`copartnerId`),
  UNIQUE KEY `abs` (`copartnerAbs`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='推广合作商';

insert into db_p2p.tb_config values('dbsql.ver',23) ON DUPLICATE KEY UPDATE v=23;