<?php
namespace Prj\Items;
/**
 * 首次购买的红包
 * @version 0.1 废弃
 */
class RedPacketForFirstBuy extends RedPacket {
	static $bonusName = 'RedPacketForFirstBuy';
	static $voucherTitle = '首购红包';

	const errAccountNotExist = '用户不存在';

	const errSendError = '发送首购红包失败';

    protected $buyAmount;
    protected $setStatus;
    protected $noInvite; //没有邀请人

    public function haveNoInvite(){
        $this->noInvite = 1;
    }

	/**
	 * 发放首购红包
	 * @param string $userId 用户ID
	 * @return array ['type' => 'RedPacketForRegister', 'amount' => '111']
	 * @throws \Sooh\Base\ErrException
	 */
	public function give($userId,$buyAmout) {
        $this->buyAmount = $buyAmout;
        //$this->setStatus = \Prj\Consts\Voucher::status_wait;
		$user = \Prj\Data\User::getCopy($userId);
		$user->load();
		if ($user->exists() === false) {
			throw new \Sooh\Base\ErrException(self::errAccountNotExist);
		}

		\Prj\Misc\OrdersVar::$introForUser = self::$voucherTitle;
		\Prj\Misc\OrdersVar::$introForCoder = 'firstBuy';
		$ret = $this->give_prepare($user, 1);
		if ($ret !== '') {
			$this->give_rollback($user);
			throw new \Sooh\Base\ErrException(self::errSendError);
		} else {
			$this->give_confirm($user);

//			try {
//				\Prj\Message\Message::run(
//					['event' => 'red_packet', 'num_packet' => '1', 'num_money' => $this->getAmountLast()[0] / 100, 'num_deadline' => 30],
//					['phone' => $user->getField('phone'), 'userId' => $userId, 'msgTitle' => '注册成功', ]
//				);
//			} catch(\Exception $e) {
//				var_log($e->getMessage(), 'Send RedPacketForRegister Message Error');
//			}

			return ['type' => 'RedPacketForRegister', 'amount' => $this->getAmountLast()[0],'voucherId'=>$this->voucherIds[0]];
		}
	}

	/**
	 * 获取额度
	 * @return int
	 */
	public function getAmount() {
        if($this->noInvite){
            $ruleStr = \Prj\Data\Config::get('ORDER_FIRST_RED_FULE_NO_INVITE');
            if(empty($ruleStr)){
                throw new \ErrorException('无效的普通用户首购红包配置',999);
            }else{
                if(!is_array($ruleStr)){
                    $rule = json_decode($ruleStr,true);
                }else{
                    $rule = $ruleStr;
                }
                krsort($rule);
                foreach($rule as $k=>$v){
                    if($this->buyAmount<$k){
                        continue;
                    }else{
                        return $v;
                    }
                }
            }
        }else{
            $ruleStr = \Prj\Data\Config::get('ORDER_FIRST_RED_FULE');
            if(empty($ruleStr)){
                throw new \ErrorException('无效的首购红包配置',999);
            }else{
                if(!is_array($ruleStr)){
                    $rule = json_decode($ruleStr,true);
                }else{
                    $rule = $ruleStr;
                }
                krsort($rule);
                //var_log($rule,'rule>>>>>>>>>>>>');
                foreach($rule as $k=>$v){
                    if($this->buyAmount<$k){
                        continue;
                    }else{
                        return $v[0];
                    }
                }
            }
        }
	}

	/**
	 * 发放时的参数
	 * @return array [day-expired, amount, limits, type]
	 */
	protected function iniForGiven() {
		if ($this->firstInvestmentOn) {
            if($this->getAmount()==null)throw new \ErrorException('红包金额为0,不送红包');
			return [['end' => \Sooh\Base\Time::getInstance()->timestamp(2)], $this->getAmount(), '', \Prj\Consts\Voucher::type_real];
		}
		throw new \Sooh\Base\ErrException('首购送红包已经关闭了', 201001);
	}
}