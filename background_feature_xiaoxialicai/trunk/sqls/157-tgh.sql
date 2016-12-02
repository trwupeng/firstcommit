use db_p2p;

INSERT INTO `tb_config` (`k`, `v`, `intro`) VALUES ('unbind_time', '[1000,2300]', '#自助解绑服务时间#[几点几分,几点几分]');

INSERT INTO `tb_config_ram` (`k`, `v`, `intro`) VALUES ('unbind_time', '[1000,2300]', '#自助解绑服务时间#[几点几分,几点几分]');

insert into db_p2p.tb_config set k='dbsql.ver',v='157-tgh' ON DUPLICATE KEY UPDATE v='157-tgh';
