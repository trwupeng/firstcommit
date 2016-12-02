<?php
namespace Prj\Data;
/**
 * 配置获取和设置
 *
 * @author gh.tang
 */
class TbConfigItem extends \Sooh\DB\Base\KVObj{
	protected static function indexForSplit($pkey){
		return 1;
	}
	/**
	 * 
	 * @param type $str
	 * return \Prj\Data\TbConfigLimit
	 */
	public static function decodeLimit($str)
	{
		$limit = new configExtLimit();
		return $limit;
	}
	/**
	 * 
	 * @param \Prj\Data\TbConfigLimit $limit
	 * @return string
	 */
	public static function encodeLimit($limit)
	{
		return "";
	}
	protected function initConstruct($cacheSetting=1,$fieldVer='iRecordVerID')
	{
		return parent::initConstruct($cacheSetting, $fieldVer);
	}
	protected static function splitedTbName($n,$isCache)
	{
		if($isCache){
			return 'tb_config_ram';
		}else{
			return 'tb_config';
		}
	}
	/**
	 * @return \Prj\Data\TbConfigItem
	 */
	public static function getCopy($pkey) {
		return parent::getCopy(['k'=>$pkey]);
	}
}
