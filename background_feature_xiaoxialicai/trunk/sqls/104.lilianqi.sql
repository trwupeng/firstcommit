use db_p2p;
alter table `tb_managers_0` add `dept` varchar(20) not null default '' comment '部门' after `nickname`;

DROP TABLE IF EXISTS `tb_managers_rights`;
CREATE TABLE `tb_managers_rights` (
  `loginName` varchar(20) NOT NULL DEFAULT '' COMMENT '管理员',
  `rightsType` varchar(20) NOT NULL DEFAULT '' COMMENT '权限分组',
  `rights` varchar(500) NOT NULL DEFAULT '' COMMENT '理管权限',
  `rptRights` varchar(500) NOT NULL DEFAULT '' COMMENT '报表权限',
  `iRecordVerID` int(11) NOT NULL DEFAULT '0',
  `sLockData` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`loginName`,`rightsType`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='用户权限表';


insert into tb_config set k='dbsql.ver',v='104-lilianqi-db_p2p' ON DUPLICATE KEY UPDATE v='104-lilianqi-db_p2p';