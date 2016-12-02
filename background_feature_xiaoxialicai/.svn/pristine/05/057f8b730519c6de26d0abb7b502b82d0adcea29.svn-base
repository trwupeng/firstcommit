-- - User 增加字段-交易密码
ALTER TABLE db_p2p.tb_user_0 ADD COLUMN `tradePwd` VARCHAR (40) NOT NULL COMMENT '交易密码' AFTER `nickname`,
 ADD COLUMN `salt` CHAR (4) NOT NULL COMMENT '盐' AFTER `tradePwd`;

insert into db_p2p.tb_config values('dbsql.ver',46) ON DUPLICATE KEY UPDATE v=46;