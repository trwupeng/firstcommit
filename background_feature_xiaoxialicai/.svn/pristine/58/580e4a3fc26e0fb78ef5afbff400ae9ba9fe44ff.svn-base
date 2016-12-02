use db_p2p;

ALTER TABLE `tb_user_0`
ADD COLUMN `redPacketDtLast`  bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '最后读取红包时间' AFTER `redPacketUsed`;

INSERT INTO db_p2p.tb_config
VALUES
	('dbsql.ver', 71) ON DUPLICATE KEY UPDATE v = 71;