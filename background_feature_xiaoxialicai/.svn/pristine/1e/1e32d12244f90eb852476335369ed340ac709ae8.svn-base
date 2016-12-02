USE db_p2p;

ALTER TABLE `tb_wares_0`
ADD COLUMN `payGift`  bigint NOT NULL DEFAULT 0 COMMENT '平台垫付' AFTER `payStatus`,
ADD COLUMN `repay`  bigint NOT NULL DEFAULT 0 COMMENT '企业还钱' AFTER `payGift`;

CREATE TABLE `tb_systally_0` (
  `sn` bigint(20) NOT NULL DEFAULT '0' COMMENT '流水号',
  `amount` bigint(20) NOT NULL DEFAULT '0' COMMENT '金额',
  `userId` bigint(20) NOT NULL DEFAULT '0' COMMENT '用户ID/借款人ID',
  `waresId` bigint(20) NOT NULL DEFAULT '0' COMMENT '标的ID',
  `type` smallint(6) NOT NULL DEFAULT '0' COMMENT '类型',
  `statusCode` smallint(6) NOT NULL DEFAULT '0' COMMENT '状态',
  `tallyYmd` bigint(20) NOT NULL DEFAULT '0',
  `exp` varchar(300) NOT NULL DEFAULT '' COMMENT '备注',
  `iRecordVerID` int(11) NOT NULL,
  PRIMARY KEY (`sn`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='平台流水表\r\n';




insert into tb_config set k='dbsql.ver',v='84-tgh' ON DUPLICATE KEY UPDATE v='84-tgh';