-- - 意见反馈
USE `db_p2p`;

CREATE TABLE `tb_feedback_0` (
`feedbackId`  bigint(20) UNSIGNED NOT NULL COMMENT '反馈ID' ,
`userId`  bigint(20) UNSIGNED NOT NULL COMMENT '提交者ID' ,
`deviceId`  varchar(256) NOT NULL COMMENT '唯一设备ID' ,
`content`  text NOT NULL COMMENT '内容' ,
`createTime`  bigint(20) UNSIGNED NOT NULL COMMENT '创建时间' ,
`status`  tinyint(4) UNSIGNED NOT NULL COMMENT '状态位' ,
`iRecordVerID`  int(11) NOT NULL COMMENT 'iRecordVerID' ,
`extends`  varchar(255) NOT NULL COMMENT '扩展、其他' ,
PRIMARY KEY (`feedbackId`)
)
ENGINE=MyISAM
DEFAULT CHARACTER SET=utf8
COMMENT='意见反馈'
CHECKSUM=0;
insert into db_p2p.tb_config values('dbsql.ver',44) ON DUPLICATE KEY UPDATE v=44;