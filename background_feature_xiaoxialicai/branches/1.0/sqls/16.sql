/*
为帐号相关表插入测试数据
*/
INSERT IGNORE INTO `db_oauth`.`tb_loginname_1` (`loginName`, `cameFrom`, `accountId`, `flgStatus`, `iRecordVerID`) VALUES ('18616700069', 'phone', '81568478941117', '1', '2');

INSERT IGNORE INTO `db_oauth`.`tb_accounts_1` (`accountId`, `passwd`, `passwdSalt`, `regYmd`, `regHHiiss`, `regClient`, `regIP`, `dtForbidden`, `loginFailed`, `nickname`, `lastIP`, `lastDt`, `contractId`, `iRecordVerID`, `phone`) VALUES ('81568478941117', '5c350b82390a586c13fa7370377d3a9b', '8506', '20151010', '173354', '902', '58.246.72.93', '0', '1445412580100004500', '18616700069', '192.168.56.140', '1445412913', '0', '64', '18616700069');

INSERT IGNORE INTO `db_p2p`.`tb_user_0` (`userId`, `ymdReg`, `ymdFirstBuy`, `ymdLastBuy`, `ymdFirstCharge`, `ymdBindcard`, `ipReg`, `ipLast`, `dtLast`, `phone`, `nickname`, `wallet`, `points`, `copartnerId`, `contractId`, `inviteByUser`, `inviteByParent`, `inviteByRoot`, `myInviteCode`, `checkinBook`, `iRecordVerID`, `sLockData`, `idCard`) VALUES ('81568478941117', '20151014', '0', '0', '0', '0', '222.44.185.17', '192.168.56.140', '20151022120812', '0', '0*****0', '0', '0', '0', '0', '0', '0', '0', 'tgtkjgh', '', '18', '', '');

INSERT IGNORE INTO `db_p2p`.`tb_invitecodes_0` (`inviteCode`, `userId`) VALUES ('tgtkjgh', '81568478941117');

insert into db_p2p.tb_config values('dbsql.ver',16) ON DUPLICATE KEY UPDATE v=16;