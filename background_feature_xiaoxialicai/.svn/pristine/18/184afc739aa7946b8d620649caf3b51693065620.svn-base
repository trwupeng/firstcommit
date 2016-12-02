USE db_p2p;

update `tb_config` set `intro`='#邀请人分享时的内容' where (`k`='CUSTOMER_INVITE_CONTENT');
update `tb_config` set `intro`='#邀请人分享时的图片链接' where (`k`='CUSTOMER_INVITE_PICURL');
update `tb_config` set `intro`='#邀请人分享时的标题' where (`k`='CUSTOMER_INVITE_TITLE');
update `tb_config` set `intro`='#邀请人分享时的超链接' where (`k`='CUSTOMER_INVITE_URL');

DELETE FROM `tb_config_ram`;

insert into db_p2p.tb_config set k='dbsql.ver',v='132-lyq' ON DUPLICATE KEY UPDATE v='132-lyq';