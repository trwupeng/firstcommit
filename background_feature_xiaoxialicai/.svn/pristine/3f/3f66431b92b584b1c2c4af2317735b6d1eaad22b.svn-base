USE db_p2p;

INSERT INTO `db_p2p`.`tb_config` (`k`, `v`, `intro`, `iRecordVerID`, `sLockData`, `extlimit`) VALUES ('management_trans', '0.005', '#放款管理费费率#', '1', '', '');
INSERT INTO `db_p2p`.`tb_config` (`k`, `v`, `intro`, `iRecordVerID`, `sLockData`, `extlimit`) VALUES ('management_confirm', '0.0002', '#还款管理费费率#', '1', '', '');

ALTER TABLE `tb_wares_0`
ADD COLUMN `managementTrans`  int NOT NULL DEFAULT 0 COMMENT '放款管理费(单位分)' AFTER `sLockData`,
ADD COLUMN `managementConfirm`  int NOT NULL DEFAULT 0 COMMENT '还款管理费(单位分)' AFTER `managementTrans`,
ADD COLUMN `waitInvestNum`  int NOT NULL DEFAULT 0 COMMENT '待处理订单数' AFTER `managementConfirm`;

ALTER TABLE `tb_wares_0_ram`
ADD COLUMN `managementTrans`  int NOT NULL DEFAULT 0 COMMENT '放款管理费(单位分)' AFTER `sLockData`,
ADD COLUMN `managementConfirm`  int NOT NULL DEFAULT 0 COMMENT '还款管理费(单位分)' AFTER `managementTrans`,
ADD COLUMN `waitInvestNum`  int NOT NULL DEFAULT 0 COMMENT '待处理订单数' AFTER `managementConfirm`;

CREATE TABLE `tb_dayManage` (
  `sn` bigint(20) NOT NULL,
  `ymd` bigint(20) NOT NULL DEFAULT '0',
  `paycorp` smallint(6) NOT NULL DEFAULT '0',
  `waresId` bigint(20) NOT NULL DEFAULT '0',
  `waresIdLocal` bigint(20) NOT NULL DEFAULT '0',
  `amount` bigint(20) NOT NULL DEFAULT '0',
  `amountLocal` bigint(20) NOT NULL DEFAULT '0',
  `file` varchar(100) NOT NULL DEFAULT '',
  `diff` varchar(100) NOT NULL DEFAULT '' COMMENT '差异',
  `checkk` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0未通过/1已通过',
  `exp` varchar(300) NOT NULL DEFAULT '',
  `updateTime` bigint(20) NOT NULL,
  `havePay` tinyint(4) NOT NULL DEFAULT '0' COMMENT '1网关有数据   0网关没数据',
  `haveLocal` tinyint(4) NOT NULL DEFAULT '0' COMMENT '1有本地数据     0没数据',
  PRIMARY KEY (`sn`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='充值流水（对账）';

ALTER TABLE `tb_systally_0`
ADD COLUMN `rebateId`  bigint NOT NULL DEFAULT 0 COMMENT '返利流水的返利ID' AFTER `iRecordVerID`;

ALTER TABLE `tb_systally_1`
ADD COLUMN `rebateId`  bigint NOT NULL DEFAULT 0 COMMENT '返利流水的返利ID' AFTER `iRecordVerID`;


insert into db_p2p.tb_config set k='dbsql.ver',v='122-tgh' ON DUPLICATE KEY UPDATE v='122-tgh';