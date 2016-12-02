use db_p2p;

INSERT INTO `tb_config` (`k`, `v`, `intro`) VALUES ('ITEM_GIVE_SWITCH', '{"NewFirstLoginAppRedPacket":1}', '#用户道具奖励开关#将值设为非1代表关闭，没有则代表打开');

insert into db_p2p.tb_config set k='dbsql.ver',v='174.lyq' ON DUPLICATE KEY UPDATE v='174.lyq';