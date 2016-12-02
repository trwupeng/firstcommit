use db_p2p;

ALTER TABLE `tb_investment_0`
ADD COLUMN `transTime`  bigint NOT NULL DEFAULT 0 COMMENT '转账扣款时间' AFTER `orderTime`;

ALTER TABLE `tb_wares_0`
ADD COLUMN `payYmd`  bigint NOT NULL DEFAULT 0 COMMENT '转账时间' AFTER `payStatus`;

ALTER TABLE `tb_wares_0`
ADD COLUMN `lastPaybackYmd`  bigint NOT NULL DEFAULT 0 COMMENT '借款人最近一次还款日' AFTER `returnTPL`;

ALTER TABLE `tb_investment_0`
ADD COLUMN `lastReturnFundYmd`  int NOT NULL DEFAULT 0 COMMENT '上次付息日' AFTER `returnType`;


insert into db_kkrpt.tb_config set k='dbsql.ver',v='81-tgh' ON DUPLICATE KEY UPDATE v='81-tgh';