<?php
namespace Sooh\DB\Cases;
/**
 * 账户表存取类
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class SessionStorage extends \Sooh\DB\Base\KVObj{
	public static $__id_in_dbByObj='default';
	public static $__nSplitedBy=1;
	protected static function idFor_dbByObj_InConf($isCache){	return self::$__id_in_dbByObj;}

	//针对缓存，非缓存情况下具体的表的名字
	protected static function splitedTbName($n,$isCache)
	{
//		if($isCache)return 'tb_test_cache_'.($n % static::numToSplit());
//		else 
		return 'tb_session_'.($n % static::numToSplit());
	}

	/**
	 * 说明getCopy实际返回的类，同时对于只有一个主键的，可以简化写法
	 * @return \Sooh\DB\Cases\SessionStorage
	 */
	public static function getCopy($sessionId)
	{
		return parent::getCopy(array('sessionId'=>$sessionId));
	}

	public function setVerId($id)
	{
		$this->r[$this->fieldName_verid]=$id;
	}
	public function setSessionData($chg)
	{
		$arr = $this->getSessionData();
		foreach($chg as $r){
			$depth = sizeof($r[1]);
			if($r[0]=='set'){
				switch ($depth){
					case 1:	$arr[ $r[1][0] ] = $r[2];	break;
					case 2:	$arr[ $r[1][0] ][ $r[1][1] ] = $r[2]; break;
					default :error_log('session too deep:'.  implode(',', $r[1]));	break;
				}
			}else{//unset
				switch ($depth){
					case 1: unset($arr[ $r[1][0] ]); break;
					case 2: unset($arr[ $r[1][0] ][ $r[1][1] ]); break;
					default :error_log('session too deep:'.  implode(',', $r[1]));	break;
				}
			}
		}
		parent::setField('accountId', $arr['accountId']);
		parent::setField('sessionData', json_encode($arr));
	}
	public function getSessionData()
	{
		$ret = $this->getField('sessionData',true);
		if(is_array($ret)){
			return $ret;
		}elseif(is_string($ret)){
			return json_decode($ret,true);
		}else{
			return array();
		}
	}
	public function getArrayTrans()
	{
		$tmp = $this->r;
		unset($tmp['sessionData']);
		return $tmp;
	}
}
