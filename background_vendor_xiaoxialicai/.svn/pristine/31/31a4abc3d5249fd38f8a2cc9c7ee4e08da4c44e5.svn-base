<?php
namespace Sooh\DB\Base;

/**
 * 主键是一个长整数的情况，
 * @author Simon Wang <sooh_simon@163.com> 
 */
class KVObjBigint extends KVObj
{
	/**
	 * 根据pkey计算分表用的id值（默认使用完整的pkey,得出0-99分布）
	 * @param type $pkey
	 * @return int
	 */
	protected static function indexForSplit($pkey)
	{
		$n = current($pkey);
		if(is_numeric($n) && !strpos($n, '.')){
			return $n%10000;
		}
	}
//	/**
//	 * 
//	 * @param string $account
//	 * @param string $camefrom
//	 * @return \Prj\Data\Orders
//	 */
//	public static function getCopy($ordersId) {
//		return parent::getCopy(['ordersId'=>$ordersId]);
//	}

	
	//针对缓存，非缓存情况下具体的表的名字
	protected static function splitedTbName($n,$isCache)
	{
//		if($isCache)return 'tb_test_cache_'.($n % static::numToSplit());
//		else 
		return 'tb_recharges_'.($n % static::numToSplit());
	}
//指定使用什么id串定位数据库配置
	protected static function idFor_dbByObj_InConf($isCache)
	{
		return 'default';
	}
	//针对缓存，非缓存情况下具体的表的名字

	//说明分几张表
	protected static function numToSplit(){return 1;}

//	/**
//	 * 是否启用cache机制
//	 * cacheSetting=0：不启用
//	 * cacheSetting=1：优先从cache表读，每次更新都先更新硬盘表，然后更新cache表
//	 * cacheSetting>1：优先从cache表读，每次更新先更新cache表，如果达到一定次数，才更新硬盘表
//	 */
//	protected function initConstruct($cacheSetting=0,$fieldVer='iRecordVerID')
//	{
//		return parent::initConstruct($cacheSetting,$fieldVer);
//	}
}
