USE db_p2p;

INSERT INTO `db_p2p`.`tb_config` (`k`, `v`, `intro`, `iRecordVerID`, `sLockData`, `extlimit`) VALUES ('borrower', '{\"71809733564160\":\"汤高航\",\"11315673597674\":\"汤大力\"}', '#借款人列表#{\"ID\":\"NAME\"}', '1', '', '');




insert into db_p2p.tb_config set k='dbsql.ver',v='141-tgh' ON DUPLICATE KEY UPDATE v='141-tgh';