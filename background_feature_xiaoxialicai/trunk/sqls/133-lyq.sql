USE db_p2p;

update `tb_config` set `intro`='#认证红包金额#单位分([有邀请人,没有邀请人])' where (`k`='BIND_FIRST_RED_AMOUNT');
update `tb_config` set `intro`='#首次登录app的红包奖励' where (`k`='FIRSTLOGINAPP_RED_AMOUNT');
update `tb_config` set `intro`='#首次充值的红包金额#单位分([有邀请人,没有邀请人])' where (`k`='CHARGE_FIRST_RED_AMOUNT');

DELETE FROM `tb_config_ram`;

insert into db_p2p.tb_config set k='dbsql.ver',v='133-lyq' ON DUPLICATE KEY UPDATE v='133-lyq';