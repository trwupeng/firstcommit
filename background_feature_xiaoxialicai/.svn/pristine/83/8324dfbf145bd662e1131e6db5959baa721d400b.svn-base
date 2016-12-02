USE db_p2prpt;

DROP TABLE IF EXISTS `tb_vouchers_final`;
CREATE TABLE `tb_vouchers_final` (
  `voucherId` bigint(20) NOT NULL DEFAULT '0' COMMENT 'vouchersId',
  `userId` bigint(20) NOT NULL DEFAULT '0' COMMENT '用户id',
  `voucherType` varchar(16) NOT NULL DEFAULT '' COMMENT '券类型',
  `amount` int(11) NOT NULL DEFAULT '0' COMMENT '券金额',
  `ymdCreate` int(11) NOT NULL DEFAULT '0' COMMENT '获取时间',
  `ymdUsed` bigint(20) NOT NULL DEFAULT '0' COMMENT '使用时间',
  `orderId` bigint(20) NOT NULL DEFAULT '0' COMMENT '订单id',
  `statusCode` int(11) NOT NULL DEFAULT '0' COMMENT '状态',
  `dtExpired` bigint(20) NOT NULL DEFAULT '0' COMMENT '失效时间',
  PRIMARY KEY (`voucherId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='券总览表';
