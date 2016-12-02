USE db_p2prpt;
DROP TABLE IF EXISTS `tb_secondmarket_list`;
CREATE TABLE `tb_secondmarket_list` (
  `ymd` int(11) NOT NULL DEFAULT '0' COMMENT '注册/绑卡日期',
  `hesitateType` tinyint(1) NOT NULL DEFAULT '0' COMMENT '营销类型 0：注册未绑卡 1：绑卡未购买',
  `userId` bigint(20) NOT NULL DEFAULT '0' COMMENT '用户id',
  PRIMARY KEY (`ymd`,`hesitateType`,`userId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='二次营销名单表';



DROP TABLE IF EXISTS `tb_secondmarket`;
CREATE TABLE `tb_secondmarket` (
  `ymd` int(11) NOT NULL DEFAULT '0' COMMENT '发送短信日期或拨打电话日期',
  `his` int(11) NOT NULL DEFAULT '0' COMMENT '发送短信日期或拨打电话日期',
  `userId` bigint(20) NOT NULL DEFAULT '0' COMMENT '用户id',
  `hesitateType` tinyint(1) NOT NULL DEFAULT '0' COMMENT '营销类型 0:注册未绑卡 1:绑卡未购买',
  `marketType` tinyint(1) NOT NULL DEFAULT '0' COMMENT '营销方式 0:短信 1:电话',
  `msg` varchar(500) NOT NULL DEFAULT '' COMMENT '发送的短信内容',
  `callStatus` tinyint(1) NOT NULL DEFAULT '0' COMMENT '拨打电话状态 0:未联系 1:未接通 2:已联系',
  `bonus` varchar(100) NOT NULL DEFAULT '' COMMENT '承诺的鼓励',
  `note` varchar(500) NOT NULL DEFAULT '' COMMENT '备注'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='二次营销表';

insert into tb_rpt_database_ver values ('7.lilianqi', '增加二次营销相关数据库表');