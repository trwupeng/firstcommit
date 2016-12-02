/*
	报表系统的数据库表为基本。
	2016年3月21日版本
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for tb_bankcard_final
-- ----------------------------
DROP TABLE IF EXISTS `tb_bankcard_final`;
CREATE TABLE `tb_bankcard_final` (
  `orderId` bigint(20) NOT NULL DEFAULT '0',
  `userId` bigint(20) NOT NULL COMMENT 'userId',
  `bankId` varchar(16) NOT NULL COMMENT '银行简写',
  `bankCard` varchar(20) NOT NULL COMMENT '行卡号银',
  `isDefault` tinyint(4) NOT NULL DEFAULT '0' COMMENT '否是默认卡',
  `statusCode` tinyint(4) NOT NULL DEFAULT '-1' COMMENT '状态：-1:待验证，0：禁用；1正常使用',
  `createYmd` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间Ymd',
  `createHis` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间His',
  `resultMsg` varchar(200) NOT NULL DEFAULT '' COMMENT '验证结果描述',
  `resultYmd` int(11) NOT NULL DEFAULT '0' COMMENT '结果更新时间Ymd',
  `resultHis` int(11) NOT NULL DEFAULT '0' COMMENT '结果更新时间His',
  `idCardType` tinyint(4) NOT NULL DEFAULT '0' COMMENT '证件类型',
  `idCardSN` varchar(32) NOT NULL DEFAULT '' COMMENT '证件号码',
  `realname` varchar(16) NOT NULL DEFAULT '' COMMENT '姓名',
  `phone` varchar(16) NOT NULL DEFAULT '' COMMENT '手机号',
  `cardId` varchar(32) DEFAULT '' COMMENT '绑卡以后产生唯一标识',
  PRIMARY KEY (`orderId`),
  KEY `userId` (`userId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='用户绑定的银行卡';

-- ----------------------------
-- Table structure for tb_copartner_worth
-- ----------------------------
DROP TABLE IF EXISTS `tb_copartner_worth`;
CREATE TABLE `tb_copartner_worth` (
  `ymd` int(11) NOT NULL,
  `clientType` smallint(4) NOT NULL DEFAULT '0',
  `copartnerId` int(11) NOT NULL DEFAULT '0',
  `contractId` bigint(20) NOT NULL DEFAULT '0',
  `promotionWay` varchar(15) NOT NULL DEFAULT '',
  `shelfId` smallint(6) NOT NULL DEFAULT '0',
  `act` varchar(32) NOT NULL DEFAULT '',
  `n` bigint(255) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ymd`,`clientType`,`contractId`,`promotionWay`,`shelfId`,`act`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='?????';

-- ----------------------------
-- Table structure for tb_evtdaily
-- ----------------------------
DROP TABLE IF EXISTS `tb_evtdaily`;
CREATE TABLE `tb_evtdaily` (
  `ymd` int(255) NOT NULL COMMENT '年月日',
  `act` varchar(32) NOT NULL COMMENT '计统项',
  `clienttype` int(16) NOT NULL DEFAULT '0' COMMENT '客户端类型',
  `flgext01` bigint(255) NOT NULL DEFAULT '0' COMMENT '扩展标志位',
  `copartnerId` int(16) NOT NULL COMMENT '推广渠道',
  `contractid` bigint(255) NOT NULL DEFAULT '0' COMMENT '推广协议',
  `n` bigint(255) NOT NULL DEFAULT '0',
  `flgext02` bigint(20) NOT NULL DEFAULT '0' COMMENT '扩展标志位',
  PRIMARY KEY (`ymd`,`act`,`clienttype`,`flgext01`,`copartnerId`,`flgext02`),
  KEY `byCopartner` (`ymd`,`act`,`copartnerId`,`flgext01`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='日报数据';

-- ----------------------------
-- Table structure for tb_orders_final
-- ----------------------------
DROP TABLE IF EXISTS `tb_orders_final`;
CREATE TABLE `tb_orders_final` (
  `ordersId` bigint(20) NOT NULL COMMENT 'ordersId',
  `waresId` bigint(20) NOT NULL DEFAULT '0' COMMENT 'waresId',
  `waresName` varchar(128) NOT NULL DEFAULT '' COMMENT '标的名称',
  `shelfId` smallint(6) NOT NULL DEFAULT '0' COMMENT '类型Id',
  `userId` bigint(20) NOT NULL COMMENT 'userId',
  `realname` varchar(36) NOT NULL DEFAULT '' COMMENT '姓名',
  `amount` bigint(20) NOT NULL DEFAULT '0' COMMENT '实际投资额 单位分',
  `amountExt` int(11) NOT NULL DEFAULT '0' COMMENT '活动赠送投资额（可取现） 单位分',
  `amountFake` int(11) NOT NULL DEFAULT '0' COMMENT '活动赠送投资额（不可取现） 单位分',
  `yieldStaticAdd` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '定固年化收益率上浮',
  `yieldStatic` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '定固年化收益率',
  `yieldExt` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '加息券加息',
  `interest` int(11) NOT NULL DEFAULT '0' COMMENT '总收益 单位分',
  `interestStatic` int(11) NOT NULL DEFAULT '0' COMMENT '固定收益',
  `interestAdd` int(11) NOT NULL DEFAULT '0' COMMENT '活动收益',
  `interestFloat` int(11) NOT NULL DEFAULT '0' COMMENT '浮动收益',
  `interestExt` int(11) NOT NULL DEFAULT '0' COMMENT '加息券收益 单位分',
  `interestSub` int(11) NOT NULL DEFAULT '0' COMMENT '平台贴息',
  `returnAmount` bigint(20) NOT NULL DEFAULT '0' COMMENT '累计返还本金',
  `returnInterest` int(10) NOT NULL DEFAULT '0' COMMENT '累计返还利息',
  `ymd` int(11) DEFAULT NULL,
  `hhiiss` int(11) NOT NULL DEFAULT '0',
  `ymdTrans` int(11) NOT NULL DEFAULT '0' COMMENT '转账扣款时间',
  `hisTrans` int(11) NOT NULL DEFAULT '0' COMMENT '转账扣款时间',
  `orderStatus` int(11) NOT NULL DEFAULT '0' COMMENT '订单状态',
  `mainType` smallint(6) NOT NULL DEFAULT '0' COMMENT '大类',
  `subType` smallint(6) NOT NULL DEFAULT '0' COMMENT '小类',
  `vouchers` varchar(400) NOT NULL DEFAULT '' COMMENT '使用券',
  `firstTimeInAll` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否是该用户的首次购买',
  `returnType` int(11) NOT NULL DEFAULT '0' COMMENT '还款方式：0未定，1：一次定还本付息，2:按月付息，到期还本。。。',
  `lastReturnFundYmd` int(11) NOT NULL DEFAULT '0' COMMENT '上次付息日',
  `returnNext` int(11) NOT NULL DEFAULT '0' COMMENT '下次还款日',
  PRIMARY KEY (`ordersId`),
  KEY `waresId` (`waresId`),
  KEY `ymdHis` (`ymd`),
  KEY `userId` (`userId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='订单表';

-- ----------------------------
-- Table structure for tb_products_final
-- ----------------------------
DROP TABLE IF EXISTS `tb_products_final`;
CREATE TABLE `tb_products_final` (
  `waresId` bigint(20) unsigned NOT NULL COMMENT 'waresId',
  `waresName` varchar(128) NOT NULL COMMENT '标的名称',
  `waresSN` int(11) NOT NULL DEFAULT '0' COMMENT '期数',
  `deadLine` smallint(6) NOT NULL DEFAULT '360' COMMENT '期限',
  `dlUnit` varchar(10) DEFAULT '天' COMMENT '期限单位',
  `tags` varchar(128) NOT NULL DEFAULT '' COMMENT '标签英文逗号分隔',
  `mainType` smallint(6) NOT NULL DEFAULT '0' COMMENT '大类',
  `subType` smallint(6) NOT NULL DEFAULT '0' COMMENT '小类',
  `userLimit` varchar(128) NOT NULL DEFAULT '0' COMMENT '限制可购买的用户类型，英文逗号隔开  0：无限制',
  `vipLevel` smallint(6) NOT NULL DEFAULT '0' COMMENT '限制购买的vip等级',
  `priceStart` int(11) DEFAULT NULL COMMENT '起投金额 单位分',
  `priceStep` int(11) DEFAULT NULL COMMENT '递增金额 单位分',
  `amount` bigint(20) DEFAULT NULL COMMENT '集募总额 单位分',
  `remain` bigint(20) NOT NULL DEFAULT '0' COMMENT '剩余额 单位分',
  `realRaise` bigint(20) NOT NULL DEFAULT '0' COMMENT '实际募集总额',
  `yieldStatic` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '定固年化收益率',
  `yieldStaticAdd` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '定固年化收益率上浮',
  `yieldFloatFrom` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '浮动年化收益率下限',
  `yieldFloatTo` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '浮动年化收益率上限',
  `shelfId` smallint(4) NOT NULL DEFAULT '0' COMMENT '类型',
  `statusCode` tinyint(4) NOT NULL DEFAULT '0' COMMENT '状态',
  `returnType` int(11) NOT NULL DEFAULT '0' COMMENT '还款方式：0未定，1：一次定还本付息，2:按月付息，到期还本。。。',
  `interestStartType` int(11) NOT NULL DEFAULT '0' COMMENT '起息方式：0:购买起息，1，购买次日起息，2:募集满起息，3:募集满次日起息',
  `ymdStartPlan` int(11) NOT NULL DEFAULT '0' COMMENT '计划上架时间',
  `ymdEndPlan` bigint(20) NOT NULL DEFAULT '0' COMMENT '闭关募集结束时间',
  `ymdStartReal` bigint(20) NOT NULL DEFAULT '0' COMMENT '际实上架时间',
  `ymdEndReal` bigint(11) NOT NULL DEFAULT '0' COMMENT '实际募集结束时间',
  `ymdPayReal` int(11) NOT NULL DEFAULT '0' COMMENT '实际还款日期',
  `ymdPayPlan` int(11) NOT NULL DEFAULT '0' COMMENT '预计还款日期',
  `payGift` bigint(20) NOT NULL DEFAULT '0' COMMENT '平台垫付',
  `repay` bigint(20) NOT NULL DEFAULT '0' COMMENT '企业还钱',
  `payYmd` bigint(20) NOT NULL DEFAULT '0' COMMENT '转账时间',
  `borrowerId` bigint(20) NOT NULL DEFAULT '0' COMMENT '借款人ID',
  PRIMARY KEY (`waresId`),
  KEY `statusCode` (`statusCode`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='标的';

-- ----------------------------
-- Table structure for tb_recharges_final
-- ----------------------------
DROP TABLE IF EXISTS `tb_recharges_final`;
CREATE TABLE `tb_recharges_final` (
  `ordersId` varchar(50) NOT NULL DEFAULT '' COMMENT 'ordersId 交易流水号  trade_no',
  `userId` varchar(50) NOT NULL COMMENT '客户userId   customer_id',
  `amount` bigint(64) DEFAULT '0' COMMENT '交易金额(单位，分)   喵叽的是小数类型 amount',
  `poundage` bigint(20) DEFAULT '0' COMMENT '手续费(单位，分) user_fee',
  `ymd` int(11) NOT NULL DEFAULT '0' COMMENT 'create_time',
  `hhiiss` int(11) NOT NULL DEFAULT '0' COMMENT 'create_time',
  `summary` varchar(1000) DEFAULT NULL COMMENT '摘要充值还是扣款等 summary',
  `bankCard` varchar(20) NOT NULL DEFAULT '' COMMENT '银行卡号 card_id',
  `balance` bigint(32) DEFAULT '0' COMMENT '当前余额(单位，分)',
  `couponId` varchar(50) DEFAULT NULL COMMENT '优惠券id coupon_id',
  `payMethod` varchar(50) DEFAULT NULL COMMENT '支付方式 在线支付还是绑卡支付 pay_method',
  `flag` int(10) DEFAULT '1' COMMENT '0老数据1新数据， 只是划分老旧数据的时间点',
  `orderStatus` int(11) NOT NULL DEFAULT '0' COMMENT '单订状态',
  `finishYmd` int(11) NOT NULL DEFAULT '0' COMMENT '定单最终完成日期',
  PRIMARY KEY (`ordersId`),
  KEY `index_userId` (`userId`),
  KEY `index_ymd` (`ymd`),
  KEY `index_hhiiss` (`hhiiss`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for tb_user_final
-- ----------------------------
DROP TABLE IF EXISTS `tb_user_final`;
CREATE TABLE `tb_user_final` (
  `userId` bigint(20) NOT NULL DEFAULT '0' COMMENT 'userId',
  `phone` bigint(16) NOT NULL DEFAULT '0' COMMENT '默认手机号',
  `nickname` varchar(36) NOT NULL DEFAULT '' COMMENT '用户名',
  `realname` varchar(36) NOT NULL DEFAULT '' COMMENT '真实姓名',
  `ymdReg` int(11) NOT NULL DEFAULT '0' COMMENT '注册年月日',
  `hisReg` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '注册时分秒',
  `gender` char(1) NOT NULL DEFAULT '-',
  `ymdBirthday` int(11) NOT NULL DEFAULT '0',
  `idCard` varchar(32) NOT NULL DEFAULT '',
  `dtLast` bigint(20) NOT NULL DEFAULT '0' COMMENT '最后登入的时间',
  `isBorrower` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是不是借款人',
  `flagUser` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否超级用户  1是 0不是',
  `vipLevel` tinyint(4) NOT NULL DEFAULT '0' COMMENT '会员等级',
  `clientType` smallint(4) unsigned NOT NULL DEFAULT '0',
  `copartnerId` int(11) NOT NULL DEFAULT '0' COMMENT '推广渠道id',
  `contractId` bigint(20) NOT NULL DEFAULT '0' COMMENT '推广协议Id',
  `promotionWay` varchar(15) NOT NULL DEFAULT '',
  `wallet` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '钱包余额',
  `points` int(11) NOT NULL DEFAULT '0' COMMENT '商城积分',
  `redPacket` int(11) NOT NULL DEFAULT '0' COMMENT '红包余额 单位分',
  `redPacketUsed` int(11) unsigned DEFAULT '0' COMMENT '????????',
  `ymdBindcard` int(11) NOT NULL DEFAULT '0' COMMENT '首次绑卡日期',
  `ymdRealnameAuth` int(11) NOT NULL DEFAULT '0' COMMENT '实名认证日期',
  `ymdFirstBuy` int(11) NOT NULL DEFAULT '0' COMMENT '首次成功下单',
  `shelfIdFirstBuy` smallint(6) NOT NULL DEFAULT '0' COMMENT '类型Id，产品分类',
  `amountFirstBuy` bigint(20) NOT NULL DEFAULT '0' COMMENT '第一次购买金额 单位分',
  `ymdSecBuy` int(11) NOT NULL DEFAULT '0',
  `shelfIdSecBuy` smallint(6) NOT NULL DEFAULT '0' COMMENT '类型Id，产品分类',
  `amountSecBuy` bigint(20) NOT NULL DEFAULT '0' COMMENT '第二次购买金额 单位分',
  `ymdLastBuy` int(11) NOT NULL DEFAULT '0' COMMENT '最后一次功成下单',
  `shelfIdLastBuy` smallint(6) NOT NULL DEFAULT '0' COMMENT '类型Id，产品分类',
  `amountLastBuy` bigint(20) NOT NULL DEFAULT '0' COMMENT '最后一次购买金额 单位分',
  `ymdMaxBuy` int(11) NOT NULL DEFAULT '0' COMMENT '购买最多的日期',
  `shelfIdMaxBuy` smallint(6) NOT NULL DEFAULT '0' COMMENT '类型Id，产品分类',
  `amountMaxBuy` bigint(20) NOT NULL DEFAULT '0' COMMENT '最大购买金额 单位分',
  `ymdFirstRecharge` int(11) NOT NULL DEFAULT '0' COMMENT '首次充值',
  `amountFirstRecharge` bigint(20) NOT NULL DEFAULT '0' COMMENT '第一次充值金额 单位分',
  `ymdSecRecharge` int(11) NOT NULL DEFAULT '0',
  `amountSecRecharge` bigint(20) NOT NULL DEFAULT '0' COMMENT '第二次充值金额 单位分',
  `ymdLastRecharge` int(11) NOT NULL DEFAULT '0' COMMENT '最后成功充值日期',
  `amountLastRecharge` bigint(20) NOT NULL DEFAULT '0' COMMENT '最后一次充值金额 单位分',
  `ymdMaxRecharge` bigint(20) NOT NULL DEFAULT '0' COMMENT '最大充值日期',
  `amountMaxRecharge` bigint(20) NOT NULL DEFAULT '0' COMMENT '最大充值金额 单位分',
  `inviteByUser` bigint(12) NOT NULL DEFAULT '0' COMMENT '邀请人',
  `inviteByParent` bigint(20) NOT NULL DEFAULT '0' COMMENT '邀请人的邀请人',
  `inviteByRoot` bigint(20) NOT NULL DEFAULT '0' COMMENT '根邀请人',
  `myInviteCode` varchar(12) NOT NULL DEFAULT '' COMMENT '我的邀请码',
  `checkinBook` varchar(1000) NOT NULL DEFAULT '' COMMENT '签到簿',
  `interestTotal` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`userId`),
  KEY `contract_reg` (`contractId`,`ymdReg`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='用户表';

-- ----------------------------
-- Table structure for tb_voucher_grant
-- ----------------------------
DROP TABLE IF EXISTS `tb_voucher_grant`;
CREATE TABLE `tb_voucher_grant` (
  `taskId` bigint(20) NOT NULL COMMENT '任务Id',
  `phone` bigint(16) NOT NULL DEFAULT '0' COMMENT '默认手机号',
  `userId` bigint(20) DEFAULT NULL COMMENT '发给哪个用户',
  `realname` varchar(36) NOT NULL DEFAULT '' COMMENT '姓名',
  `voucherType` varchar(16) NOT NULL DEFAULT '' COMMENT '券类型',
  `shelfId` varchar(20) NOT NULL DEFAULT '0' COMMENT '类型Id',
  `amount` int(11) NOT NULL DEFAULT '0' COMMENT '券金额 单位分/加息 单位%',
  `minAmount` int(11) NOT NULL DEFAULT '0' COMMENT '起投金额',
  `timeCreate` datetime DEFAULT NULL COMMENT '发放时间',
  `timeStart` datetime DEFAULT NULL COMMENT '有效起始时间',
  `timeEnd` datetime DEFAULT NULL COMMENT '有效结束时间',
  `daysExpire` smallint(6) NOT NULL DEFAULT '0' COMMENT '有效天数',
  `descCreate` varchar(128) NOT NULL DEFAULT '' COMMENT '券发放原因',
  `flgVoucher` smallint(1) NOT NULL DEFAULT '0' COMMENT '券是否发送成功',
  `flgMsg` smallint(1) NOT NULL DEFAULT '0' COMMENT '短信是否发送成功',
  `msg` varchar(500) NOT NULL DEFAULT '' COMMENT '短信内容',
  `repeatN` int(11) NOT NULL DEFAULT '1' COMMENT '此次发放号码出现次数',
  PRIMARY KEY (`taskId`,`phone`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='券手动发放记录表';

