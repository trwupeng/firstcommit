use db_p2p;

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for tb_medal
-- ----------------------------
DROP TABLE IF EXISTS `tb_medal`;
CREATE TABLE `tb_medal` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `key` varchar(50) DEFAULT NULL COMMENT '勋章英文字段',
  `name` varchar(100) DEFAULT NULL COMMENT '勋章名称',
  `icon` varchar(300) DEFAULT NULL COMMENT '勋章Icon',
  `serialNumber` mediumint(8) DEFAULT '0' COMMENT '排序',
  `description` varchar(2000) DEFAULT NULL COMMENT '勋章描述',
  `task` varchar(2000) DEFAULT NULL COMMENT '勋章任务',
  `taskLevel` text COMMENT '任务等级',
  `taskUnit` varchar(200) DEFAULT NULL COMMENT '数字对应的单位',
  `reward` text COMMENT '奖励数量',
  `rewardName` varchar(200) DEFAULT NULL COMMENT '奖励类型',
  `rewardUnit` varchar(200) DEFAULT NULL COMMENT '奖励金额单位',
  `rewardNumUnit` varchar(200) DEFAULT NULL COMMENT '奖励数量单位',
  `status` tinyint(1) DEFAULT '0' COMMENT '状态(0.关闭 1.开启)',
  `createTime` int(10) DEFAULT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='勋章表';

-- ----------------------------
-- Table structure for tb_user_medal
-- ----------------------------
DROP TABLE IF EXISTS `tb_user_medal`;
CREATE TABLE `tb_user_medal` (
  `userId` bigint(20) NOT NULL,
  `coverMedal` varchar(1000) DEFAULT NULL COMMENT '预留',
  `medals` text COMMENT '用户勋章信息',
  PRIMARY KEY (`userId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户勋章信息表';


insert into db_p2p.tb_config (`k`, `v`, `intro`) values ('SHARE_MEDAL_PIC', '', '#勋章分享图片');
insert into db_p2p.tb_config (`k`, `v`, `intro`) values ('SHARE_MEDAL_TITLE', '', '#勋章分享标题');
insert into db_p2p.tb_config (`k`, `v`, `intro`) values ('SHARE_MEDAL_DESC', '', '#勋章分享内容');
insert into db_p2p.tb_config (`k`, `v`, `intro`) values ('SHARE_MEDAL_URL', '', '#勋章分享链接');

alter table tb_investment_0 modify column vouchers varchar(1000) ;
alter table tb_investment_1 modify column vouchers varchar(1000) ;

ALTER TABLE `tb_vouchers_0` ADD COLUMN `voucherDesc`  varchar(1000) DEFAULT NULL COMMENT '券描述';
ALTER TABLE `tb_vouchers_1` ADD COLUMN `voucherDesc`  varchar(1000) DEFAULT NULL COMMENT '券描述';

use db_p2prpt;

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for tb_medal_final
-- ----------------------------
DROP TABLE IF EXISTS `tb_medal_final`;
CREATE TABLE `tb_medal_final` (
  `medalKey` varchar(255) NOT NULL COMMENT '勋章英文',
  `medalName` varchar(255) NOT NULL COMMENT '勋章名称',
  `medalReport` text COMMENT '勋章统计',
  PRIMARY KEY (`medalKey`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='勋章统计报表';
