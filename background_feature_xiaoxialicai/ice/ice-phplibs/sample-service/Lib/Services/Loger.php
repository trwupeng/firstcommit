<?php
namespace Lib\Services;
/**
 * rpc 服务器管理
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class Loger
{
	/**
	 * @var SMS
	 */
	protected static $_instance = null;

	/**
	 *
	 * @param \Sooh\Base\Rpc\Broker $rpcOnNew
	 * @return \Lib\Services\SMS
	 */
	public static function getInstance($rpcOnNew = null)
	{
		if (self::$_instance === null) {
			$c = get_called_class();
			self::$_instance = new $c;
			self::$_instance->rpc = $rpcOnNew;
		}
		return self::$_instance;
	}

	public function sayhi($jsonstr)
	{
		if ($this->rpc !== null) {
			return $this->rpc->initArgs(array('phone' => $phone, 'code' => $code, 'fmt' => $fmt))->send(__FUNCTION__);
		} else {
			error_log(__CLASS__.'->'.__FUNCTION__."($jsonstr) called");
		}
	}
	public function echohi($who)
	{
		if ($this->rpc !== null) {
			return $this->rpc->initArgs(array('phone' => $phone, 'code' => $code, 'fmt' => $fmt))->send(__FUNCTION__);
		} else {
			error_log(__CLASS__.'->'.__FUNCTION__."($who) called");
			//if(rand(0,1)){
				return "{\"code\":200,\"sayHiTo\":\"$who\"}";
			//}else{
			//	return "fake-error";
			//}
		}
	}
}
