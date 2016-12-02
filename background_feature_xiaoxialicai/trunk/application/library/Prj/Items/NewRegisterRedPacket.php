<?php
namespace Prj\Items;

/**
 * 注册红包
 * @package Prj\Items
 */
class NewRegisterRedPacket extends Voucher
{
	protected function ini_amount()
	{
		return \Prj\Data\Config::get('REGISTER_RED_AMOUNT');
	}

	protected function ini_expire()
	{
		return ['end' => \Sooh\Base\Time::getInstance()->timestamp(2)];
	}

	protected function ini_more()
	{
		return ['type' => \Prj\Consts\Voucher::type_real];
	}

	protected function ini_limit()
	{
		return ['minInvest' => 10000];
	}

	public function name()
	{
		return 'register';
	}

	public function descCreate()
	{
		return '注册红包';
	}
}