USE db_p2p;

INSERT INTO `tb_config` (`k`, `v`, `intro`) VALUES ('TONGDUN_IOS_SECRET_KEY', '73030583de7444598a026681b9947a05', '#同盾接口-IOS的secret_key');

INSERT INTO `tb_config` (`k`, `v`, `intro`) VALUES ('TONGDUN_ANDROID_SECRET_KEY', '8142b6b85fbc4693afa8e1c40993f454', '#同盾接口-ANDROID的secret_key');

INSERT INTO `tb_config` (`k`, `v`, `intro`) VALUES ('TONGDUN_WEB_SECRET_KEY', '48118ad00dab4f0ba98a5e8d12d133f2', '#同盾接口-WEB的secret_key');

delete from `tb_config_ram`;

USE db_oauth;

ALTER TABLE `tb_accounts_0`
ADD COLUMN `tongDunRet`  varchar(3000) NOT NULL DEFAULT '' COMMENT '同盾检测结果' AFTER `customData`;

ALTER TABLE `tb_accounts_1`
ADD COLUMN `tongDunRet`  varchar(3000) NOT NULL DEFAULT '' COMMENT '同盾检测结果' AFTER `customData`;

insert into db_p2p.tb_config set k='dbsql.ver',v='142-lyq' ON DUPLICATE KEY UPDATE v='142-lyq';