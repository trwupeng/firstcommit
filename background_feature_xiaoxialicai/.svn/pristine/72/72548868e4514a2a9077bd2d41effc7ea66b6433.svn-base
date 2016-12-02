USE db_p2p;
ALTER TABLE `tb_wares_0`
ADD COLUMN `item`  varchar(60) NOT NULL DEFAULT '' COMMENT '基金' AFTER `exp`;

ALTER TABLE `tb_user_0`
ADD COLUMN `isBorrower`  tinyint NOT NULL DEFAULT 0 COMMENT '是不是借款人' AFTER `checkinBook`;


INSERT INTO db_p2p.tb_config
VALUES
	('dbsql.ver', 73) ON DUPLICATE KEY UPDATE v = 73;