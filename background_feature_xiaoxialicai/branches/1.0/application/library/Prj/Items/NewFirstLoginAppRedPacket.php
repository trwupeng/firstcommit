<?php
namespace Prj\Items;

/**
 * 首次登录红包
 * @package Prj\Items
 * @author LTM <605415184@qq.com>
 */
class NewFirstLoginAppRedPacket extends Voucher
{
	protected function ini_amount()
	{
		$config = \Prj\Data\Config::get('FIRSTLOGINAPP_RED_AMOUNT');
		if (is_string($config)) {
			if (json_decode($config, true)) {
				$config = json_decode($config, true);
			}
		}
		return $config;
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
		return 'firstLoginApp';
	}

	public function descCreate()
	{
		return '首次登录红包';
	}
}