/*
修正注册逻辑：更改邀请码存储方式[在account表中增加customData字段]
*/
ALTER TABLE `db_oauth`.`tb_accounts_0` ADD COLUMN `customData`  varchar(255) NOT NULL COMMENT '冗余字段' AFTER `phone`;
ALTER TABLE `db_oauth`.`tb_accounts_1` ADD COLUMN `customData`  varchar(255) NOT NULL COMMENT '冗余字段' AFTER `phone`;

insert into db_p2p.tb_config values('dbsql.ver',17) ON DUPLICATE KEY UPDATE v=17;