<?php
namespace Lib\Services;

/**
 * Class Push
 * @author LTM <605415184@qq.com>
 */
class Push {
	/**
	 * @var Push
	 */
	public static $_instance = null;

	/**
	 * @var \Sooh\Base\Rpc\Broker
	 */
	protected $rpc;

	/**
	 * @var \Lib\Push\JPush
	 */
	private   $engine;

	/**
	 * @param \Sooh\Base\Rpc\Broker $rpcOnNew rpcOnNew
	 * @return Push
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
	 * 推送消息
	 * @param string       $platform     推送平台
	 * @param string|array $userIdArr    接收用户
	 * @param string|array $notification 通知 与message必须有一个
	 * @param string|array $message      消息
	 */
	public function push($platform, $userIdArr, $notification = null, $message = null) {
		if ($this->rpc !== null) {
			$this->rpc->initArgs(['paltform'     => $platform,
			                      'userIdArr'    => $userIdArr,
			                      'notification' => $notification,
			                      'message'      => $message])->send(__FUNCTION__);
		}

		$audience = $this->getAudience($userIdArr);
		try {
			$this->engine->push($platform, $audience, $notification, $message);
		} catch (\Exception $e) {
			var_log('JPush error>>>>>>>>>>>>>>>>>>');
		}
	}

	public function report() {
		if ($this->rpc !== null) {
			return $this->rpc->initArgs([])->send(__FUNCTION__);
		}
	}

	public function device() {
		if ($this->rpc !== null) {
			return $this->rpc->initArgs([])->send(__FUNCTION__);
		}
	}

	/**
	 * 获取设备ID列表,对于userId支持多种传递方式
	 * @param mixed $args 用户ID
	 * @return array ['id1', 'id2', ...]
	 */
	protected function getAudience($args) {
		//逗号隔开的userId
		if (!is_array($args)) {
			if ($args == 'all') {
				return 'all';
			} else {
				$audience['alias'] = explode(',', $args);
			}
		} elseif (!is_array(current($args))) {
			$audience['alias'] = $args;
		}

		foreach ($audience as $k => $v) {
			if ($k == 'alias' && $v != 'all') {
				unset($audience[$k]);
				foreach ($v as $vv) {
					$audience[$k][] = md5($vv . \Sooh\Base\Ini::getInstance()->get('JPUSH_ALIAS_RULE'));
				}
			}
		}
		return $audience;
	}
}