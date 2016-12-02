USE `db_p2p`;

ALTER TABLE `tb_user_0`
ADD COLUMN `lastWithdraw`  bigint NOT NULL DEFAULT 0 COMMENT '上次提现日期' AFTER `idCard`,
ADD COLUMN `withdrawLeft`  varchar(1000) NOT NULL DEFAULT '' COMMENT '提现赠送次数' AFTER `lastWithdraw`;

ALTER TABLE `tb_user_1`
ADD COLUMN `lastWithdraw`  bigint NOT NULL DEFAULT 0 COMMENT '上次提现日期' AFTER `idCard`,
ADD COLUMN `withdrawLeft`  varchar(1000) NOT NULL DEFAULT '' COMMENT '提现赠送次数' AFTER `lastWithdraw`;

CREATE TABLE `tb_withdraw_num_0` (
  `numId` bigint(20) NOT NULL DEFAULT '0',
  `userId` bigint(20) NOT NULL DEFAULT '0',
  `num` tinyint(4) NOT NULL DEFAULT '0',
  `month` int(11) NOT NULL DEFAULT '0',
  `updateUser` varchar(20) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `updateTime` bigint(20) NOT NULL DEFAULT '0',
  `iRecordVerID` int(11) NOT NULL DEFAULT '0',
  `statusCode` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0无效  1有效',
  `exp` varchar(200) NOT NULL DEFAULT '' COMMENT '说明',
  PRIMARY KEY (`numId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



insert into tb_config set k='dbsql.ver',v='99-tgh' ON DUPLICATE KEY UPDATE v='99-tgh';