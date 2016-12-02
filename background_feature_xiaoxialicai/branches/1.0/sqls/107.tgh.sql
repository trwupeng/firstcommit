use db_p2p;

CREATE TABLE `tb_marketing_second_0` (
  `userId` bigint(20) NOT NULL DEFAULT '0',
  `phone` bigint(20) NOT NULL DEFAULT '0',
  `nickname` varchar(60) NOT NULL DEFAULT '',
  `ymdReg` int(11) NOT NULL DEFAULT '0',
  `ymdBindcard` int(11) NOT NULL DEFAULT '0',
  `ymdFirstBuy` int(11) NOT NULL DEFAULT '0',
  `ymdSend` int(11) NOT NULL DEFAULT '0' COMMENT '短信发送日期',
  `statusSend` tinyint(4) NOT NULL DEFAULT '0' COMMENT '短信发送状态',
  `ymdCall` int(11) NOT NULL DEFAULT '0' COMMENT '拨打电话日期',
  `statusCall` tinyint(4) NOT NULL DEFAULT '0' COMMENT '拨打电话状态',
  `redPacket` int(11) NOT NULL DEFAULT '0' COMMENT '激励红包金额 单位分',
  `iRecordVerID` int(11) NOT NULL DEFAULT '0',
  `updateTime` bigint(20) NOT NULL DEFAULT '0',
  `updateUser` varchar(60) NOT NULL DEFAULT '',
  `sLockData` varchar(200) NOT NULL DEFAULT '',
  `exp` varchar(500) NOT NULL DEFAULT '' COMMENT '备注',
  `exp1` varchar(500) NOT NULL DEFAULT '',
  PRIMARY KEY (`userId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='二次营销';


alter table db_p2p.tb_user_1 add ap_RedPacketTimes bigint not null default 0 COMMENT '活跃值—子红包使用次数';
alter table db_p2p.tb_user_0 add ap_RedPacketTimes bigint not null default 0 COMMENT '活跃值—子红包使用次数';
insert into db_p2p.tb_config values('weekScore_RedPacketTimes','{5:5,10:10,15:15,30:20,50:30}','#子红包使用次数得分#格式{达到多少：可获多少积分,达到多少:可获多少积分……}',1,'','');

insert into db_p2p.tb_config set k='dbsql.ver',v='107-tgh' ON DUPLICATE KEY UPDATE v='107-tgh';