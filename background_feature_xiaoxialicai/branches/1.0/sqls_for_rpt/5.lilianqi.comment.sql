use db_p2prpt;
ALTER TABLE `tb_vouchers_final` MODIFY COLUMN `statusCode`  int(11) NOT NULL DEFAULT '0' COMMENT '-4:冻结 -2:等待激活 1:已使用 0:未使用 -1:放弃的（系统会滚掉的） -99:过期未使用的券';
insert into tb_rpt_database_ver values ('5.lilianqi.comment', '字段添加说明');

