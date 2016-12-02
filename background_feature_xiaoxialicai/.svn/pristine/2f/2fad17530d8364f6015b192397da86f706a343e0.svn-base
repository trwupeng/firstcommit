<?php

namespace Lib\Services\evts;

/**
 * Class onOauthSendInvalidcode
 * @package Lib\Services\evts
 * @author lingtm <605415184@qq.com>
 */
class onOauthSendInvalidcode
{
    /**
     * @param \Sooh\Base\Log\Data $logData logData
     */
    public function run($logData)
    {
        error_log('###EVT###' . __CLASS__);
        $this->sendMessage($logData);
    }

    /**
     * 发送消息
     * @param \Sooh\Base\Log\Data $logData logData
     */
    public function sendMessage($logData)
    {
        $phone = $logData->sarg1;
        $smsCode = $logData->sarg2;

        if (empty($smsCode)) {
            $smsCode = mt_rand(100000, 999999);
        }

        $arg = [
            [
                'event'    => 'reg_name',
                'brand'    => \Prj\Message\Message::MSG_BRAND,
                'num_num'  => $smsCode,
                'num_time' => \Prj\Message\Message::MSG_NUM_TIME_15
            ],
            ['phone' => $phone, 'smsCode' => $smsCode]
        ];

        \Prj\Message\Message::run($arg[0], $arg[1]);
    }
}