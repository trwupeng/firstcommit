<?php

/**
 * 兑换码controller
 * @author simon.wang
 */
class ExchangecodeController extends \Prj\UserCtrl
{
    /*
    public function init()
    {
        parent::init();
        $userId='82342817002358';
        $sess = \Sooh\Base\Session\Data::getInstance();
        $sess->set('accountId',$userId);
    }
    */


    /**
     * 获取一个兑换码
     * @input string grpbatch 分组和批次的组合
     * @output {code: 200,excode:xxxxxxxxxxxxxxxxxx}
     * @output {code: 400,err:兑换码已经领取光了}
     */
    public function getcodeAction()
    {
        $serv = \Lib\Services\ExchangeCode::getInstance(\Prj\BaseCtrl::getRpcDefault('ExchangeCode'));
        $k    = $this->_request->get('grpbatch');
        if (strlen($k) < 8) {
            return $this->returnError(\Prj\Lang\Broker::getMsg('excode.group_batch_error'));
        }
        $batch = substr($k, -4, 4);
        $grp   = substr($k, 0, -4);


        $userId = \Sooh\Base\Session\Data::getInstance()->get('accountId');

        $ret = $serv->getUserCode($grp, $batch, $userId);

        if ($ret['code'] == 200) {
            $this->_view->assign('excode', $ret['excode']);
            return $this->returnOK();
        } else {
            return $this->returnError($ret['msg']);
        }
    }

    //	public function resetAction()
    //	{
    //		$userId = \Sooh\Base\Session\Data::getInstance()->get('accountId');
    //		$user = \Prj\Data\User::getCopy($userId);
    //		$user->load();
    //		$user->setField('exchangecodegrp', '_');
    //		$user->update();
    //		$this->returnOK();
    //	}
    //
    /**
     * 用户将兑奖码换奖品
     * @input string excode 兑奖码
     * @output {code: 200,excode:xxxxxxxxxxxxxxxxxx, bonus:[{itemName:bonusItem1,itemNum:num1},....]}
     * @output {code: 400,excode:xxxxxxxxxxxxxxxxxx, err:兑换码已经领取光了}
     */
    public function getbonusAction()
    {
        $excode = $this->_request->get('excode');
        $userId = \Sooh\Base\Session\Data::getInstance()->get('accountId');
        $user   = \Prj\Data\User::getCopy($userId);
        $user->load();
        //		error_log(">>>>>>>>>>>>>".$userId.' wants '.$excode);
        //用户使用兑奖码换得奖品
        $fetched = $user->getField('exchangecodegrp');
        $serv    = \Lib\Services\ExchangeCode::getInstance(\Prj\BaseCtrl::getRpcDefault('ExchangeCode'));
        $ret     = $serv->useCode($excode, $userId, $fetched, 'ordersId-todo');
        if ($ret['code'] != 200) {
            $this->_view->assign('status', $ret['status']);
            return $this->returnError($ret['err']);
        } elseif (empty($ret['bonus'])) {
            $this->_view->assign('status', $ret['status']);
            return $this->returnError(\Prj\Lang\Broker::getMsg('excode.excode_dberror'));
        }

        try {
            //			var_log($ret,"===========================get from excode center");
            //更新用户的奖品信息
            $grpid     = $ret['groupid'];
            $bonusTask = new \Prj\Items\ItemGiver($userId);
            foreach ($ret['bonus'] as $bonusItem => $bonusNum) {
                $bonusTask->add($bonusItem, $bonusNum);
            }
            $realGived = $bonusTask->give();//发放红包
            //			var_log($realGived,"===========================realgived");
            if ($realGived !== null) {
                $user->setField('exchangecodegrp', $fetched . $grpid . '_');
                $user->update();
                $bonusTask->onUserUpdated();
                $bonus = [];
                foreach ($realGived as $r) {
                    $bonus[] = ['itemName' => $r[0], 'itemNum' => $r[1]];
                }
                $this->_view->assign('excode', $excode);
                $this->_view->assign('bonus', $bonus);

                //发消息
                //				$msg_title = \Prj\Lang\Broker::getMsg('excode.excode_to_redpacket_title');
                //				$msg_str = \Prj\Lang\Broker::getMsg('excode.excode_to_redpacket_content');
                //				\Lib\Services\Message::getInstance()->add(0, $userId, 5, $msg_title , $msg_str, null, false);
                $itemname             = explode('\\', $bonus[0]['itemName']);
                $bonus[0]['itemName'] = array_pop($itemname);
                $bonus_str            = $bonusTask->bonusToDesc($bonus);
                //发送兑换消息
                $this->loger->sarg1 = json_encode([
                    [
                        'event'          => 'code_gift',
                        'brand'          => \Prj\Message\Message::MSG_BRAND,
                        'code_gift_name' => $bonus_str,
                    ],
                    ['phone' => $user->getField('phone'), 'userId' => $userId]
                ]);

                $this->returnOK(\Prj\Lang\Broker::getMsg('excode.excode_change_success'));
            } else {
                return $this->returnError($bonusTask->getLastError(), 508);
            }
        } catch (\Exception $ex) {
            error_log($ex->getMessage() . "\n" . $ex->getTraceAsString());
            $this->returnError(\Prj\Lang\Broker::getMsg('system.db_error'), 509);
        }

    }


}