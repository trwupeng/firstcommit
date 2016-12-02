USE `db_p2p`;

ALTER TABLE `tb_message_0` CHANGE COLUMN `create_time` `createTime` BIGINT (20) UNSIGNED NOT NULL COMMENT '创建时间' AFTER `receiverId`;

ALTER TABLE `tb_message_1` CHANGE COLUMN `create_time` `createTime` BIGINT (20) UNSIGNED NOT NULL COMMENT '创建时间' AFTER `receiverId`;

INSERT INTO db_p2p.tb_config
VALUES
	('dbsql.ver', 59) ON DUPLICATE KEY UPDATE v = 59;