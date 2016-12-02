use db_p2p;

ALTER TABLE `tb_investment_0`
ADD COLUMN `unfreeze`  tinyint NOT NULL DEFAULT 0 COMMENT '购买失败解冻  0可以解冻  8解冻成功 4解冻失败' AFTER `firstTime`;

ALTER TABLE `tb_investment_1`
ADD COLUMN `unfreeze`  tinyint NOT NULL DEFAULT 0 COMMENT '购买失败解冻  0可以解冻  8解冻成功 4解冻失败' AFTER `firstTime`;

insert into db_p2p.tb_config set k='dbsql.ver',v='155-tgh' ON DUPLICATE KEY UPDATE v='155-tgh';