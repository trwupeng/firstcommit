<?php
namespace Sooh\Base\Session;
/**
 * Session， 使用方式：
 * 构建一个project的storage类
 *     class \Lib\SessionStorage extends \Sooh\Base\Session\Storage{}
 * 
 * framework初始化(ctrl->action之前)
 *     不通过RPC读写
 *         \Lib\SessionStorage::setStorageIni('session', 2);
 *         \Sooh\Base\Session\Data::getInstance( \Lib\SessionStorage::getInstance(null));
 *     通过RPC读写
 *         $rpc = new \Sooh\Base\Rpc\Http($this->ini->get('SignKeyForService'), $this->ini->get('hostsOfMssqlAPI.default'));
 *         \Sooh\Base\Session\Data::getInstance( \Lib\SessionStorage::getInstance($rpc));
 * 
 * ctrl的action or lib 中
 *     \Sooh\Base\Session\Data::getInstance()->get(key, defaultVal);
 *     \Sooh\Base\Session\Data::getInstance()->set(key, value[, secondExpire]);
 * 
 * @author Simon Wang <hillstill_simon@163.com>
 */
class Data {
	const SessionIdName = 'SoohSessId';
	protected static $_instance=null;
	/**
	 * @param \Sooh\Base\Session\Storage $storage
	 * @return \Sooh\Base\Session\Data
	 */
	public static function getInstance($storage=null)
	{
		if(self::$_instance===null){
			static::$_instance = new \Sooh\Base\Session\Data;
			static::$_instance->sessionId = self::getSessId();
			if($storage===null){
				throw new \Sooh\Base\ErrException('Session_data created on storage=null');
			}
			static::$_instance->storage = $storage;
		}
		return static::$_instance;
	}
	
	/**
	 * get session id
	 * @return string
	 */
	public static function getSessId()
	{
		if(empty($_COOKIE[self::SessionIdName])){
			$_COOKIE[self::SessionIdName] = md5(microtime(true).\Sooh\Base\Tools::remoteIP());
			$cookieDomain=  \Sooh\Base\Ini::getInstance()->cookieDomain();
			
			setcookie(self::SessionIdName, $_COOKIE[self::SessionIdName], time()+315360000, '/', $cookieDomain);
		}
		return $_COOKIE[self::SessionIdName];
	}

