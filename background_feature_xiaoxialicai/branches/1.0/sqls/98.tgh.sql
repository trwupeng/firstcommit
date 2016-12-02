USE 'db_p2p';

ALTER TABLE `tb_asset_0`
ADD COLUMN `remain`  bigint NOT NULL DEFAULT 0 COMMENT '资产剩余金额' AFTER `amount`;

ALTER TABLE `tb_user_bankcard_0`
ADD COLUMN `unBindTime`  bigint(20) NOT NULL DEFAULT 0 COMMENT '解绑日期' AFTER `resultTime`;

ALTER TABLE `tb_user_bankcard_1`
ADD COLUMN `unBindTime`  bigint(20) NOT NULL DEFAULT 0 COMMENT '解绑日期' AFTER `resultTime`;

ALTER TABLE `tb_asset_0`
ADD COLUMN `viewTPL`  varchar(10) NOT NULL DEFAULT '' COMMENT '模板' AFTER `status`,
ADD COLUMN `introDisplay`  text NOT NULL COMMENT '详细信息' AFTER `viewTPL`;

ALTER TABLE `tb_asset_0`
ADD COLUMN `borrowerId`  varchar(30) NOT NULL DEFAULT '' COMMENT '借款人的新浪ID' AFTER `status`;

CREATE TABLE `tb_dayInterest` (
  `sn` bigint(20) NOT NULL,
  `ymd` bigint(20) NOT NULL,
  `paycorp` smallint(6) NOT NULL DEFAULT '0',
  `userId` bigint(20) NOT NULL,
  `userIdLocal` bigint(20) NOT NULL DEFAULT '0',
  `amount` bigint(20) NOT NULL,
  `amountLocal` bigint(20) NOT NULL DEFAULT '0',
  `file` varchar(100) NOT NULL DEFAULT '',
  `diff` varchar(100) NOT NULL DEFAULT '' COMMENT '差异',
  `checkk` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0未通过/1已通过',
  `exp` varchar(300) NOT NULL DEFAULT '',
  `tallyId` bigint(20) NOT NULL DEFAULT '0' COMMENT '流水号',
  `statusCode` tinyint(4) NOT NULL DEFAULT '0' COMMENT '状态码',
  `updateTime` bigint(20) NOT NULL,
  PRIMARY KEY (`sn`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='存钱罐日收益';

ALTER TABLE `tb_investment_0`
ADD COLUMN `rebateId`  bigint NOT NULL DEFAULT 0 COMMENT '返利流水号' AFTER `userId`;

ALTER TABLE `tb_investment_1`
ADD COLUMN `rebateId`  bigint NOT NULL DEFAULT 0 COMMENT '返利流水号' AFTER `userId`;




insert into tb_config set k='dbsql.ver',v='98-tgh' ON DUPLICATE KEY UPDATE v='98-tgh';