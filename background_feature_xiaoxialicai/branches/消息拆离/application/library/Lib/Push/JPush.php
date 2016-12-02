<?php
namespace Lib\Push;
require_once '/var/www/vendor/autoload.php';

use JPush\Model as M;
use JPush\JPushClient;
use JPush\Exception\APIConnectionException;
use JPush\Exception\APIRequestException;
use Sooh\Base\ErrException;

/**
 * 极光推送
 * @author LTM <605415184@qq.com>
 */
class JPush
{
    const errInvalidDeviceType   = '设备类型无效';
    const errInvalidAudience     = 'Audience无效';
    const errInvalidNotification = '通知无效';
    const errInvalidMessage      = '消息无效';
    const errServerBusy          = '服务器忙';

    /**
     * @var null 离线消息保留时长(秒)
     */
    const TIME_TO_LIVE = null;

    /**
     * @var boolean APNs是否生产环境
     */
    const APNS_PRODUCTION = true;

    /**
     * @var null 定速推送时长
     */
    const BIG_PUSH_DURATION = null;

    /**
     * @var array 支持的平台
     */
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
    public function push($platform, $audience, $notification = null, $message = null)
    {
        if (empty($notification) && empty($message)) {
            throw new ErrException(self::errServerBusy);
        }
        $pushConf = \Sooh\Base\Ini::getInstance()->get('JPUSH');

        switch ($pushConf['DEPLOY_ENVIRONMENT']) {
            case 'dev':
                $this->pushForDev($platform, $audience, $notification, $message);
                break;
            case 'test':
                $this->pushForTest($platform, $audience, $notification, $message);
                break;
            case 'online':
                $this->pushForOnline($platform, $audience, $notification, $message);
                break;
        }

        if ($pushConf['PRO_ON']) {
            $this->pushForPro($platform, $audience, $notification, $message);
        }
    }

    /**
     * 推送-针对线上环境
     * @param mixed $platform     平台
     * @param mixed $audience     目标
     * @param null  $notification 通知内容
     * @param null  $message      自定义消息
     * @throws ErrException
     * @throws \ErrorException
     */
    protected function pushForOnline($platform, $audience, $notification = null, $message = null)
    {
        try {
            error_log('===============Online push');
            $pushConfig   = \Sooh\Base\Ini::getInstance()->get('JPUSH');
            $onlineClient = new JPushClient($pushConfig['KEY'], $pushConfig['SECRET']);
            $onlineJpush  = $onlineClient->push()->setPlatform($this->checkPlatform($platform))->setAudience($this->checkAudiecde($audience));

            if (!empty($notification)) {
                $onlineJpush->setNotification($this->checkNotification($notification));
            } else {
                $onlineJpush->setMessage($this->checkMessage($message));
            }
            $onlineJpush->setOptions($this->checkOptions(null, null, null, self::APNS_PRODUCTION, null));
            $onlineResult = $onlineJpush->send();
            var_log($onlineResult->json, 'Response JSON');
        } catch (APIRequestException $e) {
            var_log($e->httpCode, 'Http Code');
            var_log($e->json, 'Response JSON');
        } catch (APIConnectionException $e) {
            var_log($e->getMessage(), 'Error Message');
            var_log($e->isResponseTimeout, 'IsPresponseTimeout');
        } catch (\Exception $e) {
            var_log($e->getMessage(), 'Service Other Error>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>');
        }
    }

