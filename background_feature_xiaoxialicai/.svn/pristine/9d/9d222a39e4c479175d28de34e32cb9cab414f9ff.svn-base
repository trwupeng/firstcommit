use db_p2p;

ALTER TABLE `tb_config`
MODIFY COLUMN `v`  varchar(256) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `k`;

INSERT INTO db_p2p.tb_config
VALUES
	('dbsql.ver', 68) ON DUPLICATE KEY UPDATE v = 68;