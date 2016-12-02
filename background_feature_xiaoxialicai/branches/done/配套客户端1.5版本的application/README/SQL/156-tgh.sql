use db_p2p;
ALTER TABLE `tb_user_0`
ADD COLUMN `pushWaresId`  varchar(300) NOT NULL DEFAULT '' COMMENT '最近一次给我发满标推送的标的ID' AFTER `remindWares`;

ALTER TABLE `tb_user_1`
ADD COLUMN `pushWaresId`  varchar(300) NOT NULL DEFAULT '' COMMENT '最近一次给我发满标推送的标的ID' AFTER `remindWares`;
