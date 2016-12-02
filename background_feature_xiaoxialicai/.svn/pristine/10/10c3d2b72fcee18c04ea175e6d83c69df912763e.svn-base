USE db_p2p;

CREATE TABLE `tb_user_idcard_0` (
  `id` varchar(18) NOT NULL,
  `statusCode` tinyint(4) DEFAULT '0' COMMENT '1有效   -1无效',
  `iRecordVerID` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='身份证表';

ALTER TABLE `tb_calendar`
MODIFY COLUMN `workday`  tinyint(1) NOT NULL DEFAULT b'0' COMMENT '是否工作日' AFTER `Ymd`;

ALTER TABLE `tb_rebate_0`
ADD COLUMN `sLockData`  varchar(200) NULL DEFAULT '' AFTER `iRecordVerID`;

ALTER TABLE `tb_rebate_1`
ADD COLUMN `sLockData`  varchar(200) NULL DEFAULT '' AFTER `iRecordVerID`;

insert into db_p2p.tb_config set k='dbsql.ver',v='116-tgh' ON DUPLICATE KEY UPDATE v='116-tgh';