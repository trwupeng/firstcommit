use db_p2p;
ALTER TABLE `tb_investment_0`
MODIFY COLUMN `amount`  bigint(20) NOT NULL DEFAULT 0 COMMENT '实际投资额 单位分' AFTER `nickname`,
MODIFY COLUMN `amountExt`  int(11) NOT NULL DEFAULT 0 COMMENT '活动赠送投资额（可取现） 单位分' AFTER `amount`,
MODIFY COLUMN `amountFake`  int(11) NOT NULL DEFAULT 0 COMMENT '活动赠送投资额（不可取现） 单位分' AFTER `amountExt`,
MODIFY COLUMN `vouchers`  varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '使用券' AFTER `descCreate`,
ADD COLUMN `licence`  varchar(300) NOT NULL DEFAULT '' COMMENT '许可协议' AFTER `returnPlan`;

INSERT INTO db_p2p.tb_config VALUES  ('dbsql.ver', 58) ON DUPLICATE KEY UPDATE v = 58;