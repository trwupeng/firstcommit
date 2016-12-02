USE db_p2p;

CREATE TABLE `tb_rebate_0` (
  `rebateId` bigint(20) NOT NULL DEFAULT '0',
  `userId` bigint(20) NOT NULL DEFAULT '0' COMMENT '邀请人ID',
  `childUserId` bigint(20) NOT NULL DEFAULT '0' COMMENT '受邀人ID',
  `childNickname` varchar(100) NOT NULL DEFAULT '',
  `childPhone` bigint(20) NOT NULL DEFAULT '0',
  `investId` bigint(20) NOT NULL DEFAULT '0' COMMENT '订单号',
  `waresId` bigint(20) NOT NULL DEFAULT '0' COMMENT '标的ID',
  `exp` varchar(255) NOT NULL,
  `amount` bigint(20) NOT NULL,
  `sumAmount` bigint(20) NOT NULL DEFAULT '0' COMMENT '累计返利',
  `type` smallint(6) NOT NULL DEFAULT '0' COMMENT '类型',
  `statusCode` smallint(6) NOT NULL,
  `updateYmd` bigint(20) NOT NULL DEFAULT '0',
  `createYmd` bigint(20) NOT NULL,
  `sn` bigint(20) NOT NULL DEFAULT '0' COMMENT '网关流水',
  `snMsg` varchar(255) NOT NULL DEFAULT '' COMMENT '网关处理情况',
  `iRecordVerID` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`rebateId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='返利表';





insert into tb_config set k='dbsql.ver',v='85-tgh' ON DUPLICATE KEY UPDATE v='85-tgh';