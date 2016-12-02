/*
Navicat MySQL Data Transfer

Source Server         : 10.1.1.50
Source Server Version : 50173
Source Host           : 10.1.1.50:3306
Source Database       : db_p2prpt

Target Server Type    : MYSQL
Target Server Version : 50173
File Encoding         : 65001

Date: 2016-06-16 11:19:10
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for tb_weekactivity_final
-- ----------------------------
DROP TABLE IF EXISTS `tb_weekactivity_final`;
CREATE TABLE `tb_weekactivity_final` (
  `userId` bigint(20) NOT NULL DEFAULT '0' COMMENT 'userId',
  `taskNumber` int(10) NOT NULL DEFAULT '1' COMMENT '????',
  `ymdFirst` int(10) DEFAULT NULL COMMENT '???????????Ymd',
  `investmentScore` int(10) NOT NULL DEFAULT '0' COMMENT '??????',
  `investmentLast` int(10) DEFAULT NULL COMMENT '????????????Ymd',
  `shareVoucherScore` int(10) NOT NULL DEFAULT '0' COMMENT '??????',
  `shareVoucherLast` int(10) DEFAULT NULL COMMENT '????????????Ymd',
  `checkinScore` int(10) NOT NULL DEFAULT '0' COMMENT '????',
  `checkinLast` int(10) DEFAULT NULL COMMENT '????????????Ymd',
  `inviteScore` int(10) NOT NULL DEFAULT '0' COMMENT '??????',
  `inviteLast` int(10) DEFAULT NULL COMMENT '????????????Ymd',
  `friendsInvestmentScore` int(10) NOT NULL DEFAULT '0' COMMENT '??????',
  `friendsInvestmentLast` int(10) DEFAULT NULL COMMENT '??????????Ymd',
  `rewards` varchar(1000) DEFAULT NULL COMMENT '??',
  `totalScore` int(10) DEFAULT NULL COMMENT '???'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='??????';
