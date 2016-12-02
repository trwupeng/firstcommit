use db_p2p;

ALTER TABLE `tb_user_0`
ADD COLUMN `isSetProxyAuth`  tinyint NOT NULL DEFAULT 0 COMMENT '是否设置委托扣款' AFTER `pushWaresId`;

ALTER TABLE `tb_user_1`
ADD COLUMN `isSetProxyAuth`  tinyint NOT NULL DEFAULT 0 COMMENT '是否设置委托扣款' AFTER `pushWaresId`;

ALTER TABLE `tb_user_0`
ADD COLUMN `isSetPwd`  tinyint NOT NULL DEFAULT 0 COMMENT '是否设置支付密码' AFTER `pushWaresId`;

ALTER TABLE `tb_user_1`
ADD COLUMN `isSetPwd`  tinyint NOT NULL DEFAULT 0 COMMENT '是否设置支付密码' AFTER `pushWaresId`;

ALTER TABLE `tb_recharges_0`
ADD COLUMN `redirectUrl`  varchar(255) NOT NULL DEFAULT '' COMMENT '跳转页面' AFTER `iRecordVerID`;

ALTER TABLE `tb_recharges_1`
ADD COLUMN `redirectUrl`  varchar(255) NOT NULL DEFAULT '' COMMENT '跳转页面' AFTER `iRecordVerID`;