-- User表增加clientType字段
ALTER TABLE db_p2p.tb_user_0 ADD COLUMN `clientType` SMALLINT (4) NOT NULL COMMENT '客户端类型' AFTER `copartnerId`;
insert into db_p2p.tb_config values('dbsql.ver',35) ON DUPLICATE KEY UPDATE v=35;