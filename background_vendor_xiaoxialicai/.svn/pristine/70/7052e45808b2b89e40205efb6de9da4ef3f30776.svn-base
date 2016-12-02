<?php
namespace Sooh\Base\Form;
use Sooh\Base\Form\Error as form_err;
/**
 * 定义字段在不同情况下使用哪种表现形式, inputType可以是数组模式的回调函数
 * 已知缺陷：verifyPasswordCmp标记需要比较两次密码一致的时候，使用的是static变量保存的，估一个请求里只能有一次这种验证
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class Item {
	const text = 'text';
	const datepicker = 'datepicker';
	const mulit= 'textarea';
	const passwd= 'password';
	const chkbox='checkbox';
	const radio = 'radio';
	const select= 'select';
	const hidden= 'hidden';
	const constval= 'const';
	const date = 'date';
	public $valForInput;
	public $capt;
	public $value;
    public $dataRule;
	/**
	 *
	 * @var \Sooh\Base\Form\Options
	 */
	public $options;
	protected $inputDefault;
	protected $inputWhenUpdate=false;
	/**
	 * 构建input的工厂函数
	 * 
	 * @param string $capt 标题
	 * @param mix $value 值
	 * @param string $inputDefault 默认的输入方式，可以是数组模式的回调函数
	 * @return \Sooh\Base\Form\Item
	 */
	public static function factory($capt,$value,$inputDefault,$options = array(),$dataRule = []) {
		$o = new Item();
		$o->capt=$capt;
		$o->value=$value;
        if(!empty($options))$o->options = new \Sooh\Base\Form\Options($options);
        if(!empty($dataRule))$o->dataRule = $dataRule;
		$o->inputDefault = $inputDefault;
		return $o;
	}
	/**
	 * 更多设置
	 * @param \Sooh\Base\Form\Options $options
	 * @param string $inputWhenUpdate 主要用于主键在更新时不可编辑，可以是数组模式的回调函数
	 * @return \Sooh\Base\Form\Item
	 */
	public function initMore($options=null,$inputWhenUpdate=null)
	{
		$this->options = $options;
		$this->inputWhenUpdate=$inputWhenUpdate;
		return $this;
	}
	/**
	 * 
	 * @param string $crud
	 * @param array $record
	 * @return string
	 */
	public function input($crud=\Sooh\Base\Form\Broker::type_u)
	{
		if(!empty($this->inputWhenUpdate) && $crud===\Sooh\Base\Form\Broker::type_u){
			return $this->inputWhenUpdate;
		}else{
			return $this->inputDefault;
		}
	}
	public $verify=null;
	/**
	 * 
	 * @param type $required
	 * @param type $min
	 * @param type $max
	 * @return \Sooh\Base\Form\Item
	 */
	public function verifyInteger($required=0,$min=0,$max=2000000000)
	{
		$this->verify=array('required'=>$required,'type'=>'int','min'=>$min,'max'=>$max);
		return $this;
	}
	/**
	 * 检查用户输入，返回错误描述（没问题返回空串）,TODO: 日期类型
	 * @param string $inputVal 用户输入的值
	 * @param string $capt 输入项名称
	 * @param string $errClassName 指定错误类名称
	 * @return \Sooh\Base\Form\Error
	 */
	public function checkUserInput($inputVal,$capt,$errClassName=null)
	{
		if($errClassName==null){
			$errClassName = '\Sooh\Base\Form\Error';
		}
		$errClassName .= '::factory';
		if(!empty($this->verify)){
			if($this->verify['required'] && ($inputVal===null || $inputVal==='' )){
				return call_user_func($errClassName,$capt,form_err::REQUIRED);
			}else{
				switch ($this->verify['type']){
					case 'int':
						if($inputVal<$this->verify['min'] || $inputVal>$this->verify['max']){
							return call_user_func($errClassName,$capt,form_err::INT_OVERFLOW,form_err::factoryParam($this->verify['min'], $this->verify['max']));
						}
						break;
					case 'str':
						$inputVal = strlen($inputVal);
						if($inputVal<$this->verify['min'] || $inputVal>$this->verify['max']){
							return call_user_func($errClassName,$capt,form_err::STR_LENGTH,form_err::factoryParam($this->verify['min'], $this->verify['max']));
						}
						break;
				}
			}
		}
		return null;
	}
	/**
	 * 英文标识串限制
	 * @param type $required
	 * @param type $min
	 * @param type $max
	 * @return \Sooh\Base\Form\Item
	 */
	public function verifyIdentifier($required=0,$min=0,$max=0)
	{
		$this->verify=array('required'=>$required,'type'=>'str','min'=>$min,'max'=>$max,'subcheck'=>'identifier');
		return $this;
	}
	/**
	 * 邮件地址串限制
	 * @param type $required
	 * @param type $min
	 * @param type $max
	 * @return \Sooh\Base\Form\Item
	 */
	public function verifyEmail($required=0,$min=0,$max=0)
	{
		$this->verify=array('required'=>$required,'type'=>'str','min'=>$min,'max'=>$max,'subcheck'=>'email');
		return $this;
	}
	/**
	 * 通用字符串限制
	 * @param type $required
	 * @param type $min
	 * @param type $max
	 * @return \Sooh\Base\Form\Item
	 */
	public function verifyString($required=0,$min=0,$max=0)
	{
		$this->verify=array('required'=>$required,'type'=>'str','min'=>$min,'max'=>$max,'subcheck'=>'default');
		return $this;
	}
	protected static $pwdCmp=null;
	/**
	 * 
	 * @param type $min
	 * @param type $max
	 * @return \Sooh\Base\Form\Item
	 */
	public function verifyPasswordCmp($min=0,$max=0)
	{
		if(self::$pwdCmp==null){
			self::$pwdCmp= '__CSS_'.rand(10000,99999).'__';
			$this->verify=array('required'=>1,'type'=>'str','min'=>$min,'max'=>$max,'cssId'=>self::$pwdCmp);
		}else{
			$this->verify=array('required'=>1,'type'=>'str','min'=>$min,'max'=>$max,'cmpCssId'=>self::$pwdCmp);
		}
		return $this;
	}

    public function setinputDefault($str){
        eval('$this->inputDefault = self::'.$str.';');
    }
}
