<?php

namespace Lib\Services\evts;

/**
 * Class onUserUnBindCard
 * @package Lib\Services\evts
 * @author lingtm <605415184@qq.com>
 */
class onUserUnBindCard
{
    /**
     * @param \Sooh\Base\Log\Data $logData
     */
    public function run(\Sooh\Base\Log\Data $logData)
    {
        error_log('###EVT###' . __CLASS__);
        $this->sendMessage($logData);
    }

    /**
     * 发送消息
     * @param \Sooh\Base\Log\Data $logData
     */
    public function sendMessage(\Sooh\Base\Log\Data $logData)
    {
        if (!empty($logData->sarg1)) {
            $arg = $logData->sarg1;
            \Lib\Services\Push::getInstance()->push($arg[0], $arg[1], $arg[2], $arg[3]);//旧版本
        }
    }
}