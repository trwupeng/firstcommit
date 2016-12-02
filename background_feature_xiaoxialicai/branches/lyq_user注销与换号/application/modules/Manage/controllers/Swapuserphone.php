<?php

use Sooh\Base\Form\Item as form_def;

/**
 * 串号时交换两个用户的手机号等。。
 * Class SwapuserphoneController
 */
class SwapuserphoneController extends \Prj\ManagerCtrl
{
	public $logPre = '>>>Swap Two User Phone:';

	/**
	 * 检查两个用户是否符合交换条件
	 * @throws ErrorException
	 * @throws \Sooh\Base\ErrorException
	 */
	public function checkAction()
	{
		$where = \Prj\Misc\View::decodePkey($this->_request->get('_pkey_val_'));
		$frm   = \Sooh\Base\Form\Broker::getCopy('default')
			->init(\Sooh\Base\Tools::uri(), 'get', empty($where) ? \Sooh\Base\Form\Broker::type_c : \Sooh\Base\Form\Broker::type_u);

		$frm->addItem('phone1', form_def::factory('手机号1', '', form_def::text, [], ['data-rule' => 'required,length[~15]']))
			->addItem('phone2', form_def::factory('手机号2', '', form_def::text, [], ['data-rule' => 'required,length[~15]']))
			->addItem('_pkey_val_', $this->_request->get('_pkey_val_'));

		$frm->fillValues();
		//表单提交
		if ($frm->flgIsThisForm)
		{
			$fields = $frm->getFields();
			$phone1 = $fields['phone1'];
			$phone2 = $fields['phone2'];
			if (empty($phone1) || empty($phone2)) {
				return 0;
			}

			$dbPhone1 = \Sooh\DB\Cases\AccountAlias::getCopy([$phone1, 'phone']);
			$dbPhone1->load();
			if ($dbPhone1->exists() == false) {
				return $this->returnError('phone1 not found');
			}

			$dbPhone2 = \Sooh\DB\Cases\AccountAlias::getCopy([$phone2, 'phone']);
			$dbPhone2->load();
			if ($dbPhone2->exists() == false) {
				return $this->returnError('phone2 not found');
			}

			$dbPhone1 = \Sooh\DB\Cases\AccountAlias::getCopy([$phone1, 'phone']);
			$dbPhone1->load();
			$dbPhone2 = \Sooh\DB\Cases\AccountAlias::getCopy([$phone2, 'phone']);
			$dbPhone2->load();
			if($dbPhone1->exists() && $dbPhone2->exists()) {
				$userId1 = $dbPhone1->getField('accountId');
				$dbUser1 = \Prj\Data\User::getCopy($userId1);
				$dbUser1->load();
				$inviteByUser1 = $dbUser1->getField('inviteByUser');
				$inviteByParent1 = $dbUser1->getField('inviteByParent');
				$inviteByRoot1 = $dbUser1->getField('inviteByRoot');
				$myInviteCount1 = json_encode($dbUser1->getMineInvitedUserCount($userId1));
				$dbBankCard1 = \Prj\Data\BankCard::getCopy($userId1);
				$bankRet1 = $dbBankCard1->db()->getRecords($dbBankCard1->tbname(), $userId1 . ' as userId, ' .  $phone1 . ' as phone,' . $inviteByUser1 . ' as inviteByUser,  phone as phoneBankCard,statusCode', ['userId' => $userId1], 'rsort timeCreate');
				if (empty($bankRet1)) {
					$bankRet1 = [['phone' => $phone1, 'userId' => $userId1, 'inviteByUser' => $inviteByUser1, 'phoneBankCard' => '', 'statusCode' => '', ]];
				}
				$bankRet1[0] = array_merge($bankRet1[0], ['inviteByParent' => $inviteByParent1, 'inviteByRoot' => $inviteByRoot1, 'myInviteCount' => $myInviteCount1]);

				$userId2 = $dbPhone2->getField('accountId');
				$dbUser2 = \Prj\Data\User::getCopy($userId2);
				$dbUser2->load();
				$inviteByUser2 = $dbUser2->getField('inviteByUser');
				$inviteByParent2 = $dbUser2->getField('inviteByParent');
				$inviteByRoot2 = $dbUser2->getField('inviteByRoot');
				$myInviteCount2 = json_encode($dbUser2->getMineInvitedUserCount($userId2));
				$dbBankCard2 = \Prj\Data\BankCard::getCopy($userId2);
				$bankRet2 = $dbBankCard2->db()->getRecords($dbBankCard2->tbname(), $userId2 . ' as userId, ' .  $phone2 . ' as phone,' . $inviteByUser2 . ' as inviteByUser,  phone as phoneBankCard,statusCode', ['userId' => $userId2], 'rsort timeCreate');
				if (empty($bankRet2)) {
					$bankRet2 = [['phone' => $phone2, 'userId' => $userId2, 'inviteByUser' => $inviteByUser2, 'phoneBankCard' => '', 'statusCode' => '', ]];
				}
				$bankRet2[0] = array_merge($bankRet2[0], ['inviteByParent' => $inviteByParent2, 'inviteByRoot' => $inviteByRoot2, 'myInviteCount' => $myInviteCount2]);
			} else {
				return $this->returnError('phone1 or phone2 not found');
			}

			$this->_view->assign('ret', array_merge($bankRet1,$bankRet2));

			$_pkey = \Prj\Misc\View::encodePkey([
				'phone1' => $phone1,
				'phone2' => $phone2,
			]);
			$this->_view->assign('_pkey_val_',$_pkey);
		} else {
			$this->_view->assign('ret', []);
		}
	}

