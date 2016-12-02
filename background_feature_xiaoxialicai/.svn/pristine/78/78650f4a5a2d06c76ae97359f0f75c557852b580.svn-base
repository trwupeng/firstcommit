USE `db_p2p`;

ALTER TABLE `tb_recharges_0`
ADD COLUMN `batchId`  bigint NOT NULL DEFAULT 0 COMMENT '批次操作号' AFTER `bankCard`;

ALTER TABLE `tb_recharges_0`
ADD COLUMN `exp`  varchar(100) NOT NULL DEFAULT '' COMMENT '回调说明' AFTER `payTime`;

INSERT INTO db_p2p.tb_config
VALUES
	('dbsql.ver', 61) ON DUPLICATE KEY UPDATE v = 61;