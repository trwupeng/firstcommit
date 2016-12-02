ALTER TABLE db_p2p.tb_user_bankcard_0 ADD cardId VARCHAR(32) DEFAULT '' COMMENT '绑卡以后产生唯一标识' AFTER phone;
ALTER TABLE db_p2p.tb_user_bankcard_0 DROP PRIMARY KEY;
ALTER TABLE db_p2p.tb_user_bankcard_0 ADD PRIMARY KEY (orderId);
ALTER TABLE db_p2p.tb_recharges_0
MODIFY COLUMN `amount`  bigint(20) NOT NULL DEFAULT 0 COMMENT '水流的金额' AFTER `userId`,
MODIFY COLUMN `amountAbs`  bigint(20) NOT NULL DEFAULT 0 COMMENT '取正后的金额' AFTER `amount`,
MODIFY COLUMN `poundage`  bigint(10) NOT NULL DEFAULT 0 COMMENT '手续费' AFTER `amountFlg`;
CREATE INDEX userId ON db_p2p.tb_user_bankcard_0 (userId);
ALTER TABLE db_p2p.tb_user_0
ADD COLUMN `interestTotal`  bigint(20) NOT NULL DEFAULT 0 COMMENT '累计收益' AFTER `wallet`;
ALTER TABLE db_p2p.tb_investment_0
ADD COLUMN `returnAmount`  bigint(20) NULL DEFAULT 0 COMMENT '累计返还本金' AFTER `interestExt`,
ADD COLUMN `returnInterest`  int(10) NULL DEFAULT 0 COMMENT '累计返还利息' AFTER `returnAmount`;

insert into db_p2p.tb_config values('dbsql.ver',38) ON DUPLICATE KEY UPDATE v=38;