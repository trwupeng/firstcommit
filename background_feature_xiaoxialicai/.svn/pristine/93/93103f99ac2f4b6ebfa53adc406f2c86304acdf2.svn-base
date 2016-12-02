<?php
namespace Prj\Items;
/**
 * 首次绑卡的红包
 * User: LTM <605415184@qq.com>
 * Date: 2015/11/4
 * Time: 13:50
 */
class RedPacketForFirstBind extends RedPacket {
	static $bonusName = 'RedPacketForFirstBind';
	static $voucherTitle = '认证红包';

	const errAccountNotExist = '用户不存在';

	const errSendError = '发送认证红包失败';

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
		\Prj\Misc\OrdersVar::$introForCoder = 'firstBind';
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

            return ['type' => self::$bonusName, 'amount' => $this->getAmountLast()[0],'voucherId'=>$this->voucherIds[0]];
		}
	}


	/**
	 * 获取额度
	 * @return int
	 */
	public function getAmount() {
        $amount = \Prj\Data\Config::get('BIND_FIRST_RED_AMOUNT');
        if(empty($amount)){
            throw new \ErrorException('缺少首绑红包配置#BIND_FIRST_RED_AMOUNT');
        }
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

	/**
	 * 发放时的参数
	 * @return array [day-expired, amount, limits, type]
	 */
	protected function iniForGiven() {
        if($this->getAmount()==0)throw new \ErrorException('金额为0的红包不发');
		if ($this->bindingCardOn) {
			return [['end' => \Sooh\Base\Time::getInstance()->timestamp(2)], $this->getAmount(), '', \Prj\Consts\Voucher::type_real];
		}
		throw new \Sooh\Base\ErrException('首邦送红包已经关闭了', 201001);
	}
}