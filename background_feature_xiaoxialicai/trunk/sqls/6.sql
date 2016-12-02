alter table db_p2p.tb_wares_0  add ymdPayReal int not null default 0 COMMENT '实际还款日期';
alter table db_p2p.tb_wares_0  add ymdPayPlan int not null default 0 COMMENT '预计还款日期';

alter table db_p2p.tb_wares_0  add interestStartType int not null default 0 COMMENT '起息方式：0:购买起息，1，购买次日起息，2:募集满起息，3:募集满次日起息';

alter table db_p2p.tb_user_bankcard_0 add orderId bigint not null default 0 FIRST;
alter table db_p2p.tb_user_bankcard_0 add timeCreate bigint not null default 0 after statusCode comment '创建时间';

alter table db_p2p.tb_user_bankcard_0 add idCardType tinyint not null default 0 COMMENT '证件类型';
alter table db_p2p.tb_user_bankcard_0 add idCardSN varchar(32) not null default '' COMMENT '证件号码';
alter table db_p2p.tb_user_bankcard_0 add realName varchar(16) not null default '' COMMENT '姓名';
alter table db_p2p.tb_user_bankcard_0 add phone varchar(16) not null default '' COMMENT '手机号';

alter table db_p2p.tb_user_0 add idCard varchar(32) not null default '' COMMENT '身份证件号码';

alter table db_p2p.tb_investment_0 change vouchers vouchers bigint not null default 0 COMMENT '使用券';

//user 表注册的时候记录phone
insert into db_p2p.tb_config values('dbsql.ver',3) ON DUPLICATE KEY UPDATE v=3;