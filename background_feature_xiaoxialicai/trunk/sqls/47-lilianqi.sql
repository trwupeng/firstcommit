-- ----------------------------
--  Table structure for tb_products_final
-- ----------------------------
USE db_p2prpt;
DROP TABLE IF EXISTS `tb_products_final`;
CREATE TABLE `tb_products_final` (
  `waresId` bigint(20) unsigned NOT NULL COMMENT 'waresId',
  `waresName` varchar(128) NOT NULL COMMENT '标的名称',
  `waresSN` int(11) NOT NULL DEFAULT '0' COMMENT '期数',
  `deadLine` smallint(6) NOT NULL DEFAULT '360' COMMENT '期限',
  `dlUnit` varchar(10) DEFAULT '天' COMMENT '期限单位',
  `tags` varchar(128) NOT NULL DEFAULT '' COMMENT '标签英文逗号分隔',
  `mainType` smallint(6) NOT NULL DEFAULT '0' COMMENT '大类',
  `subType` smallint(6) NOT NULL DEFAULT '0' COMMENT '小类',
  -- `userLimit` varchar(128) NOT NULL DEFAULT '0' COMMENT '限制可购买的用户类型，英文逗号隔开  0：无限制',
  `vipLevel` smallint(6) NOT NULL DEFAULT '0' COMMENT '限制购买的vip等级',
  `priceStart` int(11) DEFAULT NULL COMMENT '起投金额 单位分',
  `priceStep` int(11) DEFAULT NULL COMMENT '递增金额 单位分',
  `amount` bigint(20) DEFAULT NULL COMMENT '集募总额 单位分',
  -- `remain` bigint(20) DEFAULT NULL COMMENT '剩余额 单位分',
  `realRaise` bigint(20) NOT NULL DEFAULT '0' COMMENT '实际募集总额',
  `yieldStatic` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '定固年化收益率',
  `yieldStaticAdd` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '定固年化收益率上浮',
  `yieldFloatFrom` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '浮动年化收益率下限',
  `yieldFloatTo` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '浮动年化收益率上限',
  -- `yieldDesc` varchar(256) NOT NULL DEFAULT '' COMMENT '年化率变更详细说明',
  -- `introDisplay` text NOT NULL COMMENT '产品介绍',
  `shelfId` smallint(4) NOT NULL DEFAULT '0' COMMENT '类型',
  `statusCode` tinyint(4) NOT NULL DEFAULT '0' COMMENT '状态',
  `returnType` int(11) NOT NULL DEFAULT '0' COMMENT '还款方式：0未定，1：一次定还本付息，2:按月付息，到期还本。。。',
  `interestStartType` int(11) NOT NULL DEFAULT '0' COMMENT '起息方式：0:购买起息，1，购买次日起息，2:募集满起息，3:募集满次日起息',
  `timeStartPlan` bigint(20) NOT NULL DEFAULT '0' COMMENT '计划上架时间',
  `timeStartReal` bigint(20) NOT NULL DEFAULT '0' COMMENT '际实上架时间',
  `timeEndPlan` bigint(20) NOT NULL DEFAULT '0' COMMENT '闭关募集结束时间',
  `timeEndReal` bigint(11) NOT NULL DEFAULT '0' COMMENT '实际募集结束时间',
  `ymdPayReal` int(11) NOT NULL DEFAULT '0' COMMENT '实际还款日期',
  `ymdPayPlan` int(11) NOT NULL DEFAULT '0' COMMENT '预计还款日期',
  -- `viewTPL` varchar(10) NOT NULL DEFAULT 'Std01' COMMENT '标的模板字段',
  -- `returnTPL` varchar(10) NOT NULL DEFAULT 'Std01',
  -- `iRecordVerID` int(11) NOT NULL DEFAULT '0' COMMENT '列所锁',
  -- `sLockData` varchar(200) DEFAULT '',
  PRIMARY KEY (`waresId`),
  KEY `statusCode` (`statusCode`,`timeStartPlan`),
  KEY `timeStartReal` (`timeStartReal`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='标的';
insert into db_p2p.tb_config values('dbsql.ver',47) ON DUPLICATE KEY UPDATE v=47;