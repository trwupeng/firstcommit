-- -----------------------------------------------
-- update tb_loginname
-- -----------------------------------------------
DROP TABLE IF EXISTS `tb_sms_valid_1`;
CREATE TABLE `tb_sms_valid_1` (
  `phone` bigint(20) NOT NULL,
  `dat` varchar(500) NOT NULL,
  `iRecordVerID` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`phone`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

ALTER TABLE `tb_accounts_0`
MODIFY COLUMN `regClient`  smallint(4) NOT NULL DEFAULT 0 COMMENT '注册客户端' AFTER `regHHiiss`;

insert into db_p2p.tb_config values('dbsql.ver',4) ON DUPLICATE KEY UPDATE v=4;