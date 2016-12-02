use db_p2p;

INSERT INTO `tb_config` (`k`, `v` , `intro`) VALUES ('company_account', '{"第三方企业户":"456789"}' , '#企业户ID#');
INSERT INTO `tb_config_ram` (`k`, `v` , `intro`) VALUES ('company_account', '{"第三方企业户":"456789"}' , '#企业户ID#');

insert into db_p2p.tb_config set k='dbsql.ver',v='166-tgh' ON DUPLICATE KEY UPDATE v='166-tgh';