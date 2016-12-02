
alter table db_p2p.tb_investment_0 add firstTime tinyint not null default 0 COMMENT '是否该用户的首次购买';

alter table db_p2p.tb_investment_0 add brief decimal(10,2) not null default 0.00 COMMENT '投资摘要（显示列表时的数据）' after interestExt;

create table if not exists db_p2p.tb_returnlog_0
(
	sn bigint not null default 0,
	ordersId bigint not null default 0 COMMENT 'ordersId',
	waresId  bigint not null default 0 COMMENT 'waresId',
	assetsId  bigint not null default 0 COMMENT 'assetsId',
	userId	bigint not null default 0 COMMENT 'userId',
	returnedAmount decimal(10,2) not null default 0 COMMENT '偿还本金',
	returnedInterest decimal(10,2) not null default 0 COMMENT '偿还利息',
	returnedYmd int not null default 0 COMMENT '还款日期',
	primary key (sn)
)ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT '还款记录';

insert into db_p2p.tb_config values('dbsql.ver',13) ON DUPLICATE KEY UPDATE v=13;