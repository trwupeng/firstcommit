USE db_p2p;

ALTER TABLE `tb_wares_0`
MODIFY COLUMN `sortval`  bigint NOT NULL DEFAULT 0 AFTER `waitInvestNum`;

ALTER TABLE `tb_wares_0_ram`
MODIFY COLUMN `sortval`  bigint NOT NULL DEFAULT 0 AFTER `waitInvestNum`;

ALTER TABLE `tb_wares_0`
ADD COLUMN `statusCode1`  tinyint NOT NULL DEFAULT 0 COMMENT '附加状态   -1不展示' AFTER `statusCode`;

ALTER TABLE `tb_wares_0_ram`
ADD COLUMN `statusCode1`  tinyint NOT NULL DEFAULT 0 COMMENT '附加状态   -1不展示' AFTER `statusCode`;

INSERT INTO `tb_config` (`k`, `v`, `intro`) VALUES ('WARES_PLAN_HOURS', '12', '#提前X小时在理财列表中显示等待上架的标的#单位小时');
INSERT INTO `tb_config_ram` (`k`, `v`, `intro`) VALUES ('WARES_PLAN_HOURS', '12', '#提前X小时在理财列表中显示等待上架的标的#单位小时');

INSERT INTO `tb_config` (`k`, `v`, `intro`) VALUES ('WARES_PLAN_NUM', '2', '#最多显示Y个相同类型的等待上架的标的#');
INSERT INTO `tb_config_ram` (`k`, `v`, `intro`) VALUES ('WARES_PLAN_NUM', '2', '#最多显示Y个相同类型的等待上架的标的#');

ALTER TABLE `tb_wares_0`
MODIFY COLUMN `returnPlan`  varchar(5000) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '还款计划' AFTER `payYmd`;

ALTER TABLE `tb_wares_0_ram`
MODIFY COLUMN `returnPlan`  varchar(5000) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '还款计划' AFTER `payYmd`;

ALTER TABLE `tb_investment_0`
MODIFY COLUMN `returnPlan`  varchar(5000) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `returnNext`;

ALTER TABLE `tb_investment_1`
MODIFY COLUMN `returnPlan`  varchar(5000) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `returnNext`;

CREATE TABLE `tb_returnplan_0` (
  `ordersId` bigint(20) NOT NULL DEFAULT '0',
  `periods` tinyint(4) NOT NULL DEFAULT '0',
  `userId` bigint(20) NOT NULL DEFAULT '0',
  `waresId` bigint(20) NOT NULL DEFAULT '0' COMMENT '商品ID',
  `waresName` varchar(100) NOT NULL DEFAULT '' COMMENT '标的名称',
  `interestStatic` int(11) NOT NULL DEFAULT '0',
  `interestAdd` int(11) NOT NULL DEFAULT '0',
  `interestExt` int(11) NOT NULL DEFAULT '0',
  `interestFloat` int(11) NOT NULL DEFAULT '0',
  `interestSub` int(11) NOT NULL DEFAULT '0',
  `amount` bigint(20) NOT NULL DEFAULT '0',
  `amountExt` int(11) NOT NULL DEFAULT '0',
  `realPayAmount` int(11) NOT NULL DEFAULT '0',
  `realPayInterest` int(11) NOT NULL DEFAULT '0',
  `realPayinterestSub` int(255) NOT NULL DEFAULT '0',
  `exp` varchar(255) NOT NULL DEFAULT '',
  `status` tinyint(255) NOT NULL DEFAULT '0',
  `isPay` tinyint(4) NOT NULL DEFAULT '0',
  `sn` bigint(20) NOT NULL DEFAULT '0',
  `planDateYmd` bigint(20) NOT NULL DEFAULT '0',
  `realDateYmd` bigint(20) NOT NULL DEFAULT '0',
  `ahead` tinyint(4) NOT NULL DEFAULT '0',
  `updateTime` bigint(20) NOT NULL DEFAULT '0',
  `iRecordVerID` int(11) NOT NULL DEFAULT '0',
  `sLockData` varchar(255) NOT NULL DEFAULT '',
  `days` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ordersId`,`periods`),
  UNIQUE KEY `sn` (`sn`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;




insert into db_p2p.tb_config set k='dbsql.ver',v='150-tgh' ON DUPLICATE KEY UPDATE v='150-tgh';