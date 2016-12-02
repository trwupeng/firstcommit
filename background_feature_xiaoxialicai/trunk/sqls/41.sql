-- 充值提现流水表
USE `db_p2prpt`;
DROP TABLE IF EXISTS `tb_recharges_final`;
CREATE TABLE `tb_recharges_final`(
  `ordersId` bigint(20) NOT NULL COMMENT 'ordersId',
  `userId` bigint(20) NOT NULL COMMENT 'userId',
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '水流的金额',
  `amountAbs` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '取正后的金额',
  `amountFlg` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0：提现，1充值',
  `poundage` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '手续费',
  -- `orderTime` bigint(20) NOT NULL DEFAULT '0' COMMENT '下单时间',
  `ymd` int(11) NOT NULL DEFAULT 0,
  `hhiiss` int(11) NOT NULL DEFAULT '0',
  `payTime` bigint(20) NOT NULL DEFAULT '0' COMMENT '付支状态变更时间',
  `orderStatus` int(11) NOT NULL DEFAULT '0' COMMENT '单订状态',
  `payCorp` int(11) NOT NULL DEFAULT '0' COMMENT '指定的支付通道ID',
  `bankAbs` varchar(16) NOT NULL DEFAULT '' COMMENT '银行缩写',
  `bankCard` varchar(20) NOT NULL DEFAULT '' COMMENT '银行卡号',
  PRIMARY KEY (`ordersId`)
  )ENGINE=MyISAM DEFAULT CHARSET=utf8;
  
-- tb_user_final中添加 ymdLastCharge  numLastCharge字段
ALTER TABLE db_p2prpt.tb_user_final ADD COLUMN `ymdLastCharge` int(11) NOT NULL DEFAULT 0 COMMENT '最后成功充值日期' AFTER `numFirstCharge`;
ALTER TABLE db_p2prpt.tb_user_final ADD COLUMN `numLastCharge` int(11) NOT NULL DEFAULT 0 COMMENT '最后成功充值金额' AFTER `ymdLastCharge`;

insert into db_p2p.tb_config values('dbsql.ver',41) ON DUPLICATE KEY UPDATE v=41;