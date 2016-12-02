<?php
namespace Sooh\Base\Form;
/**
 * options 封装类
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class Options {
	protected $arr=array();
	protected $setting=null;
	protected $captForEmpty=null;
	public $displaySize;
	/**
	 * 构建options， 
	 * arr ： 
	 *		array(0=>否，1=>是) 
	 *		或 "getPair,dbid,tbname,k,v,whereDefineByJson"
	 * 
	 * $displaySize的含义：
	 *		checkbox和radio： 一行显示多少个
	 *		dropdown：			显示多少行
	 * 
	 * @param mixed $arr
	 * @param string $captForEmpty '' 空串对应的显示值
	 */
	public function __construct($arr,$captForEmpty=null) {
		$this->captForEmpty=$captForEmpty;
		if(is_array($arr))$this->arr=$arr;
		else{
			$this->setting = explode(',',$arr);
		}
	}
	public function initDisplaySize($size=1)
	{
		$this->displaySize=$size;
	}
//	public static function getCopy($id='default',$crud='c',$method='get')
//	{
//		if(!isset(self::$_copy[$id])){
//			$nm = __CLASS__;
//			self::$_copy[$id] = new $nm();
//			self::$_copy[$id]->guid=$id;
//			self::$_copy[$id]->crudType=$crud;
//			self::$_copy[$id]->method=$method;
//		}
//		return self::$_copy[$id];
//	}
	
	public function initDefault($defaultDesc)
	{
		
	}
	public function getPair($record=null,$showEmpty=false)
	{
		if(!empty($this->setting)){
			if($this->setting[0]=='getPair'){
				$where = $this->setting[4];
				if(!empty($where)){
					$where = json_decode($where,true);
					foreach($where as $k=>$v){
						if($v[0]=='{' && substr($v,-1)=='}'){
							$v = substr($v,1,-1);
							$where[$k] = $record[$v];
						}
					}
				}
				$r = \Sooh\DB\Broker::getInstance($this->setting[1])->getPair($this->setting[2], $this->setting[3], $this->setting[4],$where);
			}else {
				throw new \ErrorException('unknown getPairMethod');
			}
		}else{
			$r = $this->arr;
		}
		if($showEmpty && $this->captForEmpty){
			$r['']=$this->captForEmpty;
		}
		$this->pairVals = $r;
	}
	public $pairVals;
}
