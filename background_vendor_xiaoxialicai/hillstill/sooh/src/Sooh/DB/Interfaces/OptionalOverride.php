<?php
namespace Sooh\DB\Interfaces;
/**
 *
 * @author Simmon Wang<soon_simmon@163.com>
 */
abstract class OptionalOverride { //默认需要表里有个iRecordVerID字段做为序列锁用
	//目的是简化特定对象的key, 然后用注释的方式ide中正确识别是哪个类
	public static function getCopy($roleid);
	//缓存中间表使用方式3选1，默认是 （0,'iRecordVerID'）
		
	//protected function initConstruct(){return parent::initConstruct(0,'_ver_id');}   //without cache table, field _ver_id(int) defined in table as sequence lock
	//protected function initConstruct(){return parent::initConstruct(1,'_ver_id');}  //with cache table, update disk first, then cache
	//protected function initConstruct(){return parent::initConstruct(5,'_ver_id');}//with cache table, update cache first, update disk when _ver_id%5==0

	//分表用的，根据算出的id使用不同的表  $n 的范围 0-99,
		protected static function splitedTbName($n,$isCache)
		{
			if($isCache)return 'cache_'.($n%10);
			else return 'test_'.($n%10);
		}

}
