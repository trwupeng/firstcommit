USE db_p2p;
ALTER TABLE `tb_investment_0`
MODIFY COLUMN `returnPlan`  varchar(2000) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `returnNext`;

ALTER TABLE `tb_investment_0`
ADD COLUMN `yieldExt`  decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT '加息券加息' AFTER `yieldStatic`;

ALTER TABLE `tb_investment_0`
ADD COLUMN `waresName`  varchar(128) NOT NULL DEFAULT '' COMMENT '标的名称' AFTER `waresId`;

ALTER TABLE `tb_investment_0`
ADD COLUMN `nickname`  varchar(36) NOT NULL DEFAULT '' COMMENT '姓名' AFTER `userId`;

ALTER TABLE `tb_investment_0`
MODIFY COLUMN `interest`  int(11) NOT NULL DEFAULT 0 COMMENT '总收益 单位分' AFTER `yieldExt`,
MODIFY COLUMN `interestExt`  int(11) NOT NULL DEFAULT 0 COMMENT '加息券收益 单位分' AFTER `interest`,
MODIFY COLUMN `returnAmount`  bigint(20) NOT NULL DEFAULT 0 COMMENT '累计返还本金' AFTER `interestExt`,
MODIFY COLUMN `returnInterest`  int(10) NOT NULL DEFAULT 0 COMMENT '累计返还利息' AFTER `returnAmount`,
ADD COLUMN `interestStatic`  int(11) NOT NULL DEFAULT 0 COMMENT '固定收益' AFTER `interest`,
ADD COLUMN `interestAdd`  int(11) NOT NULL DEFAULT 0 COMMENT '活动收益' AFTER `interestStatic`,
ADD COLUMN `interestFloat`  int(11) NOT NULL DEFAULT 0 COMMENT '浮动收益' AFTER `interestAdd`,
ADD COLUMN `interestSub`  int(11) NOT NULL DEFAULT 0 COMMENT '平台贴息' AFTER `interestExt`;



INSERT INTO db_p2p.tb_config
VALUES
	('dbsql.ver', 49) ON DUPLICATE KEY UPDATE v = 49;