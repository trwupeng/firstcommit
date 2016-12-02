-- 创建订单数据库
USE db_p2prpt;
DROP TABLE IF EXISTS `tb_order_final`;
CREATE TABLE `tb_orders_final` (
  `ordersId` bigint(20) NOT NULL COMMENT 'ordersId',
  `waresId` bigint(20) NOT NULL DEFAULT '0' COMMENT 'waresId',
  `userId` bigint(20) NOT NULL COMMENT 'userId',
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '实际投资额',
  `interest` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '实际投资额的预定收益',
  `amountExt` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '活动赠送投资额（可取现）',
  `amountFake` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '活动赠送投资额（不可取现）',
  `yieldStaticAdd` decimal(10,2) DEFAULT 0.00 not null COMMENT '定固年化收益率上浮',
  `yieldStatic` decimal(10,2) DEFAULT 0.00 not null COMMENT '定固年化收益率',
  `interestExt` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '动活赠送金额可获得的预定收益',
  `brief` decimal(10,2) not null default 0.00 COMMENT '投资摘要（显示列表时的数据）',
  `extDesc` varchar(256) NOT NULL COMMENT '动活赠送说明',
  -- `orderTime` bigint(20) NOT NULL DEFAULT '0' COMMENT '下单时间',
  `ymd` int(11) DEFAULT NULL,
  `hhiiss` int(11) NOT NULL DEFAULT '0',
  `orderStatus` int(11) NOT NULL DEFAULT '0' COMMENT '单订状态',
  `codeCreate` varchar(128) NOT NULL DEFAULT '' COMMENT '创建流水的代码标示',
  `descCreate` varchar(128) NOT NULL DEFAULT '' COMMENT '用途的用户说明',
  `vouchers` varchar(300) NOT NULL DEFAULT '使用券',
  `firstTime` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否首次购买',
  `returnType` int not null default 0 COMMENT '还款方式：0未定，1：一次定还本付息，2:按月付息，到期还本',
  `returnNext` int not null default 0 COMMENT '下次还款日',
  `returnPlan` varchar(2000)  COMMENT '还款计划',
  PRIMARY KEY (`ordersId`),
  KEY `waresId` (`waresId`),
  KEY `ymdHis` (`ymd`),
  KEY `userId` (`userId`)
)ENGINE=MyISAM DEFAULT CHARSET=utf8;
-- 修改db_p2prpt.tb_user_final regClient字段为clientType 与线上保持一致
ALTER TABLE db_p2prpt.tb_user_final CHANGE `regClient` `clientType` SMALLINT (4) UNSIGNED NOT NULL DEFAULT 0;
insert into db_p2p.tb_config values('dbsql.ver',38) ON DUPLICATE KEY UPDATE v=38;
insert into db_p2p.tb_config values('dbsql.ver',39) ON DUPLICATE KEY UPDATE v=39;