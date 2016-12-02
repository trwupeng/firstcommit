<?php
/**
 * 周活跃领取记录
 * User: LTM <605415184@qq.com>
 * Date: 2016/2/24
 * Time: 18:09
 */

namespace Prj\Data;
/**
 * 周活跃领取记录
 */
class APFetchLog extends \Sooh\DB\Base\KVObj
{
	protected static function indexForSplit($pkey){
		return substr(current($pkey),-4)-0;
	}
	protected function initConstruct($cacheSetting=0,$fieldVer='iRecordVerID')
	{
		return parent::initConstruct($cacheSetting, $fieldVer);
	}
	protected static function splitedTbName($n,$isCache)
	{
		return 'tb_apFetchLog_'.($n%static::numToSplit());
	}
	protected static function idFor_dbByObj_InConf($isCache)
	{
		return 'APFetchLog';
	}
	/**
	 * @return \Prj\Data\TbConfigItem
	 */
	public static function getCopy($pkey) {
		return parent::getCopy(['autoid'=>$pkey]);
	}
}