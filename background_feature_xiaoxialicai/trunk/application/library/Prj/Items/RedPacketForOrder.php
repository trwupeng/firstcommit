<?php
namespace Prj\Items;
/**
 * 首次绑卡包/首次下单包/首次充值包
 * Created by PhpStorm.
 * User: TGH
 * Date: 2015/11/9
 * Time: 14:17
 */
class RedPacketForOrder extends RedPacket {
	static $bonusName = 'redPacketForOrder';
    protected $amount = 0; //额度 单位分
    protected $expire = 2; //期限 天
    public $voucherType = '';
    protected $switch = '';
    public $setStatus = '';

 
    public function __construct()
    {
		throw new \ErrorException("use RedPacketFor[FirstBuy,ReBuy,FirstBind]");
        $this->switch = $this->firstInvestmentOn;
    }

//
//    /**
//     * 首购红包调用
//     */
//    public function thisIsFirstBuy()
//    {
//        \Prj\Misc\OrdersVar::$introForUser = '首投奖励';
//        \Prj\Misc\OrdersVar::$explain = '限投资金额及APP钱包消费满100元使用';
//        $this->amount = \Prj\Data\TbConfig::get('ORDER_FIRST_RED_AMOUNT');
//        $this->expire = 1;
//        $this->switch = \Prj\Data\TbConfig::get('ORDER_FIRST_RED_ON');
//        $this->voucherType = \Prj\Data\TbConfig::get('ORDER_FIRST_RED_TYPE');
//        $this->setStatus = \Prj\Consts\Voucher::status_wait;
//
//    }
//
//    /**
//     * 购买指定金额调用
//     */
//    public function thisIsBuyAssignAmount(){
//        \Prj\Misc\OrdersVar::$introForUser = '投资奖励';
//        \Prj\Misc\OrdersVar::$introForCoder = 'share';
//        \Prj\Misc\OrdersVar::$explain = '用于分享的红包';
//        $this->amount = \Prj\Data\TbConfig::get('ORDER_ASSIGN_RED_AMOUNT');
//        $this->expire = 1;
//        $this->switch = $this->buyAssignAmountOn;
//        $this->setStatus = \Prj\Consts\Voucher::status_wait;
//        $this->voucherType = \Prj\Consts\Voucher::type_share;
//    }
//
//    /**
//     * 实名认证红包调用
//     */
//    public function thisIsFirstBinding()
//    {
//        $this->amount = 1000;
//        $this->expire = 1;
//        $this->switch = $this->bindingCardOn;
//        \Prj\Misc\OrdersVar::$introForUser = '认证红包';
//        $this->setStatus = \Prj\Consts\Voucher::status_wait;
//    }
//
//    /**
//     * 首充红包调用
//     */
//    public function thisIsFirstRecharge()
//    {
//        $this->amount = 1000;
//        $this->expire = 1;
//        $this->switch = $this->firstRechargeOn;
//        \Prj\Misc\OrdersVar::$introForUser = '首充红包';
//        $this->setStatus = \Prj\Consts\Voucher::status_wait;
//    }
//
//    public function getVoucher()
//    {
//        return $this->vouchersLast[0];
//    }
//
//    public function getVouchers()
//    {
//        return $this->vouchersLast;
//    }
//
//	/**
//	 * 获取额度
//	 * @return int
//	 */
//	public function getAmount() {
//		return $this->amount;
//	}
//
//    public function getSwitch(){
//        return $this->switch;
//    }
//	/**
//	 * 发放时的参数
//	 * @return array [day-expired, amount, limits, type]
//	 */
//	protected function iniForGiven() {
//		if ($this->switch) {
//			return [$this->expire, $this->getAmount(), '', $this->voucherType];
//		}
//		return [];
//	}
//

}