/*
Navicat MySQL Data Transfer

Source Server         : 192.168.56.140
Source Server Version : 50173
Source Host           : 192.168.56.140:3306
Source Database       : db_p2p

Target Server Type    : MYSQL
Target Server Version : 50173
File Encoding         : 65001

Date: 2015-09-28 15:19:40
*/

SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for `tb_accounts_0`
-- ----------------------------
DROP TABLE IF EXISTS `tb_accounts_0`;
CREATE TABLE `tb_accounts_0` (
  `cameFrom` varchar(36) NOT NULL,
  `loginName` varchar(36) NOT NULL,
  `passwd` varchar(32) DEFAULT NULL,
  `passwdSalt` varchar(4) DEFAULT NULL,
  `accountId` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '账号id',
  `regYmd` int(11) NOT NULL DEFAULT '0',
  `regHHiiss` int(11) NOT NULL DEFAULT '4',
  `regClient` tinyint(4) NOT NULL DEFAULT '0',
  `regIP` varchar(16) NOT NULL DEFAULT '',
  `dtForbidden` int(11) NOT NULL DEFAULT '0' COMMENT '状态 (0 正常使用)',
  `loginFailed` bigint(36) unsigned NOT NULL DEFAULT '0' COMMENT '密码错误后的CD',
  `nickname` varchar(36) DEFAULT NULL,
  `lastIP` varchar(16) NOT NULL DEFAULT '' COMMENT '最后访问IP',
  `lastDt` int(11) NOT NULL DEFAULT '0' COMMENT '最后访问时间',
  `contractId` bigint(20) NOT NULL DEFAULT '0',
  `phone` varchar(20) NOT NULL DEFAULT '0',
  `rights` varchar(2000) NOT NULL DEFAULT '',
  `iRecordVerID` int(20) unsigned DEFAULT '0',
  PRIMARY KEY (`cameFrom`,`loginName`),
  UNIQUE KEY `accountId` (`accountId`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of tb_accounts_0
-- ----------------------------

-- ----------------------------
-- Table structure for `tb_calendar`
-- ----------------------------
DROP TABLE IF EXISTS `tb_calendar`;
CREATE TABLE `tb_calendar` (
  `Ymd` int(11) NOT NULL DEFAULT '0' COMMENT 'yyyymmdd',
  `workday` bit(1) NOT NULL DEFAULT b'0' COMMENT '是否工作日',
  PRIMARY KEY (`Ymd`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tb_calendar
-- ----------------------------

-- ----------------------------
-- Table structure for `tb_config`
-- ----------------------------
DROP TABLE IF EXISTS `tb_config`;
CREATE TABLE `tb_config` (
  `k` varchar(64) NOT NULL,
  `v` varchar(128) NOT NULL,
  PRIMARY KEY (`k`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tb_config
-- ----------------------------

-- ----------------------------
-- Table structure for `tb_files`
-- ----------------------------
DROP TABLE IF EXISTS `tb_files`;
CREATE TABLE `tb_files` (
  `fileId` varchar(64) NOT NULL,
  `fileData` mediumblob,
  PRIMARY KEY (`fileId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of tb_files
-- ----------------------------

-- ----------------------------
-- Table structure for `tb_investment_0`
-- ----------------------------
DROP TABLE IF EXISTS `tb_investment_0`;
CREATE TABLE `tb_investment_0` (
  `ordersId` bigint(20) NOT NULL COMMENT 'ordersId',
  `waresId` bigint(20) NOT NULL DEFAULT '0' COMMENT 'waresId',
  `userId` bigint(20) NOT NULL COMMENT 'userId',
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '实际投资额',
  `interest` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '实际投资额的预定收益',
  `amountExt` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '活动赠送投资额（可取现）',
  `amountFake` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '活动赠送投资额（不可取现）',
  `interestExt` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '动活赠送金额可获得的预定收益',
  `extDesc` varchar(256) NOT NULL COMMENT '动活赠送说明',
  `orderTime` bigint(20) NOT NULL DEFAULT '0' COMMENT '下单时间',
  `orderStatus` int(11) NOT NULL DEFAULT '0' COMMENT '单订状态',
  `codeCreate` varchar(128) NOT NULL DEFAULT '' COMMENT '创建流水的代码标示',
  `descCreate` varchar(128) NOT NULL DEFAULT '' COMMENT '用途的用户说明',
  `vouchers` varchar(128) NOT NULL DEFAULT '',
  `iRecordVerID` int(11) NOT NULL DEFAULT '0' COMMENT 'iRecordVerID',
  PRIMARY KEY (`ordersId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tb_investment_0
-- ----------------------------

-- ----------------------------
-- Table structure for `tb_invitecodes_0`
-- ----------------------------
DROP TABLE IF EXISTS `tb_invitecodes_0`;
CREATE TABLE `tb_invitecodes_0` (
  `inviteCode` varchar(12) NOT NULL DEFAULT '' COMMENT '邀请码',
  `userId` bigint(20) NOT NULL DEFAULT '0' COMMENT '属于哪个人的',
  PRIMARY KEY (`inviteCode`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tb_invitecodes_0
-- ----------------------------
INSERT INTO `tb_invitecodes_0` VALUES ('dxpazxz', '75380365681499');
INSERT INTO `tb_invitecodes_0` VALUES ('nsrakaq', '56283309694452');
INSERT INTO `tb_invitecodes_0` VALUES ('mgmahdm', '30256305535262');
INSERT INTO `tb_invitecodes_0` VALUES ('vpzrrla', '98190204317212');
INSERT INTO `tb_invitecodes_0` VALUES ('pbwajbn', '21560607144736');

-- ----------------------------
-- Table structure for `tb_loginname_alias_0`
-- ----------------------------
DROP TABLE IF EXISTS `tb_loginname_alias_0`;
CREATE TABLE `tb_loginname_alias_0` (
  `aliasName` bigint(255) NOT NULL COMMENT '备用注册名',
  `loginName` varchar(36) DEFAULT NULL,
  `cameFrom` varchar(36) DEFAULT NULL,
  `flgStatus` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0的时候是注册锁定中',
  `iRecordVerID` int(11) NOT NULL DEFAULT '0' COMMENT 'KVOBJ-row-version',
  PRIMARY KEY (`aliasName`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of tb_loginname_alias_0
-- ----------------------------

-- ----------------------------
-- Table structure for `tb_odd`
-- ----------------------------
DROP TABLE IF EXISTS `tb_odd`;
CREATE TABLE `tb_odd` (
  `odd` decimal(10,2) NOT NULL COMMENT '零头数额',
  `desc` varchar(64) NOT NULL COMMENT '生产原因'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of tb_odd
-- ----------------------------

-- ----------------------------
-- Table structure for `tb_pointstally_0`
-- ----------------------------
DROP TABLE IF EXISTS `tb_pointstally_0`;
CREATE TABLE `tb_pointstally_0` (
  `tallyId` bigint(20) NOT NULL DEFAULT '0' COMMENT 'tallyId',
  `userId` bigint(20) NOT NULL DEFAULT '0' COMMENT 'userId',
  `orderId` bigint(20) NOT NULL DEFAULT '0' COMMENT 'orderId',
  `tallyType` int(11) NOT NULL COMMENT '类型',
  `statusCode` int(11) NOT NULL DEFAULT '0' COMMENT '状态',
  `timeCreate` bigint(20) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `codeCreate` varchar(128) NOT NULL DEFAULT '' COMMENT '创建流水的代码说明',
  `descCreate` varchar(128) NOT NULL DEFAULT '' COMMENT '创建流水的用户说明',
  `nOld` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '原余额',
  `nAdd` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '增加额（可负）',
  `nNew` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '新余额',
  `iRecordVerID` int(11) NOT NULL COMMENT 'iRecordVerID',
  PRIMARY KEY (`tallyId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='商城积分流水表';

-- ----------------------------
-- Records of tb_pointstally_0
-- ----------------------------
INSERT INTO `tb_pointstally_0` VALUES ('144309255776165262', '30256305535262', '0', '0', '0', '20150924190237', '', '', '2.00', '1.00', '3.00', '1');
INSERT INTO `tb_pointstally_0` VALUES ('14430941586255262', '30256305535262', '0', '0', '0', '20150924192918', '', '', '3.00', '1.00', '4.00', '1');
INSERT INTO `tb_pointstally_0` VALUES ('144310861127635262', '30256305535262', '0', '0', '0', '20150924233011', '', '', '4.00', '1.00', '5.00', '1');
INSERT INTO `tb_pointstally_0` VALUES ('144314953177231499', '75380365681499', '0', '0', '0', '20150925105211', '', '', '0.00', '1.00', '1.00', '1');
INSERT INTO `tb_pointstally_0` VALUES ('144314994121499', '75380365681499', '0', '0', '0', '20150925105901', 'actives.checkin', 'ç­¾åˆ°å¥–åŠ±', '0.00', '1.00', '1.00', '1');
INSERT INTO `tb_pointstally_0` VALUES ('1443150023561499', '75380365681499', '0', '0', '0', '20150925110023', 'actives.checkin', 'ç­¾åˆ°å¥–åŠ±', '0.00', '1.00', '1.00', '1');
INSERT INTO `tb_pointstally_0` VALUES ('144315012866231499', '75380365681499', '0', '0', '0', '20150925110208', 'actives.checkin', 'ç­¾åˆ°å¥–åŠ±', '0.00', '1.00', '1.00', '1');
INSERT INTO `tb_pointstally_0` VALUES ('144315017553111499', '75380365681499', '0', '0', '0', '20150925110255', 'actives.checkin', 'ç­¾åˆ°å¥–åŠ±', '0.00', '1.00', '1.00', '1');
INSERT INTO `tb_pointstally_0` VALUES ('144315028827411499', '75380365681499', '0', '0', '0', '20150925110448', 'actives.checkin', 'ç­¾åˆ°å¥–åŠ±', '1.00', '1.00', '2.00', '1');
INSERT INTO `tb_pointstally_0` VALUES ('144315029137981499', '75380365681499', '0', '0', '0', '20150925110451', 'actives.checkin', 'ç­¾åˆ°å¥–åŠ±', '2.00', '1.00', '3.00', '1');
INSERT INTO `tb_pointstally_0` VALUES ('144315037076921499', '75380365681499', '0', '0', '0', '20150925110610', 'actives.checkin', 'ç­¾åˆ°å¥–åŠ±', '3.00', '1.00', '4.00', '1');

-- ----------------------------
-- Table structure for `tb_recharges_0`
-- ----------------------------
DROP TABLE IF EXISTS `tb_recharges_0`;
CREATE TABLE `tb_recharges_0` (
  `ordersId` bigint(20) NOT NULL COMMENT 'ordersId',
  `userId` bigint(20) NOT NULL COMMENT 'userId',
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '水流的金额',
  `amountAbs` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '取正后的金额',
  `amountFlg` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0：提现，1充值',
  `poundage` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '手续费',
  `orderTime` bigint(20) NOT NULL DEFAULT '0' COMMENT '下单时间',
  `payTime` bigint(20) NOT NULL DEFAULT '0' COMMENT '付支状态变更时间',
  `orderStatus` int(11) NOT NULL DEFAULT '0' COMMENT '单订状态',
  `payCorp` int(11) NOT NULL DEFAULT '0' COMMENT '指定的支付通道ID',
  `bankAbs` varchar(16) NOT NULL DEFAULT '' COMMENT '银行缩写',
  `bankCard` varchar(20) NOT NULL DEFAULT '' COMMENT '银行卡号',
  `iRecordVerID` int(11) NOT NULL DEFAULT '0' COMMENT 'iRecordVerID',
  PRIMARY KEY (`ordersId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tb_recharges_0
-- ----------------------------

-- ----------------------------
-- Table structure for `tb_session_0`
-- ----------------------------
DROP TABLE IF EXISTS `tb_session_0`;
CREATE TABLE `tb_session_0` (
  `sessionId` varchar(40) NOT NULL,
  `sessionData` varchar(2000) DEFAULT NULL,
  `iRecordVerID` int(20) NOT NULL DEFAULT '0',
  `accountId` varchar(36) DEFAULT NULL,
  PRIMARY KEY (`sessionId`)
) ENGINE=MEMORY DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Records of tb_session_0
-- ----------------------------

-- ----------------------------
-- Table structure for `tb_shelf`
-- ----------------------------
DROP TABLE IF EXISTS `tb_shelf`;
CREATE TABLE `tb_shelf` (
  `shelfId` int(20) unsigned NOT NULL COMMENT 'shelfId',
  `shelfName` varchar(128) NOT NULL COMMENT '类型名称',
  PRIMARY KEY (`shelfId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tb_shelf
-- ----------------------------
INSERT INTO `tb_shelf` VALUES ('1', 'shelfTest');

-- ----------------------------
-- Table structure for `tb_sms_valid_0`
-- ----------------------------
DROP TABLE IF EXISTS `tb_sms_valid_0`;
CREATE TABLE `tb_sms_valid_0` (
  `phone` bigint(20) NOT NULL,
  `dat` varchar(500) NOT NULL,
  `iRecordVerID` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`phone`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of tb_sms_valid_0
-- ----------------------------

-- ----------------------------
-- Table structure for `tb_user_0`
-- ----------------------------
DROP TABLE IF EXISTS `tb_user_0`;
CREATE TABLE `tb_user_0` (
  `userId` bigint(20) NOT NULL DEFAULT '0' COMMENT 'userId',
  `ymdReg` int(11) NOT NULL DEFAULT '0' COMMENT '注册日期',
  `ymdFirstBuy` int(11) NOT NULL DEFAULT '0' COMMENT '首次功成下单',
  `ymdLastBuy` int(11) NOT NULL DEFAULT '0' COMMENT '最后一次功成下单',
  `ymdFirstCharge` int(11) NOT NULL DEFAULT '0' COMMENT '首次充值',
  `ymdBindcard` int(11) NOT NULL DEFAULT '0' COMMENT '首次绑卡日期',
  `ipReg` varchar(16) NOT NULL DEFAULT '0.0.0.0' COMMENT '注册时的IP',
  `ipLast` varchar(16) NOT NULL DEFAULT '0.0.0.0' COMMENT '最后一次登入的IP',
  `dtLast` bigint(20) NOT NULL DEFAULT '0' COMMENT '最后登入的时间',
  `phone` bigint(16) NOT NULL DEFAULT '0' COMMENT '默认手机号',
  `nickname` varchar(36) NOT NULL DEFAULT '' COMMENT '用户名',
  `wallet` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '钱包余额',
  `points` int(11) NOT NULL DEFAULT '0' COMMENT '商城积分',
  `copartnerId` int(11) NOT NULL DEFAULT '0' COMMENT '推广渠道id',
  `contractId` bigint(20) NOT NULL DEFAULT '0' COMMENT '推广协议Id',
  `inviteByUser` bigint(12) NOT NULL DEFAULT '0' COMMENT '邀请人',
  `inviteByParent` bigint(20) NOT NULL DEFAULT '0' COMMENT '邀请人的邀请人',
  `inviteByRoot` bigint(20) NOT NULL DEFAULT '0' COMMENT '根邀请人',
  `myInviteCode` varchar(12) NOT NULL DEFAULT '' COMMENT '我的邀请码',
  `checkinBook` varchar(1000) NOT NULL DEFAULT '' COMMENT '签到簿',
  `iRecordVerID` int(11) NOT NULL DEFAULT '0' COMMENT 'iRecordVerID',
  `sLockData` varchar(100) NOT NULL DEFAULT '' COMMENT 'sLockData',
  PRIMARY KEY (`userId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tb_user_0
-- ----------------------------
INSERT INTO `tb_user_0` VALUES ('75380365681499', '20150924', '0', '0', '0', '0', '0.0.0.0', '0.0.0.0', '0', '13040925418', '130*****418', '0.00', '4', '0', '0', '0', '0', '0', 'dxpazxz', '{\"ymd\":\"20150925\",\"bonusGot\":{\"25\":{\"ShopPoint\":1,\"VoucherCheckin\":1}}}', '15', '');
INSERT INTO `tb_user_0` VALUES ('56283309694452', '20150924', '0', '0', '0', '0', '0.0.0.0', '0.0.0.0', '0', '13060719895', '130*****895', '0.00', '0', '0', '0', '0', '0', '0', 'nsrakaq', '', '1', '');
INSERT INTO `tb_user_0` VALUES ('30256305535262', '20150924', '0', '0', '0', '0', '0.0.0.0', '0.0.0.0', '0', '13094892537', '130*****537', '0.00', '4', '0', '0', '0', '0', '0', 'mgmahdm', '{\"ymd\":0,\"bonusGot\":[{\"ShopPoint\":1}],\"checked\":[]}', '33', '');
INSERT INTO `tb_user_0` VALUES ('98190204317212', '20150924', '0', '0', '0', '0', '0.0.0.0', '0.0.0.0', '0', '13099856875', '130*****875', '0.00', '0', '0', '0', '0', '0', '0', 'vpzrrla', '', '1', '');
INSERT INTO `tb_user_0` VALUES ('21560607144736', '20150924', '0', '0', '0', '0', '0.0.0.0', '0.0.0.0', '0', '13030126366', '130*****366', '0.00', '0', '0', '0', '0', '0', '0', 'pbwajbn', '', '1', '');

-- ----------------------------
-- Table structure for `tb_user_bankcard_0`
-- ----------------------------
DROP TABLE IF EXISTS `tb_user_bankcard_0`;
CREATE TABLE `tb_user_bankcard_0` (
  `userId` bigint(20) NOT NULL COMMENT 'userId',
  `payCorp` int(11) NOT NULL DEFAULT '0' COMMENT '验证的支付通道',
  `bankId` varchar(16) NOT NULL COMMENT '银行简写',
  `bankCard` varchar(20) NOT NULL COMMENT '行卡号银',
  `isDefault` tinyint(4) NOT NULL DEFAULT '0' COMMENT '否是默认卡',
  `statusCode` tinyint(4) NOT NULL DEFAULT '-1' COMMENT '状态：-1:待验证，0：禁用；1正常使用',
  `resultMsg` varchar(200) NOT NULL DEFAULT '' COMMENT '验证结果描述',
  `resultTime` bigint(20) NOT NULL DEFAULT '0' COMMENT '更新验证状态的时间',
  `iRecordVerID` int(11) NOT NULL DEFAULT '0' COMMENT 'iRecordVerID',
  PRIMARY KEY (`userId`,`payCorp`,`bankId`,`bankCard`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='用户绑定的银行卡';

-- ----------------------------
-- Records of tb_user_bankcard_0
-- ----------------------------

-- ----------------------------
-- Table structure for `tb_vouchers_0`
-- ----------------------------
DROP TABLE IF EXISTS `tb_vouchers_0`;
CREATE TABLE `tb_vouchers_0` (
  `voucherId` bigint(20) NOT NULL DEFAULT '0' COMMENT 'vouchersId',
  `userId` bigint(20) NOT NULL DEFAULT '0' COMMENT '给哪个用户的',
  `voucherType` varchar(16) NOT NULL DEFAULT '' COMMENT '券类型',
  `amount` int(11) NOT NULL DEFAULT '0' COMMENT '面值',
  `limits` varchar(256) NOT NULL DEFAULT '' COMMENT '用使限制',
  `codeCreate` varchar(128) NOT NULL DEFAULT '' COMMENT '创建流水的代码标示',
  `descCreate` varchar(128) NOT NULL DEFAULT '' COMMENT '创建流水的用户说明',
  `timeCreate` bigint(20) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `dtExpired` bigint(20) NOT NULL DEFAULT '0' COMMENT '失效时间',
  `dtUsed` bigint(20) NOT NULL DEFAULT '0' COMMENT '实际使用时间',
  `orderId` bigint(20) NOT NULL DEFAULT '0' COMMENT '订单',
  `statusCode` int(11) NOT NULL DEFAULT '0' COMMENT '状态',
  `iRecordVerID` int(11) NOT NULL DEFAULT '0' COMMENT 'iRecordVerID',
  PRIMARY KEY (`voucherId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='发放到用户的各类券';

-- ----------------------------
-- Records of tb_vouchers_0
-- ----------------------------
INSERT INTO `tb_vouchers_0` VALUES ('9014431501755321499', '0', '', '0', '0', 'actives.checkin', 'ç­¾åˆ°å¥–åŠ±', '20150925110255', '20151025235959', '0', '0', '0', '2');
INSERT INTO `tb_vouchers_0` VALUES ('9014431502882761499', '0', '', '0', '0', 'actives.checkin', 'ç­¾åˆ°å¥–åŠ±', '20150925110448', '20151025235959', '0', '0', '0', '2');
INSERT INTO `tb_vouchers_0` VALUES ('9014431502913821499', '0', '', '0', '0', 'actives.checkin', 'ç­¾åˆ°å¥–åŠ±', '20150925110451', '20151025235959', '0', '0', '0', '2');
INSERT INTO `tb_vouchers_0` VALUES ('9014431503707711499', '0', '', '0', '0', 'actives.checkin', 'ç­¾åˆ°å¥–åŠ±', '20150925110610', '20151025235959', '0', '0', '0', '2');

-- ----------------------------
-- Table structure for `tb_wallettally_0`
-- ----------------------------
DROP TABLE IF EXISTS `tb_wallettally_0`;
CREATE TABLE `tb_wallettally_0` (
  `tallyId` bigint(20) NOT NULL DEFAULT '0' COMMENT 'tallyId',
  `userId` bigint(20) NOT NULL DEFAULT '0' COMMENT 'userId',
  `orderId` bigint(20) NOT NULL DEFAULT '0' COMMENT 'orderId',
  `tallyType` int(11) NOT NULL COMMENT '类型',
  `statusCode` int(11) NOT NULL DEFAULT '0' COMMENT '状态',
  `codeCreate` varchar(128) NOT NULL DEFAULT '' COMMENT '创建流水的代码标示',
  `descCreate` varchar(128) NOT NULL DEFAULT '' COMMENT '用途的用户说明',
  `timeCreate` bigint(20) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `nOld` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '原余额',
  `nAdd` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '增加额（可负）',
  `nNew` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '新余额',
  `iRecordVerID` int(11) NOT NULL COMMENT 'iRecordVerID',
  `poundage` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '手续费',
  PRIMARY KEY (`tallyId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='钱包流水表';

-- ----------------------------
-- Records of tb_wallettally_0
-- ----------------------------

-- ----------------------------
-- Table structure for `tb_wares_0`
-- ----------------------------
DROP TABLE IF EXISTS `tb_wares_0`;
CREATE TABLE `tb_wares_0` (
  `waresId` bigint(20) unsigned NOT NULL COMMENT 'waresId',
  `waresName` varchar(128) NOT NULL COMMENT '标的名称',
  `waresSN` int(11) NOT NULL DEFAULT '0' COMMENT '期数',
  `tags` varchar(128) NOT NULL DEFAULT '' COMMENT '标签英文逗号分隔',
  `mainType` smallint(6) NOT NULL DEFAULT '0' COMMENT '大类',
  `subType` smallint(6) NOT NULL DEFAULT '0' COMMENT '小类',
  `priceStart` int(11) NOT NULL DEFAULT '1' COMMENT '起投金额',
  `priceStep` int(11) NOT NULL DEFAULT '1' COMMENT '递增金额',
  `amount` int(11) NOT NULL DEFAULT '0' COMMENT '总额',
  `remain` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '剩余额',
  `yieldStatic` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '定固年化收益率',
  `yieldStaticAdd` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '定固年化收益率上浮',
  `yieldFloatFrom` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '浮动年化收益率下限',
  `yieldFloatTo` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '浮动年化收益率上限',
  `yieldDesc` varchar(256) NOT NULL DEFAULT '' COMMENT '年化率变更详细说明',
  `licences` varchar(128) NOT NULL DEFAULT '' COMMENT '许可协议（json串）',
  `sLockData` varchar(128) NOT NULL DEFAULT '0' COMMENT '购买锁定',
  `iRecordVerID` int(11) NOT NULL DEFAULT '0' COMMENT '列所锁',
  `shelfId` tinyint(4) NOT NULL DEFAULT '0' COMMENT '类型ID',
  `statusCode` tinyint(4) NOT NULL DEFAULT '0' COMMENT '状态',
  `timeStartPlan` bigint(20) NOT NULL DEFAULT '0' COMMENT '计划上架时间',
  `timeStartReal` bigint(20) NOT NULL DEFAULT '0' COMMENT '际实上架时间',
  `timeEndPlan` bigint(20) NOT NULL DEFAULT '0' COMMENT '闭关募集结束时间',
  `timeEndReal` bigint(11) NOT NULL DEFAULT '0' COMMENT '实际募集结束时间',
  PRIMARY KEY (`waresId`),
  KEY `statusCode` (`statusCode`,`timeStartPlan`),
  KEY `timeStartReal` (`timeStartReal`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='类型上的标的';

-- ----------------------------
-- Records of tb_wares_0
-- ----------------------------
INSERT INTO `tb_wares_0` VALUES ('1233456437856', 'wares001', '0', '', '1', '1', '100', '10', '10000', '10000', '5.00', '0.50', '1.00', '8.00', 'todo:yieldDesc', '{\"buy\":\"buy_001.txt\"}', '0', '0', '1', '11', '20150101000000', '20150501000000', '20160101000000', '0');

-- ----------------------------
-- Table structure for `tb_wares_assets`
-- ----------------------------
DROP TABLE IF EXISTS `tb_wares_assets`;
CREATE TABLE `tb_wares_assets` (
  `waresId` bigint(20) DEFAULT NULL,
  `assetsType` varchar(0) DEFAULT NULL,
  `assetsId` bigint(20) DEFAULT NULL,
  KEY `waresId` (`waresId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tb_wares_assets
-- ----------------------------
