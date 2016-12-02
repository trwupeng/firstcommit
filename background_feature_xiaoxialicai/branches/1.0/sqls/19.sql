/*
修改回款计划表字段长度
*/
alter table db_p2p.tb_investment_0 MODIFY returnPlan VARCHAR(2000);
insert into db_p2p.tb_config values('dbsql.ver',19) ON DUPLICATE KEY UPDATE v=19;