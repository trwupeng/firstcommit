USE db_p2p;

ALTER TABLE `tb_user_0`
ADD COLUMN `rebating`  int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '待返金额' AFTER `idCard`;

insert into tb_config set k='dbsql.ver',v='90-lyq' ON DUPLICATE KEY UPDATE v='90-lyq';