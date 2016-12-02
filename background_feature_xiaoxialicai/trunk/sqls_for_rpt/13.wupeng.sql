USE db_p2prpt;

ALTER TABLE `tb_products_final`
ADD COLUMN `introDisplay`  text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '产品介绍' AFTER `borrowerId`;

insert into tb_rpt_database_ver values ('13.wupeng', '增加产品介绍字段');