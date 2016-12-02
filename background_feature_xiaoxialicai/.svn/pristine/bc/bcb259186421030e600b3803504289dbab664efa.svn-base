USE db_p2p;

ALTER TABLE `tb_user_0`
ADD COLUMN `firstLoginApp`  int(5) UNSIGNED NOT NULL DEFAULT 0 COMMENT '首次登录APP的平台' AFTER `ymdBindCard`;

ALTER TABLE `tb_user_1`
ADD COLUMN `firstLoginApp`  int(5) UNSIGNED NOT NULL DEFAULT 0 COMMENT '首次登录APP的平台' AFTER `ymdBindCard`;

INSERT INTO `tb_config` (`k`, `v`, `intro`) VALUES ('FIRSTLOGINAPP_RED_AMOUNT', '500', '首次激活app的红包奖励');

insert into tb_config set k='dbsql.ver',v='108-lyq' ON DUPLICATE KEY UPDATE v='108-lyq';