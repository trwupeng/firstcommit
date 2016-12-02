USE db_p2p;

INSERT INTO `tb_config` (`k`, `v`, `intro`) VALUES ('CUSTOMER_INVITE_TITLE', '小虾理财，就等你了！', '客户端分享时的标题');
INSERT INTO `tb_config` (`k`, `v`, `intro`) VALUES ('CUSTOMER_INVITE_CONTENT', '独乐了不如众乐乐，小虾理财，红包加返利，我已加入，就等你了！', '客户端分享时的内容');
INSERT INTO `tb_config` (`k`, `v`, `intro`) VALUES ('CUSTOMER_INVITE_URL', 'http://www...', '客户端分享时的超链接');
INSERT INTO `tb_config` (`k`, `v`, `intro`) VALUES ('CUSTOMER_INVITE_PICURL', 'http://www...', '客户端分享时的图片链接');

insert into tb_config set k='dbsql.ver',v='91-lyq' ON DUPLICATE KEY UPDATE v='91-lyq';