USE db_p2p;

alter table tb_clientPatch add ver1 int(11) NOT NULL DEFAULT 0 after ver;
alter table tb_clientPatch add ver2 int(11) NOT NULL DEFAULT 0 after ver1;
alter table tb_clientPatch add ver3 int(11) NOT NULL DEFAULT 0 after ver2;
alter table tb_clientPatch add ver4 int(11) NOT NULL DEFAULT 0 after ver3;

insert into db_kkrpt.tb_config set k='dbsql.ver',v='80-lilianqi' ON DUPLICATE KEY UPDATE v='80-lilianqi';