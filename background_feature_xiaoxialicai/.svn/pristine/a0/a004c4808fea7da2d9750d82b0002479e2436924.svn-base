<?php

namespace Lib\Services\evts;

/**
 * Class onUserBindcard
 * @package Lib\Services\evts
 * @author lingtm <605415184@qq.com>
 */
class onUserBindcard
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
        $logerResChanged = $logData->resChanged;
        if (isset($logerResChanged['_updateUser'])) {
            $arg = $logerResChanged['_updateUser'];
            \Prj\Message\Message::run($arg[0], $arg[1]);
        }
    }
}