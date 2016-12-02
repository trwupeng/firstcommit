USE db_p2p;

alter table tb_config add iRecordVerID int not null default 1;
alter table tb_config add sLockData varchar(200) not null default '';

CREATE TABLE `tb_config_ram` (
  `k` varchar(64) NOT NULL,
  `v` varchar(256) NOT NULL,
  `group` varchar(80) NOT NULL DEFAULT '' COMMENT '分组，直接填写组名',
  `desc` varchar(255) NOT NULL COMMENT '描述',
  `iRecordVerID` int(11) NOT NULL DEFAULT '1',
  `sLockData` varchar(200) NOT NULL DEFAULT '',
  PRIMARY KEY (`k`)
) ENGINE=memory DEFAULT CHARSET=utf8 COMMENT='系统配置表(内存)';

insert into tb_config set k='dbsql.ver',v='86-wn' ON DUPLICATE KEY UPDATE v='86-wn';