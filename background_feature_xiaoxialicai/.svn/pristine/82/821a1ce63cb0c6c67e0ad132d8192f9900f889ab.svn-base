/*
添加标的模板字段
*/
alter TABLE db_p2p.tb_wares_0 add viewTPL VARCHAR(10) not null  DEFAULT 'Std01' COMMENT '标的模板字段'   after ymdPayPlan ;

insert into db_p2p.tb_config values('dbsql.ver',15) ON DUPLICATE KEY UPDATE v=15;