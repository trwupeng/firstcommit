alter table db_p2p.tb_wares_0 add introDisplay text COMMENT '产品介绍';
drop table db_p2p.tb_users_0;

alter table db_p2p.tb_vouchers_0 drop limits;
alter table db_p2p.tb_vouchers_0 add limitsShelf varchar(20) not null default '' COMMENT '类型限制';
alter table db_p2p.tb_vouchers_0 add limitsType varchar(20) not null default '' COMMENT '类型限制';
alter table db_p2p.tb_vouchers_0 add limitsTag varchar(20) not null default '' COMMENT '标签限制';

alter table db_p2p.tb_wares_0 drop licences;

insert into db_p2p.tb_config values('dbsql.ver',9) ON DUPLICATE KEY UPDATE v=9;