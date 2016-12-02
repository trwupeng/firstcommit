use db_p2p;

CREATE TABLE `tb_trans_0` (
  `transId` bigint(20) NOT NULL DEFAULT '0',
  `fromUid` bigint(20) NOT NULL DEFAULT '0' COMMENT '来自账户',
  `toUid` bigint(20) NOT NULL DEFAULT '0' COMMENT '目标账户',
  `amount` bigint(20) NOT NULL DEFAULT '0' COMMENT '金额',
  `statusCode` int(11) NOT NULL DEFAULT '0' COMMENT '状态',
  `exp` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  `updateUser` varchar(30) NOT NULL DEFAULT '',
  `updateYmd` bigint(20) NOT NULL DEFAULT '0' COMMENT '更新时间',
  `iRecordVerID` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`transId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='手动打款记录表';


insert into db_p2p.tb_config set k='dbsql.ver',v='171.tgh' ON DUPLICATE KEY UPDATE v='171.tgh';