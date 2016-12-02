<?php

namespace Prj\Items;

/**
 * 首充红包
 * @package Prj\Items
 */
class NewFirstChargeRedPacket extends Voucher
{
	/**
	 * @var int 领取红包的用户ID
	 */
	protected $userId;

	public function __construct(array $args)
	{
		if (isset($args['userId'])) {
			$this->userId = $args['userId'];
		}
	}

	public function descCreate()
	{
		return '首充红包';
	}

	public function name()
	{
		return 'firstcharge';
	}

	public function ini_limit()
	{
		return ['minInvest' => 10000];
	}

	protected function ini_more()
	{
		return ['type' => \Prj\Consts\Voucher::type_real];
	}

	protected function ini_expire()
	{
		return ['end' => \Sooh\Base\Time::getInstance()->timestamp(2)];
	}

	protected function ini_amount()
	{
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
	 * 是否有邀请人
	 * @return bool true有邀请人，false没有邀请人
	 */
	protected function haveInvite()
	{
		try {
			$rs = \Prj\Data\User::getInvitedUser($this->userId);
		} catch (\ErrorException $e) {

		}
		if (empty($rs)) {
			return false;
		} else {
			return true;
		}
	}
}