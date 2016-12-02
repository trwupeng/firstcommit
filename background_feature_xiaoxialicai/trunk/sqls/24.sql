-- ----------------------------------
-- 用户表
-- ----------------------------------
USE `db_p2prpt`;
DROP TABLE IF EXISTS `tb_user_final`;
CREATE TABLE `tb_user_final` (
  `userId` bigint(20) NOT NULL DEFAULT '0' COMMENT 'userId',
  `vipLevel` tinyint(4) NOT NULL DEFAULT '0' COMMENT '会员等级',
  `ymdReg` int(11) NOT NULL DEFAULT '0' COMMENT '注册年月日',
  `regClient` smallint(4) NOT NULL DEFAULT '0' COMMENT '注册时客户端', 
  `contractId` bigint(20) NOT NULL DEFAULT '0' COMMENT '推广协议Id',
  `phone` bigint(16) NOT NULL DEFAULT '0' COMMENT '默认手机号',
  `dtLast` bigint(20) NOT NULL DEFAULT '0' COMMENT '最后登入的时间',
  
  `nickname` varchar(36) NOT NULL DEFAULT '' COMMENT '用户名',
  `wallet` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '钱包余额',
  `points` int(11) NOT NULL DEFAULT '0' COMMENT '商城积分',
  `redPacket` int(11) NOT NULL DEFAULT '0' COMMENT '红包余额 单位分',
  `copartnerId` int(11) NOT NULL DEFAULT '0' COMMENT '推广渠道id',
  `clientType` smallint(4) NOT NULL DEFAULT'0' COMMENT '客户端',
  
  `ymdBindcard` int(11) NOT NULL DEFAULT '0' COMMENT '首次绑卡日期',
  `ymdFirstBuy` int(11) NOT NULL DEFAULT '0' COMMENT '首次成功下单',
  `numFirstBuy` int(11) NOT NULL DEFAULT '0' COMMENT '首次成功投入金额',
  `ymdLastBuy` int(11) NOT NULL DEFAULT '0' COMMENT '最后一次功成下单',
  `numLastBuy` int(11) NOT NULL DEFAULT '0' COMMENT '最后一次充公投入金额',
 
  `ymdFirstCharge` int(11) NOT NULL DEFAULT '0' COMMENT '首次充值',
  `numFirstCharge` int(11) NOT NULL DEFAULT '0' COMMENT '首次充值的金额',
  `maxBoughtDay` int(11) NOT NULL DEFAULT '0' COMMENT '投入最多一次的日期', 
  `maxBoughtNum` int(11) NOT NULL DEFAULT '0' COMMENT '投入最多的一次的金额',
  `maxChargeDay` int(11) NOT NULL DEFAULT '0' COMMENT '充值最多一次的日期',
  `maxChargeNum` int(11) NOT NULL DEFAULT '0' COMMENT '充值最多一次的充值金额',
  
  `protocol` varchar(16) NOT NULL DEFAULT '' COMMENT '注册同意的协议版本号',
  `inviteByUser` bigint(12) NOT NULL DEFAULT '0' COMMENT '邀请人',
  `inviteByParent` bigint(20) NOT NULL DEFAULT '0' COMMENT '邀请人的邀请人',
  `inviteByRoot` bigint(20) NOT NULL DEFAULT '0' COMMENT '根邀请人',
  `myInviteCode` varchar(12) NOT NULL DEFAULT '' COMMENT '我的邀请码',
  `checkinBook` varchar(1000) NOT NULL DEFAULT '' COMMENT '签到簿',
  
  PRIMARY KEY (`userId`),
  KEY `contract_reg` (`contractId`,`ymdReg`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='平台用户表';
insert into db_p2p.tb_config values('dbsql.ver',24) ON DUPLICATE KEY UPDATE v=24;