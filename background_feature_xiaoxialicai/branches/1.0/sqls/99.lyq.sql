USE db_p2p;

ALTER TABLE `tb_vouchers_0`
CHANGE COLUMN `exp` `exp1`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '使用说明' AFTER `limitsType`,
ADD COLUMN `exp2`  varchar(100) NOT NULL DEFAULT '' COMMENT '使用说明' AFTER `exp1`;

ALTER TABLE `tb_vouchers_1`
CHANGE COLUMN `exp` `exp1`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '使用说明' AFTER `limitsType`,
ADD COLUMN `exp2`  varchar(100) NOT NULL DEFAULT '' COMMENT '使用说明' AFTER `exp1`;

insert into tb_config set k='dbsql.ver',v='99-lyq' ON DUPLICATE KEY UPDATE v='99-lyq';