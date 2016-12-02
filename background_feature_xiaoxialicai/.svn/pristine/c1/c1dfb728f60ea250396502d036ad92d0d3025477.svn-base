
CREATE DATABASE `db_oauth`;
USE `db_oauth`;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='oauth客户端表';

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='oauth客户端表';

INSERT INTO `tb_oauth_client_0` VALUES ('1104878344', '1f1c9288517ad28165e423bc59b4981b', 'lingtm', 'lingtm', 'www.lingtm.com', 'https://www.baidu.com/', '0', '1', 'basic,photo,info,admin', '1');

DROP TABLE IF EXISTS `tb_oauth_code_0`;
CREATE TABLE `tb_oauth_code_0` (
  `code` varchar(255) NOT NULL COMMENT '临时码',
  `clientId` varchar(255) NOT NULL COMMENT '客户端ID',
  `accountId` varchar(255) NOT NULL COMMENT '用户ID',
  `expiresIn` varchar(255) NOT NULL COMMENT '过期时间',
  `scope` varchar(255) NOT NULL COMMENT '权限，多个权限用逗号隔开',
  `iRecordVerID` int(11) NOT NULL DEFAULT '0' COMMENT 'KVOBJ-row-version',
  PRIMARY KEY (`code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='oauth临时码表';

DROP TABLE IF EXISTS `tb_oauth_code_1`;
CREATE TABLE `tb_oauth_code_1` (
  `code` varchar(255) NOT NULL COMMENT '临时码',
  `clientId` varchar(255) NOT NULL COMMENT '客户端ID',
  `accountId` varchar(255) NOT NULL COMMENT '用户ID',
  `expiresIn` varchar(255) NOT NULL COMMENT '过期时间',
  `scope` varchar(255) NOT NULL COMMENT '权限，多个权限用逗号隔开',
  `iRecordVerID` int(11) NOT NULL DEFAULT '0' COMMENT 'KVOBJ-row-version',
  PRIMARY KEY (`code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='oauth临时码表';

DROP TABLE IF EXISTS `tb_oauth_refresh_0`;
CREATE TABLE `tb_oauth_refresh_0` (
  `refreshToken` varchar(255) NOT NULL COMMENT '刷新密钥',
  `accessToken` varchar(255) NOT NULL COMMENT 'token',
  `expiresIn` varchar(255) NOT NULL COMMENT '过期时间',
  `iRecordVerID` int(11) NOT NULL DEFAULT '0' COMMENT 'KVOBJ-row-version',
  PRIMARY KEY (`refreshToken`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='refreshToken表';

DROP TABLE IF EXISTS `tb_oauth_refresh_1`;
CREATE TABLE `tb_oauth_refresh_1` (
  `refreshToken` varchar(255) NOT NULL COMMENT '刷新密钥',
  `accessToken` varchar(255) NOT NULL COMMENT 'token',
  `expiresIn` varchar(255) NOT NULL COMMENT '过期时间',
  `iRecordVerID` int(11) NOT NULL DEFAULT '0' COMMENT 'KVOBJ-row-version',
  PRIMARY KEY (`refreshToken`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='refreshToken表';

DROP TABLE IF EXISTS `tb_oauth_token_0`;
CREATE TABLE `tb_oauth_token_0` (
  `accessToken` varchar(255) NOT NULL COMMENT 'token',
  `accountId` varchar(255) NOT NULL COMMENT '用户ID',
  `expiresIn` bigint(20) NOT NULL COMMENT 'token过期时间',
  `scope` varchar(255) NOT NULL COMMENT '权限，多个权限用逗号隔开',
  `iRecordVerID` int(11) NOT NULL DEFAULT '0' COMMENT 'KVOBJ-row-version',
  PRIMARY KEY (`accessToken`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='accessToken表';

DROP TABLE IF EXISTS `tb_oauth_token_1`;
CREATE TABLE `tb_oauth_token_1` (
  `accessToken` varchar(255) NOT NULL COMMENT 'token',
  `accountId` varchar(255) NOT NULL COMMENT '用户ID',
  `expiresIn` bigint(20) NOT NULL COMMENT 'token过期时间',
  `scope` varchar(255) NOT NULL COMMENT '权限，多个权限用逗号隔开',
  `iRecordVerID` int(11) NOT NULL DEFAULT '0' COMMENT 'KVOBJ-row-version',
  PRIMARY KEY (`accessToken`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='accessToken表';

insert into db_p2p.tb_config values('dbsql.ver',5) ON DUPLICATE KEY UPDATE v=5;