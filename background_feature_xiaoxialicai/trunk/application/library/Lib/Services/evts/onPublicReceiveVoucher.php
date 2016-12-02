<?php

namespace Lib\Services\evts;

/**
 * Class onPublicReceiveVoucher
 * @package Lib\Services\evts
 * @author lingtm <605415184@qq.com>
 */
class onPublicReceiveVoucher
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
        //首次登录
        if (!empty($logData->sarg1)) {
            $arg1 = json_decode($logData->sarg1, true);
            \Prj\Message\Message::run($arg1[0], $arg1[1]);
        }

        //周常
        if (!empty($logData->sarg2)) {
            $arg2 = json_decode($logData->sarg2, true);
            \Lib\Services\Push::getInstance()->push($arg2[0], $arg2[1], $arg2[2], $arg2[3]);
        }

        //发送注册短信
        if (!empty($logData->sarg3)) {
            $phone = $logData->sarg3;
            $smsCode = mt_rand(100000, 999999);
            $arg3 = [
                [
                    'event' => 'reg_name',
                    'brand' => \Prj\Message\Message::MSG_BRAND,
                    'num_num' => $smsCode,
                    'num_time' => \Prj\Message\Message::MSG_NUM_TIME_15,
                ],
                [
                    'phone' => $phone,
                    'smsCode' => $smsCode
                ]
            ];
            \Prj\Message\Message::run($arg3[0], $arg3[1]);
        }
    }
}