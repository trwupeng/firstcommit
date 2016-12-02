<?php
namespace Prj\Consts;
/**
 * 订单类型，投资的，充值的，提现的，券的
 *
 * @author simon.wang
 */
class OrderType {
	const investment=10;//投资
	const recharges=20;//充值
	const withdraw=30;//提现
	const payback=40;//借款人还款
	const paysplit=50;//投资人回息
    const payAmount = 55;//投资人回本
    const advPaysplit = 60; //提前回款
	const binding=70;//绑卡
    const giftInterest = 80; //平台 贴息
	const vouchers=90;//券
    const invite = 100;//邀请返利
    const loan = 200; //放款
    const flow = 300; //流标
    const dayInterest = 240; //存钱罐收益
    const manualReturn = 250; //系统退款
    const manualCancel = 260; //系统扣款

	public static function classFor($orderId)
	{
		switch (substr($orderId,0,2)){
			case self::investment:return '\Prj\Data\Investment';
			case self::recharges:return '\Prj\Data\Recharges';
			case self::vouchers:return '\Prj\Data\Vouchers';
			case self::payback:return '\Prj\Data\Payback';
			default: throw new \ErrorException('unknown orders');
		}
	}

    public static $enum = [
        self::investment=>'投资',
        self::recharges=>'充值',
        self::withdraw=>'提现',
        // self::payback=>'回款',
        self::paysplit=>'付息',
        self::payAmount=>'还本',
        self::advPaysplit=>'提前还款',
        self::invite=>'邀请返利',
        //self::binding=>'绑卡',
        //self::vouchers=>'券',
        self::flow=>'流标',
        self::dayInterest=>'存钱罐收益',
        self::giftInterest=>'平台贴息',
        self::manualReturn=>'系统退款',
        self::manualCancel=>'系统扣款',
    ];
}
