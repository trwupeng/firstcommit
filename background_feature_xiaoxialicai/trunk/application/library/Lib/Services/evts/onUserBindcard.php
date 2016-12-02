<?php

namespace Lib\Services\evts;

/**
 * Class onUserBindcard
 * @package Lib\Services\evts
 * @author  lingtm <605415184@qq.com>
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
        if (!empty($logData->sarg3)) {
            $logerSarg3 = json_decode($logData->sarg3, true);
            if (isset($logerSarg3['_updateUser'])) {
                \Prj\Message\Message::run($logerSarg3['_updateUser'][0], $logerSarg3['_updateUser'][1]);
            }
        }
    }
}