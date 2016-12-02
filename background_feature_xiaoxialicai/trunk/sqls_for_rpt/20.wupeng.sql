USE db_p2prpt;
ALTER TABLE `tb_activeconfig`
MODIFY COLUMN `value`  varchar(500) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '值' AFTER `groupCode`,
DROP PRIMARY KEY,
ADD PRIMARY KEY (`id`, `code`, `groupCode`),
AUTO_INCREMENT=29;
insert into tb_rpt_database_ver values ('20.wupeng', '活动分组表');