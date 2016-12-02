<?php
namespace Sooh\Base\Rpc;
/**
 * rpc
 * @usage $response = \Sooh\Base\Rpc\Base::getInstance($cmd) [->initArgs()] ->send();
 * 
 * @author Simon Wang <hillstill_simon@163.com>
 */
class Broker {
	public static $writeRpcRequestLog=true;
	const serviceNameForRpcManager = 'rpcservices';
	//public static $debugMod=false;
	private static $_protocols=array();
	private static $_instances=array();
	/**
	 * 获取rpc实例
	 *		1）globals里要有 CONF.RpcConfig = array(key=>'',urls=>array(127.0.0.1/index.php?, URI2),protocol=>HttpGet,)
	 *		2）rpc-broker默认通过 rpcservices/fetchini?cmdid=cmd
	 *			获取cmd对应的配置：array(key=>sss,urls=>array(),protocol=>默认HttpGet)
	 *		3）根据class实例化，初始化
	 * @param string $serviceName
	 * @return \Sooh\Base\Rpc\Broker
	 * @throws \Sooh\Base\ErrException
	 */
	public static function factory($serviceName)
	{
		$serviceName = self::formatCmd($serviceName);
		if(!isset(self::$_instances[$serviceName])){
			$_ini = self::getRpcIni($serviceName);
			if(empty($_ini)){
				//throw new \Sooh\Base\ErrException('unknown service:'.$serviceName);
				return null;
			}
			if(empty($_ini['protocol'])){
				$_ini['protocol']='HttpGet';
			}
			self::$_instances[$serviceName] = new Broker;
			self::$_instances[$serviceName]->final_service=$serviceName;
			//self::$_instances[$serviceName]->final_cmd=$serviceName;
			self::$_instances[$serviceName]->final_key=$_ini['key'];
			self::$_instances[$serviceName]->final_protocol=$_ini['protocol'];
			self::$_instances[$serviceName]->final_hosts=$_ini['urls'];
		}
		return self::$_instances[$serviceName];
	}
	/**
	 * 格式化cmd
	 * @param string $cmd
	 * @return string
	 */
	protected static function formatCmd($cmd)
	{
		return $cmd;
	}
	protected $final_protocol;
	protected $final_service;
	protected $final_cmd;
	protected $final_args;
	protected $final_key;
	protected $final_hosts;
	public static $_rpcServices;
	/**
	 * 更改serviceName
	 * @param type $newName
	 * @return \Sooh\Base\Rpc\Broker
	 */
	public function resetServiceName($newName)
	{
		$this->final_service = $newName;
		return $this;
	}
	/**
	 * 获取PRC配置参数
	 * @param string $serviceName 
	 * @return array array(key=>sss,urls=>array(),protocol=>默认HttpGet)
	 * @throws \Sooh\Base\ErrException
	 */
	public static function getRpcIni($serviceName)
	{
		if(self::$_rpcServices!==null){
			return self::$_rpcServices->fetchini($serviceName);
		}
		$ini = \Sooh\Base\Ini::getInstance();
		if(empty(self::$_instances['_RPC_ROUTE_'])){
			self::$_instances['_RPC_ROUTE_'] = new Broker;
			self::$_instances['_RPC_ROUTE_']->final_service='rpcservices';
			//self::$_instances['_RPC_ROUTE_']->final_cmd='fetchini';
			self::$_instances['_RPC_ROUTE_']->final_protocol=$ini->get('RpcConfig.protocol');
			self::$_instances['_RPC_ROUTE_']->final_key=$ini->get('RpcConfig.key');
			self::$_instances['_RPC_ROUTE_']->final_hosts=$ini->get('RpcConfig.urls');
		}
		return self::$_instances['_RPC_ROUTE_']->initArgs(array('service'=>$serviceName))->send('fetchini');
	}
	/**
	 * 设置参数
	 * @param array $args
	 * @return \Sooh\Base\Rpc\Broker
	 */
	public function initArgs($args)
	{
		$this->final_args=$args;
		return $this;
	}
	/**
	 * 发送请求
	 * @return mixed
	 */
	public function send($cmd)
	{
		$this->final_cmd=$cmd;
		$hosts = $this->final_hosts;
		$timestamp = \Sooh\Base\Time::getInstance()->timestamp();
		while(sizeof($hosts)){
			$rand = array_rand($hosts);
			$host = $hosts[$rand];
			unset($hosts[$rand]);
//if('rpcservices'!=$this->final_service) 
			if(self::$writeRpcRequestLog)error_log("#######rpc-call####{$this->final_protocol}:{$host}, {$this->final_service}, {$this->final_cmd},".  json_encode($this->final_args));
			$ret = $this->getSender($this->final_protocol)->_send($host, $this->final_service, $this->final_cmd, $this->final_args,$timestamp,$this->clacSign($timestamp));
			if(self::$writeRpcRequestLog)error_log("#######rpc-ret####".json_encode($ret));
			if(empty($ret)){
				\Sooh\Base\Log\Data::error("found_rpc_server_down:".$host.' with cmd');
			}else{
				$arr = json_decode($ret,true);
				if(is_array($arr)){
					if($arr['code']==200){
						return $arr['data'];
					}else{
						$err = new \Sooh\Base\ErrException($arr['msg'],$arr['code']);
						$err->customData = $arr['data'];
						throw $err;
					}
				}
			}
		}
		throw new \Sooh\Base\ErrException('rpc_failed:'.$this->final_cmd);
	}
	
	/**
	 * 获取rpc通讯协议对应的发送类
	 * @param $protocol
	 * @return \Sooh\Base\Rpc\Protocol\Interfaces;
	 */
	protected function getSender($protocol)
	{
		if(!isset(self::$_protocols[$protocol])){
			if(substr($protocol,0,1)=='\\'){
				self::$_protocols[$protocol] = new $protocol();
			}else{
				$class = "\\Sooh\\Base\\Rpc\\Protocol\\".ucfirst($protocol);
				self::$_protocols[$protocol] = new $class;
			}
		}
		return self::$_protocols[$protocol];
	}
	/**
	 * 计算签名
	 * @param int $timestamp 时间戳
	 */
	public function clacSign($timestamp)
	{
		return md5($timestamp.$this->final_key);
	}
}
