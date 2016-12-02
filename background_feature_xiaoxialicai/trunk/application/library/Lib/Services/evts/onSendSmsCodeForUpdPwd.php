<?php
namespace Lib\Services\evts;

/**
 * 为修改登录密码发送短信验证码
 * @package Lib\Services\evts
 * @author lingtm <605415184@qq.com>
 */
class onSendSmsCodeForUpdPwd
{
    const MSG_FLAG = 'login_num';

    /**
     * 入口
     * @param \Sooh\Base\Log\Data $data data
     */
    public function run($data)
    {
        error_log('###EVT###' . __CLASS__);
        $this->sendSmsCode($data);
    }

    /**
     * 发送消息
     * @param \Sooh\Base\Log\Data $data data
     */
    public function sendSmsCode($data)
    {
        $phone = $data->sarg1;
        $smsCode = $data->sarg2;
        if (empty($smsCode)) {
            $smsCode = mt_rand(100000, 999999);
        }
        $brand = \Prj\Message\Message::MSG_BRAND;
        $numTime = \Prj\Message\Message::MSG_NUM_TIME_15;

        !empty($phone) && \Prj\Message\Message::run(
            ['event' => self::MSG_FLAG, 'brand' => $brand, 'num_num' => $smsCode, 'num_time' => $numTime],
            ['phone' => $phone, 'smsCode' => $smsCode]
        );
    }
}