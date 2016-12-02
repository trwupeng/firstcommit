<?php
namespace Prj\Items;
/**
 * 购买（非首购）红包
 * User: LTM <605415184@qq.com>
 * Date: 2015/11/4
 * Time: 13:50
 */
class RedPacketForReBuy extends RedPacket {
	static $bonusName = 'RedPacketForRegister';
	static $voucherTitle = '注册红包';
	protected $registerRule = ['1_100'=>1000,'100_300'=>8000,'300_500'=>1000];

	const errAccountNotExist = '用户不存在';

	const errSendError = '发送注册红包失败';

	/**
	 * 发放注册红包
	 * @param string $userId 用户ID
	 * @return array ['type' => 'RedPacketForRegister', 'amount' => '111']
	 * @throws \Sooh\Base\ErrException
	 */
	public function give($userId) {
		$user = \Prj\Data\User::getCopy($userId);
		$user->load();
		if ($user->exists() === false) {
			throw new \Sooh\Base\ErrException(self::errAccountNotExist);
		}

		\Prj\Misc\OrdersVar::$introForUser = self::$voucherTitle;
		\Prj\Misc\OrdersVar::$introForCoder = 'register';
		$ret = $this->give_prepare($user, 1);
		if ($ret !== '') {
			$this->give_rollback($user);
			throw new \Sooh\Base\ErrException(self::errSendError);
		} else {
			$this->give_confirm($user);

//			try {
//				\Prj\ReadConf::run(
//					['event' => 'red_packet', 'num_packet' => '1', 'num_money' => $this->getAmountLast()[0] / 100, 'num_deadline' => 30],
//					['phone' => $user->getField('phone'), 'userId' => $userId, 'msgTitle' => '注册成功', ]
//				);
//			} catch(\Exception $e) {
//				var_log($e->getMessage(), 'Send RedPacketForRegister Message Error');
//			}

			return ['type' => 'RedPacketForRegister', 'amount' => $this->getAmountLast()[0]];
		}
	}

	/**
	 * 获取额度
	 * @return int
	 */
	public function getAmount() {
		return parent::getRand($this->registerRule);
	}

	/**
	 * 发放时的参数
	 * @return array [day-expired, amount, limits, type]
	 */
	protected function iniForGiven() {
		if ($this->registerOn) {
			return [2, $this->getAmount(), '', \Prj\Consts\Voucher::type_real];
		}
		throw new \Sooh\Base\ErrException('注册送红包已经关闭了', 201001);
	}
}