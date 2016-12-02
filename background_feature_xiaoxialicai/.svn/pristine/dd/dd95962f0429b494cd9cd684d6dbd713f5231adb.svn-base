<?php

/**
 * 返回服务器时间戳
 */
class GetServerTime
{
	/**
	 * @param \Yaf_View_Simple $view
	 * @param      $request
	 * @param null $response
	 */
	public static function run($view, $request, $response = null)
	{
		$view->assign('timestamp', \Sooh\Base\Time::getInstance()->timestamp());
	}
}