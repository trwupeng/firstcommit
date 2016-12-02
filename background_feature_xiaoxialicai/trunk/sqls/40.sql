-- - 增加协议管理表
use `db_p2p`;
CREATE TABLE `tb_agreement_0` (
`verId`  bigint(20) UNSIGNED NOT NULL COMMENT '协议唯一ID' ,
`verType`  tinyint(4) UNSIGNED NOT NULL COMMENT '协议类型：如注册协议、购买协议' ,
`verTpl`  tinyint(4) UNSIGNED NOT NULL COMMENT '显示模版' ,
`userId`  bigint(20) UNSIGNED NOT NULL COMMENT '创建者ID' ,
`title`  char(80) NOT NULL COMMENT '标题' ,
`content`  text NOT NULL COMMENT '内容' ,
`createTime`  int(11) UNSIGNED NOT NULL COMMENT '创建时间' ,
`iRecordVerID` int(11) NOT NULL DEFAULT '0' COMMENT 'iRecordVerID',
`status`  tinyint(4) UNSIGNED NOT NULL DEFAULT 0 COMMENT '状态位：正在使用为1，未在使用为0' ,
PRIMARY KEY (`verId`)
)
ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COMMENT='协议管理' CHECKSUM=0 DELAY_KEY_WRITE=0;

ALTER TABLE db_p2p.tb_user_0
MODIFY COLUMN `redPacket`  int(11) NULL DEFAULT 0 COMMENT '未使用红包余额 单位分' AFTER `interestTotal`,
MODIFY COLUMN `redPacketUsed`  int(11) UNSIGNED NULL DEFAULT 0 COMMENT '已使用的红包额度 单位分' AFTER `redPacket`;

insert into db_p2p.tb_config values('dbsql.ver',40) ON DUPLICATE KEY UPDATE v=40;