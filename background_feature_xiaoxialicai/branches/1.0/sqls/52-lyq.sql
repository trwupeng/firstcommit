-- - 增加contractData字段
USE `db_oauth`;

ALTER TABLE `tb_accounts_0` ADD COLUMN `contractData` VARCHAR (255) NOT NULL DEFAULT '' COMMENT 'contractData' AFTER `contractId`;

ALTER TABLE `tb_accounts_1` ADD COLUMN `contractData` VARCHAR (255) NOT NULL DEFAULT '' COMMENT 'contractData' AFTER `contractId`;

INSERT INTO db_p2p.tb_config
VALUES
	('dbsql.ver', 52) ON DUPLICATE KEY UPDATE v = 52;