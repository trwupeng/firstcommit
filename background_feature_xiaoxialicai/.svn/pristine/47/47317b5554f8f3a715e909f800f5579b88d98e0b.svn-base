<?php

namespace Lib\Services\evts;

/**
 * Class onUserRecharge
 * @package Lib\Services\evts
 * @author lingtm <605415184@qq.com>
 */
class onUserRecharge
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
        if (isset($logData->resChanged['_rechargeCode'])) {
            $arg = $logData->resChanged['_rechargeCode'];
            \Prj\Message\Message::run($arg[0], $arg[1]);
        }
    }
}