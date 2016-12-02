use db_p2prpt;
alter table tb_vouchers_final add column codeCreate varchar(128) not null default '' comment '创建流水的代码标示';
alter table tb_vouchers_final add column descCreate varchar(128) not null default '' comment '创建流水的代码标示的描述';
insert into tb_rpt_database_ver values ('6.lilianqi', '券表增加两个字段，创建券的代码字段和创建券的描述字段');

