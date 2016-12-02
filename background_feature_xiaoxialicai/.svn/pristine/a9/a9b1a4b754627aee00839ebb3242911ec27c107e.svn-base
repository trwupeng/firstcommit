USE db_p2p;

ALTER TABLE `tb_systally_0`
MODIFY COLUMN `type`  smallint(6) NOT NULL DEFAULT 0 COMMENT '100:还款垫付/300:返利/400:转账垫付(红包)/500:转账服务费/600:还款手续费/700:平台贴息\r\n' AFTER `waresId`;




insert into db_p2p.tb_config set k='dbsql.ver',v='136-tgh' ON DUPLICATE KEY UPDATE v='136-tgh';