USE db_p2p;

INSERT INTO `tb_config` (`k`, `v`, `intro`) VALUES ('smscodeExpire', '15', '#短信验证码有效期（单位分钟）');

insert into db_p2p.tb_config set k='dbsql.ver',v='123-lyq' ON DUPLICATE KEY UPDATE v='123-lyq';