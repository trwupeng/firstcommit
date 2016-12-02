<?php
/**
 * Created by PhpStorm.
 * User: LTM <605415184@qq.com>
 * Date: 2016/4/21
 * Time: 11:38
 */

namespace Prj\Data;

class Regevt extends \Sooh\DB\Base\KVObj
{
	public static $tableName = 'db_logs.tb_regevt';

	public static function addOne($params)
	{
		try {
			\Sooh\DB\Broker::getInstance()->addRecord(self::$tableName, [
				'ip'        => \Sooh\Base\Tools::remoteIP(),
				'sessionId' => $_COOKIE[\Sooh\Base\Session\Data::SessionIdName],
				'ymd'       => date('Ymd'),
				'his'       => date('His'),
				'a'         => isset($params['a']) ? $params['a'] : '',
				'b'         => isset($params['b']) ? $params['b'] : '',
				'c'         => isset($params['c']) ? $params['c'] : '',
				'reged'     => isset($params['reged']) ? $params['reged'] : '',
				'sendcode'  => isset($params['sendcode']) ? $params['sendcode'] : '',
				'lt'        => isset($params['lt']) ? $params['lt'] : '',
				'source'    => isset($params['source']) ? $params['source'] : '',
				'channel'   => isset($params['channel']) ? $params['channel'] : '',
				'_post'     => json_encode($_POST),
				'_get'      => json_encode($_GET),
				'_cookie'   => json_encode($_COOKIE),
			]);
		} catch (\ErrorException $e) {
			error_log('record reg evt error:' . $e->getMessage());
		}
	}
}