<?php
namespace Sooh\DB\Conv;

/**
 * Description of Datetime
 *
 * @author Simmon Wang <hillstill_simon@163.com>
 */
class Datetime {
	/**
	 * @param \Datetime $val
	 * @return int
	 */
	public static function sqlsrv_to_timestamp_val($val)
	{
		if(is_scalar($val)){
			if(is_numeric($val))return $val;
			else {
				$n = strtotime ($val);
				if($n===false)throw new ErrorException ('value support to be datetime,but '.$val.' given');
				return $n;
			}
		}elseif($val!==null)	return $val->getTimestamp();
		else return null;
	}
	/**
	 * 
	 * @param int $val
	 * @return string
	 */
	public static function timestamp_to_sqlsrv_val($val)
	{
		return date('Y-m-d H:i:s',$val);
	}
	
	public static function loop_to_timestamp($fields,&$r,$rType='row',$funcType='sqlsrv')
	{
		$func = $funcType.'_to_timestamp_val';
		if($rType=='rows'){
			foreach($r as $i=>$v){
				foreach($fields as $field)
					$r[$i][$field]=self::$func($v[$field]);
			}
		}else{
			foreach($fields as $field)
				$r[$field]=self::$func($r[$field]);
		}
	}
	
	public static function loop_from_timestamp($fields,&$r,$rType='row',$funcType='sqlsrv')
	{
		$func = 'timestamp_to_'.$funcType.'_val';
		if($rType=='rows'){
			foreach($r as $i=>$v){
				foreach($fields as $field)
					$r[$i][$field]=self::$func($v[$field]);
			}
		}else{
			foreach($fields as $field)
				$r[$field]=self::$func($r[$field]);
		}
	}	
}
