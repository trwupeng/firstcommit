alter table db_p2p.tb_user_1 add ap_fetched varchar(500) not null default '[]' COMMENT '活跃值—领取情况';
alter table db_p2p.tb_user_0 add ap_fetched varchar(500) not null default '[]' COMMENT '活跃值—领取情况';


alter table db_p2p.tb_user_1 add ap_Checkin bigint not null default 0 COMMENT '活跃值—签到';
alter table db_p2p.tb_user_0 add ap_Checkin bigint not null default 0 COMMENT '活跃值—签到';
insert into db_p2p.tb_config values('weekScore_Checkin','{1:1,2:2,10:3,100:5}','#周活跃-签到次数得分#格式{达到多少：可获多少积分,达到多少:可获多少积分……}',1,'','');

alter table db_p2p.tb_user_1 add ap_Invited bigint not null default 0 COMMENT '活跃值—邀请用户数';
alter table db_p2p.tb_user_0 add ap_Invited bigint not null default 0 COMMENT '活跃值—邀请用户数';
insert into db_p2p.tb_config values('weekScore_Invited','{1:1,2:2,10:3,100:5}','#周活跃-邀请用户数得分#格式{达到多少：可获多少积分,达到多少:可获多少积分……}',1,'','');

alter table db_p2p.tb_user_1 add ap_InvitedInvest bigint not null default 0 COMMENT '活跃值—邀请用户购买的金额';
alter table db_p2p.tb_user_0 add ap_InvitedInvest bigint not null default 0 COMMENT '活跃值—邀请用户购买的金额';
insert into db_p2p.tb_config values('weekScore_InvitedInvest','{1:1,2:2,10:3,100:5}','#周活跃邀请用户购买的金额得分#格式{达到多少：可获多少积分,达到多少:可获多少积分……}',1,'','');

alter table db_p2p.tb_user_1 add ap_RechargeTimes bigint not null default 0 COMMENT '活跃值—充值次数';
alter table db_p2p.tb_user_0 add ap_RechargeTimes bigint not null default 0 COMMENT '活跃值—充值次数';
insert into db_p2p.tb_config values('weekScore_RechargeTimes','{1:1,2:2,10:3,100:5}','#充值次数得分#格式{达到多少：可获多少积分,达到多少:可获多少积分……}',1,'','');

alter table db_p2p.tb_user_1 add ap_RechargeAmount bigint not null default 0 COMMENT '活跃值—充值金额';
alter table db_p2p.tb_user_0 add ap_RechargeAmount bigint not null default 0 COMMENT '活跃值—充值金额';
insert into db_p2p.tb_config values('weekScore_RechargeAmount','{1:1,2:2,10:3,100:5}','#充值金额得分#格式{达到多少：可获多少积分,达到多少:可获多少积分……}',1,'','');

alter table db_p2p.tb_user_1 add ap_BuyTimes bigint not null default 0 COMMENT '活跃值—购买次数';
alter table db_p2p.tb_user_0 add ap_BuyTimes bigint not null default 0 COMMENT '活跃值—购买次数';
insert into db_p2p.tb_config values('weekScore_BuyTimes','{1:1,2:2,10:3,100:5}','#购买次数得分#格式{达到多少：可获多少积分,达到多少:可获多少积分……}',1,'','');

alter table db_p2p.tb_user_1 add ap_BuyAmount bigint not null default 0 COMMENT '活跃值—购买金额';
alter table db_p2p.tb_user_0 add ap_BuyAmount bigint not null default 0 COMMENT '活跃值—购买金额';
insert into db_p2p.tb_config values('weekScore_BuyAmount','{1:1,2:2,10:3,100:5}','#购买金额得分#格式{达到多少：可获多少积分,达到多少:可获多少积分……}',1,'','');


create table if not exists db_p2p.tb_apFetchLog_ram(
autoid bigint not null default 0 ,
userId bigint not null default 0,
dt int not null default 0 COMMENT '领奖的时间',
score int not null default 0 COMMENT '领奖的分值段',
surname varchar(5) not null default '' COMMENT  '姓',
phone bigint not null default 0 COMMENT '手机号',
itemName varchar(64) not null default '' COMMENT '奖励物品',
itemNum  int  not null default 0 COMMENT '奖励数量',
primary key (autoid)
)ENGINE=Memory DEFAULT CHARSET=utf8;

create table if not exists db_logs.tb_apFetchLog_0(
autoid bigint not null default 0 ,
userId bigint not null default 0,
dt int not null default 0 COMMENT '领奖的时间',
score int not null default 0 COMMENT '领奖的分值段',
surname varchar(5) not null default '' COMMENT  '姓',
phone bigint not null default 0 COMMENT '手机号',
itemName varchar(64) not null default '' COMMENT '奖励物品',
itemNum  int  not null default 0 COMMENT '奖励数量',
iRecordVerID int not null default 0,
sLockData varchar(200) DEFAULT '',
primary key (autoid)
)ENGINE=MyIsam DEFAULT CHARSET=utf8;

create table if not exists db_logs.tb_apFetchLog_1(
autoid bigint not null default 0 ,
userId bigint not null default 0,
dt int not null default 0 COMMENT '领奖的时间',
score int not null default 0 COMMENT '领奖的分值段',
surname varchar(5) not null default '' COMMENT  '姓',
phone bigint not null default 0 COMMENT '手机号',
itemName varchar(64) not null default '' COMMENT '奖励物品',
itemNum  int  not null default 0 COMMENT '奖励数量',
iRecordVerID int not null default 0,
sLockData varchar(200) DEFAULT '',
primary key (autoid)
)ENGINE=MyIsam DEFAULT CHARSET=utf8;

insert into db_p2p.tb_config set k='dbsql.ver',v='103-tgh' ON DUPLICATE KEY UPDATE v='103-tgh';