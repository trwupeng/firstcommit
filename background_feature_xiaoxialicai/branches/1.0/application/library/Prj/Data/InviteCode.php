<?php
namespace Prj\Data;
/**
 * TODO:邀请码
 *
 * @author simon.wang
 */
class InviteCode extends \Sooh\DB\Base\KVObj {
//	private static $tb='db_p2p.tb_invitecodes_0';

	public static function getCopy($key)
	{
		return parent::getCopy(['inviteCode' => $key]);
	}

	protected static function idFor_dbByObj_InConf($isCache)
	{
		return 'invitecodes';
	}

	protected static function splitedTbName($n, $isCache)
	{
		return 'tb_invitecodes_' . ($n % static::numToSplit());
	}
	
	/**
	 * 创建一个用户的邀请码
	 * @param string $userId
	 * @return string
	 */
	public static function add($userId){
//		$db = \Sooh\DB\Broker::getInstance();

		for ($retry = 0; $retry < 10; $retry++) {
			for ($i = 0; $i <= 20; $i++) {
				$s       = '';
				$charLib = '2345678ABCDEFGHJKLMNPQRTUVWXY';
				for ($i = 0; $i < 7; $i++) {
					//从字符库中随机一个位置[数字]
					$strLoc = rand(0, strlen($charLib) - 1);
					$s .= $charLib[$strLoc];
				}

				$dbInvitecode = self::getCopy($s);
				$dbInvitecode->load();
				if ($dbInvitecode->exists()) {
					break;
				}
				$dbInvitecode->setField('userId', $userId);
				$dbInvitecode->update();
				return $s;

//				$record = $db->getOne(self::$tb, 'userId', ['inviteCode' => $s]);
//				if (!empty($record)) {
//					break;
//				}
//
//				$ret = $db->addRecord(self::$tb, ['inviteCode' => $s, 'userId' => $userId]);
//				if ($ret) {
//					return $s;
//				}
			}
		}
		\Sooh\Base\Log\Data::getInstance()->error('create_inviteCode_failed_for_' . $userId);
		return '';
	}
	
	public static function del($code)
	{
//		\Sooh\DB\Broker::getInstance()->delRecords(self::$tb,['inviteCode'=>$code]);
		$dbInvitecode = self::getCopy($code);
		$dbInvitecode->load();
		$dbInvitecode->delete();
	}

	public static function getUser($code) {
//		$db = \Sooh\DB\Broker::getInstance();
//		$userId = $db->getOne(self::$tb, 'userId', ['inviteCode' => $code]);
//		return $userId;

		$dbInviteCode = self::getCopy($code);
		$dbInviteCode->load();
		if ($dbInviteCode->exists()) {
			return $dbInviteCode->getField('userId');
		} else {
			return false;
		}
	}
	/**
	 * 获取一个邀请码的用户串
	 * @param string $code inviteCode
	 * @param string $userId userId
	 * @return [target:xxx, parent:xxx,root:xxx]
	 */
	public static function find($code, $userId = '')
	{
		if ($userId === '') {
			if (empty($code)) {
				return ['target'=>0,'parent'=>0,'root'=>0];
			}
			$dbInviteCode = self::getCopy($code);
			$dbInviteCode->load();
			if ($dbInviteCode->exists()) {
				$u = $dbInviteCode->getField('userId');
			} else {
				$u = '';
			}
//			$db= \Sooh\DB\Broker::getInstance();
//			$u = $db->getOne(self::$tb, 'userId',['inviteCode'=>$code]);
		} else {
			$u = $userId;
		}

		if(empty($u)){
			$target=0;
			$parent=0;
			$root=0;
		}else{
			$target=$u;
			$tmp = \Prj\Data\User::getCopy($target);
			$tmp->load();
			$parent = $tmp->getField('inviteByUser',true);
			if(empty($parent)){
				$parent=0;
				$root = $target;
			}else{
				$root = $tmp->getField('inviteByRoot',true);
				if(empty($root)){
					$root=$parent;
					\Sooh\Base\Log\Data::getInstance()->error('inviteCode_root_missing_'.$target);
				}
			}
		}
		$ret= ['target'=>$target,'parent'=>$parent,'root'=>$root];
		return $ret;
	}

	/**
	 * 废弃，目前没使用过，此方法不支持分表分库
	 * @return mixed
	 * @throws \ErrorException
	 */
	public static function devRandExists()
	{
		$db= \Sooh\DB\Broker::getInstance();
		$r = $db->getCol(self::$tb, 'inviteCode',null,null,30);
		$r = array_combine($r, $r);
		return array_rand($r);
	}
}
