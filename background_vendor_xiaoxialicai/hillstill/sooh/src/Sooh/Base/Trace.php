<?php
namespace Sooh\Base;

/**
 * write error_log on special classes.
 * 加一层控制，符合指定条件的才能写到磁盘上。可同时设置prefix和session,区分角色记录log
 * usage:
 *	ON INIT of Project (like index.php)
 *		::focusClass(classBeTraced) required
 *		::focusSession(val,fieldnameOfSession=loginid) optional
 *		::setPrefix(string_prefix) optional add prefix to log such as rolename
 *	IN CLASS codes:
 *		if (Trace::needsWrite(__CLASS__)) Trace::str(string_message)
 *		if (Trace::needsWrite(get_called_class())) Trace::obj(string_message, array)
 * 
 *  IN CLASS codes, ON ErrorException Found, needs always show ErrorException
 *		if (Trace::needsWrite(__CLASS__)) Trace::exception(new \ErrorException(msgHere),null)
 * @author Simon Wang <sooh_simon@163.com> 
 */
class Trace 
{
	private static $focus=null;
	private static $field=null;
	private static $prefix='';
	private static $class=array();
	public static function needsWrite($class)
	{
		if(self::$focus!==null){
			if(empty(self::$class)) return $_SESSION[self::$field]==self::$focus;
			else return $_SESSION[self::$field]==self::$focus && in_array($class, self::$class);
		}else {
			if(!empty(self::$class))return in_array($class, self::$class);
			else return false;
		}
	}
	public static function focusClass($calledClass)
	{
		self::$class[]=$calledClass;
	}
	public static function focusSession($uid,$key='loginid')
	{
		self::$focus=$uid;
		self::$field=$key;
	}
	public static function setPrefix($prefix)
	{
		self::$prefix = $prefix;
	}
	public static function str($str)
	{
		error_log(self::$prefix.$str);
	}
	
	public static function obj($msg,$obj)
	{
		error_log(self::$prefix.$msg. "\n" . var_export($obj,true));
	}
	public static function exception($e)
	{
		error_log(self::$prefix.$e->getMessage(). "\n" . $e->getTraceAsString());
	}
}

