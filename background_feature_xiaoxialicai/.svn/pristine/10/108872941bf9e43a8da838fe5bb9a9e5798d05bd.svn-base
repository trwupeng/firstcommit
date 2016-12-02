use db_p2p;

INSERT INTO `tb_menu` VALUES ('', '资金', '资金.借款人还款计划', '[\"manage\",\"payback\",\"borrowerPlan\",[],[]]');

update tb_wares_0 set nextConfirmYmd = lastPaybackYmd where statusCode>12 and nextConfirmYmd = 0 and lastPaybackYmd <> 0;

insert into db_p2p.tb_config set k='dbsql.ver',v='175.tgh' ON DUPLICATE KEY UPDATE v='175.tgh;