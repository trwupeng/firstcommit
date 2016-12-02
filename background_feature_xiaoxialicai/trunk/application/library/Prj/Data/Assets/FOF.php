<?php
namespace Prj\Data\Assets;
/**
 * 资产 FOF，登记记录基本信息，如名称，报价，合同号等
 *
 * @author simon.wang <hillstill_simon@163.com>
 */
class FOF extends \Sooh\DB\Base\KVObj{
	/**
	 * 
	 * @param string $account
	 * @param string $camefrom
	 * @return \Prj\Data\Assets\FOF
	 */
	public static function getCopy($account, $camefrom = 'local') {
		return parent::getCopy($account, $camefrom);
	}

	
	//针对缓存，非缓存情况下具体的表的名字
	protected static function splitedTbName($n,$isCache)
	{
//		if($isCache)return 'tb_test_cache_'.($n % static::numToSplit());
//		else 
		return 'tb_assets_fof_'.($n % static::numToSplit());
	}
//指定使用什么id串定位数据库配置
	protected static function idFor_dbByObj_InConf($isCache)
	{
		return 'default';
	}
	//针对缓存，非缓存情况下具体的表的名字

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
