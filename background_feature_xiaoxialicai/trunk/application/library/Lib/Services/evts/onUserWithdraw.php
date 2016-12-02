<?php

namespace Lib\Services\evts;

/**
 * Class onUserWithdraw
 * @package Lib\Services\evts
 * @author lingtm <605415184@qq.com>
 */
class onUserWithdraw
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
    public function sendMessage($logData)
    {
        if (!empty($logData->sarg3)) {
            $sarg3 = json_decode($logData->sarg3, true);
            if (isset($sarg3['action_ask_money'])) {
                \Prj\Message\Message::run($sarg3['action_ask_money'][0], $sarg3['action_ask_money'][1]);
            }
        }
    }
}