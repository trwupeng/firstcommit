use db_p2p;
alter table db_p2p.tb_clientPatch add full tinyint not null default 1 COMMENT '是否完整包';

INSERT INTO db_p2p.tb_config VALUES  ('dbsql.ver', 55) ON DUPLICATE KEY UPDATE v = 55;