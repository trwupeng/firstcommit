USE db_p2p;

ALTER TABLE `tb_vouchers_0`
ADD COLUMN `pid`  bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '父券ID，多用于字母红包等' AFTER `limitsDeadline`;

CREATE TABLE `tb_vouchers_interim_0` (
  `voucherId` bigint(20) unsigned NOT NULL COMMENT '券ID',
  `pid` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '父ID',
  `voucherType` varchar(16) NOT NULL DEFAULT '' COMMENT '券类型',
  `amount` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '金额',
  `timeCreate` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `dtExpired` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '有效期',
  `isUsed` tinyint(2) unsigned NOT NULL DEFAULT '1' COMMENT '是否已使用：1未使用，2已使用',
  `timeUsed` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '使用时间，领取时间',
  `isLock` tinyint(2) NOT NULL DEFAULT '1' COMMENT '是否被锁：1未被锁，2被锁',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '状态位：1可领取，2已领取',
  `iRecordVerID` int(11) NOT NULL DEFAULT '0' COMMENT 'iRecordVerID',
  PRIMARY KEY (`voucherId`),
  KEY `pid` (`pid`),
  KEY `isUsed` (`isUsed`) USING BTREE,
  KEY `isLock` (`isLock`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='子红包-临时表';

insert into tb_config set k='dbsql.ver',v='82-lyq' ON DUPLICATE KEY UPDATE v='82-lyq';