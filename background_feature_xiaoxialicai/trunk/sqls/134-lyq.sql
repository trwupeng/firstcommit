USE db_oauth;

update tb_accounts_0 set passwdSalt='' where passwdSalt is null;
update tb_accounts_1 set passwdSalt='' where passwdSalt is null;

insert into db_p2p.tb_config set k='dbsql.ver',v='134-lyq' ON DUPLICATE KEY UPDATE v='134-lyq';