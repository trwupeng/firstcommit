use db_p2p;

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for tb_agreement_0
-- ----------------------------
DROP TABLE IF EXISTS `tb_agreement2_0`;
CREATE TABLE `tb_agreement2_0` (
  `verName` varchar(20) NOT NULL COMMENT '议协用途',
  `verId` bigint(20) unsigned NOT NULL COMMENT '协议版本号',
  `verTpl` tinyint(4) unsigned NOT NULL COMMENT '显示模版（保留）',
  `userId` varchar(36) NOT NULL COMMENT '更新者id',
  `userName` varchar(36) NOT NULL COMMENT '更新者昵称',
  `content` text NOT NULL COMMENT '内容',
  `createTime` int(11) unsigned NOT NULL COMMENT '创建时间',
  `iRecordVerID` int(11) NOT NULL DEFAULT '0' COMMENT 'iRecordVerID',
  `status` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '状态位：正在使用为1，未在使用为0',
  PRIMARY KEY (`verName`,`verId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='协议管理审核版本';

insert into db_p2p.tb_config (`k`, `v`, `intro`, `iRecordVerID`) values ('APP_PRO_VERSION', '\/^[1]\\.[5]\\.\\d+\\.\\d+$\/', '#app的pro版本正则', 50);
insert into db_p2p.tb_config (`k`, `v`, `intro`, `iRecordVerID`) values ('APP_PRO_CONTRACT_ID', '900220160607110000', '#app的pro版本协议id', 50);