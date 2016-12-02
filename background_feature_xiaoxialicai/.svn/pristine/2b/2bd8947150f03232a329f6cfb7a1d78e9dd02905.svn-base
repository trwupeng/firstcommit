use db_p2p;

USE db_p2p;
ALTER TABLE `tb_user_0`
MODIFY COLUMN `nickname`  varchar(60) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '用户名' AFTER `phone`;

ALTER TABLE `tb_user_bankcard_0`
MODIFY COLUMN `realName`  varchar(60) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '姓名' AFTER `idCardSN`;

INSERT INTO db_p2p.tb_config
VALUES
	('dbsql.ver', 69) ON DUPLICATE KEY UPDATE v = 69;