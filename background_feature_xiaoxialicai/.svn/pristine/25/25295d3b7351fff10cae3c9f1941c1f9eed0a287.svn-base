<?php

namespace Prj\Items;

/**
 * 认证红包
 * @package Prj\Items
 */
class NewFirstBindRedPacket extends Voucher
{
	protected $userId;

	public function __construct(array $args)
	{
		if (isset($args['userId'])) {
			$this->userId = $args['userId'];
		}
	}

	public function descCreate()
	{
		return '认证红包';
	}

	public function name()
	{
		return 'firstBind';
	}

	protected function ini_limit()
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
		$amount = \Prj\Data\Config::get('BIND_FIRST_RED_AMOUNT');
		if (empty($amount)) {
			throw new \ErrorException('缺少首绑红包配置#BIND_FIRST_RED_AMOUNT');
		}
		if (is_array($amount)) {
			if ($this->haveInvite()) {
				return $amount[0];
			} else {
				return $amount[1];
			}
		} else {
			return $amount;
		}
	}

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