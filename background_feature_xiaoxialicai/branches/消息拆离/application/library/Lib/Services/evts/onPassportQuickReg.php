<?php

namespace Lib\Services\evts;

/**
 * Class onPassportQuickReg
 * @package Lib\Services\evts
 * @author lingtm <605415184@qq.com>
 */
class onPassportQuickReg
{
    /**
     * 入口
     * @param \Sooh\Base\Log\Data $logData logData
     */
    public function run($logData)
    {
        error_log('###EVT###' . __CLASS__);
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
            \Lib\Services\Push::getInstance()->push($arg2[0], $arg2[1], $arg2[2], $arg2[3]);
        }
    }
}