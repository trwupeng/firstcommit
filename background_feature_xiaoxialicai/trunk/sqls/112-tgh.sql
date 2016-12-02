USE db_p2p;

INSERT INTO `tb_config` (`k`, `v`, `intro`) VALUES ('safety', '[{\"title\":\"xxx\",\"content\":\"xxx_xxx_xxx\"}]', '#安全保障#');

ALTER TABLE `tb_config`
MODIFY COLUMN `v`  varchar(1000) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `k`;

ALTER TABLE `tb_config_ram`
MODIFY COLUMN `v`  varchar(1000) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `k`;

insert into db_p2p.tb_config set k='dbsql.ver',v='112-tgh' ON DUPLICATE KEY UPDATE v='112-tgh';