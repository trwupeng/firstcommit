
use db_p2p;

CREATE TABLE `tb_dayBuy` (
  `sn` bigint(20) NOT NULL,
  `ymd` bigint(20) NOT NULL,
  `paycorp` smallint(6) NOT NULL DEFAULT '0',
  `waresId` bigint(20) NOT NULL,
  `waresIdLocal` bigint(20) NOT NULL DEFAULT '0',
  `shelfId` smallint(6) NOT NULL DEFAULT '0',
  `userId` bigint(20) NOT NULL,
  `userIdLocal` bigint(20) NOT NULL DEFAULT '0',
  `amount` bigint(20) NOT NULL,
  `amountLocal` bigint(20) NOT NULL DEFAULT '0',
  `amountExtra` bigint(20) NOT NULL DEFAULT '0',
  `amountExtraLocal` bigint(20) NOT NULL DEFAULT '0',
  `file` varchar(100) NOT NULL DEFAULT '',
  `diff` varchar(100) NOT NULL DEFAULT '' COMMENT '差异',
  `check` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0未通过/1已通过',
  `exp` varchar(300) NOT NULL DEFAULT '',
  `updateTime` bigint(20) NOT NULL,
  PRIMARY KEY (`sn`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='充值流水（对账）';

CREATE TABLE `tb_dayLoan` (
  `sn` bigint(20) NOT NULL,
  `ymd` bigint(20) NOT NULL,
  `waresId` bigint(20) NOT NULL DEFAULT '0',
  `waresIdLocal` bigint(20) NOT NULL DEFAULT '0',
  `paycorp` smallint(6) NOT NULL DEFAULT '0',
  `borrowerId` bigint(20) NOT NULL DEFAULT '0',
  `borrowerIdLocal` bigint(20) NOT NULL DEFAULT '0',
  `amount` bigint(20) NOT NULL,
  `amountLocal` bigint(20) NOT NULL DEFAULT '0',
  `file` varchar(100) NOT NULL DEFAULT '',
  `diff` varchar(100) NOT NULL DEFAULT '' COMMENT '差异',
  `check` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0未通过/1已通过',
  `exp` varchar(300) NOT NULL DEFAULT '',
  `updateTime` bigint(20) NOT NULL,
  PRIMARY KEY (`sn`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='放款流水（对账）';

CREATE TABLE `tb_dayPayback` (
  `sn` bigint(20) NOT NULL,
  `ymd` bigint(20) NOT NULL,
  `waresId` bigint(20) NOT NULL DEFAULT '0',
  `waresIdLocal` bigint(20) NOT NULL DEFAULT '0',
  `paycorp` smallint(6) NOT NULL DEFAULT '0',
  `borrowerId` bigint(20) NOT NULL,
  `borrowerIdLocal` bigint(20) NOT NULL DEFAULT '0',
  `amount` bigint(20) NOT NULL,
  `amountLocal` bigint(20) NOT NULL DEFAULT '0',
  `file` varchar(100) NOT NULL DEFAULT '',
  `diff` varchar(100) NOT NULL DEFAULT '' COMMENT '差异',
  `check` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0未通过/1已通过',
  `exp` varchar(300) NOT NULL DEFAULT '',
  `updateTime` bigint(20) NOT NULL,
  PRIMARY KEY (`sn`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='还款流水（对账）';

CREATE TABLE `tb_dayPaysplit` (
  `sn` bigint(20) NOT NULL,
  `ymd` bigint(20) NOT NULL,
  `paycorp` smallint(6) NOT NULL DEFAULT '0',
  `waresId` bigint(20) NOT NULL,
  `waresIdLocal` bigint(20) NOT NULL DEFAULT '0',
  `userId` bigint(20) NOT NULL,
  `userIdLocal` bigint(20) NOT NULL DEFAULT '0',
  `amount` bigint(20) NOT NULL,
  `amountLocal` bigint(20) NOT NULL DEFAULT '0',
  `interest` bigint(20) NOT NULL DEFAULT '0',
  `interestLocal` bigint(20) NOT NULL DEFAULT '0',
  `file` varchar(100) NOT NULL DEFAULT '',
  `diff` varchar(100) NOT NULL DEFAULT '' COMMENT '差异',
  `check` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0未通过/1已通过',
  `exp` varchar(300) NOT NULL DEFAULT '',
  `updateTime` bigint(20) NOT NULL,
  PRIMARY KEY (`sn`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='回款流水（对账）';

CREATE TABLE `tb_dayRecharges` (
  `sn` bigint(20) NOT NULL,
  `ymd` bigint(20) NOT NULL,
  `paycorp` smallint(6) NOT NULL DEFAULT '0',
  `userId` bigint(20) NOT NULL,
  `userIdLocal` bigint(20) NOT NULL DEFAULT '0',
  `amount` bigint(20) NOT NULL,
  `amountLocal` bigint(20) NOT NULL DEFAULT '0',
  `file` varchar(100) NOT NULL DEFAULT '',
  `diff` varchar(100) NOT NULL DEFAULT '' COMMENT '差异',
  `check` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0未通过/1已通过',
  `exp` varchar(300) NOT NULL DEFAULT '',
  `updateTime` bigint(20) NOT NULL,
  PRIMARY KEY (`sn`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='充值流水（对账）';

CREATE TABLE `tb_dayWithdraw` (
  `sn` bigint(20) NOT NULL,
  `ymd` bigint(20) NOT NULL,
  `paycorp` smallint(6) NOT NULL DEFAULT '0',
  `userId` bigint(20) NOT NULL,
  `userIdLocal` bigint(20) NOT NULL DEFAULT '0',
  `amount` bigint(20) NOT NULL,
  `amountLocal` bigint(20) NOT NULL DEFAULT '0',
  `poundage` bigint(20) NOT NULL DEFAULT '0',
  `poundageLocal` bigint(20) NOT NULL DEFAULT '0',
  `file` varchar(100) NOT NULL DEFAULT '',
  `diff` varchar(100) NOT NULL DEFAULT '' COMMENT '差异',
  `check` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0未通过/1已通过',
  `exp` varchar(300) NOT NULL DEFAULT '',
  `updateTime` bigint(20) NOT NULL,
  PRIMARY KEY (`sn`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='提现流水（对账）';

ALTER TABLE `tb_wallettally_0`
ADD COLUMN `freeze`  tinyint NOT NULL DEFAULT 0 COMMENT '是否冻结的 0没冻结 1已冻结' AFTER `tallyType`;

ALTER TABLE `tb_user_0`
ADD COLUMN `isSuperUser`  tinyint NOT NULL DEFAULT 0 COMMENT '是否超级用户  1是 0不是' AFTER `isBorrower`;

ALTER TABLE `tb_wallettally_0`
ADD COLUMN `sn`  bigint NOT NULL DEFAULT 0 COMMENT '网关流水号' AFTER `orderId`;


insert into db_kkrpt.tb_config set k='dbsql.ver',v='82-tgh' ON DUPLICATE KEY UPDATE v='82-tgh';