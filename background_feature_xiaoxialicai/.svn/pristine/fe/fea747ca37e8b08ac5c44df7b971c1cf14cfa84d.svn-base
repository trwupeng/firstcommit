use db_oauth;
CREATE TABLE `tb_tongdun_0` (
`loginName`  varchar(36) NOT NULL DEFAULT '' COMMENT '登录名' ,
`loginType`  varchar(36) NOT NULL DEFAULT '' COMMENT '登录类型' ,
`timeCreate`  bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建日期' ,
`iRecordVerID`  int(11) NOT NULL DEFAULT 0 ,
`json`  varchar(3000) NOT NULL DEFAULT '' COMMENT '完整结果' ,
`final_decision`  varchar(36) NOT NULL DEFAULT '' COMMENT '最终的风险评估结果，取所有策略中分数最高的结果' ,
`final_score`  tinyint(4) NOT NULL DEFAULT -1 COMMENT '最终的风险系数' ,
`seq_id`  bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '本次调用的请求id，用于事后反查事件' ,
`remarks`  varchar(255) NOT NULL ,
`extJson`  text NOT NULL COMMENT '扩展string，用于保存其他次的结果' ,
PRIMARY KEY (`loginName`, `loginType`),
INDEX (`seq_id`)
)
ENGINE=InnoDB
COMMENT='同盾请求记录表';

CREATE TABLE `tb_tongdun_1` (
`loginName`  varchar(36) NOT NULL DEFAULT '' COMMENT '登录名' ,
`loginType`  varchar(36) NOT NULL DEFAULT '' COMMENT '登录类型' ,
`timeCreate`  bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建日期' ,
`iRecordVerID`  int(11) NOT NULL DEFAULT 0 ,
`json`  varchar(3000) NOT NULL DEFAULT '' COMMENT '完整结果' ,
`final_decision`  varchar(36) NOT NULL DEFAULT '' COMMENT '最终的风险评估结果，取所有策略中分数最高的结果' ,
`final_score`  tinyint(4)  NOT NULL DEFAULT -1 COMMENT '最终的风险系数' ,
`seq_id`  bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '本次调用的请求id，用于事后反查事件' ,
`remarks`  varchar(255) NOT NULL ,
`extJson`  text NOT NULL COMMENT '扩展string，用于保存其他次的结果' ,
PRIMARY KEY (`loginName`, `loginType`),
INDEX (`seq_id`)
)
ENGINE=InnoDB
COMMENT='同盾请求记录表';

insert into db_p2p.tb_config set k='dbsql.ver',v='160-lyq' ON DUPLICATE KEY UPDATE v='160-lyq';