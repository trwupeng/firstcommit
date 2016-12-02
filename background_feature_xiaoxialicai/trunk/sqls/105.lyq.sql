USE db_p2p;

ALTER TABLE `tb_vouchers_0`
ADD COLUMN `uniqueId`  varchar(50) NOT NULL DEFAULT '' COMMENT '区别与userId的唯一用户标识ID，用于分享券' AFTER `pid`,
ADD COLUMN `createYmd`  bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建年月日' AFTER `uniqueId`;

ALTER TABLE `tb_vouchers_1`
ADD COLUMN `uniqueId`  varchar(50) NOT NULL DEFAULT '' COMMENT '区别与userId的唯一用户标识ID，用于分享券' AFTER `pid`,
ADD COLUMN `createYmd`  bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建年月日' AFTER `uniqueId`;

insert into tb_config set k='dbsql.ver',v='105-lyq' ON DUPLICATE KEY UPDATE v='105-lyq';