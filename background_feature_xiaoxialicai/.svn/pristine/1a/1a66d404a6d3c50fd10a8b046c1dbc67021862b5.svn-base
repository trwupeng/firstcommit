USE db_p2p;

ALTER TABLE `tb_wares_0`
MODIFY COLUMN `statusCode`  tinyint(4) NOT NULL DEFAULT 0 COMMENT '状态\r\n-2 正式上架后撤销的\r\n-1 没正式上架的撤销的\r\n0 新建\r\n5 等待上架审核\r\n10 等待上架\r\n11 上架募集中\r\n12 募集结束，等待还款\r\n20 还款结束' AFTER `shelfId`;

ALTER TABLE `tb_investment_0`
MODIFY COLUMN `orderStatus`  int(11) NOT NULL DEFAULT 0 COMMENT '单订状态\r\n0 初建（保留）\r\n-1 中断，废弃的（系统状态）\r\n4 支付失败\r\n2 订单已受理，等待处理结果\r\n3 订单已受理，等待支付网关处理结果\r\n8 支付成功,起息前\r\n10 起息后，回款中\r\n21  正常回款（延期由平台垫付）\r\n20 延期回款中\r\n38 提前还款\r\n39 结束：已全部回款' AFTER `transTime`;

ALTER TABLE `tb_investment_1`
MODIFY COLUMN `orderStatus`  int(11) NOT NULL DEFAULT 0 COMMENT '单订状态\r\n0 初建（保留）\r\n-1 中断，废弃的（系统状态）\r\n4 支付失败\r\n2 订单已受理，等待处理结果\r\n3 订单已受理，等待支付网关处理结果\r\n8 支付成功,起息前\r\n10 起息后，回款中\r\n21  正常回款（延期由平台垫付）\r\n20 延期回款中\r\n38 提前还款\r\n39 结束：已全部回款' AFTER `transTime`;



insert into db_p2p.tb_config set k='dbsql.ver',v='110-tgh' ON DUPLICATE KEY UPDATE v='110-tgh';