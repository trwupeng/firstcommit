USE `db_p2prpt`;
-- ----------------------------
-- Table structure for tb_evtdaily
-- ----------------------------
DROP TABLE IF EXISTS `tb_evtdaily`;
CREATE TABLE `tb_evtdaily` (
  `ymd` int(255) NOT NULL,
  `act` varchar(32) NOT NULL,
  `copartnerId` int(11) NOT NULL DEFAULT '0',
  `clienttype` int(255) NOT NULL DEFAULT '0',
  `contractid` bigint(255) NOT NULL DEFAULT '0',
  `flgext01` bigint(255) NOT NULL DEFAULT '0',
  `n` bigint(255) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ymd`,`act`,`clienttype`,`contractid`,`flgext01`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
insert into db_p2p.tb_config values('dbsql.ver',36) ON DUPLICATE KEY UPDATE v=36;