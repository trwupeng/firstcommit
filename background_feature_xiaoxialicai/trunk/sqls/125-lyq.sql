USE db_p2p;

UPDATE `tb_config` SET `v`='{\"100_100\":7000,\"200_200\":2000,\"500_500\":1000}' WHERE (`k`='FIRSTLOGINAPP_RED_AMOUNT');

DELETE FROM `tb_config_ram`;

insert into db_p2p.tb_config set k='dbsql.ver',v='125-lyq' ON DUPLICATE KEY UPDATE v='125-lyq';