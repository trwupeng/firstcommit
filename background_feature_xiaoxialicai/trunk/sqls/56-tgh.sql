use db_p2p;
ALTER TABLE `tb_investment_0`
ADD COLUMN `shelfId`  smallint NOT NULL DEFAULT 0 COMMENT '类型 位了区分浮动/固定' AFTER `waresName`;

ALTER TABLE `tb_vouchers_0`
MODIFY COLUMN `amount`  int(11) NOT NULL DEFAULT 0 COMMENT '券金额 单位分/加息 单位%' AFTER `voucherType`,
MODIFY COLUMN `exp`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '使用说明' AFTER `limitsType`,
ADD COLUMN `limitsAmount`  int NOT NULL DEFAULT 0 COMMENT '限制使用金额' AFTER `limitsTag`,
ADD COLUMN `limitsDeadline`  int NOT NULL DEFAULT 0 COMMENT '限制标的期限' AFTER `limitsAmount`;

INSERT INTO db_p2p.tb_config VALUES  ('dbsql.ver', 56) ON DUPLICATE KEY UPDATE v = 56;