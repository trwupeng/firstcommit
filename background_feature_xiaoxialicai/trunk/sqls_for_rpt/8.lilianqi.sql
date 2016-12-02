USE db_p2prpt;
DROP TABLE IF EXISTS `tb_voucher_grant`;
CREATE TABLE `tb_voucher_grant` (
  `taskId` bigint(20) NOT NULL COMMENT 'taskId',
  `phone` bigint(16) NOT NULL DEFAULT '0' COMMENT '手机号',
  `userId` bigint(20) DEFAULT NULL COMMENT '用户id',
  `realname` varchar(36) NOT NULL DEFAULT '' COMMENT '真实姓名',
  `msgType` tinyint(2) NOT NULL DEFAULT 0 COMMENT '1：营销短信 2：通知短信',
  `timeCreate` datetime COMMENT '创建时间',
  `voucherName` varchar(50) NOT NULL DEFAULT '' COMMENT '券类型',
  `flgVoucher` smallint(1) NOT NULL DEFAULT '0' COMMENT '是否发放本金券',
  `flgMsg` smallint(1) NOT NULL DEFAULT '0' COMMENT '是否发送信息',
  `msg` varchar(500) NOT NULL DEFAULT '' COMMENT '短信内容',
  `repeatN` int(11) NOT NULL DEFAULT '1' COMMENT '手机号重复次数',
  `sender` varchar(50) not null default '' comment '发放者',
  PRIMARY KEY (`taskId`,`phone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='券发放表';
insert into tb_rpt_database_ver values ('8.lilianqi', '增加客服券发放表');