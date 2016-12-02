<?php
/**
 * Created by PhpStorm.
 * User: LTM <605415184@qq.com>
 * Date: 2016/5/10
 * Time: 11:40
 */

use Sooh\Base\Form\Item as form_def;

/**
 * 注销与冻结手机号
 * Class UserphonecancelController
 * @author liangyanqing
 */
class UserphonecancelController extends \Prj\ManagerCtrl
{
	public function indexAction()
	{
		$where = \Prj\Misc\View::decodePkey($this->_request->get('_pkey_val_'));
		$frm = \Sooh\Base\Form\Broker::getCopy('default')
			->init(
				\Sooh\Base\Tools::uri(),
				'get',
				empty($where) ? \Sooh\Base\Form\Broker::type_c : \Sooh\Base\Form\Broker::type_u
			);

		$frm->addItem('phone', form_def::factory('手机号', '', form_def::text, [], ['data-rule' => 'required, length[~15]']))
			->addItem('_pkey_val_', $this->_request->get('_pkey_val_'));

		$frm->fillValues();

		if ($frm->flgIsThisForm) {
			$fields = $frm->getFields();
			$phone = $fields['phone'];

			if (empty($phone)) {
				return $this->returnError('手机号不能为空');
			}

			$dbLogin = \Sooh\DB\Cases\AccountAlias::getCopy([$phone, 'phone']);
			$dbLogin->load();
			if ($dbLogin->exists()) {
				$this->_view->assign('_code', 200);
				$this->_view->assign('phone', $phone);
				$this->_view->assign('_pkey_val_', \Prj\Misc\View::encodePkey(['phone' => $phone]));
			} else {
				$this->_view->assign('_code', 400);
			}
		}
	}

    /**
     * 注销用户手机
     * 原手机号不存在
     */
	public function cancelPhoneAction()
	{
		$where = \Prj\Misc\View::decodePkey($this->_request->get('_pkey_val_'));
		if (empty($where) || !isset($where['phone'])) {
			return $this->returnError('手机号不能为空');
		}

		$phone = $where['phone'];

		try {
			$ret = \Prj\Data\User::cancelUserPhone($phone);
		} catch (\Exception $e) {
			var_log(\Sooh\DB\Broker::lastCmd(false), 'an error had occurred while cancel user phone, there are last SQL cmd:');
			return $this->returnError($e->getMessage(), $e->getCode());
		}
		\Prj\Data\UserChangeLog::addLog('cancelUserPhone', $phone, json_encode(['phone' => $phone]));
		switch($ret) {
			case 0:
				return $this->returnOK('注销成功');
				break;
			case 601001:
				return $this->returnError('注销失败：手机号不存在');
				break;
		}
	}

    /**
     * 冻结用户手机
     */
    public function freezePhoneAction()
    {
        $where = \Prj\Misc\View::decodePkey($this->_request->get('_pkey_val_'));
        if (empty($where) || !isset($where['phone'])) {
            return $this->returnError('手机号不能为空');
        }

        $phone = $where['phone'];

        try {
            $dbLogin = \Sooh\DB\Cases\AccountAlias::getCopy([$phone, 'phone']);
            $dbLogin->load();
            if ($dbLogin->exists() == false) {
                return $this->returnError('找不到此用户');
            }
            $accountId = $dbLogin->getField('accountId');
            $dbAccount = \Sooh\DB\Cases\AccountStorage::getCopy($accountId);
            $dbAccount->load();
            if ($dbAccount->exists() == false) {
                return $this->returnError('找不到此用户');
            }
            $dbAccount->setField('limitStatus', 1);
            $dbAccount->update();

            //清空用户会话
            $this->clearUserSession($accountId);

            return $this->returnOK('冻结成功');
        } catch (\Exception $e) {
            error_log('freezePhone error:' . $e->getMessage());
            return $this->returnError('系统异常，请联系开发人员');
        }
    }

    /**
     * 清空用户所有会话
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