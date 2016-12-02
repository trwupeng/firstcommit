USE db_p2p;

CREATE TABLE `tb_wechat_userinfo_0` (
  `openid` varchar(50) NOT NULL COMMENT '用户的唯一标识',
  `nickname` varchar(80) DEFAULT '' COMMENT '用户昵称',
  `sex` tinyint(1) unsigned DEFAULT '0' COMMENT '性别：1男；2女；0未知',
  `province` varchar(40) DEFAULT '' COMMENT '用户个人资料填写的省份',
  `city` varchar(40) DEFAULT '' COMMENT '用户个人资料填写的城市',
  `country` varchar(80) DEFAULT '' COMMENT '国家，如中国CN',
  `headimgurl` varchar(255) DEFAULT '' COMMENT '用户头像',
  `privilege` varchar(255) DEFAULT '' COMMENT '用户特权信息，如微信沃卡用户位：chinaunicom',
  `unionid` varchar(80) DEFAULT '' COMMENT 'UnionID机制，需要开放平台',
  PRIMARY KEY (`openid`),
  KEY `unionid` (`unionid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='微信用户信息表';

insert into tb_config set k='dbsql.ver',v='92-lyq' ON DUPLICATE KEY UPDATE v='92-lyq';