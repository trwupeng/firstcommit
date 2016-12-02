use db_p2p;
ALTER TABLE `tb_wares_0`
ADD COLUMN `exp`  varchar(100) NOT NULL DEFAULT '' COMMENT '备注' AFTER `returnTPL`;

ALTER TABLE `tb_wares_0`
ADD COLUMN `paySn`  bigint(20) NOT NULL DEFAULT 0 COMMENT '网关订单号' AFTER `returnTPL`,
ADD COLUMN `payStatus`  int NOT NULL DEFAULT 0 COMMENT '网关状态' AFTER `paySn`;


INSERT INTO db_p2p.tb_config
VALUES
	('dbsql.ver', 64) ON DUPLICATE KEY UPDATE v = 64;