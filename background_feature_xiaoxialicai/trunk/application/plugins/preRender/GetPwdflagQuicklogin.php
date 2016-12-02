<?php
/**
 * Created by PhpStorm.
 * User: LTM <605415184@qq.com>
 * Date: 2016/3/10
 * Time: 14:43
 */

/**
 * Class GetPwdflagQuicklogin
 * 查看当前用户是否有密码
 */
class GetPwdflagQuicklogin
{
	public static function run($view, $request, $response=null)
	{
		$sess = \Sooh\Base\Session\Data::getInstance();
		$userId = $sess->get('accountId');

		try {
			if (isset($userId) && !empty($userId)) {
				$dbUser = \Prj\Data\User::getCopy($userId);
				$dbUser->load();
				if ($dbUser->exists()) {
					$phone = $dbUser->getField('phone');
					$oauthMap = [
						'func' => 'userInfo',
					    '_cmd_' => ['accessToken' => ''],
					];

					error_log('GetPwdflagQuicklogin oauth start');
					$oauthRet = (new \Prj\Oauth\Oauth('', '', 'nonStandardMode'))->invokeMode($oauthMap);
					var_log($oauthRet, 'oauthRet');
					if ($oauthRet['hasPwd']) {
						$view->assign('hasPwd', 1);
						return;
					}
				}
			}
		} catch (\ErrException $e) {
			\Sooh\Base\Log\Data::getInstance('c')->error($e->getMessage());
		}
		$view->assign('hasPwd', 0);
	}
}