	protected $timestamp;
	protected $sessionId;
	protected $sessionArr=null;
	/**
	 *
	 * @var \Sooh\Base\Session\Storage  
	 */
	protected $storage=null;
	protected function remove($k)
	{
		
	}
	/**
	 * start session if session not init
	 */
	protected function start()
	{
		if($this->sessionArr===null){
			$this->timestamp = \Sooh\Base\Time::getInstance()->timestamp(); 
			$tmp = $this->storage->load($this->sessionId);
			if(empty($tmp) || empty($tmp['trans'])){
				$this->record=array('sessionId'=>$this->sessionId,);
				$this->sessionArr=array();
				error_log("[TRACE-session ".$_COOKIE['SoohSessId']." data (".$_REQUEST['__'].")] skip init");
			}else{
				$this->record = $tmp['trans'];
				$this->sessionArr = $tmp['data'];
				if($this->get('accountId')){
					$secLast = $this->sessionArr['__dTaCcOuNt__']-0;
					$secPast = $this->timestamp-$secLast;
					if($secPast>300){
						$this->addExpire('accountId', min([$secPast,900]));
						$this->set('__dTaCcOuNt__', $this->timestamp);
						error_log("[TRACE-session ".$_COOKIE['SoohSessId']." data (".$_REQUEST['__'].")] update expire as secPast=$secPast");
					}else{
						error_log("[TRACE-session ".$_COOKIE['SoohSessId']." data (".$_REQUEST['__'].")] skip secPast=$secPast");
					}
				}else{
					error_log("[TRACE-session ".$_COOKIE['SoohSessId']." data (".$_REQUEST['__'].")] skip not login");
				}
			}
			\Sooh\Base\Ini::registerShutdown(array($this,'shutdown'), 'sessionOnShutdown');
		}
	}
	/**
	 * session是否已经激活
	 * @return bool
	 */
	public function isStarted()
	{
		return !empty($this->sessionArr);
	}
	protected $record=null;
	/**
	 * get session value
	 * @param string $k
	 * @param mixed $default
	 * @return mixed
	 */
	public function get($k,$default=null)
	{
		$this->start();
		if(isset($this->sessionArr['__eXpIrE__'][$k])){
			if($this->sessionArr['__eXpIrE__'][$k]>=$this->timestamp){
				return $this->sessionArr[$k];
			}else{
				return $default;
			}
		}else{
			if(isset($this->sessionArr[$k])){
				return $this->sessionArr[$k];
			}else{
				return $default;
			}
		}
	}
	protected $changed=false;
	protected function addExpire($k,$add)
	{
		$this->changed=true;
		$this->sessionArr['__eXpIrE__'][$k]+=$add;
		$this->operations[]=['set',['__eXpIrE__',$k],$this->sessionArr['__eXpIrE__'][$k]];
	}
	/**
	 * set session value with expired-seconds (0 means never expire)
	 * @param string $k
	 * @param mixed $v
	 * @param int $expireAfter
	 */
	public function set($k,$v,$expireAfter=0)
	{
		$this->start();
		$this->changed=true;
		if($v===null){
			unset($this->sessionArr[$k],$this->sessionArr['__eXpIrE__'][$k]);
			$this->operations[]=['unset',['__eXpIrE__',$k]];
			$this->operations[]=['unset',[$k]];
		}else{
			$this->sessionArr[$k] = $v;
			$this->operations[]=['set',[$k],$v];
			if($expireAfter){
				$this->sessionArr['__eXpIrE__'][$k]=$expireAfter+$this->timestamp;
				$this->operations[]=['set',['__eXpIrE__',$k],$expireAfter+$this->timestamp];
			}else{
				if($k=='accountId'){
					$errr = new \ErrorException;
					error_log("TOCHK:set account without expire".$errr->getTraceAsString());
				}
				unset($this->sessionArr['__eXpIrE__'][$k]);
				$this->operations[]=['unset',['__eXpIrE__',$k]];
			}
		}
	}
	/**
	 * 获取指定key的剩余有效时长（单位秒）
	 * @param type $k
	 * @return type
	 * @throws \ErrorException
	 */
	protected function expireLeft($k)
	{
		if(!isset($this->sessionArr['__eXpIrE__'][$k])){
			throw new \ErrorException('expire for '.$k.' is NOT set');
		}else{
			return $this->sessionArr['__eXpIrE__'][$k]-$this->timestamp;
		}
	}
	protected $operations=[];
	/**
	 * update session when shutdown
	 */
	public function shutdown()
	{
		if($this->changed ){
			error_log("SessionSave_{$this->sessionId}:start(".$_REQUEST['__'].")");
			if(is_array($this->sessionArr['__eXpIrE__'])) foreach($this->sessionArr['__eXpIrE__'] as $k=>$t){
				if($t<$this->timestamp){
					$this->operations[]=['unset',['__eXpIrE__',$k]];
					$this->operations[]=['unset',[$k]];
				}
			}
			$this->storage->update($this->sessionId, $this->operations,$this->record);
			$this->operations=[];
			$this->changed=false;
		}else{
			error_log("SessionSave_{$this->sessionId}:skip(".$_REQUEST['__'].")");
		}
	}

	/**
	 * 清除当前回话的所有session
	 */
	public function destroy()
	{
		$this->start();
		$this->changed = true;
		$this->sessionArr = [];
		error_log("[TRACE-session ".$_COOKIE['SoohSessId']." data] remove all session");
	}
}
