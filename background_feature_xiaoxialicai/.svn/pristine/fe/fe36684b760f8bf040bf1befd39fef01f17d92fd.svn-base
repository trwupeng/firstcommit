ALTER TABLE db_p2p.tb_user_0 ADD COLUMN `protocol` VARCHAR (16) NOT NULL COMMENT '注册时的协议版本号' AFTER `contractId`;

insert into db_p2p.tb_config values('dbsql.ver',31) ON DUPLICATE KEY UPDATE v=31;
