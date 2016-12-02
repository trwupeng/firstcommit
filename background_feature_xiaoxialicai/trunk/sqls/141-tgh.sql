USE db_p2p;

ALTER TABLE `tb_wares_0`
ADD COLUMN `autoConfirm`  tinyint NOT NULL DEFAULT 0 COMMENT '借款人自动还款' AFTER `autoReturnFund`,
ADD COLUMN `nextConfirmYmd`  bigint NOT NULL DEFAULT 0 COMMENT '下次满标转账时间  yyyy-mm-dd' AFTER `autoConfirm`;

ALTER TABLE `tb_wares_0_ram`
ADD COLUMN `autoConfirm`  tinyint NOT NULL DEFAULT 0 COMMENT '借款人自动还款' AFTER `autoReturnFund`,
ADD COLUMN `nextConfirmYmd`  bigint NOT NULL DEFAULT 0 COMMENT '下次满标转账时间  yyyy-mm-dd' AFTER `autoConfirm`;



insert into db_p2p.tb_config set k='dbsql.ver',v='141-tgh' ON DUPLICATE KEY UPDATE v='141-tgh';