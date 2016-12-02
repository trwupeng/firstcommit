<?php
namespace Lib\Services;
/**
 * rpc 服务器管理
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class Bysms
{
	/**
	 * @var Bysms
	 */
	protected static $_instance = null;

	/**
	 *
	 * @param \Sooh\Base\Rpc\Broker $rpcOnNew
	 * @return \Lib\Services\Bysms
	 */
	public static function getInstance($rpcOnNew = null)
	{
		if (self::$_instance === null) {
			$c = get_called_class();
			self::$_instance = new $c;
			self::$_instance->rpc = $rpcOnNew;
		}
		return self::$_instance;
	}

	/**
	 * @var \Sooh\Base\Rpc\Broker
	 */
	protected $rpc;

	/**
	 * 发送验证码信息
	 * @param string $receiver 手机号
	 * @param string $msg 消息的格式串
	 */
	public function sendCode($receiver, $msg)
	{
		if ($this->rpc !== null) {
			error_log(">>>>>msg_center>".__CLASS__.'->'.__FUNCTION__."($receiver,$msg)");
			return $this->rpc->initArgs(array('receiver' => $receiver, 'msg' => $msg))->send(__FUNCTION__);
		} else {
			error_log(">>>>>msg_center>>>>>>>".__CLASS__.'->'.__FUNCTION__."($receiver,$msg)");
		}
	}

	/**
	 * 发送通知类消息
	 * @param string $receivers 逗号分隔的手机号，一次不超过100个
	 * @param string $msg 消息内容
	 */
	public function sendNotice($receivers, $msg)
	{
		if ($this->rpc !== null) {
			return $this->rpc->initArgs(array('phone' => $receivers, 'msg' => $msg))->send(__FUNCTION__);
		} else {
			error_log(">>>>>msg_center>>>>>>>".__CLASS__.'->'.__FUNCTION__."($receivers,$msg)");
		}
	}

	/**
	 *
	 * 营销短信
	 * @param string $receivers 逗号分隔的手机号，一次不超过100个
	 * @param string $msg 消息内容
	 */
	public function sendMarket($receivers, $msg){
		if ($this->rpc !== null) {
			return $this->rpc->initArgs(array('phone' => $receivers, 'msg' => $msg))->send(__FUNCTION__);
		} else {
			error_log(">>>>>msg_center>>>>>>>".__CLASS__.'->'.__FUNCTION__."($receivers,$msg)");
		}
	}
}
