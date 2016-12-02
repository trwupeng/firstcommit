USE db_p2p;

ALTER TABLE `tb_user_0`
MODIFY COLUMN `wallet`  bigint(20) NOT NULL DEFAULT 0 COMMENT '钱包余额 单位分' AFTER `failedForbidden`,
MODIFY COLUMN `redPacket`  int(11) NOT NULL DEFAULT 0 COMMENT '未使用红包余额 单位分' AFTER `interestTotal`,
MODIFY COLUMN `redPacketUsed`  int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '已使用的红包额度 单位分' AFTER `redPacket`,
ADD COLUMN `redPacketRecentlyExpired`  bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '红包的最近过期时间' AFTER `redPacketUsed`;

ALTER TABLE `tb_user_1`
MODIFY COLUMN `wallet`  bigint(20) NOT NULL DEFAULT 0 COMMENT '钱包余额 单位分' AFTER `failedForbidden`,
MODIFY COLUMN `redPacket`  int(11) NOT NULL DEFAULT 0 COMMENT '未使用红包余额 单位分' AFTER `interestTotal`,
MODIFY COLUMN `redPacketUsed`  int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '已使用的红包额度 单位分' AFTER `redPacket`,
ADD COLUMN `redPacketRecentlyExpired`  bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '红包的最近过期时间' AFTER `redPacketUsed`;

insert into db_p2p.tb_config set k='dbsql.ver',v='138-lyq' ON DUPLICATE KEY UPDATE v='138-lyq';