USE db_p2prpt;
DROP TABLE IF EXISTS `tb_activity_spider`;
CREATE TABLE `tb_activity_spider` (
	`ticketSerialNo` varchar(50) NOT NULL COMMENT '电影票序列号',
	`userId` bigint(20) NOT NULL COMMENT '用户userId',
	`realname` varchar(30) NOT NULL DEFAULT '' COMMENT '姓名',
	`phone` bigint(16) NOT NULL DEFAULT '0' COMMENT '手机号',
	`createTime` datetime DEFAULT NULL COMMENT '发放时间',
	`flagGranted` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否已经发放给用户了',
	`flagMsg` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否发短信0 否，1 是',
	PRIMARY KEY (`ticketSerialNo`),
	UNIQUE KEY `user_id` (`userId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='蜘蛛网活动表';
insert into tb_rpt_database_ver values ('9.lilianqi', '小虾蜘蛛活动表');
