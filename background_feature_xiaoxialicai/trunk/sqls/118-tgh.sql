USE db_p2p;

INSERT INTO `db_p2p`.`tb_config` (`k`, `v`, `intro`, `iRecordVerID`, `sLockData`, `extlimit`) VALUES ('idcardUnique', '0', '#是否开启身份证唯一性验证#(1:验证 0:不验证)', '1', '', '');



insert into db_p2p.tb_config set k='dbsql.ver',v='118-tgh' ON DUPLICATE KEY UPDATE v='118-tgh';