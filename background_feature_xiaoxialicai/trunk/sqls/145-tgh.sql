USE db_p2p;

INSERT INTO `db_p2p`.`tb_config` (`k`, `v`, `intro`, `iRecordVerID`, `sLockData`, `extlimit`) VALUES ('sendDayInterest', '1', '#储钱罐收益自动发放开关#', '4', '', '');




insert into db_p2p.tb_config set k='dbsql.ver',v='144-tgh' ON DUPLICATE KEY UPDATE v='144-tgh';