    /**
     * 推送-针对开发环境
     * 安卓使用（正式服）的配置推送
     * @param mixed $platform     目标平台
     * @param mixed $audience     目标客户
     * @param null  $notification 通知内容
     * @param null  $message      自定义消息体
     * @throws ErrException
     * @throws \ErrorException
     */
    protected function pushForDev($platform, $audience, $notification = null, $message = null)
    {
        try {
            error_log('===============Dev push');
            $pushConfig = \Sooh\Base\Ini::getInstance()->get('JPUSH');

            $devClient = new JPushClient($pushConfig['KEY'], $pushConfig['SECRET']);
            $devJpush  = $devClient->push()->setPlatform($this->checkPlatform($platform))->setAudience($this->checkAudiecde($audience));
            if (!empty($notification)) {
                $devJpush->setNotification($this->checkNotification($notification));
            } else {
                $devJpush->setMessage($this->checkMessage($message));
            }

            $devResult = $devJpush->send();
            var_log($devResult->json, 'Dev Response JSON');
        } catch (APIRequestException $e) {
            var_log($e->httpCode, 'Http Code');
            var_log($e->json, 'Response JSON');
        } catch (APIConnectionException $e) {
            var_log($e->getMessage(), 'Error Message');
            var_log($e->isResponseTimeout, 'IsPresponseTimeout');
        } catch (\Exception $e) {
            var_log($e->getMessage(), 'Service Other Error>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>');
        }

        try {
            if (isset($pushConfig['KEY_EXT'], $pushConfig['SECRET_EXT'])) {
                error_log('===============Dev Ext push');
                $pushConfig   = \Sooh\Base\Ini::getInstance()->get('JPUSH');
                $devClientExt = new JPushClient($pushConfig['KEY_EXT'], $pushConfig['SECRET_EXT']);
                $devJpushExt  = $devClientExt->push()->setPlatform($this->checkPlatform($platform))->setAudience($this->checkAudiecde($audience));
                if (!empty($notification)) {
                    $devJpushExt->setNotification($this->checkNotification($notification));
                } else {
                    $devJpushExt->setMessage($this->checkMessage($message));
                }

                $devResultExt = $devJpushExt->send();
                var_log($devResultExt->json, 'Dev Ext Response JSON');
            }
        } catch (APIRequestException $e) {
            var_log($e->httpCode, 'Http Code');
            var_log($e->json, 'Response JSON');
        } catch (APIConnectionException $e) {
            var_log($e->getMessage(), 'Error Message');
            var_log($e->isResponseTimeout, 'IsPresponseTimeout');
        } catch (\Exception $e) {
            var_log($e->getMessage(), 'Service Other Error>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>');
        }
    }

    /**
     * 推送-针对测试环境
     * @param mixed $platform     目标平台
     * @param mixed $audience     接收目标
     * @param null  $notification 通知内容
     * @param null  $message      自定义消息体
     * @throws ErrException
     * @throws \ErrorException
     */
    protected function pushForTest($platform, $audience, $notification = null, $message = null)
    {
        try {
            error_log('===============Test push');
            $pushConfig = \Sooh\Base\Ini::getInstance()->get('JPUSH');

            $devClient = new JPushClient($pushConfig['KEY'], $pushConfig['SECRET']);
            $devJpush  = $devClient->push()->setPlatform($this->checkPlatform($platform))->setAudience($this->checkAudiecde($audience));
            if (!empty($notification)) {
                $devJpush->setNotification($this->checkNotification($notification));
            } else {
                $devJpush->setMessage($this->checkMessage($message));
            }

            $devResult = $devJpush->send();
            var_log($devResult->json, 'Dev Response JSON');
        } catch (APIRequestException $e) {
            var_log($e->httpCode, 'Http Code');
            var_log($e->json, 'Response JSON');
        } catch (APIConnectionException $e) {
            var_log($e->getMessage(), 'Error Message');
            var_log($e->isResponseTimeout, 'IsPresponseTimeout');
        } catch (\Exception $e) {
            var_log($e->getMessage(), 'Service Other Error>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>');
        }

        try {
            if (isset($pushConfig['KEY_EXT'], $pushConfig['SECRET_EXT'])) {
                error_log('===============Test Ext push');
                $pushConfig   = \Sooh\Base\Ini::getInstance()->get('JPUSH');
                $devClientExt = new JPushClient($pushConfig['KEY_EXT'], $pushConfig['SECRET_EXT']);
                $devJpushExt  = $devClientExt->push()->setPlatform($this->checkPlatform($platform))->setAudience($this->checkAudiecde($audience));
                if (!empty($notification)) {
                    $devJpushExt->setNotification($this->checkNotification($notification));
                } else {
                    $devJpushExt->setMessage($this->checkMessage($message));
                }

                $devResultExt = $devJpushExt->send();
                var_log($devResultExt->json, 'Dev Ext Response JSON');
            } else {
                error_log('sorry, cant found pro config');
            }
        } catch (APIRequestException $e) {
            var_log($e->httpCode, 'Http Code');
            var_log($e->json, 'Response JSON');
        } catch (APIConnectionException $e) {
            var_log($e->getMessage(), 'Error Message');
            var_log($e->isResponseTimeout, 'IsPresponseTimeout');
        } catch (\Exception $e) {
            var_log($e->getMessage(), 'Service Other Error>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>');
        }
    }

