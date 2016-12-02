-- - voucher 增加字段-解释说明
ALTER TABLE db_p2p.tb_vouchers_0
ADD COLUMN `exp`  varchar(255) NOT NULL COMMENT '解释说明' AFTER `dtExpired`;

insert into db_p2p.tb_config values('dbsql.ver',45) ON DUPLICATE KEY UPDATE v=45;