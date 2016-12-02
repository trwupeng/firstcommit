<?php
namespace Sooh\Base;
/**
 * 
 * usage: 
 *		init in index.php or somewhere:
 *			$GLOBALS[CONF][db][default]=[host=>127.0.0.1,...];
 *			Ini::getInstance();
 *		OR
 *			Ini::getInstance()->initGlobal(array('db'=>array('default=>array(...))));
 *
 * 
 * extends: load project-config and try with cache(since only yac for now, use inner function directly)
 *		Ini::getInstance()->initCache('path-of-prjConf','apc');
 *
 * @author Simon Wang <sooh_simon@163.com> 
 */
class Ini {
	private $globals=null;
	public function initGobal($arr)
	{
		if($this->globals===null){
			if(is_array($GLOBALS['CONF'])){
				$this->globals=$GLOBALS['CONF'];
			} else {
				$this->globals=array();
			}
		}
		foreach($arr as $k=>$v){
			$this->globals[$k]=$v;
		}
	}
	public function viewRenderType($newType=null)
	{
		if($newType!==null){
			$this->initGobal(array('viewRenderType'=>$newType));
			return $newType;
		}else{
			$newType = $this->get('viewRenderType');
			if(empty($newType)){
				return 'html';
			}else{
				return $newType;
			}
		}
	}
	public function initCache($basedir,$type=null)
	{
		$this->cacheFunc = $type;
		$this->basedir=$basedir;
		if($type=='yac'){
			$this->cacheSys = new Yac();
		}
	}
	private $cacheFunc=null;
	private $cacheSys = null;
	private $basedir=null;
	protected function yac_get($item)
	{
		$v = $this->cacheSys->get($item);
		if($v===false){
			$conf = $this->loadFile($item);
			if(!empty($conf)) {
				$this->cacheSys->set($item,$conf);
			}
			return $conf;
		}else{
			return $v;
		}
		
	}
	protected function yac_del($items)
	{
		$realDelete=0;
		foreach($items as $key){
			if($this->cacheSys->delete($key)){
				$realDelete++;
			}
		}
		if($realDelete===0){
			return true;
		}else{
			return $realDelete;
		}
	}

	public function delCache($items)
	{
		if($this->cacheFunc!==null){
			$f = $this->cacheFunc.'_del';
			return $this->$f($items);
		}else{
			return true;
		}
	}
	protected function loadFile($arg)
	{
		if(!is_array($arg)){
			$arg = explode ('.', $arg);
		}
		array_unshift($arg,$this->basedir);
		return include implode('/', $arg).'.php';
	}
	public function get($item, $default=null)
	{
		if ($this->globals ===null){
			$this->globals = $GLOBALS['CONF'];
		}
		if(isset($this->globals[$item])){
			return $this->globals[$item];
		}
		$path = explode('.', $item);
		switch (sizeof($path)){
			case 3: $conf=isset($this->globals[$path[0]][$path[1]][$path[2]])?$this->globals[$path[0]][$path[1]][$path[2]]:null;break;
			case 2: $conf=isset($this->globals[$path[0]][$path[1]])?$this->globals[$path[0]][$path[1]]:null;break;
			case 1: $conf=isset($this->globals[$path[0]])?$this->globals[$path[0]]:null;break;
			default:
				throw new \ErrorException("change codes support more depth");
		}
		if($conf===null){
			if($this->cacheFunc!==null){
				$f = $this->cacheFunc.'_get';
				$conf = $this->$f($item);
			}
			if($conf === null && $this->basedir!==null){
				$conf = $this->loadFile($path);
			}
		}
		if($conf===null){
			return $default;
		} else {
		return $conf;
	}
	}
	private static $_instance;
	/**
	 * 
	 * @return Ini
	 */
	public static function getInstance()
	{
		if(self::$_instance==null){
			$class = get_called_class();
			self::$_instance = new $class;
		}
		return self::$_instance;
	}
	/**
	 * 注册一个shutdown函数（一般是清理函数）
	 * @param callback $callback array(obj,func), 'class::staticfunc',....
	 * @param string $funcDesc function desc
	 */
	public static function registerShutdown($callback,$funcDesc)
	{
		if($callback===null){
			foreach(self::$funcShutdown as $funcDesc=>$func){
				try{
					if(is_array($func) || is_string($func)){
						call_user_func($func);
					}else{
						$func();
					}
				}  catch (\ErrorException $e){
					error_log('error shutdown:'.$funcDesc." ".$e->getMessage()."\n".$e->getTraceAsString());
				}
			}
			if(class_exists("\\Sooh\\DB\\Broker",false)){
				\Sooh\DB\Broker::free();
			}
		}else{
			self::$funcShutdown[$funcDesc]=$callback;
		}
	}
	protected static $funcShutdown=array();
	
	public function dump()
	{
		return $this->globals;
	}
	/**
	 * 返回cookie-domain,优先使用配置项CookieDomainBase
	 * @return string
	 */
	public function cookieDomain()
	{
		$cookieDomain=  $this->get('CookieDomainBase');
		if(empty($cookieDomain)){
			$r = explode('.', $_SERVER['SERVER_NAME']);
			if(sizeof($r)==4 && is_numeric($r[0]) && is_numeric($r[1]) && is_numeric($r[2]) && is_numeric($r[3])){
				$cookieDomain=$_SERVER['SERVER_NAME'];
			}else{
				$r[0]='';
				$cookieDomain = implode('.',$r);
			}
		}
		return $cookieDomain;
	}
}
