use db_p2p;

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for tb_menu
-- ----------------------------
DROP TABLE IF EXISTS `tb_menu`;
CREATE TABLE `tb_menu` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `mark` varchar(200) DEFAULT NULL COMMENT '标记分类',
  `name` varchar(200) DEFAULT NULL COMMENT '栏目名称',
  `value` text COMMENT '栏目对应值',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='管理后台菜单表';

insert into db_p2p.tb_menu (`mark`, `name`, `value`) values ('标的','标的.添加标的模板', '["manage","warestpl","edit",[],[]]');
insert into db_p2p.tb_menu (`mark`, `name`, `value`) values ('标的','标的.浏览标的模板', '["manage","warestpl","index",[],[]]');
insert into db_p2p.tb_menu (`mark`, `name`, `value`) values ('标的','标的.标的一览', '["manage","waresi","index",[],[]]');
insert into db_p2p.tb_menu (`mark`, `name`, `value`) values ('标的','标的.添加标的', '["manage","warese","edit",[],[]]');
insert into db_p2p.tb_menu (`mark`, `name`, `value`) values ('标的','标的.标的内容审核', '["manage","waresc","check",[],[]]');
insert into db_p2p.tb_menu (`mark`, `name`, `value`) values ('标的','标的.标的上架审核', '["manage","warescs","checkSale",[],[]]');
insert into db_p2p.tb_menu (`mark`, `name`, `value`) values ('标的','标的.资产一览', '["manage","asset","index",[],[]]');
insert into db_p2p.tb_menu (`mark`, `name`, `value`) values ('标的','标的.添加资产', '["manage","asset","edit",[],[]]');
insert into db_p2p.tb_menu (`mark`, `name`, `value`) values ('标的','标的.资产审核', '["manage","assetc","check",[],[]]');
insert into db_p2p.tb_menu (`mark`, `name`, `value`) values ('运营','运营.订单一览', '["manage","investment","index",[],[]]');
insert into db_p2p.tb_menu (`mark`, `name`, `value`) values ('运营','运营.用户一览', '["manage","useri","index",[],[]]');
insert into db_p2p.tb_menu (`mark`, `name`, `value`) values ('运营','运营.充值一览', '["manage","userre","recharge",[],[]]');
insert into db_p2p.tb_menu (`mark`, `name`, `value`) values ('运营','运营.提现一览', '["manage","userw","withdraw",[],[]]');
insert into db_p2p.tb_menu (`mark`, `name`, `value`) values ('运营','运营.提现申请', '["manage","userwing","withdrawing",[],[]]');
insert into db_p2p.tb_menu (`mark`, `name`, `value`) values ('运营','运营.消息管理', '["manage","userm","mineMsg",[],[]]');
insert into db_p2p.tb_menu (`mark`, `name`, `value`) values ('运营','运营.渠道管理', '["manage","copartners","index",[],[]]');
insert into db_p2p.tb_menu (`mark`, `name`, `value`) values ('运营','运营.协议管理', '["manage","contracts","index",[],[]]');
insert into db_p2p.tb_menu (`mark`, `name`, `value`) values ('运营','运营.查询兑换码', '["manage","excodequery","queryexcode",[],[]]');
insert into db_p2p.tb_menu (`mark`, `name`, `value`) values ('运营','运营.用户资金记录', '["manage","usertally","tally",[],[]]');
insert into db_p2p.tb_menu (`mark`, `name`, `value`) values ('运营','运营.返利一览', '["manage","user","rebate",[],[]]');
insert into db_p2p.tb_menu (`mark`, `name`, `value`) values ('运营','运营.二次营销-查找用户', '["report","secondmarketsearch","index",[],[]]');
insert into db_p2p.tb_menu (`mark`, `name`, `value`) values ('运营','运营.二次营销-短信', '["report","secondmarket","index",[],[]]');
insert into db_p2p.tb_menu (`mark`, `name`, `value`) values ('高级运营','高级运营.真实信息', '["manage","userh","honestInfo",[],[]]');
insert into db_p2p.tb_menu (`mark`, `name`, `value`) values ('高级运营','高级运营.兑换码', '["manage","excode","index",[],[]]');
insert into db_p2p.tb_menu (`mark`, `name`, `value`) values ('高级运营','高级运营.赠送提现次数', '["manage","usersendwithdrawnum","sendWithdrawNum",[],[]]');
insert into db_p2p.tb_menu (`mark`, `name`, `value`) values ('高级运营','高级运营.解锁用户', '["payfix","userUnbindCard","unlockuser",[],[]]');
insert into db_p2p.tb_menu (`mark`, `name`, `value`) values ('高级运营','高级运营.解锁标的', '["manage","wares","unlockwares",[],[]]');
insert into db_p2p.tb_menu (`mark`, `name`, `value`) values ('高级运营','高级运营.交换手机-串号', '["manage","swapuserphone","check",[],[]]');
insert into db_p2p.tb_menu (`mark`, `name`, `value`) values ('高级运营','高级运营.交换手机-登录', '["manage","userchangephone","index",[],[]]');
insert into db_p2p.tb_menu (`mark`, `name`, `value`) values ('高级运营','高级运营.注销·冻结手机', '["manage","userphonecancel","index",[],[]]');
insert into db_p2p.tb_menu (`mark`, `name`, `value`) values ('高级运营','高级运营.购买退款', '["manage","chargeback","create",[],[]]');
insert into db_p2p.tb_menu (`mark`, `name`, `value`) values ('高级运营','高级运营.退款列表', '["manage","chargeback","list",[],[]]');
insert into db_p2p.tb_menu (`mark`, `name`, `value`) values ('高级运营','高级运营.红包延期', '["manage","voucherdelay","index",[],[]]');
insert into db_p2p.tb_menu (`mark`, `name`, `value`) values ('高级运营','高级运营.勋章管理', '["manage","medal","index",[],[]]');
insert into db_p2p.tb_menu (`mark`, `name`, `value`) values ('高级运营','高级运营.自定义红包', '["manage","vouchergrantuserdefined","index",[],[]]');
insert into db_p2p.tb_menu (`mark`, `name`, `value`) values ('客服','客服.奖券管理', '["manage","voucher","index",[],[]]');
insert into db_p2p.tb_menu (`mark`, `name`, `value`) values ('客服','客服.绑卡一览', '["manage","userb1","bankcard",[],[]]');
insert into db_p2p.tb_menu (`mark`, `name`, `value`) values ('客服','客服.绑卡未购买', '["manage","userb2","bindCardNotInvest",[],[]]');
insert into db_p2p.tb_menu (`mark`, `name`, `value`) values ('客服','客服.券发放', '["manage","vouchergrant","index",[],[]]');
insert into db_p2p.tb_menu (`mark`, `name`, `value`) values ('客服','客服.反馈管理', '["manage","feedback","index",[],[]]');
insert into db_p2p.tb_menu (`mark`, `name`, `value`) values ('客服','客服.用户新浪余额', '["manage","usercheck","index",[],[]]');
insert into db_p2p.tb_menu (`mark`, `name`, `value`) values ('报表','报表.日报整合版', '["report","rptdailybasic","recent",[],[]]');
insert into db_p2p.tb_menu (`mark`, `name`, `value`) values ('报表','报表.日报数字版', '["report","rptdailybasic","recent2",[],[]]');
insert into db_p2p.tb_menu (`mark`, `name`, `value`) values ('报表','报表.渠道导入量', '["report","copartnerworth","index",[],[]]');
insert into db_p2p.tb_menu (`mark`, `name`, `value`) values ('报表','报表.渠道提现量', '["report","copartnerwithdraw","index",[],[]]');
insert into db_p2p.tb_menu (`mark`, `name`, `value`) values ('报表','报表.用户提现', '["report","withdrawuser","index",[],[]]');
insert into db_p2p.tb_menu (`mark`, `name`, `value`) values ('报表','报表.红包', '["report","redpacketreport","index",[],[]]');
insert into db_p2p.tb_menu (`mark`, `name`, `value`) values ('报表','报表.渠道漏斗图', '["report","copartnertransfunnelChart","index",[],[]]');
insert into db_p2p.tb_menu (`mark`, `name`, `value`) values ('报表','报表.渠道对比', '["report","copartnercompare","index",[],[]]');
insert into db_p2p.tb_menu (`mark`, `name`, `value`) values ('报表','报表.协议对比', '["report","contractcompare","index",[],[]]');
insert into db_p2p.tb_menu (`mark`, `name`, `value`) values ('报表','报表.赏金任务', '["report","weekactivity","index",[],[]]');
insert into db_p2p.tb_menu (`mark`, `name`, `value`) values ('资金','资金.回款确认', '["manage","payback","index",[],[]]');
insert into db_p2p.tb_menu (`mark`, `name`, `value`) values ('资金','资金.对账总览', '["manage","check","dayCheckOverview",[],[]]');
insert into db_p2p.tb_menu (`mark`, `name`, `value`) values ('资金','资金.充值对账', '["manage","check","dayRecharges",[],[]]');
insert into db_p2p.tb_menu (`mark`, `name`, `value`) values ('资金','资金.提现对账', '["manage","check","dayWithdraw",[],[]]');
insert into db_p2p.tb_menu (`mark`, `name`, `value`) values ('资金','资金.购买对账', '["manage","check","dayBuy",[],[]]');
insert into db_p2p.tb_menu (`mark`, `name`, `value`) values ('资金','资金.放款对账(给借款人)', '["manage","check","dayLoan",[],[]]');
insert into db_p2p.tb_menu (`mark`, `name`, `value`) values ('资金','资金.还款对账', '["manage","check","dayPayback",[],[]]');
insert into db_p2p.tb_menu (`mark`, `name`, `value`) values ('资金','资金.回款对账(给投资人)', '["manage","check","dayPaysplit",[],[]]');
insert into db_p2p.tb_menu (`mark`, `name`, `value`) values ('资金','资金.管理费对账', '["manage","check","dayManage",[],[]]');
insert into db_p2p.tb_menu (`mark`, `name`, `value`) values ('资金','资金.运营资金流水', '["manage","check","dayCompany",[],[]]');
insert into db_p2p.tb_menu (`mark`, `name`, `value`) values ('资金','资金.存钱罐日收益', '["manage","check","dayInterest",[],[]]');
insert into db_p2p.tb_menu (`mark`, `name`, `value`) values ('资金','资金.企业户充值', '["manage","paybackext","companyRecharge",[],[]]');
insert into db_p2p.tb_menu (`mark`, `name`, `value`) values ('资金','资金.企业户转给借款人', '["manage","paybackext","transToBorrower",[],[]]');
insert into db_p2p.tb_menu (`mark`, `name`, `value`) values ('系统','系统.管理员一览', '["manage","managers","index",[],[]]');
insert into db_p2p.tb_menu (`mark`, `name`, `value`) values ('系统','系统.客户端版本', '["manage","clientversion","index",[],[]]');
insert into db_p2p.tb_menu (`mark`, `name`, `value`) values ('系统','系统.预警', '["manage","lasthour","index",[],[]]');
insert into db_p2p.tb_menu (`mark`, `name`, `value`) values ('系统','系统.许可模板', '["manage","agreement","index",[],[]]');
insert into db_p2p.tb_menu (`mark`, `name`, `value`) values ('系统','系统.日志查询', '["manage","logquery","index",[],[]]');
insert into db_p2p.tb_menu (`mark`, `name`, `value`) values ('系统','系统.参数配置', '["manage","config","index",[],[]]');
insert into db_p2p.tb_menu (`mark`, `name`, `value`) values ('系统','系统.版本SQL维护', '["manage","sqlmanage","index",[],[]]');
insert into db_p2p.tb_menu (`mark`, `name`, `value`) values ('系统','系统.许可模板审核版本', '["manage","agreement2","index",[],[]]');
insert into db_p2p.tb_menu (`mark`, `name`, `value`) values ('调试','调试.api日志', '["manage","dev","apilog",[],[]]');
insert into db_p2p.tb_menu (`mark`, `name`, `value`) values ('调试','调试.7xx的短信', '["manage","dev","sms7",[],[]]');
insert into db_p2p.tb_menu (`mark`, `name`, `value`) values ('调试','调试.标的资金流水', '["manage","dev","waremoneylog",[],[]]');
insert into db_p2p.tb_menu (`mark`, `name`, `value`) values ('活动','活动.蜘蛛-补仓', '["report","spider","fillup",[],[]]');
insert into db_p2p.tb_menu (`mark`, `name`, `value`) values ('活动','活动.蜘蛛-查询', '["report","spidersearch","index",[],[]]');
insert into db_p2p.tb_menu (`mark`, `name`, `value`) values ('支付网关','支付网关.绑卡记录', '["payfix","bindingbankcard","index",[],[]]');
insert into db_p2p.tb_menu (`mark`, `name`, `value`) values ('支付网关','支付网关.扣款记录', '["payfix","collecttrade","index",[],[]]');
insert into db_p2p.tb_menu (`mark`, `name`, `value`) values ('支付网关','支付网关.流水记录', '["payfix","companyprofit","record",[],[]]');
insert into db_p2p.tb_menu (`mark`, `name`, `value`) values ('支付网关','支付网关.公司流水记录', '["payfix","companyprofit","index",[],[]]');
insert into db_p2p.tb_menu (`mark`, `name`, `value`) values ('支付网关','支付网关.投资记录', '["payfix","companyprofit","investment",[],[]]');
insert into db_p2p.tb_menu (`mark`, `name`, `value`) values ('支付网关','支付网关.充值记录', '["payfix","companyprofit","recharge",[],[]]');
insert into db_p2p.tb_menu (`mark`, `name`, `value`) values ('支付网关','支付网关.提现记录', '["payfix","companyprofit","withdraw",[],[]]');
insert into db_p2p.tb_menu (`mark`, `name`, `value`) values ('支付网关','支付网关.贴息修复', '["payfix","payback","subInterestFix",[],[]]');
insert into db_p2p.tb_menu (`mark`, `name`, `value`) values ('资金','资金.企业户充值', '["manage", "paybackext", "companyRecharge", [], []]');
insert into db_p2p.tb_menu (`mark`, `name`, `value`) values ('资金','资金.企业户转给借款人', '["manage", "paybackext", "transToBorrower", [], []]');
insert into db_p2p.tb_menu (`mark`, `name`, `value`) values ('报表','报表.勋章统计', '["report","medalreport","index",[],[]]');