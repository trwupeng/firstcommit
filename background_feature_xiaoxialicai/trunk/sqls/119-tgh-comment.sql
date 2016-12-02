USE db_p2p;

ALTER TABLE `tb_wares_0`
MODIFY COLUMN `statusCode`  tinyint(4) NOT NULL DEFAULT 0 COMMENT '状态\r\n-2 正式上架后撤销的\r\n-1 没正式上架的撤销的\r\n0 新建\r\n5 等待上架审核\r\n10 等待上架\r\n11 上架募集中\r\n12 募集结束，等待还款\r\n20 还款结束' AFTER `shelfId`;

ALTER TABLE `tb_investment_0`
MODIFY COLUMN `orderStatus`  int(11) NOT NULL DEFAULT 0 COMMENT '单订状态\r\n0 初建（保留）\r\n-1 中断，废弃的（系统状态）\r\n4 支付失败\r\n2 订单已受理，等待处理结果\r\n3 订单已受理，等待支付网关处理结果\r\n8 支付成功,起息前\r\n10 起息后，回款中\r\n21  正常回款（延期由平台垫付）\r\n20 延期回款中\r\n38 提前还款\r\n39 结束：已全部回款' AFTER `transTime`;

ALTER TABLE `tb_investment_1`
MODIFY COLUMN `orderStatus`  int(11) NOT NULL DEFAULT 0 COMMENT '单订状态\r\n0 初建（保留）\r\n-1 中断，废弃的（系统状态）\r\n4 支付失败\r\n2 订单已受理，等待处理结果\r\n3 订单已受理，等待支付网关处理结果\r\n8 支付成功,起息前\r\n10 起息后，回款中\r\n21  正常回款（延期由平台垫付）\r\n20 延期回款中\r\n38 提前还款\r\n39 结束：已全部回款' AFTER `transTime`;

ALTER TABLE `tb_asset_0`
MODIFY COLUMN `status`  tinyint(4) NOT NULL DEFAULT 0 COMMENT '状态 0未审核 1已审核' AFTER `endYmd`;

ALTER TABLE `tb_calendar`
MODIFY COLUMN `workday`  tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否工作日 1是 0不是' AFTER `Ymd`;

ALTER TABLE `tb_rebate_0`
MODIFY COLUMN `statusCode`  smallint(6) NOT NULL DEFAULT 0 COMMENT '状态  0新建的  3等待网关处理 39订单完成 4订单失败' AFTER `type`;

ALTER TABLE `tb_rebate_1`
MODIFY COLUMN `statusCode`  smallint(6) NOT NULL DEFAULT 0 COMMENT '状态  0新建的  3等待网关处理 39订单完成 4订单失败' AFTER `type`;

ALTER TABLE `tb_recharges_0`
MODIFY COLUMN `amountFlg`  tinyint(4) NOT NULL DEFAULT 0 COMMENT '20充值  30提现' AFTER `amountAbs`,
MODIFY COLUMN `orderStatus`  int(11) NOT NULL DEFAULT 0 COMMENT '单订状态 0初建 2订单已确认 3等待新浪处理 39结束 4失败' AFTER `exp`;

ALTER TABLE `tb_recharges_1`
MODIFY COLUMN `amountFlg`  tinyint(4) NOT NULL DEFAULT 0 COMMENT '20充值  30提现' AFTER `amountAbs`,
MODIFY COLUMN `orderStatus`  int(11) NOT NULL DEFAULT 0 COMMENT '单订状态 0初建 2订单已确认 3等待新浪处理 39结束 4失败' AFTER `exp`;

ALTER TABLE `tb_user_bankcard_0`
MODIFY COLUMN `statusCode`  tinyint(4) NOT NULL DEFAULT 0 COMMENT '状态：0初建 16有效' AFTER `isDefault`;

ALTER TABLE `tb_user_bankcard_1`
MODIFY COLUMN `statusCode`  tinyint(4) NOT NULL DEFAULT 0 COMMENT '状态：0初建 16有效' AFTER `isDefault`;

ALTER TABLE `tb_vouchers_0`
MODIFY COLUMN `voucherType`  varchar(16) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '券类型  8红包 32分享红包' AFTER `userId`,
MODIFY COLUMN `statusCode`  int(11) NOT NULL DEFAULT 0 COMMENT '状态 -1无效 0可用 1已使用 -2等待打开 -4冻结 ' AFTER `orderId`;

ALTER TABLE `tb_vouchers_1`
MODIFY COLUMN `voucherType`  varchar(16) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '券类型  8红包 32分享红包' AFTER `userId`,
MODIFY COLUMN `statusCode`  int(11) NOT NULL DEFAULT 0 COMMENT '状态 -1无效 0可用 1已使用 -2等待打开 -4冻结 ' AFTER `orderId`;

ALTER TABLE `tb_wallettally_0`
MODIFY COLUMN `tallyType`  int(11) NOT NULL COMMENT '类型 10投资 20充值 30提现 50付息 55还本 100返利 240存钱罐' AFTER `sn`,
MODIFY COLUMN `statusCode`  int(11) NOT NULL DEFAULT 0 COMMENT '状态 0有效 -1无效' AFTER `freeze`;

ALTER TABLE `tb_wallettally_1`
MODIFY COLUMN `tallyType`  int(11) NOT NULL COMMENT '类型 10投资 20充值 30提现 50付息 55还本 100返利 240存钱罐' AFTER `sn`,
MODIFY COLUMN `statusCode`  int(11) NOT NULL DEFAULT 0 COMMENT '状态 0有效 -1无效' AFTER `freeze`;



insert into db_p2p.tb_config set k='dbsql.ver',v='119-tgh' ON DUPLICATE KEY UPDATE v='119-tgh';