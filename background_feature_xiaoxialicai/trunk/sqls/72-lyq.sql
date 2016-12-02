USE db_p2p;

ALTER TABLE `tb_user_0` ADD COLUMN `failedForbidden` VARCHAR (255) NOT NULL DEFAULT '' COMMENT '用户支付密码的限制json：[\'forbidden\' => 是否锁定, \'forbiddenExpires\' => 锁定时间, \'errorExpires\' => 错误次数时间, \'errorCount\' => 错误次数]' AFTER `salt`;

INSERT INTO db_p2p.tb_config
VALUES
	('dbsql.ver', 72) ON DUPLICATE KEY UPDATE v = 72;