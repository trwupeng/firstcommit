<?php
namespace Lib\Misc;
class ZerocIceAsy extends ZerocIce
{
	///////////////////////////////////////////////////////////rpc broker-start
	public static $lastUrl;
	/**
	 * 实际发送请求到server,默认http-get
	 * @return mixed or null 
	 */
	public function _send($host,$service,$cmd,$args,$dt,$sign)
	{
		$tmpobj = \Lib\Misc\ZerocIce::getInstance();
		$pak = $host;

		$cmp = sizeof($args);
		$args = array_values($args);
		switch ($cmp){
			case 0: $tmpobj->asy($pak,$service,$cmd);break;
			case 1: $tmpobj->asy($pak,$service,$cmd,$args[0]);break;
			case 2: $tmpobj->asy($pak,$service,$cmd,$args[0],$args[1]);break;
			case 3: $tmpobj->asy($pak,$service,$cmd,$args[0],$args[1],$args[2]);break;
			case 4: $tmpobj->asy($pak,$service,$cmd,$args[0],$args[1],$args[2],$args[3]);break;
			case 5: $tmpobj->asy($pak,$service,$cmd,$args[0],$args[1],$args[2],$args[3],$args[4]);break;
			default: return '{"code":500,"data":"too many args"}';
		}
		return '{"code":200,"data":"request-sent"}';
	
	}
	public function onShutdown(){}
	///////////////////////////////////////////////////////////rpc broker-end
}