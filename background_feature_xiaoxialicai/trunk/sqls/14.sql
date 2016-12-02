/*
添加两个字段
*/

ALTER TABLE db_p2p.tb_wares_0 add userLimit VARCHAR(128) NOT null DEFAULT 0  COMMENT '限制可购买的用户类型，英文逗号隔开  0：无限制' AFTER subType;
ALTER TABLE db_p2p.tb_wares_0 add vipLevel  smallint(6)  NOT null DEFAULT 0  COMMENT '限制购买的vip等级' AFTER userLimit;

insert into db_p2p.tb_config values('dbsql.ver',14) ON DUPLICATE KEY UPDATE v=14;