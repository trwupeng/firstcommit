<?php
namespace Prj\Consts;
/**
 * 支付网关相关常量
 *
 * @author simon.wang
 */
class PayGW {
    /**
     * 取消
     */
    const abondon = -1;
	/**
	 * 已受理
	 */
	const accept=1;
	/**
	 * 失败
	 */
	const failed=4;
	/**
	 * 成功
	 */
	const success=8;

    /**
     * 新浪支付
     */
    const paycorp_sina = 101;

    static $payCorp = [
        self::paycorp_sina=>'新浪支付',
    ];

    static $status = [
        self::accept=>'已受理' ,
        self::failed=>'失败',
        self::success=>'成功',
        self::abondon=>'取消',
    ];

    static $checkResult = [
        'pay_miss'=>'网关数据缺失',
        'local_miss'=>'本地数据缺失',
        'error_waresId'=>'错误的标的ID',
        'error_amountExt'=>'错误的红包',
        'error_userId'=>'错误的用户ID',
        'error_interest'=>'错误的利息',
        'error_amount'=>'错误的金额',
        'error_borrowerId'=>'错误的借款人ID',
    ];


    //平台资金类型
    const tally_confirm = 100;//还款垫付 -
    const tally_rebate = 300;//返利 -
    const tally_trans = 400;//转账垫付  -
    const tally_managementTrans = 500;//转账手续费  +
    const tally_managementConfirm = 600;//还款手续费 +
    const tally_interestSub = 700;//平台贴息 -
    const tally_delayConfirm = 800;//逾期还款 +
    const tally_remit = 900;//打款给借款人 +

    static $tallyMap = [
        self::tally_confirm=>'还款垫付',
        self::tally_rebate=>'返利',
        self::tally_trans=>'转账垫付',
        self::tally_managementTrans=>'服务费(转账)',
        self::tally_managementConfirm=>'手续费(还款)',
        self::tally_interestSub=>'平台贴息',
        self::tally_delayConfirm=>'逾期还款',
        self::tally_remit=>'第二企业户打款给借款人'
    ];
}
