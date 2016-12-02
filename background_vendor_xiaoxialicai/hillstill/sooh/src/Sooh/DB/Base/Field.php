<?php
namespace Sooh\DB\Base;
/**
 * @author Simon Wang <sooh_simon@163.com> 
 */
class Field 
{
	const auto = 'auto';
	const int32='int32';
	const int64='int64';
	const float='float';
	const string='string';
	const str36='str36';
	const str64='str64';
	const str128='str128';
	const str256='str256';
	const str1k='str1k';
	const str2k='str2k';
	const blob='blob';
	
	public $fieldType=null;
	public $fieldLen=0;
	public $mathMethod=null;
	public $val=0;
	
	public static function math($newVal,$method='+')
	{
		$class = get_called_class();
		$o = new $class();
		$o->mathMethod=$method;
		$o->val=$newVal;
		return $o;
	}
	public static function func($newVal,$method='max')
	{
		$class = get_called_class();
		$o = new $class();
		$o->mathMethod=$method;
		$o->val=$newVal;
		return $o;
	}
	
	public static function fieldDefine($type,$len=0)
	{
		$class = get_called_class();
		$o = new $class();
		$o->fieldType=$type;
		$o->fieldLen=$len;
		return $o;
	}	
}
