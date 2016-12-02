USE db_p2p;

ALTER TABLE `tb_investment_0`
MODIFY COLUMN `yieldStaticAdd`  decimal(10,4) NOT NULL DEFAULT 0.0000 COMMENT '定固年化收益率上浮' AFTER `amountFake`,
MODIFY COLUMN `yieldStatic`  decimal(10,4) NOT NULL DEFAULT 0.0000 COMMENT '定固年化收益率' AFTER `yieldStaticAdd`,
MODIFY COLUMN `yieldExt`  decimal(10,4) NOT NULL DEFAULT 0.0000 COMMENT '加息券加息' AFTER `yieldStatic`;

ALTER TABLE `tb_investment_1`
MODIFY COLUMN `yieldStaticAdd`  decimal(10,4) NOT NULL DEFAULT 0.0000 COMMENT '定固年化收益率上浮' AFTER `amountFake`,
MODIFY COLUMN `yieldStatic`  decimal(10,4) NOT NULL DEFAULT 0.0000 COMMENT '定固年化收益率' AFTER `yieldStaticAdd`,
MODIFY COLUMN `yieldExt`  decimal(10,4) NOT NULL DEFAULT 0.0000 COMMENT '加息券加息' AFTER `yieldStatic`;



insert into db_p2p.tb_config set k='dbsql.ver',v='111-tgh' ON DUPLICATE KEY UPDATE v='111-tgh';