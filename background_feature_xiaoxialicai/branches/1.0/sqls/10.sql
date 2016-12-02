/*
Navicat MySQL Data Transfer

Source Server         : 虚拟机
Source Server Version : 50173
Source Host           : 192.168.56.140:3306
Source Database       : db_p2p

Target Server Type    : MYSQL
Target Server Version : 50173
File Encoding         : 65001

Date: 2015-10-13 10:34:50
*/

SET FOREIGN_KEY_CHECKS=0;



-- ----------------------------
-- Table structure for `tb_managers_0`
-- ----------------------------
DROP TABLE IF EXISTS `tb_managers_0`;
CREATE TABLE `tb_managers_0` (
  `cameFrom` varchar(36) NOT NULL,
  `loginName` varchar(36) NOT NULL,
  `nickname` varchar(36) DEFAULT NULL,
  `passwd` varchar(36) DEFAULT NULL,
  `passwdSalt` varchar(36) DEFAULT NULL,
  `regYmd` int(255) NOT NULL DEFAULT '0',
  `regIP` varchar(16) NOT NULL DEFAULT '0.0.0.0',
  `rights` varchar(2000) NOT NULL DEFAULT '' COMMENT '权限',
  `lastIP` varchar(16) NOT NULL DEFAULT '0.0.0.0',
  `lastYmd` int(11) NOT NULL DEFAULT '0',
  `lastHis` int(11) NOT NULL DEFAULT '0',
  `iRecordVerID` int(20) NOT NULL DEFAULT '0',
  `sLockData` varchar(100) NOT NULL DEFAULT '',
  `dtForbidden` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`cameFrom`,`loginName`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of tb_managers_0
-- ----------------------------
INSERT INTO `tb_managers_0` VALUES ('local', 'root', 'ytest123', '123456', '', '0', '0.0.0.0', '', '0.0.0.0', '0', '0', '101', '', '0');
INSERT INTO `tb_managers_0` VALUES ('local', 'root1', 'test12', 'mr89DAl2', ' ', '0', '0.0.0.0', '', '0.0.0.0', '0', '0', '6', '', '0');
INSERT INTO `tb_managers_0` VALUES ('local', 'root2', 'dsad2', '0Vq93fk0', null, '0', '0.0.0.0', '', '0.0.0.0', '0', '0', '3', '', '0');
INSERT INTO `tb_managers_0` VALUES ('local', 'tgh8', '123000', '123456', null, '0', '0.0.0.0', '', '0.0.0.0', '0', '0', '1', '', null);
INSERT INTO `tb_managers_0` VALUES ('local', 'tgh1', '12355', '50XW177J', null, '0', '0.0.0.0', '', '0.0.0.0', '0', '0', '1', '', null);
INSERT INTO `tb_managers_0` VALUES ('local', 'tgh3', '123000', '0Vq93fk0', null, '0', '0.0.0.0', '', '0.0.0.0', '0', '0', '1', '', null);
INSERT INTO `tb_managers_0` VALUES ('local', 'tgh4', '122545', '0Vq93fk0', null, '0', '0.0.0.0', '', '0.0.0.0', '0', '0', '1', '', null);
INSERT INTO `tb_managers_0` VALUES ('local', 'tgh5', '12344', '0Vq93fk0', null, '0', '0.0.0.0', '', '0.0.0.0', '0', '0', '1', '', null);
INSERT INTO `tb_managers_0` VALUES ('local', 'tgh6', '123888', '0Vq93fk0', null, '0', '0.0.0.0', '', '0.0.0.0', '0', '0', '1', '', null);
INSERT INTO `tb_managers_0` VALUES ('local', 'tgh7', '123456', '0Vq93fk0', null, '0', '0.0.0.0', '', '0.0.0.0', '0', '0', '1', '', null);
INSERT INTO `tb_managers_0` VALUES ('local', 'tgh', 'dsad', '0Vq93fk0', null, '0', '0.0.0.0', '', '0.0.0.0', '0', '0', '1', '', null);

insert into db_p2p.tb_config values('dbsql.ver',10) ON DUPLICATE KEY UPDATE v=10;