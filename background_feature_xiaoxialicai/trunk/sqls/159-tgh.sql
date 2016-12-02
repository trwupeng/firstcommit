USE db_p2p;

ALTER TABLE `tb_investment_0`
MODIFY COLUMN `unfreeze`  tinyint(4) NOT NULL DEFAULT 0 COMMENT '购买失败解冻  0可以解冻  8解冻成功 4解冻失败 3不用解冻' AFTER `firstTime`;

ALTER TABLE `tb_investment_1`
MODIFY COLUMN `unfreeze`  tinyint(4) NOT NULL DEFAULT 0 COMMENT '购买失败解冻  0可以解冻  8解冻成功 4解冻失败 3不用解冻' AFTER `firstTime`;




insert into db_p2p.tb_config set k='dbsql.ver',v='159-tgh' ON DUPLICATE KEY UPDATE v='159-tgh';


