-- User表增加redPacketUnuse字段
ALTER TABLE db_p2p.tb_user_0 ADD COLUMN `redPacketUsed` INT (11) UNSIGNED NULL DEFAULT 0 COMMENT '未使用的红包额度' AFTER `redPacket`;
insert into db_p2p.tb_config values('dbsql.ver',37) ON DUPLICATE KEY UPDATE v=37;