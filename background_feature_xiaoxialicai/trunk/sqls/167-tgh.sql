use db_p2p;

ALTER TABLE `tb_files`
ADD COLUMN `url`  varchar(200) NOT NULL DEFAULT '' COMMENT '本地图片地址' AFTER `cdn`,
ADD COLUMN `urlCdn`  varchar(200) NOT NULL DEFAULT '' COMMENT 'cdn地址' AFTER `url`,
ADD COLUMN `statusCode`  tinyint NOT NULL DEFAULT 0 AFTER `urlCdn`,
ADD COLUMN `iRecordVerID`  int NOT NULL DEFAULT 0 AFTER `urlCdn`;




insert into db_p2p.tb_config set k='dbsql.ver',v='167-tgh' ON DUPLICATE KEY UPDATE v='167-tgh';