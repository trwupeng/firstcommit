-- 期限+单位
ALTER table db_p2p.tb_wares_0 add COLUMN deadLine SMALLINT not null DEFAULT 360 COMMENT '期限' after waresSN;
ALTER table db_p2p.tb_wares_0 add COLUMN dlUnit varchar(10) null DEFAULT '天' COMMENT '期限单位' after deadLine;

insert into db_p2p.tb_config values('dbsql.ver',34) ON DUPLICATE KEY UPDATE v=34;