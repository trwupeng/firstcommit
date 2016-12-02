<?php
/**
 * Created by PhpStorm.
 * User: LTM <605415184@qq.com>
 * Date: 2016/5/18
 * Time: 19:22
 */

/**
 * Class GetStartBidFlag
 * 获取用户的开标提醒开关
 */
class GetStartBidFlag
{
	public static function run($view, $request, $response=null)
	{
		$sess = \Sooh\Base\Session\Data::getInstance();
		$userId = $sess->get('accountId');

		$_set = 0;
		try {
            if (\Prj\ReadConf::checkPush($userId, 20)) {
                $_set = 1;
            }
		} catch (\ErrorException $e) {
			\Sooh\Base\Log\Data::getInstance('c')->error($e->getMessage());
		}

		if ($_set == 1) {
			$view->assign('startBidFlag', 1);
		} else {
			$view->assign('startBidFlag', 0);
		}
	}
}