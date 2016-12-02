<?php
namespace Lib\Push;
require_once '/var/www/vendor/autoload.php';

use JPush\Model as M;
use JPush\JPushClient;
use JPush\Exception\APIConnectionException;
use JPush\Exception\APIRequestException;
use Sooh\Base\ErrException;

/**
 * Class JPush
 * @author LTM <605415184@qq.com>
 */
class JPush {
	const APP_KEY       = 'e1ecdce3c8cdc621cad55079';//正式环境的
	const MASTER_SECRET = '220a11d5b9d5f3f59f92a15d';//正式环境的

	const errInvalidDeviceType   = '设备类型无效';
	const errInvalidAudience     = 'Audience无效';
	const errInvalidNotification = '通知无效';
	const errInvalidMessage      = '消息无效';
	const errServerBusy          = '服务器忙';

	const TIME_TO_LIVE = null;//离线消息保留时长(秒)
	const APNS_PRODUCTION = true;//APNs是否生产环境
	const BIG_PUSH_DURATION = null;//定速推送时长

	static $VALID_DEVICE_TYPES = ["ios", "android", "winphone"];

	/**
	 * 极光推送
	 * @param string|array $platform     支持：all|ios|ios,winphone|[ios, android]
	 * @param string|array $audience     要推送的设备ID 支持：all|['alias' => [111,222], 'tag' => ['aa', 'bb']]
	 * @param string|array $notification 通知 notify|['default' => 'notify', 'android' => ['content', 'title', 1, ['k1'
	 *                                   => 'v1']]]
	 * @param string|array $message      消息 message|['message', 'title', 'type', 'extras']
	 * @throws \Sooh\Base\ErrException
	 */
	public function push($platform, $audience, $notification = null, $message = null) {
		if (empty($notification) && empty($message)) {
			throw new ErrException(self::errServerBusy);
		}

		try {
			//是否为生产环境
			$apns_production = \Sooh\Base\Ini::getInstance()->get('PushApnsProductionOn');
			//判断是否为开发测试环境的安卓
			if ($apns_production == false && substr($_COOKIE[\Sooh\Base\Session\Data::SessionIdName], 0, 4) != 'idfa') {
				$client = new JPushClient(self::APP_KEY, self::MASTER_SECRET);
			} else {
				$client = new JPushClient(\Sooh\Base\Ini::getInstance()->get('JPush_APP_KEY'), \Sooh\Base\Ini::getInstance()->get('JPush_MASTER_SECRET'));
			}

			$Jpush = $client->push()->setPlatform($this->checkPlatform($platform))->setAudience($this->checkAudiecde($audience));
			if (!empty($notification)) {
				$Jpush->setNotification($this->checkNotification($notification));
			} else {
				$Jpush->setMessage($this->checkMessage($message));
			}

			if ($apns_production) {
				$Jpush->setOptions($this->checkOptions(null, null, null, self::APNS_PRODUCTION, null));
			}

			$result = $Jpush->send();
//			$result = $Jpush->validate();
			error_log('Push Success!');
			var_log($result->sendno, 'sendno');
			var_log($result->msg_id, 'msg_id');
			var_log($result->json, 'Response JSON');
		} catch (APIRequestException $e) {
			var_log($e->httpCode, 'Http Code');
			var_log($e->code, 'code');
			var_log($e->message, 'message');
			var_log($e->json, 'Response JSON');
			var_log($e->rateLimitLimit, 'rateLimitLimit');
			var_log($e->rateLimitRemaining, 'rateLimitRemaining');
			var_log($e->rateLimitReset, 'rateLimitReset');
		} catch (APIConnectionException $e) {
			var_log($e->getMessage(), 'Error Message');
			var_log($e->isResponseTimeout, 'IsPresponseTimeout');
		} catch (\Exception $e) {
			var_log($e->getMessage(), 'Service Other Error>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>');
		}
	}

	public function report() {

	}

	public function device() {

	}

	/**
	 * 校验消息
	 * @param string|array $message 消息
	 * @return mixed
	 * @throws ErrException
	 */
	protected function checkMessage($message) {
		if (is_string($message)) {
			return M\message($message);
		} elseif (is_array($message)) {
			return call_user_func_array('\JPush\Model\message', [$message[0],
			                                                     $message[1] ? : null,
			                                                     $message[2] ? : null,
			                                                     $message[3] ? : null]);
		} else {
			throw new ErrException(self::errInvalidMessage);
		}
	}

