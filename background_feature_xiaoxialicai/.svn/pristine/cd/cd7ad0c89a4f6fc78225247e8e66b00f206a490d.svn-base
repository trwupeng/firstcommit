<?php
namespace Lib\Services;
/**
 * ice service test
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class Sample0000 {
	protected static $_instance=null;
	/**
	 * 
	 * @param \Sooh\Base\Rpc\Broker $rpcOnNew
	 * @return Sample0000
	 */
	public static function getInstance($rpcOnNew=null)
	{
		if(self::$_instance===null){
			$c = get_called_class();
			self::$_instance = new $c;
			self::$_instance->rpc = $rpcOnNew;
		}
		return self::$_instance;
	}
	/**
	 * 
	 * @var \Sooh\Base\Rpc\Broker
	 */
	protected $rpc;
	public function echohi($who)
	{
		if($this->rpc!==null){
			return $this->rpc->initArgs(array('who'=>$who,))->send(__FUNCTION__);
		}else{
			return '{"code":200,"msg":"php hi,'.$who.'"}';
		}
	}
	public function forwardSyn($serviceClass,$serviceFunc,$jsonedArgArray)
	{
		if($this->rpc!==null){
			return $this->rpc->initArgs(array('serviceClass'=>$serviceClass,'serviceFunc'=>$serviceFunc,'jsonedArgArray'=>$jsonedArgArray))->send(__FUNCTION__);
		}else{
			$fullClassName = "\\Lib\\Services\\$serviceClass";
			$obj = $fullClassName::getInstance(null);
			return call_user_func_array([$obj,$serviceFunc],  json_decode($jsonedArgArray,true));
		}
	}
	public function forwardAsy($serviceClass,$serviceFunc,$jsonedArgArray)
	{
		if($this->rpc!==null){
			return $this->rpc->initArgs(array('serviceClass'=>$serviceClass,'serviceFunc'=>$serviceFunc,'jsonedArgArray'=>$jsonedArgArray))->send(__FUNCTION__);
		}else{
			$this->forwardSyn($serviceClass,$serviceFunc,$jsonedArgArray);
		}
	}
}
