<?php
namespace Prj\Data;
class Account extends \Sooh\DB\Cases\AccountStorage
{
	//针对缓存，非缓存情况下具体的表的名字
	protected static function splitedTbName($n,$isCache){return 'tb_accounts_'.($n % static::numToSplit());}
	
	protected static function idFor_dbByObj_InConf($isCache){return 'oauth';}
}