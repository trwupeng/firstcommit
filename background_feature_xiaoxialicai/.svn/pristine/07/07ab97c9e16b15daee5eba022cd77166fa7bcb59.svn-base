USE db_p2p;

update db_p2p.tb_user_0 set redPacket=0 where redPacket < 0;

update db_p2p.tb_user_1 set redPacket=0 where redPacket < 0;

insert into db_p2p.tb_config set k='dbsql.ver',v='143-lyq' ON DUPLICATE KEY UPDATE v='143-lyq';