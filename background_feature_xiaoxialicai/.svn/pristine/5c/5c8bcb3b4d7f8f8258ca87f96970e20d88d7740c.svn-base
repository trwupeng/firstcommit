USE db_p2p;
ALTER TABLE `tb_wares_0`
ADD COLUMN `assetId`  bigint NOT NULL DEFAULT 0 COMMENT '来自于哪个资产' AFTER `item`;

ALTER TABLE `tb_investment_0`
MODIFY COLUMN `vouchers`  varchar(400) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '使用券' AFTER `descCreate`;


INSERT INTO db_p2p.tb_config
VALUES
	('dbsql.ver', 76) ON DUPLICATE KEY UPDATE v = 76;