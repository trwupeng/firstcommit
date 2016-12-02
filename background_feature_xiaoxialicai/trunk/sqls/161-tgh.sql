USE db_p2p;

ALTER TABLE `tb_investment_0`
ADD COLUMN `finallyOrdersAward`  tinyint NOT NULL DEFAULT 0 COMMENT '兜底红包发放情况  0:未发放  1:已发放' AFTER `unfreeze`;

ALTER TABLE `tb_investment_1`
ADD COLUMN `finallyOrdersAward`  tinyint NOT NULL DEFAULT 0 COMMENT '兜底红包发放情况  0:未发放  1:已发放' AFTER `unfreeze`;

INSERT INTO `tb_config` (`k`, `v`, `intro`) VALUES ('FINALLY_RED_AMOUNT', '{\"25_50\":230,\"50_75\":1355,\"75_100\":3415,\"100_125\":3415,\"125_150\":1355,\"150_175\":230}', '#兜底红包规则#');

INSERT INTO `tb_config_ram` (`k`, `v`, `intro`) VALUES ('FINALLY_RED_AMOUNT', '{\"25_50\":230,\"50_75\":1355,\"75_100\":3415,\"100_125\":3415,\"125_150\":1355,\"150_175\":230}', '#兜底红包规则#');

INSERT INTO `tb_config` SET `intro`='#兜底活动开关#1:开 0:关' , `k`='FINALLY_RED_SWITCH';

INSERT INTO `tb_config_ram` SET `intro`='#兜底活动开关#1:开 0:关' , `k`='FINALLY_RED_SWITCH';

insert into db_p2p.tb_config set k='dbsql.ver',v='161-tgh' ON DUPLICATE KEY UPDATE v='161-tgh';


