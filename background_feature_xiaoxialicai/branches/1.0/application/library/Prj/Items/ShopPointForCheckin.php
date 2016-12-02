<?php
namespace Prj\Items;
/**
 * Created by PhpStorm.
 * User: LTM <605415184@qq.com>
 * Date: 2015/11/9
 * Time: 15:08
 */
class ShopPointForCheckin extends ShopPoint {
	static $bonusName = 'RedPacketForRegister';
	static $voucherTitle = '签到积分';
	protected $pointRule = ['10'=>1000];

	/**
	 * 发放时的参数
	 * @return array [day-expired, amount, limits, type]
	 */
	protected function iniForGiven() {
		return [30, $this->getRand($this->pointRule), '', 0];
	}
}