<?php
namespace Sooh\DB\Cases;
/**
 * 基础账号
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class AccountAlias extends \Sooh\DB\Base\KVObj
{
	protected static function splitedTbName($n, $isCache)
	{
//		if($isCache)return 'tb_test_cache_'.($n % static::numToSplit());
//		else 
		return 'tb_loginname_' . ($n % static::numToSplit());
	}

	/**
	 * @param array $pkey ['loginName', 'cameFrom']
	 * @return AccountAlias
	 */
	public static function getCopy($pkey)
	{
		return parent::getCopy(array('loginName' => $pkey[0], 'cameFrom' => $pkey[1]));
	}

	protected static function idFor_dbByObj_InConf($isCache)
	{
		return 'oauth';
	}
}
