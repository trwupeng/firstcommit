USE db_p2p;
ALTER TABLE `tb_feedback_0`
MODIFY COLUMN `exp`  varchar(2000) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '备注' AFTER `extends`;

ALTER TABLE `tb_feedback_1`
MODIFY COLUMN `exp`  varchar(2000) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '备注' AFTER `extends`;
insert into db_p2p.tb_config set k='dbsql.ver',v='126-wupeng' ON DUPLICATE KEY UPDATE v='126-wupeng';