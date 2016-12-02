<?php
namespace Lib\Services;

/**
 * 消息中心：推送
 * @author LTM <605415184@qq.com>
 */
class Bypush {
	/**
	 * @var Bypush
	 */
	public static $_instance = null;

	/**
	 * @var \Sooh\Base\Rpc\Broker
	 */
	protected $rpc;



	/**
	 * @param \Sooh\Base\Rpc\Broker $rpcOnNew rpcOnNew
	 * @return \Lib\Services\Bypush
	 * @throws \ErrorException
	 */
	public static function getInstance($rpcOnNew = null) {
		if (self::$_instance === null) {
			$c                    = get_called_class();
			self::$_instance      = new $c;
			self::$_instance->rpc = $rpcOnNew;
		}
		$engine = '\Lib\Push\\' . \Sooh\Base\Ini::getInstance()->get('PushConf');
		self::$_instance->engine = new $engine;

		return self::$_instance;
	}
	/**
	 * @var \Lib\Push\JPush
	 */
	private   $engine;
	/**
	 * 全平台推送一般消息
	 * @param string       $receivers     receivers是逗号分隔的用户id列表，一次不超过100个
	 * @param string       $msg    消息内容
	 */
	public function sendMsg($receivers, $msg) {
		if ($this->rpc !== null) {
			$this->rpc->initArgs(['receivers'=> $receivers, 'msg' => $msg])->send(__FUNCTION__);
		}else{
			error_log(">>>>>msg_center>>>>>>>".__CLASS__.'->'.__FUNCTION__."($receivers,$msg)");
		}
	}
	
	/**
	 * 全平台推送一般消息
	 * @param string       $receivers     receivers是逗号分隔的用户id列表，一次不超过100个
	 * @param string       $cmd    json格式的定义的命令
	 */
	public function sendCmd($receivers, $cmd) {
		if ($this->rpc !== null) {
			$this->rpc->initArgs(['receivers'=> $receivers, 'cmd' => $cmd])->send(__FUNCTION__);
		}else{
			error_log(">>>>>msg_center>>>>>>>".__CLASS__.'->'.__FUNCTION__."($receivers,$cmd)");
		}
	}
}