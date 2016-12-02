<?php
namespace Lib\SMS;
/**
 * Description of ZhuTong
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class ZhuTong
{
	private $prefix = '【小虾理财】';//内容前缀
//	private $prefix = '【金银猫】';//内容前缀
	const channel_regcode = 676767;//验证码通道
	const channel_notify = 48661;//通知通道
	const username = 'jymao';//账户名
	const password = 'DRTkGfh9';//密码
	const url = 'http://www.ztsms.cn:8800/sendXSms.do?';//发送短信URL地址
	protected $channel = 0;
	protected $todo = array();

	/**
	 * 准备发送消息
	 * @param integer $phone 手机号
	 * @param string $msg 发送内容
	 * @param int $channel 发送通道
	 * @return mixed
	 */
	public function send($phone, $msg, $channel = 0)
	{
		if (strpos($msg, $this->prefix) === false) {
			$msg = $this->prefix . $msg;
		}
		if ($channel === 0) {
			if (strpos($msg, '验证')) {
				$this->channel = self::channel_regcode;//验证码通道
			} else {
				$this->channel = self::channel_notify;//通知通道
			}
		}

		$this->_sendMsg($phone, $msg);
		return $this->_sendMsg(null, $msg);
	}

	/**
	 * 通过Curl发送短信
	 * @param integer $phone 手机号
	 * @param string $msg 发送内容
	 * @return mixed|void
	 */
	public function _sendMsg($phone, $msg)//,$charset
	{
		if ($phone === null) {
			if (empty($this->todo)) {
				return;
			}
		} else {
			$this->todo[] = $phone;
			if (sizeof($this->todo) < 200) {
				return;
			}
		}
		$phone = implode(',', $this->todo);
		$this->todo = array();

		//$content=iconv("UTF-8", "UTF-8", $content);
		$argv = array(
			'username' => self::username,
			'password' => md5(self::password),
			'mobile' => $phone,
			'content' => $msg,
			'dstime' => '',
			'productid' => $this->channel,
			'xh' => '',
		);
		$url = self::url . http_build_query($argv);
		$ch = curl_init();
		$timeout = 5;
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		$file_contents = curl_exec($ch);
		curl_close($ch);

		error_log('send code fro phone:' . $phone);
		var_log($file_contents);
		return $file_contents;
	}
}
