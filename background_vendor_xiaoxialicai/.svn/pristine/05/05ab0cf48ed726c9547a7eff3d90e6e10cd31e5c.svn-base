<?php
namespace Sooh\Base\Form;
/**
 * 表单基类, items可以设置非\Sooh\Base\Form\Item：表示hidden模式的
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class Broker {
	const type_c = 'create';
	const type_u = 'update';
	const type_s = 'search';
	public $url;
	public $method='get';
	public $html_id='';
	
	/**
	 * return "<form action='...' method='get'  ....>" 
	 * 
	 * @param string $otherAttr "style='xxx' onsubmit='xxx'"
	 * @return string 
	 */
	public function renderFormTag($otherAttr=null)
	{
		$str = $this->getRenderer()->htmlFormTag($this);
		if(!empty($otherAttr)){
			return "<form $otherAttr ".substr($str,5);
		}else{
			return $str;
		}
	}
	/**
	 * @return Renderer
	 */
	protected function getRenderer()
	{
		if(empty($this->renderer)){
			$this->renderer = new Renderer();
		}
		return $this->renderer;
	}
	/**
	 * 
	 * @param Renderer $renderer
	 */
	public function setRenderer($renderer)
	{
		$this->renderer=$renderer;
	}
	/**
	 *
	 * @var Renderer 
	 */
	protected $renderer;
	/**
	 * 
	 * @param string $title title-displayed
	 * @param string $tpl default:<tr><td>{btn}</td></tr>
	 * @return string
	 */
	public function renderSubmit($title,$tpl='<tr><td>{btn}</td></tr>')
	{
		$btn = $this->getRenderer()->htmlFormButton($title, 'submit');
		return str_replace('{btn}', $btn, $tpl);
	}
	
	protected static $_copy=array();
	/**
	 * 
	 * @param string $id
	 * @return \Sooh\Base\Form\Broker
	 */	
	public static function getCopy($id='default')
	{
		if(!isset(self::$_copy[$id])){
			$nm = get_called_class();
			self::$_copy[$id] = new $nm();
			self::$_copy[$id]->guid=$id;
		}
		return self::$_copy[$id];
	}
	/**
	 * 
	 * @param type $url
	 * @param type $method
	 * @param string $cruds  c(create) r(read) u(update) d(delete) s(search)
	 * @param type $html_id
	 * 
	 * @return \Sooh\Base\Form\Broker
	 */
	public function init($url,$method='get',$cruds=self::type_s,$html_id='')
	{
		$this->url      = $url;
		$this->method   = $method;
		$this->html_id  = $html_id;
		$this->crudType = $cruds;
		return $this;
	}
	/**
	 * 
	 * @param string $k
	 * @param \Sooh\Base\Form\Item $item
	 * @return \Sooh\Base\Form\Broker
	 */
	public function addItem($k,$item)
	{
		$this->items[$k]=$item;
		return $this;
	}
	
	protected $guid;
	public $items=array();
	protected $methods=array(
		'_eq'=>'=',	'_ne'=>'!',
		'_gt'=>'>',	'_g2'=>']',
		'_lt'=>'<',	'_l2'=>'[',
		'_lk'=>'*',
	);

	/**
	 * 从request中获取表单的值
	 * @param array $request
	 * @param string $errClassName
	 * @param \Sooh\Base\Form\Item $_ignore_
	 * @return \Sooh\Base\Form\Error
	 */
	public function fillValues($request=null,$errClassName=null,$_ignore_=null)
	{
		if(empty($request)){
			$request = array_merge($_GET,$_POST);
		}
		$this->flgIsThisForm=isset($request['__formguid__'])?$request['__formguid__']:null;
		$this->flgIsThisForm = $this->flgIsThisForm===$this->guid;
		foreach($this->items as $k=>$_ignore_){
			$inputVal = isset($request[$k])?$request[$k]:null;
			if($inputVal!==null){
				if(!is_a($_ignore_,'\Sooh\Base\Form\Item')){
					$this->values[$k]=$inputVal;
				}else{
					if($_ignore_->verify['type']=='int'){
						$isDate = $_ignore_->input()==\Sooh\Base\Form\Item::datepicker || $_ignore_->input()==\Sooh\Base\Form\Item::date;
						if($isDate){
							if(!is_numeric($inputVal)){
								$inputVal = strtotime($inputVal);
							}
						}
					}
					
					if($_ignore_->input()==\Sooh\Base\Form\Item::constval){
						$this->values[$k]=$inputVal;
					}else {
						$err = $_ignore_->checkUserInput($inputVal,$_ignore_->capt,$errClassName);
						if($err===null){
							$this->values[$k]=$inputVal;
						}else{
							return $err;
						}
					}
				}
			}
		}
		return null;
	}
	public function isThisFormSubmited()
	{
		return $this->flgIsThisForm;
	}
	public $flgIsThisForm=false;
	protected $values=array();

	/**
	 * 获取一个输入item
	 * @param type $k
	 * @return \Sooh\Base\Form\Item
	 */
	public function item($k)
	{
		return $this->items[$k];
	}
	/**
	 * 
	 * @deprecated since version 0
	 */
	public function cruds()
	{
		error_log('cruds is deprecated,use type instead');
		return $this->crudType;
	}
	public function type()
	{
		return $this->crudType;
	}
	/**
	 * 获取非_开头的参数，排除指定的那些参数（默认排除pageid,pagesize）
	 * @param array $keyExclude 排除哪些值
	 * @return type
	 */
	public function getFields($keyExclude=array('pageid','pagesize','orderField'))
	{
		$tmp=array();
		foreach($this->values as $k=>$v){
			if($k['0']!=='_' && !in_array($k, $keyExclude)){
				$tmp[$k]=$v;
			}
		}
		return $tmp;
	}
	public function getWhereInInputs()
	{
		$where=array();
		if($this->flgIsThisForm){
			foreach($this->values as $k=>$v){
				if($k[0]=='_'){
					$where[$k]=$v;
				}
			}
			foreach($this->items as $k=>$_ignore_){
				if(is_scalar($_ignore_) && $k[0]=='_'){
					$where[$k]=$_ignore_;
				}
			}
		}else{
			foreach($this->items as $k=>$_ignore_){
				if($k[0]=='_'){
					if(is_scalar($_ignore_) || $_ignore_===null){
						$where[$k]=$_ignore_;
					}else{
						$where[$k]=$_ignore_->value;
					}
				}
			}
		}
		return $where;
	}
	/**
	 * 以_开头的参数构建where数组
	 * @param \Sooh\Base\Form\Item $_ignore_
	 * @return array
	 */
	public function getWhere($_ignore_=null)
	{
		$where=array();
		if($this->flgIsThisForm){
			foreach($this->values as $k=>$v){
				if($v===''){
					continue;
				}
				if($k[0]=='_'){
					if($k[1]!=='_'){
						$cmp=substr($k,-3);
						$k = substr($k,1,-3);
						if(isset($this->methods[$cmp])){
							if($cmp=='_lk'){
								$where[$k.$this->methods[$cmp]]='%'.$v.'%';
							}else{
								$where[$k.$this->methods[$cmp]]=$v;
							}
						}
					}
				}
			}
			foreach($this->items as $k=>$_ignore_){
				if(is_scalar($_ignore_) && $k[0]=='_'){
					if($k[1]!=='_'){
						$cmp=substr($k,-3);
						$k = substr($k,1,-3);
						if(isset($this->methods[$cmp])){
							if($cmp=='_lk'){
								$where[$k.$this->methods[$cmp]]='%'.$_ignore_.'%';
							}else{
								$where[$k.$this->methods[$cmp]]=$_ignore_;
							}
						}
					}
				}
			}
		}else{
			foreach($this->items as $k=>$_ignore_){
				if($k[0]=='_'){
					$cmp=substr($k,-3);
					$k = substr($k,1,-3);
					if(isset($this->methods[$cmp])){
						if(is_scalar($_ignore_) || $_ignore_===null){
							$where[$k.$this->methods[$cmp]]=$_ignore_;
						}else{
							$where[$k.$this->methods[$cmp]]=$_ignore_->value;
						}
					}
				}
			}
		}
		return $where;
	}
	public function toArray()
	{
		
		return $this->values;
	}
	/**
	 * 重新设置获取的values的值（慎用）
	 * @param string $k
	 * @param mixed $v
	 */
	public function resetValue($k,$v)
	{
		if(is_scalar($this->items[$k])){
			$this->items[$k]=$v;
		}
		$this->values[$k]=$v;
	}
	protected $crudType=self::type_s;
	/**
	 * 
	 * @param char $type [c|r|u|d]
	 * @return \Sooh\Base\Form\Base
	 */
	public function switchType($type)
	{
		$this->crudType=$type;
		return $this;
	}

	/**
	 * 构建输入表单列表
	 * @param string $tpl
	 * @param string $k
	 * @param \Sooh\Base\Form\Item $_ignore_
	 * @return string
	 */
	public function renderDefault($tpl='<tr><td>{capt}</td><td>{input}</td></tr>',$k=null,$_ignore_=null)
	{
		if($k===null){
			$str='<input type=hidden name=__formguid__ value='.$this->guid.'>';
			$ks = array_keys($this->items);
			foreach($ks as $k){
				$str.=$this->renderDefault($tpl,$k)."\n";
			}
			return $str;
		}else{
			$_ignore_=$this->items[$k];
			if(!is_a($_ignore_, '\Sooh\Base\Form\Item')){//给出其他值代表hidden
				if($_ignore_===null){
					return '<input type=hidden name="'.$k.'" value="'.(isset($this->values[$k])?htmlspecialchars($this->values[$k]):'').'">';
				}elseif(is_scalar($_ignore_)){
					return '<input type=hidden name="'.$k.'" value="'.htmlspecialchars($_ignore_).'">';
				}
			}
			else{
				$inputType = $_ignore_->input($this->crudType);
				if(is_array($inputType)){
					$input = call_user_func($inputType, $k, $_ignore_);
				}elseif($tpl===null || $inputType===\Sooh\Base\Form\Item::hidden){
					return '<input type=hidden id="'.$k.'" name="'.$k.'" value="'.htmlspecialchars($this->valForInput($_ignore_->value, $k)).'">';
				}else{
					if($_ignore_->options){
						$_ignore_->options->getPair($this->values,$this->crudType==self::type_s);
					}
					$_ignore_->valForInput=$this->valForInput($_ignore_->value,$k);
					$input = $this->getRenderer()->render($k, $_ignore_, $inputType , $_ignore_->dataRule);
				}
			}
			return str_replace(array('{capt}','{input}'), array($_ignore_->capt,$input), $tpl);
		}
	}
	/**
	 * 从第几个开始就是强制hidden的方式了,0对应第一个，1对应第2个
	 * @param int $begin 从第几个开始隐藏
	 * @param string $tpl 显示模板
	 * @return string
	 */
	public function renderHiddenAfter($begin=0,$tpl=null)
	{
		$total = sizeof($this->items);
		if($begin==0){
			$str='<input type=hidden name=__formguid__ value='.$this->guid.'>';
			$ks = array_keys($this->items);
			foreach($ks as $k){
				$str.=$this->renderDefault(null,$k)."\n";
			}
			return $str;
		}else{
			$str='<input type=hidden name=__formguid__ value='.$this->guid.'>';
			$ks = array_keys($this->items);
			for($i=0;$i<$begin && $i<$total;$i++){
				$k = $ks[$i];
				$str.=$this->renderDefault($tpl,$k)."\n";
			}
			for(;$i<$total;$i++){
				$k = $ks[$i];
				$str.=$this->renderDefault(null,$k)."\n";
			}
			return $str;
		}
		
	}
	protected function valForInput($valDefined,$keyInput)
	{
		if(isset($this->values[$keyInput])){
			$ret= $this->values[$keyInput];
		}else{
			$ret= $valDefined;
		}
		return $ret;
	}
	
}
