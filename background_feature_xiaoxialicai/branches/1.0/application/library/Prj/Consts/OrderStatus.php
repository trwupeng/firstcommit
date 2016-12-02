<?php
namespace Prj\Consts;

/**
 * 订单的状态
 *
 * @author simon.wang
 */
class OrderStatus {
    /**
     * 进行中的订单
     */
    public static $running = [
        self::waiting, //2
        self::waitingGW,//3
        self::payed, //8
        self::going, //10
        self::igoing, //21
        self::delay, //20
        self::advanced, //38
        self::done, //39
    ];

    /**
     * 异常
     */
    const unusual = -4;

    /**
     * 流标
     */
    const flow = -3;

    /**
     * 中断，废弃的（系统状态）
     */
    const abandon=-1;

	/**
	 * 初建（保留）
	 */
	const created=0;

	/**
	 * 订单已受理，等待处理结果
	 */
	const waiting=2;

    /**
     * 订单已受理，等待支付网关处理结果
     */
    const waitingGW=3;

    /**
     * 支付失败
     */
    const failed=4;


    /**
	 * 支付成功,起息前
	 */
	const payed=8;

	/**
	 * 起息后，回款中
	 */
	const going=10;
    /**
     * 正常回款（延期由平台垫付）
     */
    const igoing=21;
	/**
	 * 延期回款中
	 */
	const delay=20;
	/**
	 * 提前还款
	 */
	const advanced=38;
	/**
	 * 结束：已全部回款，提现成功，充值成功
	 */
	const done=39;


	/**
	 * 充值订单专用:成功充值，但需要更新用户的钱包余额
	 */
	const nextUpdateUserWallet=37;

	/**
	 * 增加新的订单未成功状态的时候,请在这里添加一下，
	 * 有多个地方用 
	 */
	public static $unsuccessful =[
	    self::abandon,
	    self::created,
	    self::failed,
	    self::waiting,
	    self::waitingGW,
	];

    public static $enum = array(
        self::abandon=>'失效',
        self::advanced=>'提前还款',
        self::created=>'废弃',
        self::delay=>'延期回款中',
        self::done=>'结束：已全部回款',
        self::failed=>'支付失败',
        self::igoing=>'正常回款（延期由平台垫付）',
        self::payed=>'支付成功',
        self::waiting=>'购买成功(等待网关)',
        self::going=>'支付成功,开始计息',
        self::waitingGW=>'网关处理中...',
        self::done=>'订单完成',
        self::flow=>'流标',
    );
    /**
     * 提现订单的状态
     * @var array
     */
    public static $wEnum = array(
        self::abandon=>'失效',
        self::advanced=>'提前还款',
        self::created=>'废弃',
        self::delay=>'延期回款中',
        self::done=>'结束：已全部回款',
        self::failed=>'支付失败',
        self::unusual=>'支付失败,已退款',
        self::igoing=>'正常回款（延期由平台垫付）',
        self::payed=>'支付成功',
        self::waiting=>'申请成功(等待确认)',
        self::waitingGW=>'网关处理中...',
        self::done=>'订单完成',
    );
    
    
}
