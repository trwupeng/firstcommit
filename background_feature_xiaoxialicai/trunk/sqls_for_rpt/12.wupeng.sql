USE db_p2prpt;

CREATE TABLE `tb_apiaccounts` (
  `utype` varchar(36) NOT NULL DEFAULT '' COMMENT '类型',
  `u` varchar(36) NOT NULL DEFAULT '' COMMENT '用户名',
  `pass` varchar(36) NOT NULL DEFAULT '' COMMENT '密码',
  `token` varchar(36) DEFAULT NULL COMMENT 'token值',
  `expired` varchar(255) DEFAULT NULL COMMENT '过期时间',
  `ips` varchar(255) DEFAULT NULL COMMENT 'ip白名单',
  `iplast` varchar(16) DEFAULT NULL COMMENT '上次登录ip地'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='网贷之家字段验证表';

insert into tb_rpt_database_ver values ('12.wupeng.sql', '网贷之家字段验证表');
