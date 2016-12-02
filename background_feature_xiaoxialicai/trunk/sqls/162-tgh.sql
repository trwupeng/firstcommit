USE db_p2p;

INSERT INTO `tb_config` (`k`, `v`, `intro`) VALUES ('hide_sensitive', '1', '#隐藏敏感按钮#避免误操作');
INSERT INTO `tb_config_ram` (`k`, `v`, `intro`) VALUES ('hide_sensitive', '1', '#隐藏敏感按钮#避免误操作');

ALTER TABLE `tb_wares_0`
ADD COLUMN `waresNameSim`  varchar(128) NOT NULL DEFAULT '' COMMENT '标的简称' AFTER `waresName`;

ALTER TABLE `tb_wares_0_ram`
ADD COLUMN `waresNameSim`  varchar(128) NOT NULL DEFAULT '' COMMENT '标的简称' AFTER `waresName`;

CREATE TABLE `tb_wares_tpl` (
  `tplId` bigint(20) unsigned NOT NULL COMMENT 'waresId',
  `tplName` varchar(20) NOT NULL,
  `waresNameSim` varchar(128) NOT NULL COMMENT '商品名称',
  `deadLine` smallint(6) NOT NULL DEFAULT '360' COMMENT '期限',
  `dlUnit` varchar(10) NOT NULL DEFAULT '月',
  `tags` varchar(128) NOT NULL DEFAULT '' COMMENT '标签英文逗号分隔',
  `mainType` smallint(6) NOT NULL DEFAULT '0' COMMENT '大类',
  `subType` smallint(6) NOT NULL DEFAULT '0' COMMENT '小类',
  `vipLevel` smallint(6) NOT NULL DEFAULT '0' COMMENT '限制购买的vip等级',
  `priceStart` int(11) NOT NULL DEFAULT '0' COMMENT '起投金额 单位分',
  `priceStep` int(11) NOT NULL DEFAULT '1' COMMENT '递增金额 单位分',
  `amount` bigint(20) NOT NULL DEFAULT '0' COMMENT '集募总额 单位分',
  `yieldStatic` decimal(10,4) NOT NULL DEFAULT '0.0000' COMMENT '定固年化收益率',
  `yieldStaticAdd` decimal(10,4) NOT NULL DEFAULT '0.0000' COMMENT '定固年化收益率上浮',
  `yieldFloatFrom` decimal(10,4) NOT NULL DEFAULT '0.0000' COMMENT '浮动年化收益率下限',
  `yieldFloatTo` decimal(10,4) NOT NULL DEFAULT '0.0000' COMMENT '浮动年化收益率上限',
  `yieldDesc` varchar(256) NOT NULL DEFAULT '' COMMENT '年化率变更详细说明',
  `shelfId` smallint(4) DEFAULT '0' COMMENT '货架',
  `statusCode` tinyint(4) NOT NULL DEFAULT '0' COMMENT '状态',
  `returnType` int(11) NOT NULL DEFAULT '0' COMMENT '还款方式：0未定，1：一次定还本付息，2:按月付息，到期还本。。。',
  `interestStartType` int(11) NOT NULL DEFAULT '0' COMMENT '起息方式：0:购买起息，1，购买次日起息，2:募集满起息，3:募集满次日起息',
  `returnTPL` varchar(10) NOT NULL DEFAULT 'Std01',
  `exp` varchar(100) NOT NULL DEFAULT '' COMMENT '备注',
  `iRecordVerID` int(11) NOT NULL DEFAULT '0' COMMENT '列所锁',
  `sLockData` varchar(200) DEFAULT '',
  `autoReturnFund` tinyint(4) NOT NULL DEFAULT '0' COMMENT '自动回本付息开关',
  `autoConfirm` tinyint(4) NOT NULL DEFAULT '0' COMMENT '借款人自动还款',
  PRIMARY KEY (`tplId`),
  KEY `statusCode` (`statusCode`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='标的模板';



insert into db_p2p.tb_config set k='dbsql.ver',v='162-tgh' ON DUPLICATE KEY UPDATE v='162-tgh';


