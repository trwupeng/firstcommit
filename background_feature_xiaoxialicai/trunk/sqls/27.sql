-- 创建红包流水表
CREATE TABLE `tb_redpackettally_0` (
  `tallyId` bigint(20) NOT NULL DEFAULT '0' COMMENT 'tallyId',
  `userId` bigint(20) NOT NULL DEFAULT '0' COMMENT 'userId',
  `orderId` bigint(20) NOT NULL DEFAULT '0' COMMENT 'orderId',
  `tallyType` int(11) NOT NULL COMMENT '类型',
  `statusCode` int(11) NOT NULL DEFAULT '0' COMMENT '状态',
  `codeCreate` varchar(128) NOT NULL DEFAULT '' COMMENT '创建流水的代码标示',
  `descCreate` varchar(128) NOT NULL DEFAULT '' COMMENT '用途的用户说明',
  `timeCreate` bigint(20) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `nOld` bigint(20) DEFAULT NULL COMMENT '原余额 单位分',
  `nAdd` bigint(20) DEFAULT NULL COMMENT '增加额（可负） 单位分',
  `nNew` bigint(20) DEFAULT NULL COMMENT '新余额 单位分',
  `iRecordVerID` int(11) NOT NULL COMMENT 'iRecordVerID',
  
  PRIMARY KEY (`tallyId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='红包流水表';

insert into db_p2p.tb_config values('dbsql.ver',27) ON DUPLICATE KEY UPDATE v=27;