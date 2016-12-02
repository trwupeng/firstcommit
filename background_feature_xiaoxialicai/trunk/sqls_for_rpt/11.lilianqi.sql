USE db_p2prpt;
alter table tb_user_final add column rechargeTotalAmount bigint(20) not null default 0 comment '累计充值金额';
ALTER TABLE tb_user_final add column  investTotalAmount bigint(20) not null default 0 comment '累计投资金额';
insert into tb_rpt_database_ver values ('11.lilianqi', '增加总充值，总投资金额字段');



