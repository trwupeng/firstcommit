USE `db_p2p`;

DROP TABLE IF EXISTS `tb_checkin_0`;
CREATE TABLE `tb_checkin_0` (
`userId`  bigint(20) UNSIGNED NOT NULL COMMENT '用户ID' ,
`ymd`  int(11) UNSIGNED NOT NULL COMMENT '签到日期' ,
`total`  int(11) UNSIGNED ZEROFILL NOT NULL COMMENT '总签到次数' ,
`number`  int(11) UNSIGNED NOT NULL COMMENT '当前签到次数' ,
`date`  int(11) UNSIGNED NOT NULL COMMENT '具体签到时间' ,
`iRecordVerID`  int(11) NOT NULL COMMENT 'iRecordVerID' ,
`bonus`  varchar(255) NULL COMMENT '奖励' ,
PRIMARY KEY (`userId`),
INDEX USING BTREE (`ymd`) 
)DEFAULT CHARACTER SET=utf8 COMMENT='用户签到表';

DROP TABLE IF EXISTS `tb_checkin_1`;
CREATE TABLE `tb_checkin_1` (
`userId`  bigint(20) UNSIGNED NOT NULL COMMENT '用户ID' ,
`ymd`  int(11) UNSIGNED NOT NULL COMMENT '签到日期' ,
`total`  int(11) UNSIGNED ZEROFILL NOT NULL COMMENT '总签到次数' ,
`number`  int(11) UNSIGNED NOT NULL COMMENT '当前签到次数' ,
`date`  int(11) UNSIGNED NOT NULL COMMENT '具体签到时间' ,
`iRecordVerID`  int(11) NOT NULL COMMENT 'iRecordVerID' ,
`bonus`  varchar(255) NULL COMMENT '奖励' ,
PRIMARY KEY (`userId`),
INDEX USING BTREE (`ymd`) 
)DEFAULT CHARACTER SET=utf8 COMMENT='用户签到表';


insert into db_p2p.tb_config values('dbsql.ver',28) ON DUPLICATE KEY UPDATE v=28;
