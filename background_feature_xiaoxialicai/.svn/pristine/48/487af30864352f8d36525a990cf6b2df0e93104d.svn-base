<?php

namespace Lib\Services\evts;

/**
 * Class onUserSendSmscode
 * @package Lib\Services\evts
 * @author lingtm <605415184@qq.com>
 */
class onUserSendSmscode
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
     * @param \Sooh\Base\Log\Data $logData logData
     */
    public function sendMessage(\Sooh\Base\Log\Data $logData)
    {
        $phone = $logData->sarg1;
        $smsCode = $logData->sarg2;
        if (empty($smsCode)) {
            $smsCode = mt_rand(100000, 999999);
        }
        $str = \Prj\SMS::$formats['resetTradePwd'];
        \Prj\Message\Message::send($str, ['sms' => 1], ['phone' => $phone, 'smsCode' => $smsCode]);
    }
}