<?php
namespace Lib\Misc;
class ZerocIceSyn extends ZerocIce
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
			case 0: $ret = $tmpobj->syn($pak,$service,$cmd);break;
			case 1: $ret = $tmpobj->syn($pak,$service,$cmd,$args[0]);break;
			case 2: $ret = $tmpobj->syn($pak,$service,$cmd,$args[0],$args[1]);break;
			case 3: $ret = $tmpobj->syn($pak,$service,$cmd,$args[0],$args[1],$args[2]);break;
			case 4: $ret = $tmpobj->syn($pak,$service,$cmd,$args[0],$args[1],$args[2],$args[3]);break;
			case 5: $ret = $tmpobj->syn($pak,$service,$cmd,$args[0],$args[1],$args[2],$args[3],$args[4]);break;
			default: return '{"code":500,"data":"too many args"}';
		}
		$tmp = json_decode($ret, true);
		if(is_array($tmp)){
			return '{"code":200,"data":'.$ret.'}';
		}else{
			return '{"code":200,"data":"'.$ret.'"}';
		}
	}
	public function onShutdown(){}
	///////////////////////////////////////////////////////////rpc broker-end
}