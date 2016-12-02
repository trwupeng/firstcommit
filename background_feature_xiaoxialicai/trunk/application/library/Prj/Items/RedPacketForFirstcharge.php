<?php
namespace Prj\Items;
/**
 * 首次充值的红包
 * @version 0.1 废弃
 */
class RedPacketForFirstcharge extends RedPacket {
	static $bonusName = 'RedPacketForFirstcharge';
	static $voucherTitle = '首充红包';
	protected $registerRule = ['1_100'=>1000,'100_300'=>8000,'300_500'=>1000];

	const errAccountNotExist = '用户不存在';

	const errSendError = '发送注册红包失败';

    protected $user;
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
        $this->user = $user;
		\Prj\Misc\OrdersVar::$introForUser = self::$voucherTitle;
		\Prj\Misc\OrdersVar::$introForCoder = 'firstcharge';
		$ret = $this->give_prepare($user, 1);
		if ($ret !== '') {
			$this->give_rollback($user);
			throw new \Sooh\Base\ErrException(self::errSendError);
		} else {
			$this->give_confirm($user);

			try {
				\Prj\Message\Message::run(
					['event' => 'red_recharge_packet', 'num_packet' => 1, 'private_gift' => $this->getAmountLast()[0] / 100, 'num_deadline' => 48, 'brand' => \Prj\Message\Message::MSG_BRAND],
					['phone' => $user->getField('phone'), 'userId' => $userId]
				);
			} catch(\Exception $e) {
				var_log($e->getMessage(), 'Send RedPacketForRegister Message Error');
			}

			return ['type' => self::$bonusName, 'amount' => $this->getAmountLast()[0]];
		}
	}

	/**
	 * 获取额度
	 * @return int
	 */
	public function getAmount() {
        $amount = \Prj\Data\Config::get('CHARGE_FIRST_RED_AMOUNT');
        if(empty($amount)){
            throw new \ErrorException('缺少首充红包配置#CHARGE_FIRST_RED_AMOUNT');
        }else{
            if(is_array($amount)){
                if($this->haveInvite()){
                    return $amount[0];
                }else{
                    return $amount[1];
                }
            }else{
                return $amount;
            }
        }

	}

    /**
     * 发放时的参数
     * @return array [day-expired, amount, limits, type]
     */
    protected function iniForGiven() {
        if($this->getAmount()==0)throw new \ErrorException('金额为0的红包不发');
        if ($this->firstRechargeOn) {
            return [['end' => \Sooh\Base\Time::getInstance()->timestamp(2)], $this->getAmount(), '', \Prj\Consts\Voucher::type_real];
        }
        throw new \Sooh\Base\ErrException('首充送红包已经关闭了', 201001);
    }
}