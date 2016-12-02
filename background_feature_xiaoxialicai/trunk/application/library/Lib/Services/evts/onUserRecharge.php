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
        if (!empty($logData->sarg3)) {
            $srag3 = json_decode($logData->sarg3, true);
            if (isset($srag3['_rechargeCode'])) {
                \Prj\Message\Message::run($srag3['_rechargeCode'][0], $srag3['_rechargeCode'][1]);
            }
        }
    }
}