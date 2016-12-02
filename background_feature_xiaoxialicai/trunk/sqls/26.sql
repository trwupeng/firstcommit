-- -----------------------------------
-- 为重置密码增加两个字段
-- -----------------------------------
ALTER TABLE db_oauth.tb_accounts_0 ADD COLUMN `pwdVer` INT (11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '重置密码次数' AFTER `passwdSalt`,
 ADD COLUMN `deviceId` VARCHAR (256) NOT NULL COMMENT '唯一设备ID-重置密码' AFTER `pwdVer`;

ALTER TABLE db_oauth.tb_accounts_1 ADD COLUMN `pwdVer` INT (11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '重置密码次数' AFTER `passwdSalt`,
 ADD COLUMN `deviceId` VARCHAR (256) NOT NULL COMMENT '唯一设备ID-重置密码' AFTER `pwdVer`;

insert into db_p2p.tb_config values('dbsql.ver',26) ON DUPLICATE KEY UPDATE v=26;