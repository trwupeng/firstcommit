<?php
/**
 * 获取用户密码是否已锁定
 *
 * @author wang.ning
 */
class Getpaypwdlock {
	public static function run($view,$request,$response=null)
	{
		$view->assign("plugin",'added');
		$view->assign('plugin_getpaypwdlock', 0);
		$userIdentifier = \Sooh\Base\Session\Data::getInstance()->get('accountId');
		error_log('postDispatch............'.__CLASS__.':u:'.$userIdentifier);
		if(!empty($userIdentifier)){
			$user = \Prj\Data\User::getCopy($userIdentifier);

			$user->load();
			if ($user->exists()) {
				$failedForbidden = $user->getField('failedForbidden');
				if (!empty($failedForbidden)) {
					if ($failedForbidden['forbidden'] == 1 && \Sooh\Base\Time::getInstance()->timestamp() <= $failedForbidden['forbiddenExpires']) {
						$view->assign('plugin_getpaypwdlock', 1);
					}
				}
			}
		}
	}
}
