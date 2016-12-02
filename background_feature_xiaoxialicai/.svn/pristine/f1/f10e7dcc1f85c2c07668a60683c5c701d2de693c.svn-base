USE db_p2p;

INSERT INTO `tb_config` (`k`, `v`, `intro`) VALUES ('WECHAT_APP_ID', 'wxeb292725a9b34381', '#微信公众平台appID#');

INSERT INTO `tb_config` (`k`, `v`, `intro`) VALUES ('WECHAT_APP_SECRET', '87c8e5ce89d7cdbc76bc2accc3034259', '#微信公众平台appsecret#');

INSERT INTO `tb_config` (`k`, `v`, `intro`) VALUES ('WECHAT_APP_CRYPT', 'q6FPCUoCQWaOiR3UUe5RfQu8A7hlJcMW4BnNyH9z2il', '#微信公众平台crypt#');

delete from tb_config_ram;

insert into db_p2p.tb_config set k='dbsql.ver',v='140-lyq' ON DUPLICATE KEY UPDATE v='140-lyq';