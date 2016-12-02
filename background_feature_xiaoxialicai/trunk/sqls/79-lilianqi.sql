USE db_p2p;

alter table tb_clientPatch add copartnerId int(11) DEFAULT NULL after autoid;

insert into db_kkrpt.tb_config set k='dbsql.ver',v='79.lilianqi' ON DUPLICATE KEY UPDATE v='79.lilianqi';