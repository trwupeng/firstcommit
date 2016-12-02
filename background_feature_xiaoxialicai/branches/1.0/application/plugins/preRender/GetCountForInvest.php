<?php

/**
 * 总的投资额
 */
class GetCountForInvest
{
	public static function run(\Yaf_View_Simple $view, $request, $response = null)
	{
		$view->assign('investCount', '7929819');//单位元
	}
}