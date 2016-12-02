USE db_p2p;

ALTER TABLE `tb_wares_0`
MODIFY COLUMN `payGift`  bigint(20) NOT NULL DEFAULT 0 COMMENT '满标转账时候的平台垫付' AFTER `payStatus`;

ALTER TABLE `tb_wares_0`
ADD COLUMN `confirmGift`  bigint NOT NULL DEFAULT 0 COMMENT '企业还款时候的垫付' AFTER `payGift`;

ALTER TABLE `tb_wares_0_ram`
ADD COLUMN `confirmGift`  bigint NOT NULL DEFAULT 0 COMMENT '企业还款时候的垫付' AFTER `payGift`;

ALTER TABLE `tb_systally_0`
ADD COLUMN `payYmd`  bigint NOT NULL DEFAULT 0 COMMENT '支付时间' AFTER `tallyYmd`;

ALTER TABLE `tb_systally_1`
ADD COLUMN `payYmd`  bigint NOT NULL DEFAULT 0 COMMENT '支付时间' AFTER `tallyYmd`;

ALTER TABLE `tb_wares_0`
ADD COLUMN `autoReturnFund`  tinyint NOT NULL DEFAULT 0 COMMENT '自动回本付息开关' AFTER `sortval`;

ALTER TABLE `tb_wares_0_ram`
ADD COLUMN `autoReturnFund`  tinyint NOT NULL DEFAULT 0 COMMENT '自动回本付息开关' AFTER `sortval`;





insert into db_p2p.tb_config set k='dbsql.ver',v='140-lyq' ON DUPLICATE KEY UPDATE v='140-lyq';