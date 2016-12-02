use db_p2prpt;

DROP TABLE IF EXISTS `tb_copartner_notify`;
create table tb_copartner_notify(
`deviceId` varchar(64) not null comment '设备号',
`copartnerId` int(11) not null default 0 comment '渠道号',
`contractId` bigint(20) not null default 0 comment '协议号',
`appId` varchar(64) not null default '' comment '应用id',
`mac` varchar(64) not null default '' comment 'MAC地址',
`openUDID` varchar(64) not null default '' comment 'openUDID',
`OSVer` varchar(64) not null default '' comment '客户端版本',
`dtInstallNotify` bigint(20) not null default 0 comment '安装通知时间',
`isActivated` tinyint(1) not null default 0 comment '是否激活 0 否 1 是',
`dtActivated` bigint(20) not null default 0 comment '激活时间',
`flagActivatedNotfy` smallint(4) not null default 0 comment '通知合作方激活的结果 0 合作方返回的结果失败  1 成功  -1 调用回调接口失败',
`callback` varchar(1000) not null default '' comment '回调网址',
PRIMARY KEY (`deviceId`)
)ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='渠道安装激活通知';