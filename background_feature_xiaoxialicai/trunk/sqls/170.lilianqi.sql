use db_p2p;
insert into `tb_config` (k, intro) values('EXCLUDE_PHONES', '#报表系统抓取数据不抓取的手机号码用户#1xxxxxxxxxx|1xxxxxxxxxx');
insert into db_p2p.tb_config set k='dbsql.ver',v='170.lilianqi' ON DUPLICATE KEY UPDATE v='170.lilianqi';