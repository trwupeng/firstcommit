<?php
/**
 * Created by PhpStorm.
 * User: Ramb
 * Date: 2016/4/25
 * Time: 21:16
 */

class SpiderController extends \Prj\ManagerCtrl {

    /**
     * 查询
     */
    public function indexAction () {
        $phone = $this->_request->get('phone');
        $ticket = $this->_request->get('ticket');
        if(!empty($phone)) {
            $where['phone'] = $phone;
        }
        if(!empty($ticket)){
            $where['ticketSerialNo'] = $ticket;
        }
        $fieldsMap = [
            'ticketSerialNo' => ['电影票兑换码', null],
            'userId' => ['用户ID', null],
            'realname' => ['姓名', null],
            'phone' => ['手机号码', null],
            'createTime' => ['发放时间', null],
            'flagMsg' => ['是否提交短信', null],
        ];


        foreach($fieldsMap as $v) {
            $header[$v[0]] = $v[1];
        }
        reset($fieldsMap);
        $records = [];
        if(!empty($where)) {
            $db = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_p2prpt);
            $record = $db->getRecord('db_p2prpt.tb_activity_spider', '*', $where);
            $tmp = [];
            foreach($fieldsMap as $k => $v) {

                if($k == 'flagMsg') {
                    if ($record['flagGranted']) {
                        if($record[$k]){
                            $tmp[$k] = '是';
                        }else{
                            $tmp[$k] = '否';
                        }
                    }else {
                        $tmp[$k] = '';
                    }
                }else {
                    $tmp[$k] = $record[$k];
                }
            }
            $records[] = $tmp;
        }

        $this->_view->assign('records', $records);
        $this->_view->assign('tableHeaders', $header);
        $this->_view->assign('phone', $phone);
        $this->_view->assign('ticket', $ticket);
    }


    /**
     * 补仓
     */

    public function fillupAction () {
        $tickets = $this->_request->get('tickets');
        if(empty($tickets)){
            return;
        }
        $match = [];
        preg_match_all('/([0-9a-zA-z]+)/', $tickets, $match);
        $match = $match[0];
        if(empty($match)){
            return;
        }elseif(sizeof($match)>1000){
            $this->_view->assign('errorTip', '每次最多提交1000个兑换码！');
            $this->_view->assign('tickets', $tickets);
            return;
        }

        $existsTickets = [];
        $addFailedTickets = [];
        $addSuccessTickets = [];

        $db_rpt = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_p2prpt);
        $nextUserId = $db_rpt->getOne('db_p2prpt.tb_activity_spider', 'max(userId)', ['flagMsg'=>0, 'flagGranted'=>0, 'CHAR_LENGTH(userId)['=>6]);
        if(!$nextUserId){
            $nextUserId = 1;
        }else{
            $nextUserId += 1;
        }
        foreach($match as $ticket) {
            try{
                \Sooh\DB\Broker::errorMarkSkip();
                $db_rpt->addRecord('db_p2prpt.tb_activity_spider', ['ticketSerialNo'=>$ticket, 'userId'=>$nextUserId]);
                $addSuccessTickets[] = $ticket;
            }catch(\ErrorException $e){
                if(\Sooh\DB\Broker::errorIs($e)){
                    $existsTickets[] = $ticket;
                }else{
                    $addFailedTickets[] = $ticket;
                }
            }
            $nextUserId++;
        }

        $this->_view->assign('existsTickets', $existsTickets);
        $this->_view->assign('addFailedTickets', $addFailedTickets);
        $this->_view->assign('addSuccessTickets', $addSuccessTickets);
        $this->_view->assign('tickets', $tickets);
    }


    /**
     * 尚未发送的兑换码
     */

    public function unusedAction (){
        $db_rpt = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_p2prpt);
        $records = $db_rpt->getCol('db_p2prpt.tb_activity_spider', 'ticketSerialNo', ['flagGranted'=>0]);
        $this->_view->assign('records', $records);
    }

    /**
     * 发放过的兑换码
     */

    public function usedAction(){
        $fieldsMap = [
            'ticketSerialNo' => ['电影票兑换码', null],
            'userId' => ['用户ID', null],
            'realname' => ['姓名', null],
            'phone' => ['手机号码', null],
            'createTime' => ['发放时间', null],
            'flagMsg' => ['是否提交短信', null],
        ];
        $db_rpt = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_p2prpt);
        $records = $db_rpt->getRecords('db_p2prpt.tb_activity_spider', array_keys($fieldsMap), ['flagGranted'=>1]);

        foreach($fieldsMap as $v) {
            $headers[$v[0]] = $v[1];
        }
        $this->_view->assign('records', $records);
        $this->_view->assign('headers', $headers);
    }

    /**
     *
     * 发票发短信
     */

    public function grantAction () {
        $phone = $this->_request->get('phone');
        $ticket = $this->_request->get('ticketSerialNo');
        $msg = $this->_request->get('msg');

        if(!empty($_POST)) {
            if(empty($phone) || empty($ticket) || empty($msg)) {
                return $this->returnError('数据填写不正确');
            }

            $db_rpt = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_p2prpt);
            $userInfo = $db_rpt->getRecord(\Rpt\Tbname::tb_user_final, 'userId,realname', ['phone'=>$phone]);
            if(empty($userInfo)) {
                return $this->returnError('未找到此用户，检查手机号码是否正确');
            }

            $ticketInfo = $db_rpt->getRecord('db_p2prpt.tb_activity_spider', 'userId, flagGranted', ['ticketSerialNo'=>$ticket]);
            if(empty($ticketInfo)) {
                return $this->returnError('此兑换码不存在');
            }
            if($ticketInfo['flagGranted'] == 1) {
                return $this->returnError('此兑换码已经发放过了');
            }

            try {
                \Sooh\DB\Broker::errorMarkSkip();
                $n = $db_rpt->updRecords('db_p2prpt.tb_activity_spider',
                    ['userId' => $userInfo['userId'], 'phone' => $phone, 'realname' => $userInfo['realname'], 'createTime' => date('Y-m-d H:i:s'), 'flagGranted' => 1],
                    ['flagGranted' => 0, 'flagMsg' => 0, 'ticketSerialNo' => $ticket]);
            }catch(\ErrorException $e) {
                if (\Sooh\DB\Broker::errorIs($e)){
                    return $this->returnError('此用户已经发过兑换码了');
                }else {
                    return $this->returnError($e->getMessage());
                }
            }
            if($n!==true){
                $result = $this->sendMsg($phone, $msg);
                if($result){
                    $db_rpt->updRecords('db_p2prpt.tb_activity_spider', ['flagMsg'=>1], ['ticketSerialNo'=>$ticket]);
                    return $this->returnOk('短信提交成功');
                }else{
                    $db_rpt->updRecords('db_p2prpt.tb_activity_spider',
                        ['userId'=>$ticketInfo['userId'], 'realname'=>'', 'phone'=>0, 'createTime'=>null, 'flagGranted'=>0],
                        ['ticketSerialNo'=>$ticket]);
                    return $this->returnError('短信发送失败');
                }

            }else{
                return $this->returnError('短信发送失败');
            }



        }

    }

    protected function sendMsg($phone, $msg) {
        try {
            \Lib\Services\SMS::getInstance()->sendNotice($phone, $msg);
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }
}












