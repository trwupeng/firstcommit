<?php

namespace Lib\Services\evts;

/**
 * Class onOauthWebreg
 * @package Lib\Services\evts
 * @author lingtm <605415184@qq.com>
 */
class onOauthWebreg
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
        if (!empty($logData->sarg1)) {
            $arg1 = json_decode($logData->sarg1, true);
            \Prj\Message\Message::run($arg1[0], $arg1[1]);
        }

        if (!empty($logData->sarg2)) {
            $arg2 = json_decode($logData->sarg2, true);
            \Prj\Message\Message::run($arg2[0], $arg2[1]);
        }

        if (!empty($logData->sarg3)) {
            $arg3 = json_decode($logData->sarg3, true);
            \Prj\Message\Message::run($arg3[0], $arg3[1], $arg3[2], $arg3[3]);
        }
    }
}