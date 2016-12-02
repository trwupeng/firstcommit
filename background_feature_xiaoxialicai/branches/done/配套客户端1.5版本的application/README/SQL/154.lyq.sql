ALTER TABLE db_oauth.tb_accounts_0
ADD COLUMN `limitStatus`  tinyint(4) NOT NULL DEFAULT 0 COMMENT '限制的状态位：\r\n0不限制；\r\n1冻结；' AFTER `regIP`;

ALTER TABLE db_oauth.tb_accounts_1
ADD COLUMN `limitStatus`  tinyint(4) NOT NULL DEFAULT 0 COMMENT '限制的状态位：\r\n0不限制；\r\n1冻结；' AFTER `regIP`;

insert into db_p2p.tb_config set k='dbsql.ver',v='154-lyq' ON DUPLICATE KEY UPDATE v='154-lyq';