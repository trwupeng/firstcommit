<?php
namespace Lib\Services;
/**
 * 注册事件
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class EvtRegister {
	protected static $_instance=null;
	/**
	 * 
	 * @param \Sooh\Base\Rpc\Broker $rpcOnNew
	 * @return \Lib\Services\EvtInvestment
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
	/**
	 * 成功注册
	 * @param int $userId 用户id
	 * @param int $contractId 推广ID
	 * @param int $clientType 客户端类型
	 * @return boolean
	 */
	public function occur($userId,$contractId,$clientType)
	{
		if($this->rpc!==null){
			return $this->rpc->initArgs(['userId'=>$userId,'contractId'=>$contractId,'clientType'=>$clientType])->send(__FUNCTION__);
		}else{
			error_log("[evtRegister]$userId,$contractId,$clientType");
			return true;
		}
	}
}
