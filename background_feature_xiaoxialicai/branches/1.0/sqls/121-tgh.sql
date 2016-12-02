USE db_p2p;

ALTER TABLE `tb_rebate_1`
ADD COLUMN `sn`  bigint NOT NULL DEFAULT 0 COMMENT '网关流水' AFTER `createYmd`,
ADD COLUMN `snMsg`  varchar(100) NOT NULL DEFAULT '' COMMENT '网关处理信息' AFTER `iRecordVerID`,
ADD COLUMN `sLockData`  varchar(200) NULL DEFAULT '' AFTER `snMsg`;

ALTER TABLE `tb_rebate_0`
ADD COLUMN `sn`  bigint NOT NULL DEFAULT 0 COMMENT '网关流水' AFTER `createYmd`,
ADD COLUMN `snMsg`  varchar(100) NOT NULL DEFAULT '' COMMENT '网关处理信息' AFTER `iRecordVerID`,
ADD COLUMN `sLockData`  varchar(200) NULL DEFAULT '' AFTER `snMsg`;



insert into db_p2p.tb_config set k='dbsql.ver',v='121-tgh' ON DUPLICATE KEY UPDATE v='121-tgh';