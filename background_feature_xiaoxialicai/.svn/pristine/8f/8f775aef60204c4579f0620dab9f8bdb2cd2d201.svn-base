USE db_p2p;
-- 周活动人数
insert  into db_p2p.tb_config set k='DayActiveUserNum', v='[]', intro='周常奖励当日领取人数',  iRecordVerID=1,sLockData='', extlimit='' on duplicate  key update  v='[]';
delete from  db_p2p.tb_config_ram where k='DayActiveUserNum';

delete from db_p2p.tb_apFetchLog_ram;



insert into db_p2p.tb_config set k='dbsql.ver',v='115-wn' ON DUPLICATE KEY UPDATE v='115-wn';