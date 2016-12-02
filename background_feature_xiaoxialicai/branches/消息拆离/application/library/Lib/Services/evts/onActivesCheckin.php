<?php

namespace Lib\Services\evts;

/**
 * Class onActivesCheckin
 * @package Lib\Services\evts
 * @author lingtm <605415184@qq.com>
 */
class onActivesCheckin
{
    /**
     * @param \Sooh\Base\Log\Data $logData logData
     */
    public function run(\Sooh\Base\Log\Data $logData)
    {
        error_log('###EVT###' . __CLASS__);
        $this->sendMessage($logData);
    }

    /**
     * 发送消息
     * @param \Sooh\Base\Log\Data $logData logData
     */
    public function sendMessage(\Sooh\Base\Log\Data $logData)
    {
        $amount = $logData->sarg1;
        $phone  = $logData->sarg2;
        $userId = $logData->sarg3;

        \Prj\Message\Message::run(
            ['event' => 'red_sign_packet', 'brand' => \Prj\Message\Message::MSG_BRAND, 'sign_money' => $amount],
            ['phone' => $phone, 'userId' => $userId]
        );
    }
}