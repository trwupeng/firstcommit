USE db_p2p;

ALTER TABLE `tb_wares_0`
ADD COLUMN `borrowerId`  bigint NOT NULL DEFAULT 0 COMMENT '借款人ID' AFTER `assetId`;



INSERT INTO db_p2p.tb_config
VALUES
	('dbsql.ver', 77) ON DUPLICATE KEY UPDATE v = 77;