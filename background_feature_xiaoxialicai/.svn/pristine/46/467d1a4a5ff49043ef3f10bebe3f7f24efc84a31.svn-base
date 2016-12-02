USE db_p2p;

ALTER TABLE `tb_dayInterest` RENAME `tb_dayInterest_0`;

ALTER TABLE `tb_dayInterest_0`
ADD COLUMN `sLockData`  varchar(200) NOT NULL DEFAULT '' AFTER `haveLocal`,
ADD COLUMN `iRecordVerID`  int NOT NULL DEFAULT 0 AFTER `sLockData`;

insert into db_p2p.tb_config set k='dbsql.ver',v='135-tgh' ON DUPLICATE KEY UPDATE v='135-tgh';