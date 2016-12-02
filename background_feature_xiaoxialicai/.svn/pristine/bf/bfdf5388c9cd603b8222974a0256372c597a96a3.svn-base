<?php

namespace Lib\Services\evts;

/**
 * 为快速登录发送短信验证码
 * @package Lib\Services\evts
 * @author lingtm <605415184@qq.com>
 */
class onSendSmsCodeForQuickLogin
{
    const MSG_FLAG = 'fast_name';

    /**
     * 入口
     * @param \Sooh\Base\Log\Data $logData logData
     */
    public function run($logData)
    {
        error_log('###EVT###' . __CLASS__);
        $this->sendSmsCode($logData);
    }

    /**
     * 发送消息
     * @param \Sooh\Base\Log\Data $logData logData
     */
    public function sendSmsCode($logData)
    {
        $phone = $logData->sarg1;
        $smsCode = $logData->sarg2;

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