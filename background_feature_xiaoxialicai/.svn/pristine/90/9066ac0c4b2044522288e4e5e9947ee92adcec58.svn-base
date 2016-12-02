<?php

/**
 * 获取注册红包的金额
 * Class GetRegRedPacketA
 */
class GetRegRedPacketA
{
	public function run(\Yaf_View_Simple $view, $request, $response = null)
	{
		$amount = \Prj\Data\Config::get('REGISTER_RED_AMOUNT');
		$view->assign('amount', $amount / 100);
		$view->assign('code', 200);
		$view->assign('msg', 'success');
	}
}