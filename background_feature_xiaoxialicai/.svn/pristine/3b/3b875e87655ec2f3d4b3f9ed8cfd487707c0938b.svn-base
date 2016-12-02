<?php

namespace Lib\Services\evts;

/**
 * Class onPassportLogin
 * @package Lib\Services\evts
 * @author lingtm <605415184@qq.com>
 */
class onPassportLogin
{
    /**
     * 入口
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
        //首次登陆的红包
        if (!empty($logData->sarg1)) {
            $arg1 = json_decode($logData->sarg1, true);
            try {
                \Prj\Message\Message::run($arg1[0], $arg1[1]);
            } catch (\Exception $e) {
                //TODO deal Exception
            }
        }

        //周常邀请的消息
        if (!empty($logData->sarg2)) {
            $arg2 = json_decode($logData->sarg2, true);
            \Lib\Services\Push::getInstance()->push($arg2[0], $arg2[1], $arg2[2], $arg2[3]);
        }

        //首次激活app的红包奖励
        if (!empty($logData->sarg3)) {
            $arg3 = json_decode($logData->sarg3, true);
            try {
                \Prj\Message\Message::run($arg3[0], $arg3[1]);
            } catch (\Exception $e) {
                //TODO deal Exception
            }
        }
    }
}