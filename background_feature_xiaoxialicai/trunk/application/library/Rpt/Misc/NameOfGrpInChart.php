<?php
namespace Rpt\Misc;
/* 
 * 日报中图片报表中二级分组对应名称的处理
 */
class NameOfGrpInChart{
	public static function clienttype($val,$forACT=null)
	{

			$clientName = \Prj\Consts\ClientType::clientTypes($val);
			if(empty($clientName)) {
				$clientName =  '未知端类型';
			}
			return $clientName;
	}

	public static function copartnerId($val, $forAct=null) {
		$copartners = \Prj\Data\Copartner::getCopy();
		$copartners = $copartners->getAllCopartnerNameById();
		if(!isset($copartners[$val])) {
			return '渠道号'.$val;
		}
		return $copartners[$val];
	}

	public static function flgext01($val,$forACT=null)
	{
		if(substr($forACT, 0,3)=='Buy' || substr($forACT, 0,4)=='Prdt') {
			$waresTye = \Prj\Consts\Wares::$shilfIdName[$val];
			if(empty($waresTye)) {
				$waresTye = '位置产品类型';
			}
			return $waresTye;

			// 下面两个情况暂时未使用到
		}elseif(substr($forACT, 0,7)=='Finance'){
			return self::flgext01_finance($val);
		}else{
			$f = 'flgext01_'.strtolower($forACT);
			return self::$f($val);
		}
	}

	public static function flgext02($val,$forACT=null)
	{
		if(substr($forACT, 0,3)=='Buy' || substr($forACT, 0,5)=='NewRe' || substr($forACT, 0,5)=='Accou'){
			return self::flgext01_accounts($val);
		}else{
			throw new \ErrorException('unknown flgext02 for '.$forACT);
		}
	}

	/**
	 *
	 * 目前只知道0和1的用户，其他的暂时未定义
	 * @param $val
	 * @return string
	 */
	protected static function flgext01_accounts($val)
	{
		switch ($val){
			case 0:return '普通用户';
			case 1:return '◎系统◎';
			default: return '未知类型用户';
		}
	}
}
