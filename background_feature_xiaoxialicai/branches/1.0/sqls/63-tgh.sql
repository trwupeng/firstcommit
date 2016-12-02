USE db_p2p;
ALTER TABLE `tb_wares_0`
MODIFY COLUMN `dlUnit`  varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '月' AFTER `deadLine`,
MODIFY COLUMN `priceStart`  int(11) NOT NULL DEFAULT 0 COMMENT '起投金额 单位分' AFTER `vipLevel`,
MODIFY COLUMN `priceStep`  int(11) NOT NULL DEFAULT 1 COMMENT '递增金额 单位分' AFTER `priceStart`,
MODIFY COLUMN `amount`  bigint(20) NOT NULL DEFAULT 0 COMMENT '集募总额 单位分' AFTER `priceStep`,
MODIFY COLUMN `remain`  bigint(20) NOT NULL DEFAULT 0 COMMENT '剩余额 单位分' AFTER `amount`,
MODIFY COLUMN `yieldStatic`  decimal(10,4) NOT NULL DEFAULT 0.00 COMMENT '定固年化收益率' AFTER `realRaise`,
MODIFY COLUMN `yieldStaticAdd`  decimal(10,4) NOT NULL DEFAULT 0.00 COMMENT '定固年化收益率上浮' AFTER `yieldStatic`,
MODIFY COLUMN `yieldFloatFrom`  decimal(10,4) NOT NULL DEFAULT 0.00 COMMENT '浮动年化收益率下限' AFTER `yieldStaticAdd`,
MODIFY COLUMN `yieldFloatTo`  decimal(10,4) NOT NULL DEFAULT 0.00 COMMENT '浮动年化收益率上限' AFTER `yieldFloatFrom`;


INSERT INTO db_p2p.tb_config
VALUES
	('dbsql.ver', 63) ON DUPLICATE KEY UPDATE v = 63;