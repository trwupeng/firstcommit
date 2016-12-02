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
        if (isset($logData->resChanged['action_ask_money'])) {
            $arg = $logData->resChanged['action_ask_money'];
            \Prj\Message\Message::run($arg[0], $arg[1]);
        }
    }
}