alter table db_p2p.tb_wares_0 add returnType int not null default 0 COMMENT '还款方式：0未定，1：一次定还本付息，2:按月付息，到期还本。。。';
alter table db_p2p.tb_investment_0 add returnType int not null default 0 COMMENT '还款方式：0未定，1：一次定还本付息，2:按月付息，到期还本。。。';
alter table db_p2p.tb_investment_0 add returnNext int not null default 0 COMMENT '下次还款日';
alter table db_p2p.tb_investment_0 add returnPlan varchar(800) COMMENT '还款计划';

insert into db_p2p.tb_config values('dbsql.ver',7) ON DUPLICATE KEY UPDATE v=7;