	/**
	 * 通知栏
	 * @param string|array $notify
	 * example:
	 *      1: 'alert'
	 *      2: ['default' => 'alert']
	 *      3: ['default' => 'alert',
	 *          'android' => ['alert', 'title', 'builder_id', ['k1' => 'v2', 'k2' => 'v2']],
	 *          'ios' => ['alert', 'sound', 'badge', 'contentAvailable', ['k1' => 'v2', 'k2' => 'v2'], 'category'],
	 *          'winphone' => ['alert', 'title', '_open_page', ['k1' => 'v1', 'k2' => 'v2']]]
	 * @return mixed
	 * @throws \Sooh\Base\ErrException
	 */
	protected function checkNotification($notify) {
		if (is_string($notify)) {
			return M\notification($notify);
		} elseif (is_array($notify)) {
			if (count($notify) == 1) {
				if (isset($notify['default'])) {
					return M\notification($notify['default']);
				} else {
					throw new ErrException(self::errInvalidNotification);
				}
			} else {
				foreach ($notify as $k => $v) {
					switch ($k) {
						case 'default':
							$temp[] = $v;
							break;
						case 'android':
							$temp[] = M\android($v[0], $v[1] ? : null, $v[2] ? : null, $v[3] ? : null);
							break;
						case 'ios':
							$temp[] = M\ios($v[0], $v[1] ? : null, $v[2] ? : null, $v[3] ? : null, $v[4] ? : null, $v[5] ? : null);
							break;
						case 'winphone':
							$temp[] = M\winphone($v[0], $v[1] ? : null, $v[2] ? : null, $v[3] ? : null);
							break;
						default:
							break;
					}
				}
				return call_user_func_array('\JPush\Model\notification', $temp);
			}
		} else {
			throw new ErrException(self::errInvalidNotification);
		}


	}

	/**
	 * 检查audience合法性
	 * @param array $audience audience支持多种写法
	 * example:
	 *         1. 'all'
	 *         2. ['android', 'ios', 'winphone']
	 *         3. ['alias' => ['v1', 'v2']]
	 *         4. ['tag' => ['v1', 'v2']]
	 *         5. ['tag_and' => ['v1', 'v2']]
	 *         6. ['registration_id' => ['v1', 'v2]]
	 * @return array ['alias' => ['1111','2222',], 'tag' => ['stu1', 'stu2']]
	 * @throws \Sooh\Base\ErrException
	 */
	protected function checkAudiecde($audience) {
		if (!is_array($audience)) {
			if ($audience == 'all') {
				return M\all;
			}
			throw new ErrException(self::errInvalidAudience);
		}

		if (key($audience) === 0) {
			$_intersectArr = array_intersect($audience, self::$VALID_DEVICE_TYPES);
			if (array_intersect($_intersectArr, self::$VALID_DEVICE_TYPES) === $_intersectArr) {
				return $audience;
			}
			throw new ErrException(self::errInvalidAudience);
		}

		$ret = [];
		foreach ($audience as $k => $v) {
			switch ($k) {
				case 'alias':
					$ret[] = M\alias($v);
					break;
				case 'tag':
					$ret[] = M\tag($v);
					break;
				case 'tag_and':
					$ret[] = M\tag_and($v);
					break;
				case 'registration_id':
					$ret[] = M\registration_id($v);
					break;
				default:
					throw new ErrException(self::errInvalidAudience);
					break;
			}
		}

		$arr = [];
		foreach ($ret as $v) {
			$arr[key($v)] = current($v);
		}

		return M\audience($arr);
	}

	/**
	 * 校验平台合法性
	 * @param string|array $platform 平台，支持all/ios,android,winphone
	 * @return array ['ios', 'android', 'winphone']
	 * @throws \Sooh\Base\ErrException
	 */
	protected function checkPlatform($platform) {
		if (!is_array($platform)) {
			if ($platform === 'all') {
				return M\all;
			} else {
				$platformArr = explode(',', $platform);
			}
		} else {
			$platformArr = $platform;
		}

		if (is_array($platformArr)) {
			foreach ($platformArr as $v) {
				if (!in_array($v, self::$VALID_DEVICE_TYPES)) {
					throw new ErrException(self::errInvalidDeviceType);
				}
			}
		} else {
			throw new ErrException(self::errInvalidDeviceType);
		}

		return $platformArr;
	}

	/**
	 * 格式化推送的可选项
	 * @param null $sendno 推送序号
	 * @param null $time_to_live 离线消息保留时长(秒)
	 * @param null $override_msg_id 要覆盖的消息ID
	 * @param null $apns_production APNs是否生产环境
	 * @param null $big_push_duration 定速推送时长(分钟)
	 * @return mixed
	 */
	protected function checkOptions($sendno=null, $time_to_live=null, $override_msg_id=null, $apns_production=null, $big_push_duration=null) {
		return M\options($sendno, $time_to_live, $override_msg_id, $apns_production, $big_push_duration);
	}
}