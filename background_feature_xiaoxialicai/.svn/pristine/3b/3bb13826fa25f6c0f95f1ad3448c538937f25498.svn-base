<?php

namespace Lib\Services\evts;

/**
 * Class onOrdersAddReal
 * @package Lib\Services\evts
 * @author LTM <605415184@qq.com>
 */
class onOrdersAddReal
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
        if (!empty($logData->resChanged)) {
            $arg       = json_decode($logData->resChanged);
            $waresName = $arg['waresName'];
            $phone     = $arg['phone'];
            $userId    = $arg['userId'];
            $contOk    = $arg['contOk'];
            $timeAll   = $arg['timeAll'];

            $ret  = \Prj\Message\Message::run(
                ['event' => 'suc_all', 'pro_name' => $waresName,],
                ['phone' => $phone, 'userId' => $userId]
            );
            $ret1 = \Prj\Message\Message::run(
                ['event'    => 'join_us',
                 'brand'    => \Prj\Message\Message::MSG_BRAND,
                 'cont_ok'  => $contOk,
                 'time_all' => $timeAll
                ],
                ['phone' => $phone, 'userId' => $userId]
            );
        }
    }
}