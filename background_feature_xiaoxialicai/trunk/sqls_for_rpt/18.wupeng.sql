USE db_p2prpt;
CREATE TABLE `tb_activeconfig` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `code` varchar(36) NOT NULL DEFAULT '' COMMENT 'code',
  `groupCode` varchar(36) NOT NULL DEFAULT '' COMMENT '分组Code',
  `value` varchar(36) NOT NULL DEFAULT '' COMMENT '值',
  `des` text COMMENT '描述',
  PRIMARY KEY (`id`,`code`,`groupCode`,`value`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8 COMMENT='配置表';

insert into tb_rpt_database_ver values ('18.wupeng', '活动配置表');