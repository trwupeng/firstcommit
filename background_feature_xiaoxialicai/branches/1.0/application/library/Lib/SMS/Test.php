<?php
namespace Lib\SMS;
/**
 * 测试的短信通道
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */

class Test
{
	private $prefix = '【小虾理财】';//内容前缀
	const tb="db_log.a_sms";
	/**
	 * 准备发送消息
	 * @param integer $phone 手机号
	 * @param string $msg 发送内容
	 * @param int $channel 发送通道
	 * @return mixed
	 */
	public function send($phone, $msg, $channel = 0)
	{
		$sys = new \Sooh\Base\Tests\SMS(\Sooh\DB\Broker::getInstance('default'), self::tb);
		$sys->send($phone, $msg, $channel);
		return true;
	}
	
}
