/*
订单表添加两个字段 定固年化收益率/定固年化收益率上浮
*/
ALTER TABLE db_p2p.tb_investment_0 add yieldStatic decimal(10) DEFAULT 0.00 not null COMMENT '定固年化收益率' after amountFake;
ALTER TABLE db_p2p.tb_investment_0 add yieldStaticAdd decimal(10) DEFAULT 0.00 not null COMMENT '定固年化收益率上浮' after amountFake;
insert into db_p2p.tb_config values('dbsql.ver',18) ON DUPLICATE KEY UPDATE v=18;