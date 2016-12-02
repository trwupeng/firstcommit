<?php

namespace Lib\Services\evts;

/**
 * Class onExchangecodeGetbonus
 * @package Lib\Services\evts
 * @author lingtm <605415184@qq.com>
 */
class onExchangecodeGetbonus
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
        //发送兑换消息
        if (!empty($logData->sarg1)) {
            $arg1 = json_decode($logData->sarg1, true);
            \Prj\Message\Message::run($arg1[0], $arg1[1]);
        }
    }
}