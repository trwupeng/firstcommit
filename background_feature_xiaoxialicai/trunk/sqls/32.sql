-- 锁定的字段太短了 
alter table db_p2p.tb_wares_0 add realRaise BIGINT not null DEFAULT 0 COMMENT '实际募集总额' after remain ;
alter table db_p2p.tb_investment_0 modify vouchers VARCHAR(300) DEFAULT 0 COMMENT '使用券';
alter table db_p2p.tb_user_0 modify sLockData VARCHAR(200) DEFAULT '' ;
alter table db_p2p.tb_wares_0 modify sLockData VARCHAR(200) DEFAULT '' ;

insert into db_p2p.tb_config values('dbsql.ver',32) ON DUPLICATE KEY UPDATE v=32;
