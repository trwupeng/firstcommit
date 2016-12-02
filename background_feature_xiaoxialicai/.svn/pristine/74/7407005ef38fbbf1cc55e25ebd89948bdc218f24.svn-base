<?php
namespace Lib\Services;
/**
 * 致命错误报告
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class FatalError {
	protected static $_instance=null;
	/**
	 * 
	 * @param \Sooh\Base\Rpc\Broker $rpcOnNew
	 * @return CheckinBook
	 */
	public static function getInstance($rpcOnNew=null)
	{
		if(self::$_instance===null){
			self::$_instance = new CheckinBook;
			self::$_instance->rpc = $rpcOnNew;
		}
		return self::$_instance;
	}
	/**
	 *
	 * @var \Sooh\Base\Rpc\Broker
	 */
	protected $rpc;
	public function write($logData)
	{
		if($this->rpc!==null){
			return $this->rpc->initArgs(array('logData'=>$logData))->send(__FUNCTION__);
		}else{
			$dt = \Sooh\Base\Time::getInstance();
			\Sooh\DB\Broker::getInstance()->addLog('db_monitor.tb_error_log', array('ymd'=>$dt->YmdFull,'hhiiss'=>$dt->his,'msg'=>$logData));
			return 'done';
		}
	}
}
