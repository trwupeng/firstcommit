USE db_p2p;

alter table tb_config change `desc` intro varchar(255) not null default '' COMMENT '描述';
alter table tb_config_ram change `desc` intro varchar(255) not null default '' COMMENT '描述';


insert into tb_config set k='dbsql.ver',v='88-wn' ON DUPLICATE KEY UPDATE v='88-wn';