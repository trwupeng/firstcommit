/*
Navicat MySQL Data Transfer

Source Server         : rds-----------rds
Source Server Version : 50616
Source Host           : rdsww7570ykm55z6u603o.mysql.rds.aliyuncs.com:3306
Source Database       : db_oauth

Target Server Type    : MYSQL
Target Server Version : 50616
File Encoding         : 65001

Date: 2016-03-31 11:03:59
*/

SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for `tb_accounts_0`
-- ----------------------------
DROP TABLE IF EXISTS `tb_accounts_0`;
CREATE TABLE `tb_accounts_0` (
  `accountId` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '用户标识',
  `passwd` varchar(32) DEFAULT NULL COMMENT '密码',
  `passwdSalt` varchar(4) DEFAULT NULL COMMENT '安全码',
  `pwdVer` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '重置密码次数',
  `deviceId` varchar(256) NOT NULL COMMENT '唯一设备ID-重置密码',
  `regYmd` int(11) NOT NULL DEFAULT '0' COMMENT '注册日期',
  `regHHiiss` int(11) NOT NULL DEFAULT '4' COMMENT '注册时间：时分秒',
  `regClient` smallint(4) NOT NULL DEFAULT '0' COMMENT '注册客户端',
  `regIP` varchar(16) NOT NULL DEFAULT '' COMMENT '注册客户端',
  `dtForbidden` int(11) NOT NULL DEFAULT '0' COMMENT '状态 (0 正常使用)',
  `loginFailed` bigint(36) unsigned NOT NULL DEFAULT '0' COMMENT '密码错误后的CD',
  `nickname` varchar(36) DEFAULT NULL COMMENT '昵称',
  `lastIP` varchar(16) NOT NULL DEFAULT '' COMMENT '最后访问IP',
  `lastDt` int(11) NOT NULL DEFAULT '0' COMMENT '最后访问时间',
  `contractId` bigint(20) NOT NULL DEFAULT '0',
  `contractData` varchar(255) NOT NULL DEFAULT '' COMMENT 'contractData',
  `iRecordVerID` int(20) unsigned DEFAULT '0',
  `phone` varchar(36) DEFAULT NULL COMMENT '手机号',
  `customData` varchar(255) NOT NULL COMMENT '冗余字段',
  PRIMARY KEY (`accountId`),
  UNIQUE KEY `accountId` (`accountId`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='帐号信息表';

-- ----------------------------
-- Table structure for `tb_accounts_1`
-- ----------------------------
DROP TABLE IF EXISTS `tb_accounts_1`;
CREATE TABLE `tb_accounts_1` (
  `accountId` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '用户标识',
  `passwd` varchar(32) DEFAULT NULL COMMENT '密码',
  `passwdSalt` varchar(4) DEFAULT NULL COMMENT '安全码',
  `pwdVer` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '重置密码次数',
  `deviceId` varchar(256) NOT NULL COMMENT '唯一设备ID-重置密码',
  `regYmd` int(11) NOT NULL DEFAULT '0' COMMENT '注册日期',
  `regHHiiss` int(11) NOT NULL DEFAULT '4' COMMENT '注册时间：时分秒',
  `regClient` smallint(4) NOT NULL DEFAULT '0' COMMENT '注册客户端',
  `regIP` varchar(16) NOT NULL DEFAULT '' COMMENT '注册客户端',
  `dtForbidden` int(11) NOT NULL DEFAULT '0' COMMENT '状态 (0 正常使用)',
  `loginFailed` bigint(36) unsigned NOT NULL DEFAULT '0' COMMENT '密码错误后的CD',
  `nickname` varchar(36) DEFAULT NULL COMMENT '昵称',
  `lastIP` varchar(16) NOT NULL DEFAULT '' COMMENT '最后访问IP',
  `lastDt` int(11) NOT NULL DEFAULT '0' COMMENT '最后访问时间',
  `contractId` bigint(20) NOT NULL DEFAULT '0',
  `contractData` varchar(255) NOT NULL DEFAULT '' COMMENT 'contractData',
  `iRecordVerID` int(20) unsigned DEFAULT '0',
  `phone` varchar(36) DEFAULT NULL COMMENT '手机号',
  `customData` varchar(255) NOT NULL COMMENT '冗余字段',
  PRIMARY KEY (`accountId`),
  UNIQUE KEY `accountId` (`accountId`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='帐号信息表';


-- ----------------------------
-- Table structure for `tb_loginname_0`
-- ----------------------------
DROP TABLE IF EXISTS `tb_loginname_0`;
CREATE TABLE `tb_loginname_0` (
  `loginName` varchar(36) NOT NULL COMMENT '登录名',
  `cameFrom` varchar(36) NOT NULL COMMENT '登录类型，例如：phone,local',
  `accountId` bigint(255) NOT NULL COMMENT '用户标识',
  `flgStatus` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态位',
  `iRecordVerID` int(11) NOT NULL DEFAULT '0' COMMENT 'KVOBJ-row-version',
  PRIMARY KEY (`loginName`,`cameFrom`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='帐号名表';


-- ----------------------------
-- Table structure for `tb_loginname_1`
-- ----------------------------
DROP TABLE IF EXISTS `tb_loginname_1`;
CREATE TABLE `tb_loginname_1` (
  `loginName` varchar(36) NOT NULL COMMENT '登录名',
  `cameFrom` varchar(36) NOT NULL COMMENT '登录类型，例如：phone,local',
  `accountId` bigint(255) NOT NULL COMMENT '用户标识',
  `flgStatus` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态位',
  `iRecordVerID` int(11) NOT NULL DEFAULT '0' COMMENT 'KVOBJ-row-version',
  PRIMARY KEY (`loginName`,`cameFrom`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='帐号名表';


-- ----------------------------
-- Table structure for `tb_oauth_client_0`
-- ----------------------------
DROP TABLE IF EXISTS `tb_oauth_client_0`;
CREATE TABLE `tb_oauth_client_0` (
  `id` varchar(36) NOT NULL COMMENT '客户端ID',
  `secret` varchar(36) NOT NULL COMMENT '客户端密钥',
  `name` varchar(255) NOT NULL COMMENT '客户端名称',
  `info` varchar(255) NOT NULL COMMENT '客户端简介',
  `url` varchar(255) NOT NULL COMMENT '客户端官网地址',
  `redirectUri` varchar(255) NOT NULL COMMENT '客户端回调地址',
  `createTime` bigint(20) NOT NULL COMMENT '创建时间',
  `status` tinyint(3) NOT NULL COMMENT '状态位：1正常',
  `scope` varchar(255) NOT NULL COMMENT '权限',
  `iRecordVerID` int(11) NOT NULL DEFAULT '0' COMMENT 'KVOBJ-row-version',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='oauth客户端表';

-- ----------------------------
-- Records of tb_oauth_client_0
-- ----------------------------
INSERT INTO `tb_oauth_client_0` VALUES ('1104878344', '1f1c9288517ad28165e423bc59b4981b', 'lingtm', 'lingtm', 'www.lingtm.com', 'https://www.baidu.com/', '0', '1', 'basic,photo,info,admin', '1');

-- ----------------------------
-- Table structure for `tb_oauth_client_1`
-- ----------------------------
DROP TABLE IF EXISTS `tb_oauth_client_1`;
CREATE TABLE `tb_oauth_client_1` (
  `id` varchar(36) NOT NULL COMMENT '客户端ID',
  `secret` varchar(36) NOT NULL COMMENT '客户端密钥',
  `name` varchar(255) NOT NULL COMMENT '客户端名称',
  `info` varchar(255) NOT NULL COMMENT '客户端简介',
  `url` varchar(255) NOT NULL COMMENT '客户端官网地址',
  `redirectUri` varchar(255) NOT NULL COMMENT '客户端回调地址',
  `createTime` bigint(20) NOT NULL COMMENT '创建时间',
  `status` tinyint(3) NOT NULL COMMENT '状态位：1正常',
  `scope` varchar(255) NOT NULL COMMENT '权限',
  `iRecordVerID` int(11) NOT NULL DEFAULT '0' COMMENT 'KVOBJ-row-version',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='oauth客户端表';


-- ----------------------------
-- Table structure for `tb_oauth_code_0`
-- ----------------------------
DROP TABLE IF EXISTS `tb_oauth_code_0`;
CREATE TABLE `tb_oauth_code_0` (
  `code` varchar(255) NOT NULL COMMENT '临时码',
  `clientId` varchar(255) NOT NULL COMMENT '客户端ID',
  `accountId` varchar(255) NOT NULL COMMENT '用户ID',
  `expiresIn` varchar(255) NOT NULL COMMENT '过期时间',
  `scope` varchar(255) NOT NULL COMMENT '权限，多个权限用逗号隔开',
  `iRecordVerID` int(11) NOT NULL DEFAULT '0' COMMENT 'KVOBJ-row-version',
  PRIMARY KEY (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='oauth临时码表';


-- ----------------------------
-- Table structure for `tb_oauth_code_1`
-- ----------------------------
DROP TABLE IF EXISTS `tb_oauth_code_1`;
CREATE TABLE `tb_oauth_code_1` (
  `code` varchar(255) NOT NULL COMMENT '临时码',
  `clientId` varchar(255) NOT NULL COMMENT '客户端ID',
  `accountId` varchar(255) NOT NULL COMMENT '用户ID',
  `expiresIn` varchar(255) NOT NULL COMMENT '过期时间',
  `scope` varchar(255) NOT NULL COMMENT '权限，多个权限用逗号隔开',
  `iRecordVerID` int(11) NOT NULL DEFAULT '0' COMMENT 'KVOBJ-row-version',
  PRIMARY KEY (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='oauth临时码表';


-- ----------------------------
-- Table structure for `tb_oauth_refresh_0`
-- ----------------------------
DROP TABLE IF EXISTS `tb_oauth_refresh_0`;
CREATE TABLE `tb_oauth_refresh_0` (
  `refreshToken` varchar(255) NOT NULL COMMENT '刷新密钥',
  `accessToken` varchar(255) NOT NULL COMMENT 'token',
  `expiresIn` varchar(255) NOT NULL COMMENT '过期时间',
  `iRecordVerID` int(11) NOT NULL DEFAULT '0' COMMENT 'KVOBJ-row-version',
  PRIMARY KEY (`refreshToken`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='refreshToken表';


-- ----------------------------
-- Table structure for `tb_oauth_token_1`
-- ----------------------------
DROP TABLE IF EXISTS `tb_oauth_token_1`;
CREATE TABLE `tb_oauth_token_1` (
  `accessToken` varchar(255) NOT NULL COMMENT 'token',
  `accountId` varchar(255) NOT NULL COMMENT '用户ID',
  `expiresIn` bigint(20) NOT NULL COMMENT 'token过期时间',
  `scope` varchar(255) NOT NULL COMMENT '权限，多个权限用逗号隔开',
  `clientId` varchar(36) NOT NULL COMMENT '客户端ID',
  `iRecordVerID` int(11) NOT NULL DEFAULT '0' COMMENT 'KVOBJ-row-version',
  PRIMARY KEY (`accessToken`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='accessToken表';
