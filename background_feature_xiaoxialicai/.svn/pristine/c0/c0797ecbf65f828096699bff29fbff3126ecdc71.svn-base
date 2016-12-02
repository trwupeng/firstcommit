USE db_p2p;

ALTER TABLE `tb_managers_0`
MODIFY COLUMN `dtForbidden`  varchar(10) CHARACTER SET utf8 NULL DEFAULT 0 COMMENT '禁止登录时间' AFTER `sLockData`,
DEFAULT CHARACTER SET=utf8;


INSERT INTO db_p2p.tb_config
VALUES
	('dbsql.ver', 74) ON DUPLICATE KEY UPDATE v = 74;