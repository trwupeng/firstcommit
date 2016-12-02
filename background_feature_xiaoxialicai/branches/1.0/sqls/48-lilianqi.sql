USE db_p2prpt;
ALTER TABLE db_p2prpt.tb_user_final ADD COLUMN ymdSecBuy int(11) NOT NULL DEFAULT '0' AFTER numFirstBuy;
ALTER TABLE db_p2prpt.tb_user_final ADD COLUMN numSecBuy int(11) NOT NULL DEFAULT '0' AFTER ymdSecBuy;
ALTER TABLE db_p2prpt.tb_user_final ADD COLUMN ymdSecCharge int(11) NOT NULL DEFAULT '0' AFTER numFirstCharge;
ALTER TABLE db_p2prpt.tb_user_final ADD COLUMN numSecCharge int(11) NOT NULL DEFAULT '0' AFTER ymdSecCharge;
ALTER TABLE db_p2prpt.tb_user_final ADD COLUMN idCard varchar(32) NOT NULL DEFAULT '' AFTER realName;
ALTER TABLE db_p2prpt.tb_user_final ADD COLUMN ymdBirthday int(11)  NOT NULL DEFAULT '0' AFTER realName;
ALTER TABLE db_p2prpt.tb_user_final ADD COLUMN gender char(1)  NOT NULL DEFAULT '-' AFTER realName;
ALTER TABLE db_p2prpt.tb_user_final ADD COLUMN  promotionWay varchar(15) NOT NULL DEFAULT '' AFTER contractId,

ALTER TABLE `tb_orders_final`
ADD COLUMN `returnAmount`  bigint(20) NULL DEFAULT 0 COMMENT '累计返还本金' AFTER `interestExt`,
ADD COLUMN `returnInterest`  int(10) NULL DEFAULT 0 COMMENT '累计返还利息' AFTER `returnAmount`;
ALTER TABLE `tb_orders_final`
MODIFY COLUMN `returnPlan`  varchar(2000) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `returnNext`;

ALTER TABLE `tb_orders_final`
ADD COLUMN `yieldExt`  decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT '加息券加息' AFTER `yieldStatic`;

ALTER TABLE `tb_orders_final`
ADD COLUMN `waresName`  varchar(128) NOT NULL DEFAULT '' COMMENT '标的名称' AFTER `waresId`;

ALTER TABLE `tb_orders_final`
ADD COLUMN `nickname`  varchar(36) NOT NULL DEFAULT '' COMMENT '姓名' AFTER `userId`;

ALTER TABLE `tb_orders_final`
MODIFY COLUMN `interest`  int(11) NOT NULL DEFAULT 0 COMMENT '总收益 单位分' AFTER `yieldExt`,
MODIFY COLUMN `interestExt`  int(11) NOT NULL DEFAULT 0 COMMENT '加息券收益 单位分' AFTER `interest`,
MODIFY COLUMN `returnAmount`  bigint(20) NOT NULL DEFAULT 0 COMMENT '累计返还本金' AFTER `interestExt`,
MODIFY COLUMN `returnInterest`  int(10) NOT NULL DEFAULT 0 COMMENT '累计返还利息' AFTER `returnAmount`,
ADD COLUMN `interestStatic`  int(11) NOT NULL DEFAULT 0 COMMENT '固定收益' AFTER `interest`,
ADD COLUMN `interestAdd`  int(11) NOT NULL DEFAULT 0 COMMENT '活动收益' AFTER `interestStatic`,
ADD COLUMN `interestFloat`  int(11) NOT NULL DEFAULT 0 COMMENT '浮动收益' AFTER `interestAdd`,
ADD COLUMN `interestSub`  int(11) NOT NULL DEFAULT 0 COMMENT '平台贴息' AFTER `interestExt`;
ADD COLUMN `shelfId`  smallint(6) NOT NULL DEFAULT 0 COMMENT '类型Id' AFTER `waresName`;



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
  `resultTime` bigint(20) NOT NULL DEFAULT '0' COMMENT '更新验证状态的时间',
  `resultYmd` int(11) NOT NULL DEFAULT '0' COMMENT '结果更新时间Ymd',
  `resultHis` int(11) NOT NULL DEFAULT '0' COMMENT '结果更新时间His',
  `idCardType` tinyint(4) NOT NULL DEFAULT '0' COMMENT '证件类型',
  `idCardSN` varchar(32) NOT NULL DEFAULT '' COMMENT '证件号码',
  `realName` varchar(16) NOT NULL DEFAULT '' COMMENT '姓名',
  `phone` varchar(16) NOT NULL DEFAULT '' COMMENT '手机号',
  `cardId` varchar(32) DEFAULT '' COMMENT '绑卡以后产生唯一标识',
  PRIMARY KEY (`orderId`),
  KEY `userId` (`userId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='用户绑定的银行卡';

DROP TABLE IF EXISTS `tb_copartner_worth`;
CREATE TABLE `tb_copartner_worth` (
  `ymd` int(11) NOT NULL,
  `clientType` smallint(4) NOT NULL DEFAULT 0,
  `copartnerId` int(11) NOT NULL DEFAULT 0,
  `contractId` bigint(20) NOT NULL DEFAULT 0,
  `promotionWay` varchar(15) NOT NULL DEFAULT '',
  `shelfId` smallint(6) NOT NULL DEFAULT 0,
  `act` varchar(32) NOT NULL DEFAULT '',
  `n` bigint(255) NOT NULL DEFAULT 0,
 PRIMARY KEY (`ymd`,`clienttype`,`contractid`,`promotionWay`,`shelfId`,`act`)
)ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='渠道导入量';


use db_p2prpt;
ALTER TABLE `tb_orders_final`
MODIFY COLUMN `amount`  bigint(20) NOT NULL DEFAULT 0 COMMENT '实际投资额 单位分' AFTER `nickname`,
MODIFY COLUMN `amountExt`  int(11) NOT NULL DEFAULT 0 COMMENT '活动赠送投资额（可取现） 单位分' AFTER `amount`,
MODIFY COLUMN `amountFake`  int(11) NOT NULL DEFAULT 0 COMMENT '活动赠送投资额（不可取现） 单位分' AFTER `amountExt`,
MODIFY COLUMN `vouchers`  varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '使用券' AFTER `descCreate`;

ALTER TABLE `tb_user_final`
MODIFY COLUMN `numFirstBuy` bigint(20) NOT NULL DEFAULT 0 COMMENT '第一次购买金额 单位分',
MODIFY COLUMN `numSecBuy` bigint(20) NOT NULL DEFAULT 0 COMMENT '第二次购买金额 单位分',
MODIFY COLUMN `numLastBuy` bigint(20) NOT NULL DEFAULT 0 COMMENT '最后一次购买金额 单位分',
MODIFY COLUMN `numSecBuy` bigint(20) NOT NULL DEFAULT 0 COMMENT '第二次购买金额 单位分',
MODIFY COLUMN `numSecBuy` bigint(20) NOT NULL DEFAULT 0 COMMENT '第二次购买金额 单位分',
MODIFY COLUMN `numSecBuy` bigint(20) NOT NULL DEFAULT 0 COMMENT '第二次购买金额 单位分',