    /**
     * 向IOS推送PRO
     * @param mixed $platform     目标平台
     * @param mixed $audience     接收目标
     * @param null  $notification 通知内容
     * @param null  $message      自定义消息体
     * @throws ErrException
     * @throws \ErrorException
     */
    protected function pushForPro($platform, $audience, $notification = null, $message = null)
    {
        try {
            error_log('===============PRO push');
            $pushConfig = \Sooh\Base\Ini::getInstance()->get('JPUSH');
            if (isset($pushConfig['KEY_PRO'], $pushConfig['SECRET_PRO'])) {
                $proClient = new JPushClient($pushConfig['KEY_PRO'], $pushConfig['SECRET_PRO']);
                $proJpush  = $proClient->push()->setPlatform($this->checkPlatform('ios'))->setAudience($this->checkAudiecde($audience));
                if (!empty($notification)) {
                    $proJpush->setNotification($this->checkNotification($notification));
                } else {
                    $proJpush->setMessage($this->checkMessage($message));
                }

                if ($pushConfig['DEPLOY_ENVIRONMENT'] == 'online') {
                    $proJpush->setOptions($this->checkOptions(null, null, null, self::APNS_PRODUCTION, null));
                }

                $proResult = $proJpush->send();
                var_log($proResult->json, 'Pro Response JSON');
            } else {
                error_log('sorry, cant found pro config');
            }
        } catch (APIRequestException $e) {
            var_log($e->httpCode, 'Http Code');
            var_log($e->json, 'Response JSON');
        } catch (APIConnectionException $e) {
            var_log($e->getMessage(), 'Error Message');
            var_log($e->isResponseTimeout, 'IsPresponseTimeout');
        } catch (\Exception $e) {
            var_log($e->getMessage(), 'Service Other Error>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>');
        }
    }

    public function report()
    {

    }

    public function device()
    {

    }

    /**
     * 校验自定义消息
     * @param string|array $message 消息
     * @return mixed
     * @throws ErrException
     */
    protected function checkMessage($message)
    {
        if (is_string($message)) {
            return M\message($message);
        } elseif (is_array($message)) {
            return call_user_func_array('\JPush\Model\message', [
                $message[0],
                $message[1] ? : null,
                $message[2] ? : null,
                $message[3] ? : null
            ]);
        } else {
            throw new ErrException(self::errInvalidMessage);
        }
    }

    /**
     * 校验通知内容体
     * @param string|array $notify
     *          example:
     *          1: 'alert'
     *          2: ['default' => 'alert']
     *          3: ['default' => 'alert',
     *          'android' => ['alert', 'title', 'builder_id', ['k1' => 'v2', 'k2' => 'v2']],
     *          'ios' => ['alert', 'sound', 'badge', 'contentAvailable', ['k1' => 'v2', 'k2' => 'v2'], 'category'],
     *          'winphone' => ['alert', 'title', '_open_page', ['k1' => 'v1', 'k2' => 'v2']]]
     * @return mixed
     * @throws \Sooh\Base\ErrException
     */
    protected function checkNotification($notify)
    {
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
     *                        example:
     *                        1. 'all'
     *                        2. ['android', 'ios', 'winphone']
     *                        3. ['alias' => ['v1', 'v2']]
     *                        4. ['tag' => ['v1', 'v2']]
     *                        5. ['tag_and' => ['v1', 'v2']]
     *                        6. ['registration_id' => ['v1', 'v2]]
     * @return array ['alias' => ['1111','2222',], 'tag' => ['stu1', 'stu2']]
     * @throws \Sooh\Base\ErrException
     */
    protected function checkAudiecde($audience)
    {
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
    protected function checkPlatform($platform)
    {
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

        switch (count($platformArr)) {
            case 1:
                return M\Platform($platformArr[0]);
            default:
                return call_user_func_array('\JPush\Model\platform', $platformArr);
        }
    }

    /**
     * 格式化推送的可选项
     * @param null $sendno            推送序号
     * @param null $time_to_live      离线消息保留时长(秒)
     * @param null $override_msg_id   要覆盖的消息ID
     * @param null $apns_production   APNs是否生产环境
     * @param null $big_push_duration 定速推送时长(分钟)
     * @return mixed
     */
    protected function checkOptions($sendno = null, $time_to_live = null, $override_msg_id = null, $apns_production = null, $big_push_duration = null)
    {
        return M\options($sendno, $time_to_live, $override_msg_id, $apns_production, $big_push_duration);
    }
}