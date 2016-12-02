/*
Navicat MySQL Data Transfer

Source Server         : rds-----------rds
Source Server Version : 50616
Source Host           : rdsww7570ykm55z6u603o.mysql.rds.aliyuncs.com:3306
Source Database       : db_logs

Target Server Type    : MYSQL
Target Server Version : 50616
File Encoding         : 65001

Date: 2016-03-31 11:07:17
*/

SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for `tb_a`
-- ----------------------------
DROP TABLE IF EXISTS `tb_a`;
CREATE TABLE `tb_a` (
  `ip` varchar(16) DEFAULT NULL,
  `sessionId` varchar(64) DEFAULT NULL,
  `accountId` varchar(64) DEFAULT NULL,
  `phone` varchar(16) DEFAULT NULL,
  `ymd` int(11) DEFAULT NULL,
  `his` int(11) DEFAULT NULL,
  `_post` blob,
  `_cookie` blob,
  `_get` blob,
  `returned` blob,
  `jsonOk` tinyint(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for `tb_apFetchLog_0`
-- ----------------------------
DROP TABLE IF EXISTS `tb_apFetchLog_0`;
CREATE TABLE `tb_apFetchLog_0` (
  `autoid` bigint(20) NOT NULL DEFAULT '0',
  `userId` bigint(20) NOT NULL DEFAULT '0',
  `dt` int(11) NOT NULL DEFAULT '0' COMMENT '领奖的时间',
  `score` int(11) NOT NULL DEFAULT '0' COMMENT '领奖的分值段',
  `surname` varchar(5) NOT NULL DEFAULT '' COMMENT '姓',
  `phone` bigint(20) NOT NULL DEFAULT '0' COMMENT '手机号',
  `itemName` varchar(64) NOT NULL DEFAULT '' COMMENT '奖励物品',
  `itemNum` int(11) NOT NULL DEFAULT '0' COMMENT '奖励数量',
  `iRecordVerID` int(11) NOT NULL DEFAULT '0',
  `sLockData` varchar(200) DEFAULT '',
  PRIMARY KEY (`autoid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of tb_apFetchLog_0
-- ----------------------------

-- ----------------------------
-- Table structure for `tb_apFetchLog_1`
-- ----------------------------
DROP TABLE IF EXISTS `tb_apFetchLog_1`;
CREATE TABLE `tb_apFetchLog_1` (
  `autoid` bigint(20) NOT NULL DEFAULT '0',
  `userId` bigint(20) NOT NULL DEFAULT '0',
  `dt` int(11) NOT NULL DEFAULT '0' COMMENT '领奖的时间',
  `score` int(11) NOT NULL DEFAULT '0' COMMENT '领奖的分值段',
  `surname` varchar(5) NOT NULL DEFAULT '' COMMENT '姓',
  `phone` bigint(20) NOT NULL DEFAULT '0' COMMENT '手机号',
  `itemName` varchar(64) NOT NULL DEFAULT '' COMMENT '奖励物品',
  `itemNum` int(11) NOT NULL DEFAULT '0' COMMENT '奖励数量',
  `iRecordVerID` int(11) NOT NULL DEFAULT '0',
  `sLockData` varchar(200) DEFAULT '',
  PRIMARY KEY (`autoid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of tb_apFetchLog_1
-- ----------------------------

-- ----------------------------
-- Table structure for `tb_crond_log`
-- ----------------------------
DROP TABLE IF EXISTS `tb_crond_log`;
CREATE TABLE `tb_crond_log` (
  `ymdh` bigint(20) NOT NULL DEFAULT '0',
  `taskid` varchar(64) NOT NULL,
  `lastStatus` varchar(512) DEFAULT NULL,
  `lastRet` tinyint(4) NOT NULL DEFAULT '0',
  `isManual` tinyint(4) NOT NULL DEFAULT '0',
  `ymdhis` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ymdh`,`taskid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;


-- ----------------------------
-- Table structure for `tb_devsms`
-- ----------------------------
DROP TABLE IF EXISTS `tb_devsms`;
CREATE TABLE `tb_devsms` (
  `phone` bigint(20) NOT NULL,
  `dt` bigint(20) NOT NULL,
  `msg` varchar(300) NOT NULL,
  PRIMARY KEY (`phone`,`dt`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

