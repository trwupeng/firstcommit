USE db_p2p;

alter table tb_user_0 add clientFlgs varchar(300) not null default '{"ever":{},"daily":{}}' COMMENT '客户端自用标志位' after ap_UsedShareVoucher;
alter table tb_user_1 add clientFlgs varchar(300) not null default '{"ever":{},"daily":{}}' COMMENT '客户端自用标志位' after ap_UsedShareVoucher;

insert into tb_config set k='dbsql.ver',v='114.wupeng' ON DUPLICATE KEY UPDATE v='114.wupeng';