use db_p2p;

ALTER TABLE `tb_user_0`
ADD COLUMN `rebate`  int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '返利金额' AFTER `points`;

INSERT INTO db_p2p.tb_config
VALUES
	('dbsql.ver', 66) ON DUPLICATE KEY UPDATE v = 66;