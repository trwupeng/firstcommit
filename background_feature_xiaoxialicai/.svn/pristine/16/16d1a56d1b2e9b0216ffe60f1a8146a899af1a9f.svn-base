<?php
namespace Prj\Data;
/**
 * User
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class User  extends \Sooh\DB\Base\KVObj{
	public $userId;

	/**
	 * user注册用户
	 * @param string $accountId   userId
	 * @param string $phone       手机号
	 * @param string $contractId  contractI的
	 * @param string $inviterCode 邀请码
	 * @param string $protocol    注册时的协议版本号
	 * @param string $clientType  客户端类型
	 * @return User
	 * @throws \ErrorException
	 * @throws \Exception
	 */
	public static function createNew($accountId, $phone, $contractId, $inviterCode, $protocol, $clientType) {
		$myInvideCode = \Prj\Data\InviteCode::add($accountId);
		$sys          = self::getCopy($accountId);
		$sys->load();
		if ($sys->exists()) {
			throw new \ErrorException(\Prj\Lang\Broker::getMsg('account.accounts_existing'));
		}

		if (!empty($inviterCode)) {
			list($inviter, $inviterParent, $inviterRoot) = array_values(\Prj\Data\InviteCode::find($inviterCode));
		} else {
			$inviter       = 0;
			$inviterParent = 0;
			$inviterRoot   = 0;
		}
		$ip   = \Sooh\Base\Tools::remoteIP();
		$dt00 = \Sooh\Base\Time::getInstance();

		$nickname = '';
		$charLib  = '2345678abcdefhijkmnpqrstuvwxyzABCDEFGHJKLMNPQRTUVWXY';
		for ($i = 0; $i < 4; $i++) {
			$nickname .= $charLib[mt_rand(0, strlen($charLib) - 1)];
		}

		$sys->setField('ymdReg', $dt00->YmdFull);
		$sys->setField('hisReg', $dt00->his);
		$sys->setField('ymdFirstBuy', 0);
		$sys->setField('ymdLastBuy', 0);
		$sys->setField('ymdFirstCharge', 0);
		$sys->setField('ipReg', $ip);
		$sys->setField('ipLast', $ip);
		$sys->setField('dtLast', $dt00->ymdhis());
		$sys->setField('phone', $phone);
		$sys->setField('nickname', 'xx' . $nickname . substr($phone, -4));
		$sys->setField('wallet', 0);
		$sys->setField('points', 0);
		$sys->setField('copartnerId', substr($contractId, 0, 4) - 0);
		$sys->setField('clientType', $clientType);
		$sys->setField('contractId', $contractId);
		$sys->setField('protocol', $protocol);
		$sys->setField('inviteByUser', $inviter);
		$sys->setField('inviteByParent', $inviterParent);
		$sys->setField('inviteByRoot', $inviterRoot);
		$sys->setField('myInviteCode', $myInvideCode);
		$sys->setField('checkinBook', '');
		$sys->setField('redPacket', 0);
		$sys->setField('redPacketUsed', 0);
		$sys->setField('redPacketRecentlyExpired', 0);
		$sys->setField('redPacketDtLast', 0);
		$sys->setField('pushSetting', '');
		$sys->setField('ymdBindcard', 0);
		$sys->setField('tradePwd', '');
		$sys->setField('firstLoginApp', 0);
		$sys->setField('ap_fetched', '');
		$sys->setField('ap_Checkin', 0);
		$sys->setField('ap_Invited', 0);
		$sys->setField('ap_InvitedInvest', 0);
		$sys->setField('ap_RechargeTimes', 0);
		$sys->setField('ap_RechargeAmount', 0);
		$sys->setField('ap_BuyTimes', 0);
		$sys->setField('ap_BuyAmount', 0);
		$sys->setField('ap_UsedShareVoucher', 0);
		$sys->setField('clientFlgs', '{"ever":[],"daily":[]}');
		$sys->setField('ap_RedPacketTimes', 0);
		try {
			$sys->update();
			return $sys;
		} catch (\ErrorException $e) {
			\Prj\Data\InviteCode::del($myInvideCode);
			throw $e;
		}

	}

	/**
	 * 补填邀请码
	 * @param string $userId 用户ID
	 * @param string $inviteCode 邀请码
	 * @return array ['target'=>邀请码对应的用户,'parent'=>父级邀请用户,'root'=>根邀请用户];
	 * @throws \ErrException
	 * @throws \ErrorException
	 * @throws \Exception
	 */
	public static function setInviteCode($userId, $inviteCode)
	{
		var_log(func_get_args(), 'setInviteCode::func_get_args');
		$sys = self::getCopy($userId);
		$sys->load();
		if (!$sys->exists()) {
			throw new \ErrorException(\Prj\Lang\Broker::getMsg('user.user_notfound'));
		}
		$_inviteByUser = $sys->getField('inviteByUser');
		if (!empty($_inviteByUser)) {
			throw new \ErrorException(\Prj\Lang\Broker::getMsg('user.user_has_been_invited'));
		}

		$_myInviteCode = $sys->getField('myInviteCode');
		if ($_myInviteCode == $inviteCode) {
			throw new \ErrorException(\Prj\Lang\Broker::getMsg('user.cant_fill_out_own_invitation_code'));
		}

		if (!empty($inviteCode)) {
			list($inviter, $inviterParent, $inviterRoot) = array_values(\Prj\Data\InviteCode::find($inviteCode));

			if (empty($inviter)) {
				throw new \ErrorException(\Prj\Lang\Broker::getMsg('user.setinvite_codemissing'));
			}

			$sys->setField('inviteByUser', $inviter);
			$sys->setField('inviteByParent', $inviterParent);
			$sys->setField('inviteByRoot', $inviterRoot);

			try {
				$sys->update();
				return ['target'=>$inviter,'parent'=>$inviterParent,'root'=>$inviterRoot];
			} catch (\ErrorException $e) {
				error_log('update user failed on '.__FUNCTION__.'() :'.$e->getMessage()."\n".$e->getTraceAsString());
				throw $e;
			}
			return [];
		}
	}

	/**
	 * 分页
	 * @param \Sooh\DB\Pager $pager  pagerClass
	 * @param array          $where  条件数组
	 * @param null           $order  排序条件
	 * @param string         $fields select fields
	 * @return array
	 */
	public static function paged($pager, $where = [], $order = null, $fields) {
		$sys = self::getCopy('');
		$db  = $sys->db();
		$tb  = $sys->tbname();

		$maps = [

		];
		$maps = array_merge($maps, $where);
		$pager->init($db->getRecordCount($tb, $maps), -1);

		if (empty($order)) {
			$order = 'rsort ymdReg';
		} else {
			$order = str_replace('_', ' ', $order);
		}

		$rs = $db->getRecords($tb, $fields, $maps, $order, $pager->page_size, $pager->rsFrom());
		return $rs;
	}

	/**
	 * 获取符合条件的记录条数
	 * @param array $where 查询条件
	 * @return mixed
	 */
	public static function getCount($where) {
		return static::loopGetRecordsCount($where);
	}

	protected static function idFor_dbByObj_InConf($isCache) {
		return 'user' . ($isCache ? 'Cache' : '');
	}


	protected static function splitedTbName($n, $isCache) {
		return 'tb_user_' . ($n % static::numToSplit());
	}

	/**
	 * getCopye
	 * @param string $userId userId
	 * @return User
	 */
	public static function getCopy($userId) {
		$tmp         = parent::getCopy(array('userId' => $userId));
		$tmp->userId = $userId;
		return $tmp;
	}

	/**
	 * 推送设置
	 * @param string  $userId 用户ID
	 * @param string  $key    推送开关名
	 * @param integer $value  1|0表示开启|关闭
	 * @return bool
	 * @throws \ErrorException
	 * @throws \Exception
	 * @throws \Sooh\Base\ErrException
	 */
	public static function setToPush($userId, $key, $value) {
		$dbUser = self::getCopy($userId);
		$dbUser->load();
		if ($dbUser->exists()) {
			$pushSetting       = $dbUser->getField('pushSetting');
			$pushSetting[$key] = $value;
			$dbUser->setField('pushSetting', $pushSetting);
			$dbUser->update();
			return true;
		} else {
			throw new \Sooh\Base\ErrException(\Prj\Lang\Broker::getMsg('account.account_is_not_existing'));
		}
	}

	/**
	 * 获取我邀请的用户
	 * @param string $userId userId
	 */
	public static function getInvitedUser($userId) {
		$sys = self::getCopy($userId);
		$db  = $sys->db();
		$tb  = $sys->tbname();
		$sys->load();
		if ($sys->exists()) {
			$rs = parent::loopFindRecords(['inviteByUser' => $userId]);
			return $rs;
		} else {
			throw new \Sooh\Base\ErrException(\Prj\Lang\Broker::getMsg('account.account_is_not_existing'));
		}
	}

	/**
	 * 获取我的邀请码树
	 * @param string $userId userId
	 */
	public static function getMineInvitedTree($userId) {
		$sys = self::getCopy($userId);
		$sys->load();
		if ($sys->exists()) {
			return \Prj\Data\InviteCode::find($sys->getField('myInviteCode'));
		} else {
			throw new \Sooh\Base\ErrException(\Prj\Lang\Broker::getMsg('account.account_is_not_existing'));
		}
	}

	/**
	 * 获得用户邀请的人数
	 * @param string $userId 用户ID
	 * @return int
	 * @throws \ErrorException
	 */
	public static function getMineInvitedUserCount($userId) {
		$sys = self::getCopy($userId);
		$sys->load();
		if ($sys->exists()) {
			$inviteCode = $sys->getField('myInviteCode');
			$where = ['inviteByUser' => $userId];
			$counts = self::loopGetRecordsCount($where);
			return $counts;
		} else {
			return 0;
		}
	}

	/**
	 * 交换用户后（串号）并交换用户的邀请关系（仅在符合交换手机的串号情况下，才可使用）
	 * @param \Prj\Data\User $user1 拥有被邀请人的User(有且只有User1有资格邀请)
	 * @param \Prj\Data\User $user2 没有被邀请人的User
	 */
	public static function swapUserInviteData($user1, $user2) {
		$userId1         = $user1->getField('userId');
		$inviteByUser1   = $user1->getField('inviteByUser');
		$inviteByParent1 = $user1->getField('inviteByParent');
		$inviteByRoot1   = $user1->getField('inviteByRoot');
		$inviteCode1     = $user1->getField('myInviteCode');

		$userId2         = $user2->getField('userId');
		$inviteByUser2   = $user2->getField('inviteByUser');
		$inviteByParent2 = $user2->getField('inviteByParent');
		$inviteByRoot2   = $user2->getField('inviteByRoot');
		$inviteCode2     = $user2->getField('myInviteCode');

		//交换邀请码
		$user1->setField('myInviteCode', $inviteCode2);
		$user1->update();
		$user2->setField('myInviteCode', $inviteCode1);
		$user2->update();

		//交换一级邀请关系:inviteByUser
		$where = [
			'inviteByUser' => $userId1,
		];
		$_retUser = self::loopFindRecords($where);
		if (!empty($_retUser)) {
			foreach ($_retUser as $v) {
				$_userId = $v['userId'];
				$_user = self::getCopy($_userId);
				$_user->load();
				if ($_user->exists()) {
					$_user->setField('inviteByUser', $userId2);
					$_user->setField('inviteByParent', $inviteByUser2);
					$_user->setField('inviteByRoot', $inviteByRoot2 ? : $userId2);
					$_user->update();
					var_log(\Sooh\DB\Broker::lastCmd(), 'swap inviteByUser user(' . $_userId . ') in Prj\\Data\\User\\swapUserInviteData');
				}
				unset($_userId);
				unset($_user);
			}
		}

		//交换二级邀请关系:inviteByParent
		$whereForParent = [
			'inviteByParent' => $userId1,
		];
		$_retParent = self::loopFindRecords($whereForParent);
		if (!empty($_retParent)) {
			foreach ($_retParent as $v) {
				$_userId = $v['userId'];
				$_user = self::getCopy($_userId);
				$_user->load();
				if ($_user->exists()) {
					$_user->setField('inviteByParent', $userId2);
					$_user->setField('inviteByRoot', $inviteByRoot2 ? : $userId2);
					$_user->update();
					var_log(\Sooh\DB\Broker::lastCmd(), 'swap inviteByParent user(' . $_userId . ') in Prj\\Data\\User\\swapUserInviteData');
				}
				unset($_userId);
				unset($_user);
			}
		}

		//交换根邀请关系:inviteByRoot
		$whereForRoot = [
			'inviteByRoot' => $userId1,
		];
		$_retRoot = self::loopFindRecords($whereForRoot);
		if (!empty($_retRoot)) {
			foreach ($_retRoot as $v) {
				$_userId = $v['userId'];
				$_user = self::getCopy($_userId);
				$_user->load();
				if ($_user->exists()) {
					$_user->setField('inviteByRoot', $userId2);
					$_user->update();
					var_log(\Sooh\DB\Broker::lastCmd(), 'swap inviteByRoot user(' . $_userId . ') in Prj\\Data\\User\\swapUserInviteData');
				}
				unset($_userId);
				unset($_user);
			}
		}
	}

    /**
     * 通过手机号获取用户对象
     * @param $phone
     * @return null|\Sooh\DB\Base\KVObj
     */
    public static function getByPhone($phone){
        $userId = self::loopFindRecords(['phone'=>$phone])[0]['userId'];
        if(empty($userId)){
            return null;
        }else{
            $tmp = self::getCopy($userId);
            $tmp->load();
            return $tmp;
        }
    }

	/**
	 * 更新红包余额（sum未过期红包）
	 * @param string $userId 用户ID
	 * @throws \ErrorException
	 * @throws \Exception
	 */
	public static function refreshExpiredRedpacketAmount($userId) {
		$user = self::getCopy($userId);
		$user->load();
		if ($user->exists()) {
			$voucher = \Prj\Data\Vouchers::getCopy($userId);
			$db = $voucher->db();
			$tbname = $voucher->tbname();
			$nowTime = date('YmdHis', \Sooh\Base\Time::getInstance()->timestamp());
			$where = [
				'userId'      => $userId,
				'voucherType' => \Prj\Consts\Voucher::type_real,
				'statusCode'  => \Prj\Consts\Voucher::status_unuse,
				'dtExpired>'  => $nowTime,
			];
			$ret = $db->getOne($tbname, 'sum(amount)', $where);
			$ret -= 0;
			$user->setField('redPacket', $ret);
			if ($ret) {
				$recentlyExpired = $db->getOne($tbname, 'dtExpired', $where, 'sort dtExpired');
				$user->setField('redPacketRecentlyExpired', $recentlyExpired - 0);
			}
			$user->update();
			var_log(\Sooh\DB\Broker::lastCmd(false), 'refresh Expired redPacket Amounts sql');
		} else {
			error_log('user(' . $userId . ') not found in refresh Expired redPacket Amount');
		}
	}

	/**
	 * 新增红包时更新红包最近过期时间
	 * @param string $userId 用户ID
	 * @param string $expired 新红包的过期时间
	 * @throws \ErrorException
	 * @throws \Exception
	 * @return true
	 */
	public static function updateExpiredRedpacket($userId, $expired) {
		$nowTime = date('YmdHis', \Sooh\Base\Time::getInstance()->timestamp());
		if ($expired > $nowTime) {
			$user = self::getCopy($userId);
			$user->load();
			if ($user->exists()) {
				$recentlyExpired = $user->getField('redPacketRecentlyExpired') - 0;
				if ($recentlyExpired == 0 || $expired < $recentlyExpired) {
					$user->setField('redPacketRecentlyExpired', $expired);
					$user->update();
					var_log(\Sooh\DB\Broker::lastCmd(false), 'update Expired Redpacket');
					error_log('updateExpiredRedpacket success, userId:' . $userId . '; expired:' . $expired);
				}
			} else {
				error_log('updateExpiredRedpacket error, because user not found');
			}
		}
		return true;
	}

	/**
     * 交换登录手机号（只换手机号）
	 * @param string $oldPhone 旧手机号
	 * @param string $newPhone 新手机号
	 * @return int
	 *  601001      旧手机号不存在
	 *  601002      新手机号已经存在
	 * @throws \ErrorException
	 * @throws \Exception
	 */
	public static function changeUserPhone($oldPhone, $newPhone)
	{
		$dbOld = \Sooh\DB\Cases\AccountAlias::getCopy([$oldPhone, 'phone']);
		$dbOld->load();
		if ($dbOld->exists()) {
			$userId = $dbOld->getField('accountId');

			$dbNew = \Sooh\DB\Cases\AccountAlias::getCopy([$newPhone, 'phone']);
			$dbNew->load();
			if ($dbNew->exists()) {
				return 601002;
			}

			//变更tb_loginname
			if ($dbOld->tbname() == $dbNew->tbname()) {
				//在同一张表中
				$dbOld->setField('loginName', $newPhone);
				$dbOld->update();
			} else {
				//在不同的表中
				$dbOld->setField('loginName', 'changed(New:' . $newPhone . ')');
				$dbOld->update();
				$dbNew->setField('accountId', $userId);
				$dbNew->setField('flgStatus', 0);
				$dbNew->update();
			}
			var_log(\Sooh\DB\Broker::lastCmd(false), 'change user phone for tb_loginname');

			//变更tb_accounts
			$dbAccount = \Sooh\DB\Cases\AccountStorage::getCopy($userId);
			$dbAccount->load();
			if ($dbAccount->exists()) {
				$dbAccount->setField('nickname', substr($newPhone, 0, 3) . '****' . substr($newPhone, -4));
				$dbAccount->setField('phone', $newPhone);
				$dbAccount->update();
			}
			var_log(\Sooh\DB\Broker::lastCmd(false), 'change user phone for tb_accounts');

			//变更tb_user
			$dbUser = \Prj\Data\User::getCopy($userId);
			$dbUser->load();
			if ($dbUser->exists()) {
				$dbUser->setField('phone', $newPhone);
				if (preg_match('/^[a-zA-Z0-9]{6}\d{4}$/', $dbUser->getField('nickname')) >= 1) {
					$dbUser->setField('nickname', (substr($newPhone, 0, 3) . '****' . substr($newPhone, -4)));
				}
				$dbUser->update();
			}
			var_log(\Sooh\DB\Broker::lastCmd(false), 'change user phone for tb_user');
			return 0;
		} else {
			return 601001;
		}
	}

	/**
	 * 注销手机号
     * 帐号无法登录
	 * @param string $phone 手机号
	 * @return int
	 *  601001      旧手机号不存在
	 * @throws \ErrorException
	 * @throws \Exception
	 */
	public static function cancelUserPhone($phone)
	{
		$dbLogin = \Sooh\DB\Cases\AccountAlias::getCopy([$phone, 'phone']);
		$dbLogin->load();
		if ($dbLogin->exists()) {
			$userId = $dbLogin->getField('accountId');
			$dbLogin->setField('cameFrom', 'cancelled(' . \Sooh\Base\Time::getInstance()->YmdFull . ')');
			$dbLogin->update();

			$dbAccount = \Sooh\DB\Cases\AccountStorage::getCopy($userId);
			$dbAccount->load();
			if ($dbAccount->exists()) {
				$dbAccount->setField('phone', $dbAccount->getField('phone') . '(cancelled)');
				$dbAccount->update();
			}

			$dbUser = \Prj\Data\User::getCopy($userId);
			$dbUser->load();
			if ($dbUser->exists()) {
				$dbUser->setField('phone', $dbUser->getField('phone') . '00');
				$dbUser->update();
				$dbUser->lock('cancelled:' . \Sooh\Base\Time::getInstance()->ymdhis());
			}
			var_log(\Sooh\DB\Broker::lastCmd(false), 'cannel user phone:' . $phone);
		} else {
			return 601001;
		}
	}

    public static function getUserByPhoneOrUserId($uid){
        if(strlen($uid)==11){
            $phone = $uid;
            return self::getByPhone($phone);
        }else{
            $userId = $uid;
            $user = self::getCopy($userId);
            $user->load();
            return $user->exists()?$user:null;
        }
    }
}
