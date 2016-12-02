use db_p2p;
ALTER TABLE `tb_investment_0`
MODIFY COLUMN `vouchers`  varchar(500) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '使用券' AFTER `descCreate`;

INSERT INTO db_p2p.tb_config
VALUES
	('dbsql.ver', 53) ON DUPLICATE KEY UPDATE v = 53;