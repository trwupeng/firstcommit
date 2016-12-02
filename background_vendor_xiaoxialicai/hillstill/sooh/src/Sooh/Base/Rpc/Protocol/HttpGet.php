<?php
namespace Sooh\Base\Rpc\Protocol;
/**
 * http-get
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class HttpGet {
	/**
	 * 实际发送请求到server,默认http-get
	 * @return mixed or null 
	 */	
	public function _send($host,$service,$cmd,$args,$dt,$sign)
	{
		$dt = \Sooh\Base\Time::getInstance()->timestamp();
		if(is_string($args)){
			$url = $host.'&service='.$service.'&cmd='.$cmd.'&args='. urlencode($args).'&dt='.($dt-0).'&sign='.urlencode($sign);
		}else{
			$url = $host.'&service='.$service.'&cmd='.$cmd.'&args='. urlencode(json_encode($args)).'&dt='.($dt-0).'&sign='.urlencode($sign);
		}
if('rpcservices'!=$service)error_log("[RPC@".  getmypid()."]".$url);
		$ret = \Sooh\Base\Tools::httpGet($url);
if('rpcservices'!=$service)error_log("[RPC@".  getmypid()."]".$ret);
		if(200==\Sooh\Base\Tools::httpCodeLast()){
			return $ret;
		}else{
			return null;
		}
	}
	public function onShutdown(){}
}
