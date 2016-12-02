<?php
namespace Lib\Services\evts;
/**
 * 用户发出购买请求 以后做哪些任务
 * @author wang.ning
 */
class onBuyRequest
{

    protected $arr;

    /**
     * @param \Sooh\Base\Log\Data $data
     * @return void
     */
    public function run($data)
    {
        $this->notifyRptCenter();
        $this->sendMessage($data);
//        \Lib\Services\Bysms::getInstance(\Prj\BaseCtrl::getRpcDefault('Bysms'))
//            ->sendCode('130123456789', __CLASS__ . '随文');
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

    /**
     * 通知报表中心
     */
    protected function notifyRptCenter()
    {

    }


}
