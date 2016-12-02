use db_p2p;
DROP TABLE IF EXISTS `tb_wechat_bind_phone_0`;
CREATE TABLE `tb_wechat_bind_phone_0` (
  `userId` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `createTime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `openId` varchar(50) NOT NULL DEFAULT '' COMMENT '微信用户唯一标识',
  `phone` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '绑定手机号',
  `updTime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `iRecordVerID` int(11) NOT NULL,
  PRIMARY KEY (`userId`,`createTime`),
  KEY `phone` (`phone`),
  KEY `openId` (`openId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='微信绑定手机号-不用于SNS';


DROP TABLE IF EXISTS `tb_wechat_bind_phone_1`;
CREATE TABLE `tb_wechat_bind_phone_1` (
  `userId` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `createTime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `openId` varchar(50) NOT NULL DEFAULT '' COMMENT '微信用户唯一标识',
  `phone` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '绑定手机号',
  `updTime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `iRecordVerID` int(11) NOT NULL,
  PRIMARY KEY (`userId`,`createTime`),
  KEY `phone` (`phone`),
  KEY `openId` (`openId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='微信绑定手机号-不用于SNS';

insert into db_p2p.tb_config set k='dbsql.ver',v='165-lyq' ON DUPLICATE KEY UPDATE v='165-lyq';