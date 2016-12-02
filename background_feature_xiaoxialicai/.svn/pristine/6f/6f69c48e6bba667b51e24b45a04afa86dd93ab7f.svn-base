use db_p2p;

ALTER TABLE `tb_user_0`
ADD COLUMN `hisReg`  int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '注册时分秒' AFTER `ymdReg`;

INSERT INTO db_p2p.tb_config
VALUES
	('dbsql.ver', 67) ON DUPLICATE KEY UPDATE v = 67;