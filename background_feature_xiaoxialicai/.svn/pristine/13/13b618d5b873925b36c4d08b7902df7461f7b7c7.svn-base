<?php

use \Lib\Misc\InputValidation as InputValidation;

/**
 * 活动相关
 * @author lingtm <605415184@qq.com>
 */
class ActivesController extends \Prj\UserCtrl
{
    /**
     * 签到
     * @input string withBonus 是否显示签到记录
     * @input string dowhat checkin标识签到
     * //返回客户端旧标识信息
     * @output {"account": {"accountId": "81568478941117","nickname": "UID:****117"},"checkinBook": {"ymd":
     *         "20151119","checked": [{"ymd": 20151118,"bonus": {"RedPacketForCheckin": 156,"ShopPointForCheckin":
     *         10}}],"todaychked": 0,"bonusList": [{"VoucherSmall": 1},{"VoucherSmall": 1},{"VoucherSmall":
     *         1},{"VoucherSmall": 1},{"VoucherSmall": 1},{"VoucherSmall": 1},{"VoucherBig": 1}]},"code": 200,"info":
     *         {"accountId": "81568478941117","contractId": "0","phone": "18616700069","invitationCode":
     *         "tgtkjgh","protocol": "","nickname": "UID:1117"},"msg": "成功"}
     * //返回客户端新标识信息
     * @output {"account": {"accountId": "81568478941117","nickname": "UID:****117"},"checkBonusbook": {"ymd":
     *         "20151119","checked": [{"ymd": 20151118,"bonus": {"RedPacketForCheckin": 156,"ShopPointForCheckin":
     *         10}}],"todaychked": 0,"bonusList": [{"VoucherSmall": 1},{"VoucherSmall": 1},{"VoucherSmall":
     *         1},{"VoucherSmall": 1},{"VoucherSmall": 1},{"VoucherSmall": 1},{"VoucherBig": 1}]},"code": 200,"info":
     *         {"accountId": "81568478941117","contractId": "0","phone": "18616700069","invitationCode":
     *         "tgtkjgh","protocol": "","nickname": "UID:1117"},"msg": "成功"}
     * @errors {"code":400,"msg":"error"}
     */
    public function checkinAction()
    {
        $params = [
            'userId'    => $this->user->userId,
            'withBonus' => $this->_request->get('withBonus'),
        ];
        $rules  = [];
        if (InputValidation::validateParams($params, $rules) == false) {
            return $this->returnError(InputValidation::$errorMsg, InputValidation::$errorCode);
        }

        $checkinBook         = \Lib\Services\CheckinBook::getInstance(\Prj\BaseCtrl::getRpcDefault('CheckinBook'));
        $this->loger->target = $tmp = $this->_request->get('dowhat');
        try {
            switch ($tmp) {
                case 'checkin':
                    $ret                   = $checkinBook->doCheckIn($params['userId'], $params['withBonus']);
                    $ret['checkBonusbook'] = $ret['data'];
                    //uset($ret['data'])
                    foreach ($ret as $k => $v) {
                        $this->_view->assign($k, $v);
                    }

                    //发送消息
                    try {
                        $amount = 0;
                        $_user  = \Prj\Data\User::getCopy($params['userId']);
                        $_user->load();

                        $dbCheckin = \Prj\Data\Checkin::getCopy($params['userId'], \Sooh\Base\Time::getInstance()->ymd);
                        $dbCheckin->load();
                        if ($dbCheckin->exists()) {
                            $dbBonus = $dbCheckin->getField('bonus');
                            if (is_string($dbBonus) && !empty($dbBonus)) {
                                $dbBonus = json_decode($dbBonus, true);
                            }
                            $amount = $dbBonus['RedPacketForCheckin'] ? : 0;
                        }

                        $this->loger->sarg1 = sprintf('%0.2f', $amount / 100);
                        $this->loger->sarg2 = $_user->getField('phone');
                        $this->loger->sarg3 = $params['userId'];
//                        $ret                = \Prj\ReadConf::run(
//                            [
//                                'event'      => 'red_sign_packet',
//                                'brand'      => \Prj\Message\Message::MSG_BRAND,
//                                'sign_money' => sprintf('%.02f', $amount / 100)
//                            ],
//                            [
//                                'phone'  => $_user->getField('phone'),
//                                'userId' => $params['userId']
//                            ]
//                        );
                    } catch (\Exception $e) {
                        $this->loger->target = $params['userId'];
                    }

                    break;
                case 'reset':
                    break;
                default:
                    //return $this->returnError('faile');
                    return $this->returnError(\Prj\Lang\Broker::getMsg('system.server_busy'));
            }
            return $this->returnOK('success');
        } catch (\Exception $e) {
            return $this->returnError($e->getMessage(), $e->getCode());
        }
    }

    /**
     * 仅获取签到奖励列表
     * @input integer withBonus 是否获取签到奖励列表，1表示获取
     * //返回客户端旧标识信息
     * @output {"account": {"accountId": "81568478941117","nickname": "UID:****117"},"checkinBook": {"ymd":
     *         "20151119","checked": [{"ymd": 20151118,"bonus": {"RedPacketForCheckin": 156,"ShopPointForCheckin":
     *         10}}],"todaychked": 0,"bonusList": [{"VoucherSmall": 1},{"VoucherSmall": 1},{"VoucherSmall":
     *         1},{"VoucherSmall": 1},{"VoucherSmall": 1},{"VoucherSmall": 1},{"VoucherBig": 1}]},"code": 200,"info":
     *         {"accountId": "81568478941117","contractId": "0","phone": "18616700069","invitationCode":
     *         "tgtkjgh","protocol": "","nickname": "UID:1117"},"msg": "成功"}
     * //返回客户端新标识信息
     * @output {"account": {"accountId": "81568478941117","nickname": "UID:****117"},"checkBonusList": {"ymd":
     *         "20151119","checked": [{"ymd": 20151118,"bonus": {"RedPacketForCheckin": 156,"ShopPointForCheckin":
     *         10}}],"todaychked": 0,"bonusList": [{"VoucherSmall": 1},{"VoucherSmall": 1},{"VoucherSmall":
     *         1},{"VoucherSmall": 1},{"VoucherSmall": 1},{"VoucherSmall": 1},{"VoucherBig": 1}]},"code": 200,"info":
     *         {"accountId": "81568478941117","contractId": "0","phone": "18616700069","invitationCode":
     *         "tgtkjgh","protocol": "","nickname": "UID:1117"},"msg": "成功"}
     * @errors {"code":400,"msg":"****"}
     */
    public function getBonusListAction()
    {
        $withBonus = $this->_request->get('withBonus');
        if ($withBonus != 1) {
            $withBonus = false;
        }
        $checkinBook         = \Lib\Services\CheckinBook::getInstance(\Prj\BaseCtrl::getRpcDefault('CheckinBook'));
        $this->loger->target = 'getBonusList';
        try {
            $ret                   = $checkinBook->doGetTodayStatus($withBonus, $this->user->userId);
            $ret['checkBonusList'] = $ret['data'];
            //uset($ret['data'])
            foreach ($ret as $k => $v) {
                $this->_view->assign($k, $v);
            }
            return $this->returnOK('success');
        } catch (\Exception $e) {
            return $this->returnError($e->getMessage(), $e->getCode());
        }
    }
}
