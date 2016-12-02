<?php

namespace Lib\Services\evts;

include_once(APP_PATH . '/application/library/Lib/Services/evts/traitRegister.php');
include_once(APP_PATH . '/application/library/Lib/Services/evts/traitLogin.php');

/**
 * Class onPassportQuickLogin
 * @package Lib\Services\evts
 * @author lingtm <605415184@qq.com>
 */
class onPassportQuickLogin
{
    use \TraitRegister, \TraitLogin {
        \TraitRegister::run as regRun;
        \TraitLogin::run as loginRun;
    }

    /**
     * @param \Sooh\Base\Log\Data $logData logData
     */
    public function run($logData)
    {
        error_log('###EVT###' . __CLASS__);

        if ($logData->mainType == 1) {
            $this->regRun($logData);
        } else {
            $this->loginRun($logData);
        }

        $this->sendMessage($logData);
    }

    /**
     * 发送消息
     * @param \Sooh\Base\Log\Data $logData logData
     */
    public function sendMessage($logData)
    {
        //本地注册成功消息
        if (!empty($logData->sarg1)) {
            $arg1 = json_decode($logData->sarg1, true);
            \Prj\Message\Message::run($arg1[0], $arg1[1]);
        }

        //周长消息
        if (!empty($logData->sarg2)) {
            $arg2 = json_decode($logData->sarg2, true);
            \Lib\Services\Push::getInstance()->push($arg2[0], $arg2[1], $arg2[2], $arg2[3]);
        }

        if (!empty($logData->sarg3)) {
            $arg3 = json_decode($logData->sarg3, true);
            \Prj\Message\Message::run($arg3[0], $arg3[1]);
        }
    }
}