USE db_p2p;

UPDATE `tb_config` SET `intro`='#首次激活app的红包奖励' WHERE (`k`='FIRSTLOGINAPP_RED_AMOUNT');

UPDATE `tb_config` SET `intro`='#客户端分享时的内容' WHERE (`k`='CUSTOMER_INVITE_CONTENT');

UPDATE `tb_config` SET `intro`='#客户端分享时的图片链接' WHERE (`k`='CUSTOMER_INVITE_PICURL');

UPDATE `tb_config` SET `intro`='#客户端分享时的标题' WHERE (`k`='CUSTOMER_INVITE_TITLE');

UPDATE `tb_config` SET `intro`='#客户端分享时的超链接' WHERE (`k`='CUSTOMER_INVITE_URL');

DELETE FROM `tb_config_ram`;

insert into db_p2p.tb_config set k='dbsql.ver',v='131-lyq' ON DUPLICATE KEY UPDATE v='131-lyq';