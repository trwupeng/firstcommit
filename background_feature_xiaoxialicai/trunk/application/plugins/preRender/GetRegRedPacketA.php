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
		$view->assign('amount', $amount / 100);//出现多个assign中的amount字段重复，修改如下突出唯一识别
		$view->assign('GetRegRedPacketAamount', $amount / 100);
	}
}