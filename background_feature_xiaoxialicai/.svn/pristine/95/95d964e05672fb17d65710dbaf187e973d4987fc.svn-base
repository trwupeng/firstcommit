use db_p2p;

ALTER TABLE `tb_wallettally_0`
ADD COLUMN `ext`  bigint(20) NOT NULL DEFAULT 0 COMMENT '使用的红包' AFTER `nNew`;

ALTER TABLE `tb_investment_0`
ADD COLUMN `exp`  varchar(100) NOT NULL DEFAULT '' COMMENT '网关处理结果' AFTER `returnPlan`;

INSERT INTO db_p2p.tb_config
VALUES
	('dbsql.ver', 70) ON DUPLICATE KEY UPDATE v = 70;