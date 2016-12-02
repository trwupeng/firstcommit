USE db_p2prpt;

DROP TABLE IF EXISTS `tb_rpt_database_ver`;
create table `tb_rpt_database_ver`  (
	`ver_id` varchar(50) NOT NULL COMMENT '是版本号也是主键',
	`intro` varchar(300) NOT NULL DEFAULT '此版本的介绍',
	PRIMARY KEY (`ver_id`)
)ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='报表系统的数据库版本记录';

insert into tb_rpt_database_ver values (
	'1.lilianqi',
	'添加一个报表系统数据库版本记录的表'
);