USE db_p2p;

ALTER TABLE `tb_user_idcard_0`
CHANGE COLUMN `statusCode` `userId`  bigint NULL DEFAULT 0 COMMENT '用户ID' AFTER `id`;


insert into db_p2p.tb_config set k='dbsql.ver',v='117-tgh' ON DUPLICATE KEY UPDATE v='117-tgh';