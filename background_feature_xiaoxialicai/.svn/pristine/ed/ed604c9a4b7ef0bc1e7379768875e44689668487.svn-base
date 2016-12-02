USE db_p2p;

ALTER TABLE `tb_wares_0`
ADD COLUMN `retryUrl`  varchar(500) NOT NULL DEFAULT '' COMMENT '重发请求url' AFTER `nextConfirmYmd`,
ADD COLUMN `retryBtnShow`  int NOT NULL DEFAULT 0 COMMENT '是否显示重复按钮 1显示 0不显示' AFTER `retryUrl`;

ALTER TABLE `tb_wares_0_ram`
ADD COLUMN `retryUrl`  varchar(500) NOT NULL DEFAULT '' COMMENT '重发请求url' AFTER `nextConfirmYmd`,
ADD COLUMN `retryBtnShow`  int NOT NULL DEFAULT 0 COMMENT '是否显示重复按钮 1显示 0不显示' AFTER `retryUrl`;



insert into db_p2p.tb_config set k='dbsql.ver',v='149-tgh' ON DUPLICATE KEY UPDATE v='149-tgh';