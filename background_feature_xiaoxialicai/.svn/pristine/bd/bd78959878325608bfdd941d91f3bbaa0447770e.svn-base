USE db_p2p;

alter table db_p2p.tb_user_1 add exchangecodegrp varchar(501) not null default '_' COMMENT '兑换码领取情况';
	alter table db_p2p.tb_user_0 add exchangecodegrp varchar(501) not null default '_' COMMENT '兑换码领取情况';


	CREATE TABLE `tb_exchangecodes_0` (
	  `excode` varchar(8) NOT NULL DEFAULT '' COMMENT '兑换码',
	  `grpId` varchar(10) NOT NULL DEFAULT '' COMMENT '分组id',
	  `batchId` char(4) NOT NULL DEFAULT '' COMMENT '批次id',
	  `dtExpire` int(11) NOT NULL DEFAULT '0' COMMENT '过期时间',
	  `userId` varchar(36) NOT NULL DEFAULT '0' COMMENT '领奖的用户id',
	  `dtFetch` int(11) NOT NULL DEFAULT '0' COMMENT '领取时间',
	  `ordersId` varchar(36) NOT NULL DEFAULT '0' COMMENT '领奖的订单id',
	  `iRecordVerID` int(11) NOT NULL COMMENT 'iRecordVerID',
	  PRIMARY KEY (`excode`),
	  KEY `ifused2` (`grpId`,`batchId`,`userId`),
	  KEY `ifused1` (`grpId`,`userId`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8;

	CREATE TABLE `tb_exchangecodes_1` (
	  `excode` varchar(8) NOT NULL DEFAULT '' COMMENT '兑换码',
	  `grpId` varchar(10) NOT NULL DEFAULT '' COMMENT '分组id',
	  `batchId` char(4) NOT NULL DEFAULT '' COMMENT '批次id',
	  `dtExpire` int(11) NOT NULL DEFAULT '0' COMMENT '过期时间',
	  `userId` varchar(36) NOT NULL DEFAULT '0' COMMENT '领奖的用户id',
	  `dtFetch` int(11) NOT NULL DEFAULT '0' COMMENT '领取时间',
	  `ordersId` varchar(36) NOT NULL DEFAULT '0' COMMENT '领奖的订单id',
	  `iRecordVerID` int(11) NOT NULL COMMENT 'iRecordVerID',
	  PRIMARY KEY (`excode`),
	  KEY `ifused2` (`grpId`,`batchId`,`userId`),
	  KEY `ifused1` (`grpId`,`userId`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8;


	create table if not exists db_p2p.tb_exchangecodes_grp(
	grpId varchar(10) not null default '' COMMENT '分组id',
	batchId char(4) not null default '' COMMENT '批次id',
	dtAddGrp int(11) not null default 0 COMMENT '分组创建时间',
	dtAddBatch int(11) not null default 0 COMMENT '批次创建时间', 
	intro varchar(500) not null default '' COMMENT '说明',
	bonusini varchar(200) not null default '' COMMENT '奖励',
	dtExpire int(11) not null default 0 COMMENT '过期时间',
	batchNum mediumint(8) not null default 0 COMMENT '批次兑换码数量',
	useNum	mediumint(8) not null default 0 COMMENT '批次兑换码已兑换数量',
	primary key (grpId, batchId)
	)ENGINE=MyIsam DEFAULT CHARSET=utf8;


insert into db_p2p.tb_config set k='dbsql.ver',v='106-jzc' ON DUPLICATE KEY UPDATE v='106-jzc';