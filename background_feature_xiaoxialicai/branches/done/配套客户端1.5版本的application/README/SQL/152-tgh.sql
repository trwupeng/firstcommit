USE db_p2p;

ALTER TABLE `tb_user_change_log`
MODIFY COLUMN `phone`  bigint NOT NULL DEFAULT 0 COMMENT '涉及用户手机号' AFTER `data`;

ALTER TABLE `tb_user_0`
ADD COLUMN `remindWares`  varchar(500) NOT NULL DEFAULT '' COMMENT '标的上架提醒' AFTER `redPacketRecentlyExpired`;

ALTER TABLE `tb_user_1`
ADD COLUMN `remindWares`  varchar(500) NOT NULL DEFAULT '' COMMENT '标的上架提醒' AFTER `redPacketRecentlyExpired`;

INSERT INTO `tb_config` (`k`, `v`, `intro`) VALUES ('wares_remind_second', '300', '#标的上架提醒的有效时间#单位秒');
INSERT INTO `tb_config_ram` (`k`, `v`, `intro`) VALUES ('wares_remind_second', '300', '#标的上架提醒的有效时间#单位秒');


insert into db_p2p.tb_config set k='dbsql.ver',v='152-tgh' ON DUPLICATE KEY UPDATE v='152-tgh';