USE db_p2p;

CREATE TABLE `tb_chargeback_0` (
`orderId`  bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '订单ID' ,
`userId`  bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户ID' ,
`amount`  bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '扣款金额' ,
`reason`  varchar(255) NOT NULL DEFAULT '' COMMENT '扣款原因' ,
`sn`  varchar(40) NOT NULL DEFAULT '' COMMENT '网关处理的序列号' ,
`serviceRet`  varchar(255) NOT NULL DEFAULT '' COMMENT '网关处理的消息' ,
`serviceCode`  varchar(40) NOT NULL DEFAULT '' COMMENT '网关处理的状态' ,
`retryMsg`  varchar(1000) NOT NULL DEFAULT '' COMMENT '每次重试的结果' ,
`createTime`  bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间' ,
`transTime`  bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '网关处理时间' ,
`status`  tinyint(4) NOT NULL DEFAULT 1 COMMENT '状态：\r\n1 本地记录；\r\n4 已提交网关\r\n8 网关处理完成\r\n' ,
`iRecordVerID`  int(11) NOT NULL DEFAULT 0 COMMENT 'iRecordVerID' ,
PRIMARY KEY (`orderId`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8
COMMENT='扣款记录表';

CREATE TABLE `tb_chargeback_1` (
`orderId`  bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '订单ID' ,
`userId`  bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户ID' ,
`amount`  bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '扣款金额' ,
`reason`  varchar(255) NOT NULL DEFAULT '' COMMENT '扣款原因' ,
`sn`  varchar(40) NOT NULL DEFAULT '' COMMENT '网关处理的序列号' ,
`serviceRet`  varchar(255) NOT NULL DEFAULT '' COMMENT '网关处理的消息' ,
`serviceCode`  varchar(40) NOT NULL DEFAULT '' COMMENT '网关处理的状态' ,
`retryMsg`  varchar(1000) NOT NULL DEFAULT '' COMMENT '每次重试的结果' ,
`createTime`  bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间' ,
`transTime`  bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '网关处理时间' ,
`status`  tinyint(4) NOT NULL DEFAULT 1 COMMENT '状态：\r\n1 本地记录；\r\n4 已提交网关\r\n8 网关处理完成\r\n' ,
`iRecordVerID`  int(11) NOT NULL DEFAULT 0 COMMENT 'iRecordVerID' ,
PRIMARY KEY (`orderId`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8
COMMENT='扣款记录表';


insert into db_p2p.tb_config set k='dbsql.ver',v='153-lyq' ON DUPLICATE KEY UPDATE v='153-lyq';