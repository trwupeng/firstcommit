USE db_p2p;

ALTER TABLE `tb_checkin_0`
DROP PRIMARY KEY,
ADD PRIMARY KEY (`userId`, `ymd`);

ALTER TABLE `tb_checkin_1`
DROP PRIMARY KEY,
ADD PRIMARY KEY (`userId`, `ymd`);

insert into tb_config set k='dbsql.ver',v='101-lyq' ON DUPLICATE KEY UPDATE v='101-lyq';