<?php
namespace Prj\Items;

/**
 * 第二版的红包的基类(兼容券，主要是名字和剩余数量和give_prepare（）使用的红包的配置)
 * @package Prj\Items
 * @author wang.ning
 */
abstract class Voucher extends \Prj\Items\Base implements \Lib\Interfaces\ItemV2 {
	const fmt_limit_platform = '限投资金额及APP钱包';
	const fmt_limit_amount   = '消费满{yuan}元使用';

	/**
	 * 发放成功后的状态
	 * @var int
	 */
	protected $setStatus;

	protected $vouchersLast = [];

	protected $totalAmount = 0;

	protected $arrAmount = [];

	public function __construct(array $args = [])
	{
		//TODO log
	}

	public function name() {
		return '红包';
	}

	public function descCreate() {
		return '红包';
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

	/**
	 * 添加对应券的流水记录并返回最后总金额
	 * @param \Prj\Data\User $user user
	 * @param int            $num  num
	 * @return int|string
	 */
	protected function addVoucherLog_onGive($user, $num) {

		$amountIni = $this->ini_amount();
		$limits    = $this->ini_limit();
		$more      = $this->ini_more();
		$expire    = $this->ini_expire();
		for ($i = 0; $i < $num; $i++) {
			if (is_array($amountIni)) {
				$amount = $this->realAmoundByWeight($amountIni);
			} else {
				$amount = $amountIni * $num;
				$num    = 1;
			}
			//			var_log($amountIni,">>>($i) amount=$amount num=$num ini is:");
			$tmp = \Prj\Data\Vouchers::newForUser($user->userId, $more['type'], $amount, $expire);
			if ($tmp != null) {
				if (!empty($limits['minInvest'])) {
					$tmp->setField('limitsAmount', $limits['minInvest']);
					$tmp->setField('exp1', self::fmt_limit_platform);
					$tmp->setField('exp2', str_replace('{yuan}', $limits['minInvest']  / 100, self::fmt_limit_amount));
				}
				if (!empty($limits['shelf'])) {
					$tmp->setField('limitsShelf', $limits['shelf']);
				}
				try {
					$tmp->update();
					parent::addGived($this->parseClassName(get_class($this)), $amount, $expire, $tmp->getField('voucherId'));
					$this->vouchersLast[] = $tmp;
					$this->totalAmount += $amount;
					$this->arrAmount[] = $amount;
				} catch (\Exception $e) {
					\Sooh\Base\Log\Data::getInstance()->error("error on give voucher to user:{$user->userId} on " . \Prj\Misc\OrdersVar::$introForCoder . " : " . $e->getMessage());
					return $e->getMessage();
				}
			}
		}
		return $this->totalAmount;
	}

	/**
	 * 准备发放红包
	 * @param \Prj\Data\User $user 用户对象
	 * @param int            $num  一次发放的数量
	 * @return string
	 * @throws \ErrorException
	 */
	public function give_prepare($user, $num = 1) {
		$cur   = $user->getField('redPacket');
		$added = $this->addVoucherLog_onGive($user, $num);
		var_log($added, 'added from give_prepare');
		if ($added > 0) {
			$user->setField('redPacket', $cur + $added);
			return '';
		}
		return $added;
	}

	protected $voucherLog;

	/**
	 * 回滚
	 * @param \Prj\Data\User $user
	 * @return string
	 * @throws \ErrorException
	 * @throws \Exception
	 */
	public function give_rollback($user) {
		$loger = \Sooh\Base\Log\Data::getInstance();
		foreach ($this->vouchersLast as $tmp) {
			try {
				$tmp->setField('statusCode', \Prj\Consts\Voucher::status_abandon);
				$tmp->update();
			} catch (\Exception $e) {
				$errMsg = $e->getMessage();
				$loger->error("error on give voucher to user:{$user->userId} on " . \Prj\Misc\OrdersVar::$introForCoder . " : $errMsg ");
				continue;
			}
		}

		$cur = $this->numLeft($user);
		$user->setField('redPacket', $cur - $this->totalAmount);
		$user->update();
		return '';
	}

	public function give_confirm($user) {
		$loger = \Sooh\Base\Log\Data::getInstance();
		foreach ($this->vouchersLast as $tmp) {
			try {
				$tmp->setField('statusCode', \Prj\Consts\Voucher::status_unuse);
				$tmp->update();
			} catch (\Exception $e) {
				$errMsg = $e->getMessage();
				$loger->error("!Error:give_confirm:{$user->userId} on " . \Prj\Misc\OrdersVar::$introForCoder . " : $errMsg ");
			}
		}
	}

	public function onUserUpdated() {
		$loger = \Sooh\Base\Log\Data::getInstance();
		foreach ($this->vouchersLast as $tmp) {
			$status = \Prj\Consts\Voucher::status_unuse;
			if (!empty($this->setStatus))
				$status = $this->setStatus;

			try {
				$tmp->setField('statusCode', $status);
				$tmp->update();
			} catch (\ErrorException $e) {
				$err = $e->getMessage();
				$loger->error("error on give voucher to user:{$tmp->getField('userId')} on " . \Prj\Misc\OrdersVar::$introForCoder . " : $err ");
			}
		}
	}

	/**
	 * 解析具体类名
	 * @param string $name 获得的类名
	 * @return string 不带命名空间的类名
	 */
	private function parseClassName($name)
	{
		$classPos = strrpos($name, '\\');
		if ($classPos !== false) {
			return substr($name, $classPos + 1);
		} else {
			return $name;
		}
	}
}
