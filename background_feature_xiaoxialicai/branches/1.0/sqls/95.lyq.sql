USE db_p2p;

CREATE TABLE `tb_wechat_bind_phone_0` (
  `openId` varchar(50) NOT NULL,
  `phone` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '绑定手机号',
  `userId` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `createTime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updTime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `iRecordVerID` int(11) NOT NULL,
  PRIMARY KEY (`openId`),
  KEY `phone` (`phone`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='微信绑定手机号-不用于SNS';

insert into tb_config set k='dbsql.ver',v='95-lyq' ON DUPLICATE KEY UPDATE v='95-lyq';