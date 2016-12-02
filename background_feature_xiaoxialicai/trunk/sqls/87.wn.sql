USE db_p2p;

alter table tb_config drop `group`;
alter table tb_config_ram drop `group`;

alter table tb_config add extlimit varchar(300) not null default '';
alter table tb_config_ram add extlimit varchar(300) not null default '';

insert into tb_config set k='dbsql.ver',v='87-wn' ON DUPLICATE KEY UPDATE v='87-wn';