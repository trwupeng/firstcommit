use db_p2p;
ALTER TABLE `tb_session_0` add lastUpdate int not null default 0;
ALTER TABLE `tb_session_1` add lastUpdate int not null default 0;

INSERT INTO db_p2p.tb_config VALUES  ('dbsql.ver', 54) ON DUPLICATE KEY UPDATE v = 54;