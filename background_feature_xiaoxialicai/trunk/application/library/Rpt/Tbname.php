<?php
namespace Rpt;
/**
 * Rpt数据库，表的常量定义
 * 
 * TODO:管理员账号表 以后待修改，现在用的是分表
 */
class Tbname {
   
//  主库
    const db_p2p = 'default';
//	从库
	const db_p2p_slave = 'slaveCatches';
//	报表数据库
	const db_p2prpt = 'dbForRpt';
//	最终用户表
	const tb_user_final='db_p2prpt.tb_user_final';
//	日常报表
	const tb_evtdaily = 'db_p2prpt.tb_evtdaily';
//	订单表
	const tb_orders_final = 'db_p2prpt.tb_orders_final';
//	充值提现表
	const tb_recharges_final = 'db_p2prpt.tb_recharges_final';
//	产品表
	const tb_products_final = 'db_p2prpt.tb_products_final';
// 	银行卡表
	const tb_bankcard_final = 'db_p2prpt.tb_bankcard_final';
//	渠道导入量表
	const tb_copartner_worth = 'db_p2prpt.tb_copartner_worth';
// 	券发放使用表
    const tb_vouchers_final = 'db_p2prpt.tb_vouchers_final';
//  手动发放券记录
    const tb_voucher_grant = 'db_p2prpt.tb_voucher_grant';

//	二次营销表
	const tb_secondmarket_list = 'db_p2prpt.tb_secondmarket_list';
	const tb_secondmarket = 'db_p2prpt.tb_secondmarket';

//	蜘蛛活动表
	const tb_activity_spider = 'db_p2prpt.tb_activity_spider';

// 过期记录表
	const tb_vouchers_overdue = 'db_p2prpt.tb_vouchers_overdue';
        
    const tb_weekactivity_final = 'db_p2prpt.tb_weekactivity_final';

// 渠道通知
	const tb_copartner_notify = 'db_p2prpt.tb_copartner_notify';
}

class Fields {

	public static $tb_user_produce_fields = ['userId','phone','nickname','ymdReg','hisReg','ymdBindcard','dtLast',
	    'copartnerId','contractId','clientType','inviteByUser','inviteByParent','inviteByRoot','myInviteCode','isBorrower',
	    'isSuperUser','idCard',
	];

	public static $tb_orders_produce_fields = ['ordersId','waresId','waresName','shelfId','userId','nickname','amount',
			'amountExt','amountFake', 'yieldStaticAdd','yieldStatic','yieldExt','interest','interestStatic','interestAdd',
			'interestFloat','interestExt','interestSub','returnAmount','returnInterest','orderTime','orderStatus','codeCreate',
			'transTime','vouchers','firstTime','returnType','returnNext','lastReturnFundYmd'];

	public static $tb_products_produce_fields = ['waresId','waresName','waresSN','deadLine','dlUnit','tags','mainType','subType','userLimit',
			'vipLevel','priceStart','priceStep','amount','remain','realRaise','yieldStatic','yieldStaticAdd','yieldFloatFrom','yieldFloatTo',
			'returnType','interestStartType','timeStartPlan','timeStartReal','timeEndPlan','timeEndReal','ymdPayReal','ymdPayPlan',
			'payGift','repay','payYmd','borrowerId','shelfId','statusCode','introDisplay'
	];

	public static $tb_bankcard_produce_fields = array('orderId','userId','bankId','bankCard','isDefault','statusCode','timeCreate',
				'resultMsg','resultTime','idCardType','idCardSN','realName','phone','cardId');
	
	public static $tb_recharge_produce_fields = ['ordersId','userId','amount','amountAbs','amountFlg','poundage',
			'orderTime','payTime','orderStatus','payCorp','bankAbs','bankCard'];

	public static $tb_vouchers_produce_fields = ['voucherId','userId','voucherType','amount','timeCreate','dtUsed','orderId','statusCode','dtExpired','codeCreate', 'descCreate'];
}