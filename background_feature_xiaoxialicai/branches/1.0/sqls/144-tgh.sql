USE db_p2p;

ALTER TABLE `tb_files`
ADD COLUMN `ymd`  bigint NOT NULL DEFAULT 0 COMMENT '日期' AFTER `fileData`;

ALTER TABLE `tb_files`
ADD COLUMN `cdn`  tinyint NOT NULL DEFAULT 0 COMMENT '是否CDN镜像' AFTER `ymd`;

ALTER TABLE `tb_dayManage`
ADD COLUMN `type`  int NOT NULL DEFAULT 0 COMMENT '类型' AFTER `ymd`;



insert into db_p2p.tb_config set k='dbsql.ver',v='144-tgh' ON DUPLICATE KEY UPDATE v='144-tgh';