	/**
	 * 交换串号时的手机号
	 * @throws ErrorException
	 */
	public function swapUserAction()
	{
		$where = \Prj\Misc\View::decodePkey($this->_request->get('_pkey_val_'));
		if (empty($where) || !isset($where['phone1']) || !isset($where['phone2'])) {
			return $this->returnError('需要提供两个手机号码！');
		}

		$isSwapInvite = $this->_request->get('swapInvite', 0);

		$phone1 = $where['phone1'];
		$phone2 = $where['phone2'];

		$dbPhone1 = \Sooh\DB\Cases\AccountAlias::getCopy([$phone1, 'phone']);
		$dbPhone1->load();
		if ($dbPhone1->exists() == false) {
			return $this->returnError('phone1 not found');
		}
		var_log($dbPhone1, $this->logPre . 'db_phone1');

		$dbPhone2 = \Sooh\DB\Cases\AccountAlias::getCopy([$phone2, 'phone']);
		$dbPhone2->load();
		if ($dbPhone2->exists() == false) {
			return $this->returnError('phone2 not found');
		}
		var_log($dbPhone2, $this->logPre . 'db_phone2');

		$userId1 = $dbPhone1->getField('accountId');
		$userId2 = $dbPhone2->getField('accountId');

		$dbAccount1 = \Sooh\DB\Cases\AccountStorage::getCopy($userId1);
		$dbAccount1->load();
		if ($dbAccount1->exists() == false) {
			return $this->returnError('account1 not found');
		}
		var_log($dbAccount1, $this->logPre . 'db_account1');

		$dbAccount2 = \Sooh\DB\Cases\AccountStorage::getCopy($userId2);
		$dbAccount2->load();
		if ($dbAccount2->exists() == false) {
			return $this->returnError('account2 not found');
		}
		var_log($dbAccount2, $this->logPre . 'db_account2');

		$dbUser1 = \Prj\Data\User::getCopy($userId1);
		$dbUser1->load();
		if ($dbUser1->exists() == false) {
			return $this->returnError('user1 not found');
		}
		var_log($dbUser1, $this->logPre . 'db_user1');

		$dbUser2 = \Prj\Data\User::getCopy($userId2);
		$dbUser2->load();
		if ($dbUser2->exists() == false) {
			return $this->returnError('user2 not found');
		}
		var_log($dbUser2, $this->logPre . 'db_user2');

		$_account_passwd1      = $dbAccount1->getField('passwd');
		$_account_passwdSalt1  = $dbAccount1->getField('passwdSalt');
		$_account_regYmd1      = $dbAccount1->getField('regYmd');
		$_account_reghis1      = $dbAccount1->getField('regHHiiss');
		$_account_contractId1  = $dbAccount1->getField('contractId');
		$_user_ymdReg1         = $dbUser1->getField('ymdReg');
		$_user_hisReg1         = $dbUser1->getField('hisReg');
		$_user_nickname1       = $dbUser1->getField('nickname');
		$_user_tradePwd1       = $dbUser1->getField('tradePwd');
		$_user_tradeSalt1      = $dbUser1->getField('salt');
        $_user_copartnerId1    = $dbUser1->getField('copartnerId');
		$_user_contractId1     = $dbUser1->getField('contractId');
		$_user_inviteByUser1   = $dbUser1->getField('inviteByUser');
		$_user_inviteByParent1 = $dbUser1->getField('inviteByParent');
		$_user_inviteByRoot1   = $dbUser1->getField('inviteByRoot');
		$_user_inviteCount1    = $dbUser1->getMineInvitedUserCount($userId1);

		$_account_passwd2      = $dbAccount2->getField('passwd');
		$_account_passwdSalt2  = $dbAccount2->getField('passwdSalt');
		$_account_regYmd2      = $dbAccount2->getField('regYmd');
		$_account_reghis2      = $dbAccount2->getField('regHHiiss');
		$_account_contractId2  = $dbAccount2->getField('contractId');
		$_user_ymdReg2         = $dbUser2->getField('ymdReg');
		$_user_hisReg2         = $dbUser2->getField('hisReg');
		$_user_nickname2       = $dbUser2->getField('nickname');
		$_user_tradePwd2       = $dbUser2->getField('tradePwd');
		$_user_tradeSalt2      = $dbUser2->getField('salt');
        $_user_copartnerId2    = $dbUser2->getField('copartnerId');
		$_user_contractId2     = $dbUser2->getField('contractId');
		$_user_inviteByUser2   = $dbUser2->getField('inviteByUser');
		$_user_inviteByParent2 = $dbUser2->getField('inviteByParent');
		$_user_inviteByRoot2   = $dbUser2->getField('inviteByRoot');
		$_user_inviteCount2    = $dbUser2->getMineInvitedUserCount($userId2);

		try {
			//交换tb_loginname
			$dbPhone1->setField('accountId', $userId2);
			$dbPhone1->update();
			$dbPhone2->setField('accountId', $userId1);
			$dbPhone2->update();
			var_log(\Sooh\DB\Broker::lastCmd(false), 'swap tb_loginname');

			//交换tb_accounts
			$dbAccount1->setField('passwd', $_account_passwd2);
			$dbAccount1->setField('passwdSalt', $_account_passwdSalt2);
			$dbAccount1->setField('regYmd', $_account_regYmd2);
			$dbAccount1->setField('regHHiiss', $_account_reghis2);
			$dbAccount1->setField('nickname', substr($phone2, 0, 3) . '****' . substr($phone2, -4));
			$dbAccount1->setField('contractId', $_account_contractId2);
			$dbAccount1->setField('phone', $phone2);
			$dbAccount1->update();

			$dbAccount2->setField('passwd', $_account_passwd1);
			$dbAccount2->setField('passwdSalt', $_account_passwdSalt1);
			$dbAccount2->setField('regYmd', $_account_regYmd1);
			$dbAccount2->setField('regHHiiss', $_account_reghis1);
			$dbAccount2->setField('nickname', substr($phone1, 0, 3) . '****' . substr($phone1, -4));
			$dbAccount2->setField('contractId', $_account_contractId1);
			$dbAccount2->setField('phone', $phone1);
			$dbAccount2->update();
			var_log(\Sooh\DB\Broker::lastCmd(false), 'swap tb_accounts');

			//交换tb_user
			$dbUser1->setField('ymdReg', $_user_ymdReg2);
			$dbUser1->setField('hisReg', $_user_hisReg2);
			$dbUser1->setField('phone', $phone2);
			if (preg_match('/^[a-zA-Z0-9]{6}\d{4}$/', $_user_nickname1) >= 1) {
				$dbUser1->setField('nickname', substr($_user_nickname1, 0, -4) . substr($phone2, -4));
			}
//			$dbUser1->setField('tradePwd', $_user_tradePwd2);
//			$dbUser1->setField('salt', $_user_tradeSalt2);
            $dbUser1->setField('copartnerId', $_user_copartnerId2);
			$dbUser1->setField('contractId', $_user_contractId2);
//			$dbUser1->setField('inviteByUser', $_user_inviteByUser2);
//			$dbUser1->setField('inviteByParent', $_user_inviteByParent2);
//			$dbUser1->setField('inviteByRoot', $_user_inviteByRoot2);
			$dbUser1->update();
			var_log(\Sooh\DB\Broker::lastCmd(), 'swap tb_user1');
			if ($isSwapInvite && $_user_inviteCount1 > 0) {
				\Prj\Data\User::swapUserInviteData($dbUser1, $dbUser2);
			}

			$dbUser2->setField('ymdReg', $_user_ymdReg1);
			$dbUser2->setField('hisReg', $_user_hisReg1);
			$dbUser2->setField('phone', $phone1);
			if (preg_match('/^[a-zA-Z0-9]{6}\d{4}$/', $_user_nickname2) >= 1) {
				$dbUser2->setField('nickname', substr($_user_nickname2, 0, -4) . substr($phone1, -4));
			}
//            $dbUser2->setField('tradePwd', $_user_tradePwd1);
//            $dbUser2->setField('salt', $_user_tradeSalt1);
            $dbUser2->setField('copartnerId', $_user_copartnerId1);
			$dbUser2->setField('contractId', $_user_contractId1);
//			$dbUser2->setField('inviteByUser', $_user_inviteByUser1);
//			$dbUser2->setField('inviteByParent', $_user_inviteByParent1);
//			$dbUser2->setField('inviteByRoot', $_user_inviteByRoot1);
			$dbUser2->update();
			var_log(\Sooh\DB\Broker::lastCmd(), 'swap tb_user2');
			if ($isSwapInvite && $_user_inviteCount2 > 0) {
				\Prj\Data\User::swapUserInviteData($dbUser2, $dbUser1);
			}

			try {
				\Prj\Data\UserChangeLog::addLog('swapUser', $phone1, json_encode(['phone1' => $phone1, 'phone2' => $phone2]));
			} catch (\ErrorException $e) {
				error_log("swapUser Error#" . $phone1 . ':' . $phone2 . '#' . $e->getMessage());
			}

            $this->clearUserSession($userId1);
            $this->clearUserSession($userId2);

			return $this->returnOK('成功！');
		} catch (\Exception $e) {
			error_log($this->logPre . 'Error!!');
			error_log($e->getMessage());
			return $this->returnError('交换失败，请联系开发人员定位问题！');
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
