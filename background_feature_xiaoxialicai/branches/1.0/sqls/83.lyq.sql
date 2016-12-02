USE db_p2p;

ALTER TABLE `tb_config`
ADD COLUMN `group`  varchar(80) NOT NULL DEFAULT '' COMMENT '分组，直接填写组名' AFTER `v`;

ALTER TABLE `tb_config`
ADD COLUMN `desc`  varchar(255) NOT NULL COMMENT '描述' AFTER `group`;

INSERT INTO `tb_config` (`k`, `v`) VALUES ('SHARE_VOUCHER_TITLE', '我是分享标题');
INSERT INTO `tb_config` (`k`, `v`) VALUES ('SHARE_VOUCHER_DESC', '我是分享描述');
INSERT INTO `tb_config` (`k`, `v`) VALUES ('SHARE_VOUCHER_PIC', 'pic_url');
INSERT INTO `tb_config` (`k`, `v`) VALUES ('SHARE_VOUCHER_URL', 'url');

insert into tb_config set k='dbsql.ver',v='83-lyq' ON DUPLICATE KEY UPDATE v='83-lyq';