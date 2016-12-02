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
	public static function refreshExpiredRedpacketAmount($userId){
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
}
