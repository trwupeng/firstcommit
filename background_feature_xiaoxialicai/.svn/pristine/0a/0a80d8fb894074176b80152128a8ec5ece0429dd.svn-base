<?php
namespace Prj\Items;
/**
 * 红包的基类
 * Created by PhpStorm.
 * User: LTM <605415184@qq.com>
 * Date: 2015/11/2
 * Time: 19:09
 * @author lingtm
 */
class RedPacket implements \Lib\Interfaces\Item {
	public function realGived(){
		$s = get_called_class();
		$r = explode('\\', $s);
		return [   
			[array_pop($r),$this->getAmount()]
		];
	}
	/**
	 * 默认的发放规则 以分为单位
	 * @var array
	 */
	protected $defaultRule = ['1_100'=>1000,'100_300'=>8000,'300_500'=>1000];
	/**
	 * 首次投资的发放规则 以分为单位
	 * @var array
	 */
	protected $firstInvestmentRule = ['1_100'=>1000,'100_300'=>8000,'300_500'=>1000];
	/**
	 * 首次购买后给邀请人的发放规则 以分为单位
	 * @var array
	 */
	protected $firstInvestmentToInviterRule = ['1_100'=>1000,'100_300'=>8000,'300_500'=>1000];
	/**
	 * 注册n年内给邀请人的发放规则 以分为单位
	 * @var array
	 */
	protected $eachInvestmentToInviterByYearsRule = ['1_100'=>1000,'100_300'=>8000,'300_500'=>1000];

	/**
	 * 注册即送
	 * @var int
	 */
	protected $registerOn = 1;
	/**
	 * 绑卡即送
	 * @var int
	 */
	protected $bindingCardOn = 1;
	/**
	 * 首次充值即送
	 * @var int
	 */
	protected $firstRechargeOn = 1;
	/**
	 * 首次购买即送
	 * @var int
	 */
	protected $firstInvestmentOn = 1;

    /**
     * 购买指定金额即送
     */
    protected $buyAssignAmountOn = 1;

	/**
	 * 首次注册即送邀请人
	 * @var int
	 */
	protected $registerToInviterOn = 1;
	/**
	 * 首次注册即送被邀请人
	 * @var int
	 */
	protected $registerToInviteeOn = 1;
	/**
	 * 首次购买即送被邀请人
	 * @var int
	 */
	protected $firstInvestmentToInviteeOn = 1;
	/**
	 * 注册时是否可以获得默认红包
	 * @var int
	 */
	protected $registerGetDefaultOn = 1;
	/**
	 * 被邀请人首次购买后，被邀请人是否可以获得平台默认的首购红包
	 * @var int
	 */
	protected $inviteeFirstInvestmentToInviterGetDefaultOn = 1;
	/**
	 * 投资红包
	 * @var int
	 */
	protected $investmentOn = 1;
	/**
	 * 分享红包，来源自投资红包
	 * @var int
	 */
	protected $shareFromInvestment = 1;

	/**
	 * 红包的金额
	 * @var array
	 */
	protected $amountLast = [];

	/**
	 * 成功的券的集合
	 * @var array
	 */
	protected $vouchersLast = [];

	protected $voucherIds = [];

	protected $dtExpireds = [];

	protected static $rule;

	public function __construct()
	{
		if (empty(self::$rule)) {
			$this->initRule();
		}
	}

	/**
	 * 读取红包配置
	 */
	protected function initRule()
	{

	}


	protected function getAmount() {
	}

    /**
     * 是否存在邀请人
     */
    protected function haveInvite(){
        try{
            $rs = \Prj\Data\User::getInvitedUser($this->user->userId);
        }catch (\ErrorException $e){

        }
        if(empty($rs)){
            return false;
        }else{
            return true;
        }
    }

	/**
	 * 获取一个随机数
	 * @param array $rule 随机规则:['1_100'=>99,'100_900'=>891,'900_1000'=>10]
	 * @return int
	 */
	protected function getRand($rule) {
		if (count($rule) == 1) {
			$result = key($rule);
		} else {
			$result = '';
			$ruleSum = array_sum($rule);

			//概率数组循环
			$vSum = 0;
			$randNum = mt_rand(1, $ruleSum);
			foreach ($rule as $k => $v) {
				$vSum += $v;
				if ($randNum <= $vSum) {
					$result = $k;
					break;
				}
			}

			//		foreach ($rule as $key => $val) {
			//			$randNum = mt_rand(1, $ruleSum);
			//			if ($randNum <= $val) {
			//				$result = $key;
			//				break;
			//			} else {
			//				$ruleSum -= $val;
			//			}
			//		}
		}

		unset ($rule);

		$loc = strpos($result, '_');
		if ($loc) {
			$result = mt_rand(substr($result, 0, $loc), substr($result, $loc + 1));
		}
		return $result;
	}

	/**
	 * 发放时的参数
	 * @return array [day-expired, amount, limits, type]
	 */
	protected function iniForGiven() {
		return [2, $this->getAmount(), '', 0];
	}

	public function getAmountLast() {
		return $this->amountLast;
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
	 * 回滚准备发放的红包
	 * @param \Prj\Data\User $user 用户对象
	 * @return string
	 * @throws \ErrorException
	 */
	public function give_rollback($user) {
		$loger = \Sooh\Base\Log\Data::getInstance();
		foreach ($this->vouchersLast as $tmp) {
			$err = $this->updateStatusAndSave($tmp, \Prj\Consts\Voucher::status_abandon);
			if ($err !== '') {
				$loger->error("error on give voucher to user:{$user->userId} on " . \Prj\Misc\OrdersVar::$introForCoder . " : $err ");
			}
		}

		$cur = $user->getField('redPacket');
        if($this->setStatus!=\Prj\Consts\Voucher::status_wait){
	        $user->setField('redPacket', $cur - array_sum($this->amountLast));
	        $user->update();
        }
		return '';
	}

	/**
	 * 提交发放的红包
	 * @param \Prj\Data\User $user 用户对象
	 * @return string
	 */
	public function give_confirm($user) {
		$loger = \Sooh\Base\Log\Data::getInstance();
		foreach ($this->vouchersLast as $tmp) {
            $status = \Prj\Consts\Voucher::status_unuse;
            if(!empty($this->setStatus))$status = $this->setStatus;
			$err = $this->updateStatusAndSave($tmp, $status);
			if ($err !== '') {
				$loger->error("error on give voucher to user:{$user->userId} on " . \Prj\Misc\OrdersVar::$introForCoder . " : $err ");
			}
		}
		return '';
	}

	/**
	 * 设置券状态
	 * @param \Prj\Data\Vouchers $item
	 * @param int                $status
	 * @return string error-message
	 */
	private function updateStatusAndSave($item, $status) {
		try {
			$item->setField('statusCode', $status);
			$item->update();
			return '';
		} catch (\ErrorException $e) {
			return $e->getMessage();
		}
	}

	/**
	 * 使用红包
	 * @param array $args ['user' => \Prj\Data\User,...]
	 * @param int   $num
	 * @return string
	 */
	public function useit($args, $num) {

	}

	/**
	 * 碰上错误，回滚使用行为
	 * @param array $args ['user' => \Prj\Data\User,...]
	 * @return string
	 */
	public function rollbackUse($args) {

	}

	/**
	 * 剩余数量
	 * @param \Prj\Data\User $user 用户对象
	 * @return null
	 * @throws \ErrorException
	 */
	public function numLeft($user) {
		return $user->getField('redPacket');
	}
}
