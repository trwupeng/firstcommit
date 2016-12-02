-- -----------------------------------
-- token表增加clientId字段
-- -----------------------------------

ALTER TABLE db_oauth.tb_oauth_token_0 ADD COLUMN `clientId`  varchar(36) NOT NULL COMMENT '客户端ID' AFTER `scope`;
ALTER TABLE db_oauth.tb_oauth_token_1 ADD COLUMN `clientId`  varchar(36) NOT NULL COMMENT '客户端ID' AFTER `scope`;

insert into db_p2p.tb_config values('dbsql.ver',25) ON DUPLICATE KEY UPDATE v=25;