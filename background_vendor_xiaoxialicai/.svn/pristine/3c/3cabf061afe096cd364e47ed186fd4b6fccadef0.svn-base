<?php
namespace Sooh\DB\Cases;
/**
 * 账户表存取类
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class AccountStorage extends \Sooh\DB\Base\KVObj
{
	//针对缓存，非缓存情况下具体的表的名字
	protected static function splitedTbName($n, $isCache)
	{
//		if($isCache)return 'tb_test_cache_'.($n % static::numToSplit());
//		else 
		return 'tb_accounts_' . ($n % static::numToSplit());
	}

	/**
	 * @return AccountStorage
	 */
	public static function getCopy($accountId)
	{
		return parent::getCopy(array('accountId' => $accountId));
	}

	protected static function idFor_dbByObj_InConf($isCache)
	{
		return 'oauth';
	}

	/**
	 * 修改密码
	 * @param string $accountname
	 * @param string $password
	 * @param string $camefrom
	 * @param array $customArgs 附带修改什么
	 * @return boolean
	 */
	public function resetPWD($password, $customArgs = array())
	{
		$cmp = md5($password . $this->getField('passwdSalt'));
		$this->setField('passwd', $cmp);
		foreach ($customArgs as $k => $v) {
			$this->setField($k, $v);
		}
		try {
			$this->update();
			return true;
		} catch (\ErrorException $ex) {
			error_log($ex->getMessage() . "\n" . $ex->getTraceAsString());
			return false;
		}
	}
}
