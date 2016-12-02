USE db_p2p;
ALTER TABLE `tb_clientPatch`
ADD COLUMN `contractId`  bigint(20) NOT NULL DEFAULT '0' COMMENT '议协ID' AFTER `copartnerId`;
insert into db_p2p.tb_config set k='dbsql.ver',v='147-lilianqi' ON DUPLICATE KEY UPDATE v='147-lilianqi';