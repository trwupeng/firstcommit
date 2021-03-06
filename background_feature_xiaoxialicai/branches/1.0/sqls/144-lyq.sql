USE db_logs;

CREATE TABLE `tb_regevt` (
	`ip` VARCHAR (16) NULL DEFAULT '' COMMENT 'IP地址',
	`sessionId` VARCHAR (64) NULL DEFAULT '' COMMENT 'sessionId',
	`ymd` INT (11) UNSIGNED NULL DEFAULT 0 COMMENT '年月日',
	`his` INT (11) UNSIGNED NULL DEFAULT 0 COMMENT '时分秒',
	`a` TINYINT (4) UNSIGNED NULL DEFAULT 0 COMMENT '手机输入长度',
	`b` TINYINT (4) UNSIGNED NULL DEFAULT 0 COMMENT '密码输入长度',
	`c` TINYINT (4) UNSIGNED NULL DEFAULT 0 COMMENT '短信验证码输入长度',
	`reged` TINYINT (4) UNSIGNED NULL DEFAULT 0 COMMENT '是否注册：0未注册；1注册',
	`sendcode` TINYINT (4) UNSIGNED NULL DEFAULT 0 COMMENT '是否获取验证码：0未获取；1已获取',
	`lt` VARCHAR (16) NULL DEFAULT '' COMMENT '最后焦点的输入框',
	`source` VARCHAR (60) NULL DEFAULT '' COMMENT '渠道号',
	`channel` INT (255) UNSIGNED NULL DEFAULT 0 COMMENT '前端类型',
	`_post`  blob NULL,
  `_cookie`  blob NULL,
  `_get`  blob NULL
) ENGINE = MyISAM DEFAULT CHARACTER
SET = utf8 CHECKSUM = 0 DELAY_KEY_WRITE = 0;

INSERT INTO db_p2p.tb_config
SET k = 'dbsql.ver',
 v = '144-lyq' ON DUPLICATE KEY UPDATE v = '144-lyq';