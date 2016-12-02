<?php
namespace Prj\Misc;
/**
 * 新版通过post raw传输参数
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class JavaService1 {

    public static $lastUrl;
	/**
	 * 实际发送请求到server,默认http-get
	 * @return mixed or null 
	 */	
	public function _send($host,$service,$cmd,$args,$dt,$sign)
	{//http://114.215.112.129:8080/ws
		$url = "$host/{$service}/{$cmd}";
		if(!is_array($args))throw new \ErrorException('args should be array');
//		$args['dt']=$dt;
//		$args['sign']=$sign;
		$posts = json_encode(['dt'=>$dt,'sign'=>$sign,'data'=>$args]);
		//$posts = json_encode($args);
		//TODO:sign
		
		$ret =	\Sooh\Base\Tools::httpPost($url, $posts,array('Content-Type: application/json'),null,7);
        self::$lastUrl = $url; //todo 记录url
		if('rpcservices'!=$service)error_log("[RPC ".\Sooh\Base\Tools::httpCodeLast()."@".getmypid()."]\nret ".$ret." \nby ".$url. " \npost-data ".$posts);
		
		if(200==\Sooh\Base\Tools::httpCodeLast()){
			$tmp = json_decode($ret,true);
			if(is_array($tmp) && isset($tmp['code'])){
                return json_encode(['code'=>200,'data'=>$tmp]);
				//return "{\"code\":200,\"data\":$ret}";
			}else{
				error_log("[RPC-failed ".\Sooh\Base\Tools::httpCodeLast()."@".getmypid()."]".$ret." by ".$url. " ".$posts);
				return null;
			}
		}else{
			return null;
		}
	}
	public function onShutdown(){}
}
