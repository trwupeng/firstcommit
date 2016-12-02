/*
修改amount  支持加息券
*/
alter table db_p2p.tb_vouchers_0 MODIFY amount decimal(10,2);
insert into db_p2p.tb_config values('dbsql.ver',20) ON DUPLICATE KEY UPDATE v=20;