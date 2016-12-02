USE db_p2p;

INSERT INTO `db_p2p`.`tb_config` (`k`, `v`, `intro`, `iRecordVerID`, `sLockData`, `extlimit`) VALUES ('AUTO_TRANS', '1', '#自动满标转账#1:开 0:关', '1', '', '');

ALTER TABLE `tb_investment_0`
MODIFY COLUMN `yieldStaticAdd`  decimal(10,4) NOT NULL DEFAULT 0.00 COMMENT '定固年化收益率上浮' AFTER `amountFake`,
MODIFY COLUMN `yieldStatic`  decimal(10,4) NOT NULL DEFAULT 0.00 COMMENT '定固年化收益率' AFTER `yieldStaticAdd`,
MODIFY COLUMN `yieldExt`  decimal(10,4) NOT NULL DEFAULT 0.00 COMMENT '加息券加息' AFTER `yieldStatic`;

ALTER TABLE `tb_investment_1`
MODIFY COLUMN `yieldStaticAdd`  decimal(10,4) NOT NULL DEFAULT 0.00 COMMENT '定固年化收益率上浮' AFTER `amountFake`,
MODIFY COLUMN `yieldStatic`  decimal(10,4) NOT NULL DEFAULT 0.00 COMMENT '定固年化收益率' AFTER `yieldStaticAdd`,
MODIFY COLUMN `yieldExt`  decimal(10,4) NOT NULL DEFAULT 0.00 COMMENT '加息券加息' AFTER `yieldStatic`;

insert into db_p2p.tb_config set k='dbsql.ver',v='127-tgh' ON DUPLICATE KEY UPDATE v='127-tgh';