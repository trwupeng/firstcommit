<?php

namespace Lib\Services\evts;

/**
 * Class onOrdersAdd-废弃方法
 * @package Lib\Services\evts
 * @author lingtm <605415184@qq.com>
 */
class onOrdersAdd
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
        if (!empty($logData->sarg3)) {
            $sarg3 = json_decode($logData->sarg3, true);

            if (isset($sarg3['addReal_redInvitePacket'])) {
                \Prj\Message\Message::run($sarg3['addReal_redInvitePacket'][0], $sarg3['addReal_redInvitePacket'][1]);
            }

            if (isset($sarg3['addReal_sucAll'])) {
                \Prj\Message\Message::run($sarg3['addReal_sucAll'][0], $sarg3['addReal_sucAll'][1]);
            }

            if (isset($sarg3['addRedl_joinUs'])) {
                \Prj\Message\Message::run($sarg3['addRedl_joinUs'][0], $sarg3['addRedl_joinUs'][1]);
            }

            if (isset($sarg3['invitedInvestPush'])) {
                $args = $sarg3['invitedInvestPush'];
                \Lib\Services\Push::getInstance()->push($args[0], $args[1], $args[2], $args[3]);
                unset($args);
            }

            if (isset($sarg3['parentRedPacketPointPush'])) {
                $args = $sarg3['parentRedPacketPointPush'];
                \Lib\Services\Push::getInstance()->push($args[0], $args[1], $args[2], $args[3]);
                unset($args);
            }
        }
    }
}