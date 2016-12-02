ALTER TABLE db_p2p.tb_checkin_0 DROP PRIMARY KEY,
 ADD PRIMARY KEY (`userId`, `ymd`);

insert into db_p2p.tb_config values('dbsql.ver',30) ON DUPLICATE KEY UPDATE v=30;
