USE `db_p2p`;
ALTER TABLE `tb_calendar`
ADD COLUMN `planTotalWithdraw`  bigint NOT NULL DEFAULT 0 COMMENT '当日总提现限额' AFTER `workday`,
ADD COLUMN `realTotalWithdraw`  bigint NOT NULL DEFAULT 0 COMMENT '当日实际申请提现总额' AFTER `planTotalWithdraw`,
ADD COLUMN `perWithdraw`  bigint NOT NULL DEFAULT 0 COMMENT '个人当日提现限额' AFTER `realTotalWithdraw`,
ADD COLUMN `iRecordVerID`  int NOT NULL DEFAULT 0 AFTER `perWithdraw`;

ALTER TABLE `tb_recharges_0`
ADD COLUMN `withdrawYmd`  int NOT NULL DEFAULT 0 COMMENT '提现计划到账日期' AFTER `bankCard`;


insert into db_p2p.tb_config values('dbsql.ver',43) ON DUPLICATE KEY UPDATE v=43;