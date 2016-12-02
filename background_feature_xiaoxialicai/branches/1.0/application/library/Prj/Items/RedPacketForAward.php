<?php
namespace Prj\Items;
/**
 * 手动发放奖励红包
 * User: LTM <605415184@qq.com>
 * Date: 2015/11/4
 * Time: 13:50
 */
class RedPacketForAward extends RedPacket {
	static $bonusName = 'RedPacketForAward';
	static $voucherTitle = '赠送红包';

	const errAccountNotExist = '用户不存在';

	const errSendError = '赠送红包失败';

    protected $user;

    protected $amount;
    protected $expire;
    protected $limitAmount;
    protected $limitShelf;

    public function giveBefore($amount,$expire = 2,$limitAmount = 0,$limitShelf = ''){
        $this->amount = $amount;
        $this->expire = $expire;
        $this->limitAmount = $limitAmount;
        $this->limitShelf = $limitShelf;
    }

    /**
     * 准备发放红包
     * @param \Prj\Data\User $user 用户对象
     * @param int            $num  一次发放的数量
     * @return string
     * @throws \ErrorException
     */
    public function give_prepare($user, $num = 1) {
        $loger = \Sooh\Base\Log\Data::getInstance();
        $cur    = $user->getField('redPacket');
        $userId = $user->userId;
        for ($i = 0; $i < $num; $i++) {
            list($expire, $amount, $limits, $type) = $this->iniForGiven();
            $tmp = \Prj\Data\Vouchers::newForUser($userId, $type, $amount, $expire);
            if ($tmp != null) {
                if(!empty($this->limitAmount)){
                    $tmp->setField('limitsAmount',$this->limitAmount);
//                    $tmp->setField('exp','限投资金额及APP钱包消费满'.$this->limitAmount.'元使用');
	                $tmp->setField('exp1', '限APP投资');
	                $tmp->setField('exp2', '消费满' . $this->limitAmount . '元使用');
                }
                if(!empty($this->limitShelf)){
                    $tmp->setField('limitsShelf',$this->limitShelf);
                }
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
	 * 发放赠送红包
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
		\Prj\Misc\OrdersVar::$introForCoder = 'manual';
		$ret = $this->give_prepare($user, 1);
		if ($ret !== '') {
			$this->give_rollback($user);
			throw new \Sooh\Base\ErrException(self::errSendError);
		} else {
			$this->give_confirm($user);
            $user->update();
//			try {
//				\Prj\ReadConf::run(
//					['event' => 'red_packet', 'num_packet' => '1', 'num_money' => $this->getAmountLast()[0] / 100, 'num_deadline' => 30],
//					['phone' => $user->getField('phone'), 'userId' => $userId, 'msgTitle' => '注册成功', ]
//				);
//			} catch(\Exception $e) {
//				var_log($e->getMessage(), 'Send RedPacketForRegister Message Error');
//			}

            return ['type' => self::$bonusName, 'amount' => $this->getAmountLast()[0],'voucherId'=>$this->voucherIds[0]];
		}
	}


	/**
	 * 获取额度
	 * @return int
	 */
	public function getAmount() {
        return $this->amount;
	}

	/**
	 * 发放时的参数
	 * @return array [day-expired, amount, limits, type]
	 */
	protected function iniForGiven() {
        if($this->getAmount()==0)throw new \ErrorException('金额为0的红包不发');
        return [$this->expire, $this->getAmount(), '', \Prj\Consts\Voucher::type_real];
	}
}