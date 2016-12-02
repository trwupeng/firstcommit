USE db_p2p;

INSERT INTO `db_p2p`.`tb_config` (`k`, `v`, `intro`, `iRecordVerID`, `sLockData`, `extlimit`) VALUES ('ORDER_FIRST_RED_FULE_NO_INVITE', '{\"10000\":\"500\",\"50000\":\"3000\",\"500000\":\"6000\",\"1000000\":\"10000\"}', '#普通用户首购红包配置#', '1', '', '');


insert into db_p2p.tb_config set k='dbsql.ver',v='125-tgh' ON DUPLICATE KEY UPDATE v='125-tgh';