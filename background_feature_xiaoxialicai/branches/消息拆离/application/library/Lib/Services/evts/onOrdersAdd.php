<?php

namespace Lib\Services\evts;

/**
 * Class onOrdersAdd
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
        if (!empty($logData->resChanged)) {
            $resChanged = $logData->resChanged;

            if (isset($resChanged['addReal_redInvitePacket'])) {
                \Prj\Message\Message::run($resChanged['addReal_redInvitePacket'][0], $resChanged['addReal_redInvitePacket'][1]);
            }

            if (isset($resChanged['addReal_sucAll'])) {
                \Prj\Message\Message::run($resChanged['addReal_sucAll'][0], $resChanged['addReal_sucAll'][1]);
            }

            if (isset($resChanged['addRedl_joinUs'])) {
                \Prj\Message\Message::run($resChanged['addRedl_joinUs'][0], $resChanged['addRedl_joinUs'][1]);
            }

            if (isset($resChanged['invitedInvestPush'])) {
                $args = $resChanged['invitedInvestPush'];
                \Lib\Services\Push::getInstance()->push($args[0], $args[1], $args[2], $args[3]);
                unset($args);
            }

            if (isset($resChanged['parentRedPacketPointPush'])) {
                $args = $resChanged['parentRedPacketPointPush'];
                \Lib\Services\Push::getInstance()->push($args[0], $args[1], $args[2], $args[3]);
                unset($args);
            }
        }
    }
}