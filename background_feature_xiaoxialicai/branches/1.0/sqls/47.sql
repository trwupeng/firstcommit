ALTER TABLE db_p2p.tb_user_0
MODIFY COLUMN `tradePwd`  varchar(40) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '交易密码' AFTER `nickname`,
MODIFY COLUMN `salt`  char(4) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '盐' AFTER `tradePwd`;

insert into db_p2p.tb_config values('dbsql.ver',47) ON DUPLICATE KEY UPDATE v=47;