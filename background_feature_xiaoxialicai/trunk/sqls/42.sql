USE db_p2prpt;
ALTER TABLE db_p2prpt.tb_user_final DROP COLUMN maxBoughtDay;
ALTER TABLE db_p2prpt.tb_user_final DROP COLUMN maxChargeDay;


insert into db_p2p.tb_config values('dbsql.ver',42) ON DUPLICATE KEY UPDATE v=42;