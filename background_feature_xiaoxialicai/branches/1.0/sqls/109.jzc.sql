USE db_p2p;
INSERT INTO `tb_config_ram` VALUES ('DayActiveUserNum', '{\"day\":\"20160324\",\"user_num\":3}', '周活跃用户当天领取奖品总数', 1, '', '');

insert into db_p2p.tb_config set k='dbsql.ver',v='109-jzc' ON DUPLICATE KEY UPDATE v='109-jzc';