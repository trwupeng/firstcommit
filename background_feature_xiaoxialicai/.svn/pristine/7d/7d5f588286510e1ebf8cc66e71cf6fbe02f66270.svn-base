-- 给用户表tb_user_final添加第三次投资，第三次充值的相关数据

use db_p2prpt;

alter table tb_user_final add column ymdThirdBuy int (11) not null default 0 comment '第三次购买日期' after amountSecBuy;
alter table tb_user_final add column shelfIdThirdBuy smallint(6) not null default 0 comment '第三次购买标的类型' after ymdThirdBuy;
alter table tb_user_final add column amountThirdBuy bigint(20) not null default 0 comment '第三次购买金额' after shelfIdThirdBuy;

insert into tb_rpt_database_ver values ('15.lilianqi', '给用户表tb_user_final添加第三次投资，第三次充值的相关数据');
