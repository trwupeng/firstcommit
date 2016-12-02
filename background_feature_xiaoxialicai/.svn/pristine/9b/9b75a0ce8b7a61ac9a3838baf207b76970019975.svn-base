use db_p2prpt;
ALTER TABLE `tb_bankcard_final` MODIFY COLUMN `statusCode` tinyint(4) NOT NULL DEFAULT '-1' COMMENT '0:待验证的 -1:放弃的 4:禁用  16:启用的';
ALTER TABLE `tb_evtdaily` MODIFY COLUMN `n` bigint(255) NOT NULL DEFAULT 0 COMMENT '金额或者人数等数量';
ALTER TABLE `tb_orders_final` MODIFY COLUMN `shelfId` smallint(6) NOT NULL DEFAULT '0' COMMENT '类型Id 2000:房贷  3000:车贷';
ALTER TABLE `tb_orders_final` MODIFY COLUMN `ymd` int(11) DEFAULT NULL COMMENT '下单日期';
ALTER TABLE `tb_orders_final` MODIFY COLUMN `hhiiss` int(11) NOT NULL DEFAULT 0 COMMENT '下单时间';
ALTER TABLE `tb_orders_final` MODIFY COLUMN `orderStatus` int(11) NOT NULL DEFAULT '0' COMMENT '订单状态 2:订单已受理等待处理结果 3:订单已受理等待支付网关处理结果 8:支付成功起息前 10:起息后回款中 21:正常回款（延期由平台垫付） 20:延期回款中 38:提前还款  39:已全部回款 -4:异常';
ALTER TABLE `tb_products_final` MODIFY COLUMN `statusCode` tinyint(4) NOT NULL DEFAULT '0' COMMENT '11: 上架募集中 12: 募集结束 20:还款结束';
ALTER TABLE `tb_recharges_final` MODIFY COLUMN `orderStatus`  int(11) NOT NULL DEFAULT '0' COMMENT '39: 充值或提现成功 2:订单已受理，等待处理结果';
ALTER TABLE `tb_vouchers_final` MODIFY COLUMN `statusCode`  int(11) NOT NULL DEFAULT '0' COMMENT '-4:冻结 -2:等待激活 1:已使用 0:未使用 -1:';
insert into tb_rpt_database_ver values ('4.lilianqi.comment', '字段添加说明');

