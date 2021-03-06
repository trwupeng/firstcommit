<?php
namespace Prj\Consts;
/**
 * Voucher的常量
 *
 * @author simon.wang
 */
class Voucher {
    /**
     *  冻结  首购邀请人送包 于次日解冻金额前三的红包
     */
    const status_freeze = -4;
    /**
     * 等待激活
     */
    const status_wait=-2;
	/**
	 * 已使用
	 */
	const status_used=1;
	/**
	 * 未使用
	 */
	const status_unuse=0;
	/**
	 * 废弃(系统回滚掉的)
	 */
	const status_abandon=-1;
    /**
     * 用来分享的红包
     */
    const type_share=32;
	/**
	 * (说白了，就是红包)用户可领取现金的代金券，80本金+20券，可以当成100元购买额
	 */
	const type_real=8;
	/**
	 * 用户不可领取现金的利息券100本金+20券，可以当成100元购买额，收取的是120的利息
	 */
	const type_fake=4;
	/**
	 * 加息券，提高利率
	 */
	const type_yield=2;

	/**
	 * 返利
	 * @var type_rebate
	 */
	const type_rebate=16;


    public static $typeTPL = array(

    );

    public static $isUsableForMsg = '';

    public static $voucherTypeArr = [
        self::type_real => '红包', 
        self::type_yield => '加息券', 
        self::type_fake => '利息券',
        self::type_rebate => '返利券',
        self::type_share => '分享红包',
    ];
	public static function getName($id){
		return isset(self::$voucherTypeArr[$id])?self::$voucherTypeArr[$id]:'未知';
	}
	public static function getStatus($id) {
		switch ($id) {
			case self::status_unuse:
				return '未使用';
			case self::status_used:
				return '已使用';
			case self::status_abandon:
				return '禁用';
			case self::status_wait:
				return '待激活';
			default:
				return '未知';
		}
	}
}
