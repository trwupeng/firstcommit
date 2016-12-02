USE db_p2p;

ALTER TABLE `tb_dayPaysplit`
ADD COLUMN `ordersId`  bigint NOT NULL DEFAULT 0 AFTER `haveLocal`,
ADD COLUMN `ordersIdLocal`  bigint NOT NULL DEFAULT 0 AFTER `ordersId`;

insert into db_p2p.tb_config set k='dbsql.ver',v='137-tgh' ON DUPLICATE KEY UPDATE v='137-tgh';