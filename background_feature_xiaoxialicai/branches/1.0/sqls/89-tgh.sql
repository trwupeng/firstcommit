USE db_p2p;

ALTER TABLE `tb_rebate_0`
ADD COLUMN `childNickname`  varchar(100) NOT NULL DEFAULT '' AFTER `childUserId`,
ADD COLUMN `childPhone`  bigint(20) NOT NULL DEFAULT 0 AFTER `childNickname`,
ADD COLUMN `sumAmount`  bigint NOT NULL DEFAULT 0 AFTER `amount`;

ALTER TABLE `tb_dayRecharges`
CHANGE COLUMN `check` `checkk`  tinyint(4) NOT NULL DEFAULT 0 COMMENT '0未通过/1已通过' AFTER `diff`;

ALTER TABLE `tb_dayBuy`
CHANGE COLUMN `check` `checkk`  tinyint(4) NOT NULL DEFAULT 0 COMMENT '0未通过/1已通过' AFTER `diff`;

ALTER TABLE `tb_dayLoan`
CHANGE COLUMN `check` `checkk`  tinyint(4) NOT NULL DEFAULT 0 COMMENT '0未通过/1已通过' AFTER `diff`;

ALTER TABLE `tb_dayPayback`
CHANGE COLUMN `check` `checkk`  tinyint(4) NOT NULL DEFAULT 0 COMMENT '0未通过/1已通过' AFTER `diff`;

ALTER TABLE `tb_dayPaysplit`
CHANGE COLUMN `check` `checkk`  tinyint(4) NOT NULL DEFAULT 0 COMMENT '0未通过/1已通过' AFTER `diff`;

ALTER TABLE `tb_dayWithdraw`
CHANGE COLUMN `check` `checkk`  tinyint(4) NOT NULL DEFAULT 0 COMMENT '0未通过/1已通过' AFTER `diff`;

insert into tb_config set k='dbsql.ver',v='89-tgh' ON DUPLICATE KEY UPDATE v='89-tgh';