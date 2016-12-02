use db_p2p;
ALTER TABLE tb_contract_0 ADD COLUMN promotionWay varchar(15) NOT NULL DEFAULT '' COMMENT '推广方式';

INSERT INTO db_p2p.tb_config VALUES  ('dbsql.ver', 57) ON DUPLICATE KEY UPDATE v = 57;