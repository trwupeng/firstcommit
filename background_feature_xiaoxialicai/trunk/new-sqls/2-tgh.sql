use db_p2p;

INSERT INTO `tb_menu` (`id`, `mark`, `name`, `value`) VALUES ('90', '系统', '系统.银行配置', '[\"manage\",\"bank\",\"index\",[],[]]');

CREATE TABLE `tb_bank_config` (
  `bank` varchar(10) NOT NULL DEFAULT '' COMMENT '银行的key(如：boc)',
  `name` varchar(255) DEFAULT '' COMMENT '银行名字(如：中国银行)',
  `icon` varchar(255) DEFAULT '' COMMENT '图标路径',
  `chargeFirst` bigint(20) DEFAULT '0' COMMENT '首充限额(分)',
  `chargeSingle` bigint(20) DEFAULT '0' COMMENT '单笔充值限额(分)',
  `chargeDay` bigint(20) DEFAULT '0' COMMENT '日充值限额(分)',
  `withdawSingle` bigint(20) DEFAULT '0' COMMENT '单笔提现限额(分)',
  `withdawDay` bigint(20) DEFAULT '0' COMMENT '日提现限额(分)',
  `intercept` tinyint(4) DEFAULT '0' COMMENT '客户端是否拦截用户绑卡， 1 拦截， 0 不拦截',
  `chargeAtLeast` int(11) DEFAULT '0' COMMENT '最低充值额度(分)',
  `iRecordVerID` int(11) DEFAULT '0',
  PRIMARY KEY (`bank`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='银行配置表';

