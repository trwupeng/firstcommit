
USE db_p2prpt;
alter table tb_user_final add flagDisplay tinyint(1) not null default 0 comment'是否展示这个用户的数据给渠道 0 不展示，1 展示';

insert into tb_rpt_database_ver values ('2.lilianqi', '增加字段来标识是否显示这个用户的数据给渠道，这个字段工作的基础必须要执行sql目录下的110.lilianqi.sql');