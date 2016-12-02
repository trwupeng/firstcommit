<?php
namespace Prj\Items;
/**
 * Created by PhpStorm.
 * User: LTM <605415184@qq.com>
 * Date: 2015/11/5
 * Time: 9:31
 */
class RedPacketForCheckin extends RedPacket {
	static $bonusName = 'redPacketForCheckin';
	static $voucherTitle = '签到红包';
	static $maxNum = 7;//最大签到次数
	static $maxAmount = 200;//固定金额
//	protected $checkinRule = ['1_100'=>1000,'100_300'=>8000,'300_500'=>1000];
	protected $checkinRule = [
		'16_24' => 230,
		'24_33' => 1355,
		'33_41' => 3415,
		'41_50' => 3415,
		'50_58' => 1355,
		'58_67' => 230,
	];

	protected $checkinNum = 1;

	/**
	 * 准备发放红包
	 * @param \Prj\Data\User $user 用户对象
	 * @param int            $num  一次发放的数量
	 * @param int $checkinNum 当前签到次数
	 * @return string
	 * @throws \ErrorException
	 */
	public function give_prepare($user, $num = 1, $checkinNum = 1) {
		$this->checkinNum = $checkinNum;
		$loger = \Sooh\Base\Log\Data::getInstance();
		$cur    = $user->getField('redPacket');
		$userId = $user->userId;
		for ($i = 0; $i < $num; $i++) {
			list($expire, $amount, $limits, $type) = $this->iniForGiven();
			$tmp = \Prj\Data\Vouchers::newForUser($userId, $type, $amount, $expire);
			if ($tmp != null) {
				try {
					$tmp->update();
					$this->vouchersLast[] = $tmp;
					$this->voucherIds[] = $tmp->getField('voucherId');
					$this->dtExpireds[] = $tmp->getField('dtExpired');
					$this->amountLast[] = $amount;
				} catch (\Exception $e) {
					$loger->error("error on give voucher to user:{$user->userId} on " . \Prj\Misc\OrdersVar::$introForCoder . " : " . $e->getMessage());
					return $e->getMessage();
				}
			}
		}
		if($this->setStatus!=\Prj\Consts\Voucher::status_wait && $this->setStatus!=\Prj\Consts\Voucher::status_freeze)$user->setField('redPacket', $cur + array_sum($this->amountLast));
		return '';
	}

	/**
	 * 获取额度
	 * @return int
	 */
	public function getAmount() {
		if ($this->checkinNum >= self::$maxNum) {
			return self::$maxAmount;
		} else {
			return parent::getRand(\Prj\Data\Config::get('CHECKIN_RED_AMOUNT'));
//			return parent::getRand($this->checkinRule);
		}
	}

	/**
	 * 发放时的参数
	 * @return array [day-expired, amount, limits, type]
	 */
	protected function iniForGiven() {
		if ($this->registerOn) {
			return [0, $this->getAmount(), '', \Prj\Consts\Voucher::type_real];//当日有效
		}
		return [];
	}
}