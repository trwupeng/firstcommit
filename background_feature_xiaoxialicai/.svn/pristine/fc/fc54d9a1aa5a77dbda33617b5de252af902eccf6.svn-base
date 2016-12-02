USE db_p2prpt;
DROP TABLE IF EXISTS `tb_vouchers_overdue`;
CREATE TABLE `tb_vouchers_overdue` (
  `userId` bigint(20) NOT NULL COMMENT '用户ID',
  `ymdRemind` int(11) NOT NULL DEFAULT '0' COMMENT '提醒日期',
  `hisRemind` int(11) NOT NULL DEFAULT '0' COMMENT '提醒时间点',
  `ymdExpired` int(11) NOT NULL DEFAULT '0' COMMENT '红包的失效时期',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态：0 记录初建状态， 1 已发送提醒，2 这个时间范围即将过期的红包用户使用了，不需要发送 ',
  PRIMARY KEY (`userId`,`ymdRemind`,`hisRemind`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='过期红包用户表';
insert into tb_rpt_database_ver values ('10.lilianqi', '抓取明天红包过期的用户，到规定时间点进行红包过期提醒');



