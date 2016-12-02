<?php
namespace Lib\Services;
/**
 * rpc 服务器管理
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class Rpcservices {
	protected static $_instance=null;
	/**
	 * 
	 * @param \Sooh\Base\Rpc\Broker $rpcOnNew
	 * @return CheckinBook
	 */
	public static function getInstance($rpcOnNew=null)
	{
		if(self::$_instance===null){
			self::$_instance = new Rpcservices;
			self::$_instance->rpc = $rpcOnNew;
		}
		return self::$_instance;
	}
	/**
	 *
	 * @var \Sooh\Base\Rpc\Broker
	 */
	protected $rpc;
	public static $javaService=['paygw','payck'];
	public static $javaService1=['paygwcmd'];
	public static $iceService=['loger'=>'logcenter','triggers'=>'evtcenter','bysms'=>'msgcenter','bypush'=>'msgcenter'];
	public static $iceService1=['sample0000'=>'samples',];
	public function fetchini($service)
	{
		if($this->rpc===null){
			$javaService = ['paygw','payck'];
			$javaService1= ['paygwcmd'];
			//异步调用的接口（没回调的那种异步）
			$iceService = ['sample0000'=>'samples','loger'=>'logcenter','triggers'=>'evtcenter','bysms'=>'msgcenter','bypush'=>'msgcenter'];
			//同步调用的接口
			$iceService1 = [];
			$cmp = strtolower($service);
			$ini = \Sooh\Base\Ini::getInstance()->get('RpcConfig');

			if(in_array($cmp, self::$javaService)){
				unset($ini['force']);
				$ini['protocol'] = '\Prj\Misc\JavaService';
                $ini['urls']=\Sooh\Base\Ini::getInstance()->get(lcfirst($service));
				//var_log($ini['urls'],">>>>>>service:{$service}");
			}elseif(in_array($cmp, self::$javaService1)){
				unset($ini['force']);
				$ini['protocol'] = '\Prj\Misc\JavaService1';
                $ini['urls']=\Sooh\Base\Ini::getInstance()->get(lcfirst($service));
				//var_log($ini['urls'],">>>>>>service:{$service}");
			}elseif(isset(self::$iceService[$cmp])){
				\Sooh\Base\Rpc\Broker::$writeRpcRequestLog=false;
				unset($ini['force']);
				$ini['protocol'] = '\Lib\Misc\ZerocIceAsy';
				$pak = self::$iceService[$cmp];
                $ini['urls']=[$pak];
				//var_log($ini['urls'],">>>>>>service:{$service}");		
			}elseif(isset(self::$iceService1[$cmp])){
				\Sooh\Base\Rpc\Broker::$writeRpcRequestLog=false;
				unset($ini['force']);
				$ini['protocol'] = '\Lib\Misc\ZerocIceSyn';
				$pak = self::$iceService1[$cmp];
                $ini['urls']=[$pak];
				//var_log($ini['urls'],">>>>>>service:{$service}");		
			}else{
				if ($cmp === 'rpcservices' || $cmp === 'sessionStorage') {
					return null;
				}elseif($cmp=='accouunt'){
					unset($ini['force']);
				}else{
					return null;
				}
			}
			return $ini;
		}else{
			\Sooh\Base\Log\Data::error('Rpcservices should never be called by rpc');
			throw new \Sooh\Base\ErrException('Rpcservices should never be called by rpc');
			//return $this->rpc->initArgs(array('arg1'=>$arg1))->send();//call('CheckinBook/'.__FUNCTION__, array($withBonus,$userOrAccountId));
		}
	}
}
