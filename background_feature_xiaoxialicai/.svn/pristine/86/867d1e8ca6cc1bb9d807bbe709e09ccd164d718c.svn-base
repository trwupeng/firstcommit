USE db_p2p;


ALTER TABLE `tb_user_idcard_0`
ADD COLUMN `statusCode`  tinyint NOT NULL DEFAULT 0 COMMENT '状态 :   -1 无效      0有效' AFTER `iRecordVerID`;




insert into db_p2p.tb_config set k='dbsql.ver',v='146-tgh' ON DUPLICATE KEY UPDATE v='146-tgh';