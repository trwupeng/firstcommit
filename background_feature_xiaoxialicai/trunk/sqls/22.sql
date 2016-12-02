/*
添加 模板字段
*/
ALTER TABLE db_p2p.tb_wares_0 add COLUMN returnTPL VARCHAR(10) not null DEFAULT 'Std01' after viewTPL;
ALTER TABLE db_p2p.tb_vouchers_0 add COLUMN voucherTPL VARCHAR(10) not null DEFAULT 'Std01' after dtExpired;
insert into db_p2p.tb_config values('dbsql.ver',22) ON DUPLICATE KEY UPDATE v=22;
