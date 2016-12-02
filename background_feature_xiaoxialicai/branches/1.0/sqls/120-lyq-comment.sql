USE db_p2p;

ALTER TABLE `tb_rebate_0`
MODIFY COLUMN `rebateId`  bigint(20) NOT NULL DEFAULT 0 COMMENT '主键' FIRST ,
MODIFY COLUMN `childNickname`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '称为' AFTER `childUserId`,
MODIFY COLUMN `childPhone`  bigint(20) NOT NULL DEFAULT 0 COMMENT '手机号' AFTER `childNickname`,
MODIFY COLUMN `exp`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '扩展' AFTER `waresId`,
MODIFY COLUMN `amount`  bigint(20) NOT NULL COMMENT '金额' AFTER `exp`,
MODIFY COLUMN `sumAmount`  bigint(20) NOT NULL DEFAULT 0 COMMENT '当前累计金额' AFTER `amount`,
MODIFY COLUMN `statusCode`  smallint(6) NOT NULL COMMENT '状态位：39已返，0未返' AFTER `type`,
MODIFY COLUMN `updateYmd`  bigint(20) NOT NULL DEFAULT 0 COMMENT '更新时间' AFTER `statusCode`,
MODIFY COLUMN `createYmd`  bigint(20) NOT NULL COMMENT '创建时间' AFTER `updateYmd`;

ALTER TABLE `tb_rebate_1`
MODIFY COLUMN `rebateId`  bigint(20) NOT NULL DEFAULT 0 COMMENT '主键' FIRST ,
MODIFY COLUMN `childNickname`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '称为' AFTER `childUserId`,
MODIFY COLUMN `childPhone`  bigint(20) NOT NULL DEFAULT 0 COMMENT '手机号' AFTER `childNickname`,
MODIFY COLUMN `exp`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '扩展' AFTER `waresId`,
MODIFY COLUMN `amount`  bigint(20) NOT NULL COMMENT '金额' AFTER `exp`,
MODIFY COLUMN `sumAmount`  bigint(20) NOT NULL DEFAULT 0 COMMENT '当前累计金额' AFTER `amount`,
MODIFY COLUMN `statusCode`  smallint(6) NOT NULL COMMENT '状态位：39已返，0未返' AFTER `type`,
MODIFY COLUMN `updateYmd`  bigint(20) NOT NULL DEFAULT 0 COMMENT '更新时间' AFTER `statusCode`,
MODIFY COLUMN `createYmd`  bigint(20) NOT NULL COMMENT '创建时间' AFTER `updateYmd`;

ALTER TABLE `tb_message_0`
MODIFY COLUMN `status`  tinyint(4) NOT NULL DEFAULT 0 COMMENT '状态（0：未读；1：已读；-1：已删除；）' AFTER `createTime`;

ALTER TABLE `tb_message_1`
MODIFY COLUMN `status`  tinyint(4) NOT NULL DEFAULT 0 COMMENT '状态（0：未读；1：已读；-1：已删除；）' AFTER `createTime`;

ALTER TABLE `tb_feedback_0`
MODIFY COLUMN `status`  tinyint(4) UNSIGNED NOT NULL COMMENT '状态位（0未读、1已读、2已处理）' AFTER `createTime`;

ALTER TABLE `tb_feedback_1`
MODIFY COLUMN `status`  tinyint(4) UNSIGNED NOT NULL COMMENT '状态位（0未读、1已读、2已处理）' AFTER `createTime`;

insert into db_p2p.tb_config set k='dbsql.ver',v='120-lyq' ON DUPLICATE KEY UPDATE v='120-lyq';