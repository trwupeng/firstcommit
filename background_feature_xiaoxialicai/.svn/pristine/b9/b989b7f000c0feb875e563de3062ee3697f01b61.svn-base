<?php

namespace Lib\Services\evts;

/**
 * Class onWeekactiveFetch
 * @package Lib\Services\evts
 * @author lingtm <605415184@qq.com>
 */
class onWeekactiveFetch
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
     * @param \Sooh\Base\Log\Data $logData logData
     */
    public function sendMessage(\Sooh\Base\Log\Data $logData)
    {
        $itemNum = $logData->sarg1;
        $userId = $logData->sarg2;
        $arg = [
            [
                'event' => 'red_admire_packet',
                'num_packet' => 1,
                'private_gift' => $itemNum,
                'num_deadline' => 48,
                'brand' => \Prj\Message\Message::MSG_BRAND,
            ],
            [
                'userId' => $userId,
            ]
        ];
        \Prj\Message\Message::run($arg[0], $arg[1]);
    }
}