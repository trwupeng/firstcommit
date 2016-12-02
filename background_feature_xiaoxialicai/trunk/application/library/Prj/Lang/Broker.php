<?php
namespace Prj\Lang;

/**
 * 系统语言文字控制类
 *
 * @author wang.ning
 */
class Broker {
	/**
	 * 获取对应的msg
	 * @param string $id 标识
	 * @return mixed
	 */
	public static function getMsg($id)
	{
		$r = explode('.', $id);
		$root = array_shift($r);
		if(!isset(self::$_loaded[$root])){
			self::$_loaded[$root] = include __DIR__.'/'.ucfirst($root).".php";
		}
		$bak = implode('.', $r);
		$tmp = self::$_loaded[$root];
		if(empty($tmp)){
			return $bak;
		}
		while(sizeof($r)){
			$k = array_shift($r);
			if(false===isset($tmp[$k])){
				return $bak;
			}else{
				$tmp = $tmp[$k];
			}
		}
		return $tmp;
	}

	protected static $_loaded=[];

	/**
	 * 获取一个ErrorException
	 * @param string $msgid
	 * @param int $code
	 * @return \ErrorException
	 */
	public static function getErrorException($msgid,$code=0)
	{
		return new \ErrorException(self::getMsg($msgid),$code);
	}
}
