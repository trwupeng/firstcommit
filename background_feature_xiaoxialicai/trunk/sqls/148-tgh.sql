USE db_p2p;


CREATE TABLE if not exists `tb_user_change_log` (
  `operator` varchar(20) NOT NULL DEFAULT '' COMMENT '操作人',
  `updateTime` bigint(20) NOT NULL DEFAULT '0' COMMENT '更新时间',
  `evt` varchar(20) NOT NULL DEFAULT '' COMMENT '事件标识',
  `data` varchar(500) NOT NULL DEFAULT '' COMMENT '事件详情',
  `phone` bigint(11) NOT NULL DEFAULT '0' COMMENT '涉及用户手机号'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='用户变更记录';





insert into db_p2p.tb_config set k='dbsql.ver',v='148-tgh' ON DUPLICATE KEY UPDATE v='148-tgh';