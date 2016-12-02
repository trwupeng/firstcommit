<?php

namespace Prj\Data;

/**
 * Class Config
 * @author LTM <605415184@qq.com>
 */
class Wechat{

	public static function get($key = null) {
		$db = \Sooh\DB\Broker::getInstance('default');

		$ret = $db->getRecord('tb_config', 'v', ['k' => 'WECHAT_' . strtoupper($key)]);
		return json_decode($ret['v'], true);
	}

	public static function set($key, $value) {
		$db = \Sooh\DB\Broker::getInstance('default');
		$ret = $db->getRecord('tb_config', 'v', ['k' => 'WECHAT_' . strtoupper($key)]);
		if ($ret) {
			return $db->updRecords('tb_config', ['v' => is_array($value) ? json_encode($value) : $value], ['k' => 'WECHAT_' . strtoupper($key)]);
		} else {
			return $db->addRecord('tb_config', ['k' => 'WECHAT_' . strtoupper($key), 'v' => is_array($value) ? json_encode($value) : $value]);
		}
	}
}