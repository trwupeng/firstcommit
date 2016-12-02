<?php

use Sooh\Base\Form\Item as form_def;

/**
 * 交换手机号
 * Class UserchangephoneController
 * @author liangyanqing
 */
class UserchangephoneController extends \Prj\ManagerCtrl
{
	public function indexAction()
	{
		$where = \Prj\Misc\View::decodePkey($this->_request->get('_pkey_val_'));
		$frm   = \Sooh\Base\Form\Broker::getCopy('default')
			->init(\Sooh\Base\Tools::uri(), 'get',
				empty($where) ? \Sooh\Base\Form\Broker::type_c : \Sooh\Base\Form\Broker::type_u);

		$frm->addItem('oldPhone', form_def::factory('旧手机号', '', form_def::text, [], ['data-rule' => 'required, length[~15]']))
			->addItem('newPhone', form_def::factory('新手机号(未注册)', '', form_def::text, [], ['data-rule' => 'required, length[~15]']))
			->addItem('_pkey_val_', $this->_request->get('_pkey_val_'));

		$frm->fillValues();

		if ($frm->flgIsThisForm) {
			$fields = $frm->getFields();
			$oldPhone = $fields['oldPhone'];
			$newPhone = $fields['newPhone'];

			if (empty($oldPhone) || empty($newPhone)) {
				return $this->returnError('两个手机号都不能为空');
			}

			$dbOld = \Sooh\DB\Cases\AccountAlias::getCopy([$oldPhone, 'phone']);
			$dbOld->load();
			if ($dbOld->exists()) {
				$dbNew = \Sooh\DB\Cases\AccountAlias::getCopy([$newPhone, 'phone']);
				$dbNew->load();
				if ($dbNew->exists()) {
					return $this->returnError('新手机号已经存在');
				}
			} else {
				return $this->returnError('旧手机号不存在');
			}
			$_pkey = \Prj\Misc\View::encodePkey([
				'oldPhone' => $oldPhone,
			    'newPhone' => $newPhone,
			]);
			$this->_view->assign('_pkey_val_',$_pkey);
			$this->_view->assign('oldPhone', $oldPhone);
			$this->_view->assign('newPhone', $newPhone);
			$this->_view->assign('statusCode', 200);
			$this->_view->assign('message', 'success');
		} else {
			$this->_view->assign('statusCode', 201001);
		}
	}

	public function changePhoneAction()
	{
		$where = \Prj\Misc\View::decodePkey($this->_request->get('_pkey_val_'));
		if (empty($where) || !isset($where['oldPhone']) || !isset($where['newPhone'])) {
			return $this->returnError('需要提供两个手机号码！');
		}

		$oldPhone = $where['oldPhone'];
		$newPhone = $where['newPhone'];

        $dbLogin = \Sooh\DB\Cases\AccountAlias::getCopy([$oldPhone, 'phone']);
        $dbLogin->load();
        if ($dbLogin->exists() == false) {
            return $this->returnError('找不到此用户');
        }
        $accountId = $dbLogin->getField('accountId');

		try {
			$ret = \Prj\Data\User::changeUserPhone($oldPhone, $newPhone);
		} catch (\Exception $e) {
			var_log(\Sooh\DB\Broker::lastCmd(false), 'an error had occurred while change user phone, there are last SQL cmd:');
			return $this->returnError($e->getMessage(), $e->getCode());
		}
		\Prj\Data\UserChangeLog::addLog('changeUserPhone', $oldPhone, json_encode(['oldPhone' => $oldPhone, 'newPhone' => $newPhone]));
		switch($ret) {
			case 0:
                $this->clearUserSession($accountId);
				return $this->returnOK('交换成功');
				break;
			case 601001:
				return $this->returnError('交换失败：旧手机号不存在');
				break;
			case 601002:
				return $this->returnError('交换失败：新手机号已经存在');
				break;
		}
	}

    /**
     * 清空用户的所有会话
     * @param string $userId 用户ID
     * @return bool
     */
    private function clearUserSession($userId) {
        if (empty($userId)) {
            return false;
        }

        $where = [
            'accountId' => $userId,
        ];
        $arrSession = \Sooh\DB\Cases\SessionStorage::loopFindRecords($where);
        if (!empty($arrSession)) {
            foreach ($arrSession as $v) {
                $sessionId = $v['sessionId'];
                $_dbSession = \Sooh\DB\Cases\SessionStorage::getCopy($sessionId);
                $_dbSession->load();
                if ($_dbSession->exists()) {
                    $_dbSession->delete();
                }
                unset($sessionId);
                unset($_dbSession);
            }
        }
    }
}