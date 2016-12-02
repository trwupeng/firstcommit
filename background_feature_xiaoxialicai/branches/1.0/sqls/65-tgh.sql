use db_p2p;
ALTER TABLE `tb_wares_0`
ADD COLUMN `returnPlan`  varchar(4000) NOT NULL DEFAULT '' COMMENT '还款计划' AFTER `payStatus`;

ALTER TABLE `tb_investment_0`
MODIFY COLUMN `returnPlan`  varchar(4000) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `returnNext`;


INSERT INTO db_p2p.tb_config
VALUES
	('dbsql.ver', 65) ON DUPLICATE KEY UPDATE v = 65;