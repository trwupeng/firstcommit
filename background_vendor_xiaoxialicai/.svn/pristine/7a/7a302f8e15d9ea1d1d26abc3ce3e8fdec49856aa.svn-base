<?php
namespace Sooh\Base;

use \Sooh\Base\Time as sooh_time;
/**
 * usage:
 *		::decode('123456',array(a=>4,b=>2))  
 *				return array(a=>1234,b=>56);
 *		::encode(array(a=>1234,b=>569),array(a=>4,b=>2)) 
 *				return '123499';
 * 
 * $obj = ::factory(12011401,array(mdy=>6,n=>2),array(n=>0));
 * when today is 2014-12-01 : $obj->val('n')==1; $obj->toString()=='12011401';
 * when today is 2014-12-02 : $obj->val('n')==0; $obj->toString()=='12021400';
 * 
 * @author Simon Wang <sooh_simon@163.com> 
 */
class NumStr
{
	public static function encode($values,$define)
	{
		$str = '';
		foreach($define as $k=>$l){
			if($l>0){
				$max=  str_repeat('9', $l);
				if($values[$k]>$max)$str.=$max;
				else $str .= sprintf('%0'.$l.'d',$values[$k]);
			}
		}
		$str = ltrim($str,'0');
		if($str=='')return '0';
		else return $str;
	}

	public static function decode($str,$define)
	{	
		$ret=array();
		$size = array_sum($define);
		$l=strlen($str);
		if($l>$size)return null;
		elseif($l<$size)$str = str_repeat('0',$size-$l).$str;
		$i=0;
		foreach($define as $k=>$l){
			if($l>0){
				$n = substr($str,$i,$l);
				$i+=$l;
				$ret[$k]=$n-0;
			}
		}
		return $ret;
	}
	/**
	 * 
	 * @param int or string $str 
	 * @param array $fieldDefine
	 * @return \Sooh\Tool\NumStr
	 */
	public function factory($str,$fieldDefine,$defaultOnDayChange=null)
	{
		$class = get_called_class();
		return new $class($str,$fieldDefine,$defaultOnDayChange);
	}
	protected $fieldDef;
	protected $vals;
	public function __construct($str,$fieldDefine,$defaultOnDayChange) {
		$this->fieldDef = $fieldDefine;
		$this->vals = self::decode($str, $fieldDefine);
		$this->ifResetByDayChange($defaultOnDayChange);
	}
	protected function ifResetByDayChange($defaultOnDayChange)
	{
		if(isset($this->fieldDef['mdy'])){
			$today = sooh_time::getInstance()->mdy;
			$cmp = $this->vals['mdy'];
			$field = 'mdy';
		}elseif(isset($this->fieldDef['ymd'])){
			$today = sooh_time::getInstance()->ymd;
			$cmp = $this->vals['ymd'];
			$field = 'ymd';
		}elseif(isset($this->fieldDef['Ymd'])){
			$today = sooh_time::getInstance()->YmdFull;
			$cmp = $this->vals['Ymd'];
			$field = 'Ymd';
		}
		if($cmp!==$today){
			if(is_array($defaultOnDayChange)){
				foreach($defaultOnDayChange as $k=>$v)
					$this->vals[$k]=$v;
			}else{
				$this->vals=array();
				foreach ($this->fieldDef as $k=>$v)
					$this->vals[$k]=0;
			}
			$this->vals[$field]=$today;
		}
	}
	public function val($field)
	{
		return $this->vals[$field];
	}
	public function toString()
	{
		return self::encode($this->vals, $this->fieldDef);
	}
}
