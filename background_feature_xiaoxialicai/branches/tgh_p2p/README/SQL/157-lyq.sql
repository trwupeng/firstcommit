use db_p2p;
ALTER TABLE `tb_investment_0`
ADD COLUMN `chargeBackStatus`  tinyint(4) NOT NULL DEFAULT 0 COMMENT '退款状态\r\n0 未退款\r\n1 退款中\r\n4 退款失败\r\n8 退款成功' AFTER `unfreeze`;

ALTER TABLE `tb_investment_1`
ADD COLUMN `chargeBackStatus`  tinyint(4) NOT NULL DEFAULT 0 COMMENT '退款状态\r\n0 未退款\r\n1 退款中\r\n4 退款失败\r\n8 退款成功' AFTER `unfreeze`;

insert into db_p2p.tb_config set k='dbsql.ver',v='157-lyq' ON DUPLICATE KEY UPDATE v='157-lyq';