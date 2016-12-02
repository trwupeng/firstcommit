/*
订单表修改两个字段 定固年化收益率/定固年化收益率上浮 保留两位小数
*/
ALTER TABLE db_p2p.tb_investment_0 modify yieldStatic decimal(10,2) DEFAULT 0.00 not null COMMENT '定固年化收益率';
ALTER TABLE db_p2p.tb_investment_0 modify yieldStaticAdd decimal(10,2) DEFAULT 0.00 not null COMMENT '定固年化收益率上浮';
insert into db_p2p.tb_config values('dbsql.ver',21) ON DUPLICATE KEY UPDATE v=21;
