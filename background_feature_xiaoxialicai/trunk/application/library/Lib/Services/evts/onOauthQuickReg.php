<?php

namespace Lib\Services\evts;

/**
 * Class onOauthQuickReg
 * @package Lib\Services\evts
 * @author lingtm <605415184@qq.com>
 */
class onOauthQuickReg
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
     * 发送短信
     * @param \Sooh\Base\Log\Data $logData logData
     */
    public function sendMessage($logData)
    {
        if (!empty($logData->sarg1)) {
            $arg1 = json_decode($logData->sarg1, true);
            \Prj\Message\Message::run($arg1[0], $arg1[1]);
        }
    }
}