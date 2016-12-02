-- - 增加推送设置字段
ALTER TABLE db_p2p.tb_user_0 ADD COLUMN `pushSetting` VARCHAR (255) NOT NULL DEFAULT '' COMMENT '推送设置' AFTER `points`;

INSERT INTO db_p2p.tb_config
VALUES
	('dbsql.ver', 51) ON DUPLICATE KEY UPDATE v = 51;