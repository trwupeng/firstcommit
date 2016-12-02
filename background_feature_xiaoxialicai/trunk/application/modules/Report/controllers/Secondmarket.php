<?php
/**
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/4/6 0006
 * Time: 下午 2:34
 */

use Sooh\Base\Form\Item as form_def;
use Sooh\Base\Form\Options as options_def;

class SecondmarketController extends \Prj\ManagerCtrl{
    public function init(){
        parent::init();
    }

    protected $pageSizeEnum = [50,100, 150, 300, 500, 1000];
    protected $marketType = ['短信', '电话'];
    protected $hesitateType =['注册未绑卡', '绑卡未购买'];
    protected $callStatus = ['未联系', '未接通', '已联系'];

    protected $headers = [
        'userId'=>['用户ID', null],
        'phone'=>['手机号', null],
        'realname'=>['姓名', null],
        'ymdReg'=>['注册日期', null],
        'ymdBindCard'=>['认证绑卡日期', null],
        'ymdFirstBuy'=>['首投日期', null],
        'hesitateDaysBind'=>['认证绑卡犹豫期（天）', null],
        'hesitateDaysFistBuy'=>['首投犹豫期（天）', null],
        'sendMsgTimes'=>['发送短信次数', null],
        'ymdLastSendMsg'=>['最后一次发送短信', null],
        'callTimes'=>['拨打电话次数', null],
        'YmdLastCall'=>['最后一次拨打日期', null],
    ];

    public function indexAction() {
        $saveAsExcel = $this->_request->get('__EXCEL__');

        $pageid = $this->_request->get('pageId', 1)-0;
        $pagesize = $this->_request->get('pageSize',current($this->pageSizeEnum))-0;
        $pager = new \Sooh\DB\Pager($pagesize,$this->pageSizeEnum,false);
        $dt = \Sooh\Base\Time::getInstance();
        $ymdDefault = date('Y-m-d', $dt->timestamp(-4));
        $form = \Sooh\Base\Form\Broker::getCopy('default')
            ->init(\Sooh\Base\Tools::uri(),'get',\Sooh\Base\Form\Broker::type_s);
        $form->addItem('hesitateType', form_def::factory('营销类型', 0, form_def::select, $this->hesitateType))
            ->addItem('ymd', form_def::factory('日期',$ymdDefault , form_def::datepicker))
            ->addItem('isOk', form_def::factory('选项', '', form_def::radio, [1=>'全部&nbsp;&nbsp;', 0=>'仍未绑卡(购买)&nbsp;&nbsp;']))
            ->addItem('userId', form_def::factory('用户ID', '', form_def::text))
            ->addItem('phone', form_def::factory('手机号', '', form_def::text,[], ['data-rule' => 'digits']))
            ->addItem('pageid', $pageid)
            ->addItem('pagesize', $pager->page_size);

        $form->fillValues();
        $fields = $form->getFields();

        if($saveAsExcel) {
            $fields = \Prj\Misc\View::decodePkey($this->_request->get('where'));
        }

        foreach($this->headers as $r){
            $headers[$r[0]] = $r[1];
        }
        $db = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_p2prpt);

        if(!empty($fields)) {
            $where['hesitateType'] = $fields['hesitateType'];
            if(empty($fields['userId']) && empty($fields['phone'])) {
                $where['ymd'] = date('Ymd', strtotime($fields['ymd']));
                $users = $db->getCol(\Rpt\Tbname::tb_secondmarket_list, 'userId', $where);
            }else {
                if(!empty($fields['phone'])) {
                    $users = $db->getOne(\Rpt\Tbname::tb_user_final, 'userId', ['phone'=>$fields['phone']]);
                }else {
                    $users = $fields['userId'];
                }
                $where['userId'] = $users;
                $users = $db->getOne(\Rpt\Tbname::tb_secondmarket_list, 'userId', $where);
            }
            if(!empty($users)) {
                $tmpWhere['userId'] = $users;
                if(!$fields['isOk']) {
                    if($where['hesitateType'] == 0){
                        $tmpWhere['ymdBindCard'] = 0;
                    }elseif($tmpWhere['hesitateType'] ==1) {
                        $tmpWhere['ymdFirstBuy'] = 0;
                    }
                }
                $count = $db->getRecordCount(\Rpt\Tbname::tb_user_final, $tmpWhere);
                $pager->init($count, $pageid);

                if($saveAsExcel) {
                    $records = $db->getRecords(\Rpt\Tbname::tb_user_final, 'userId,phone,ymdReg,realname,ymdBindCard,ymdFirstBuy', $tmpWhere);
                    return $this->downExcel($this->recordsTrans($records), array_keys($headers));
                }

                $records = $db->getRecords(\Rpt\Tbname::tb_user_final, 'userId,phone,ymdReg,realname,ymdBindCard,ymdFirstBuy', $tmpWhere, null, $pagesize, $pager->rsFrom());
                $records = $this->recordsTrans($records, $fields['hesitateType']);
            }
        }

        $this->_view->assign('records', $records);
        $this->_view->assign('headers', $headers);
        $this->_view->assign('pager', $pager);
        $this->_view->assign('where', \Prj\Misc\View::encodePkey($fields));
    }

    protected function recordsTrans ($records, $hesitateType = null) {
        $dt = \Sooh\Base\Time::getInstance();
        $db = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_p2prpt);
        $arr = array_combine(array_keys($this->headers), array_fill(0, sizeof($this->headers), ''));
        foreach($records as $k => $r) {
            $tmp = $arr;
            $tmp['userId'] = $r['userId'];
            $tmp['phone'] = $r['phone'];
            $tmp['realname'] = $r['realname'];
            $tmp['ymdReg'] = date('Y-m-d', strtotime($r['ymdReg']));


            if($r['ymdBindCard']) {
                $tmp['ymdBindCard'] = empty($r['ymdBindCard'])?'':date('Y-m-d', strtotime($r['ymdBindCard']));
                $tmp['hesitateDaysBind'] = floor((strtotime($r['ymdBindCard']) - strtotime($r['ymdReg']))/86400);
            }else {
                $tmp['hesitateDaysBind'] = floor(($dt->timestamp() - strtotime($r['ymdReg']))/86400);
            }
            if($r['ymdFirstBuy']) {
                $tmp['ymdFirstBuy'] = empty($r['ymdBindCard'])?'':date('Y-m-d', strtotime($r['ymdFirstBuy']));
                $tmp['hesitateDaysFistBuy'] = floor((strtotime($r['ymdFirstBuy']) - strtotime($r['ymdBindCard']))/86400);
            }else {
                if($r['ymdBindCard']){
                    $tmp['hesitateDaysFistBuy'] = floor(($dt->timestamp() - strtotime($r['ymdBindCard']))/86400);
                }else{
                    $tmp['hesitateDaysFistBuy'] = floor(($dt->timestamp() - strtotime($r['ymdReg']))/86400);
                }
            }

            $tmp['sendMsgTimes'] = $db->getRecordCount(\Rpt\Tbname::tb_secondmarket, ['userId'=>$r['userId'], 'marketType'=>0]);
            if($tmp['sendMsgTimes']) {
                $ymdHis = $db->getRecord(\Rpt\Tbname::tb_secondmarket, 'ymd, his', ['userId'=>$r['userId'], 'marketType'=>0], 'rsort ymd rsort his');
                $tmp['ymdLastSendMsg'] = date('Y-m-d H:i:s', strtotime($ymdHis['ymd'].sprintf("%06d", $ymdHis['his'])));
            }

            $tmp['callTimes'] = $db->getRecordCount(\Rpt\Tbname::tb_secondmarket, ['userId'=>$r['userId'], 'marketType'=>1]);
            if($tmp['callTimes']) {
                $ymdHis = $db->getRecord(\Rpt\Tbname::tb_secondmarket, 'ymd, his', ['userId'=>$r['userId'], 'marketType'=>1], 'rsort ymd rsort his');
                $tmp['YmdLastCall'] = date('Y-m-d H:i:s', strtotime($ymdHis['ymd'].sprintf("%06d", $ymdHis['his'])));
            }
            if($hesitateType !== null){
                $tmp['_pkey_'] = \Prj\Misc\View::encodePkey(['userId'=>$r['userId'], 'hesitateType'=>$hesitateType]);
            }
            $records[$k] = $tmp;
        }
        return $records;
    }

    public function sendmsgAction() {
        $pkey                          = \Prj\Misc\View::decodePkey($this->_request->get('_pkey_val_'));
        $content = $this->content1;
        $db = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_p2prpt);
        if(empty($pkey)){
            return $this->returnError('参数错误');
        }else{
            $where['hesitateType'] = $pkey['hesitateType'];
            if(empty($pkey['userId']) && empty($pkey['phone'])) {
                $where['ymd'] = date('Ymd', strtotime($pkey['ymd']));
                $users = $db->getCol(\Rpt\Tbname::tb_secondmarket_list, 'userId', $where);
            }else {
                if(!empty($pkey['phone'])) {
                    $users = $db->getOne(\Rpt\Tbname::tb_user_final, 'userId', ['phone'=>$pkey['phone']]);
                }else {
                    $users = $pkey['userId'];
                }
                $where['userId'] = $users;
                $users = $db->getOne(\Rpt\Tbname::tb_secondmarket_list, 'userId', $where);
            }

            $tmpWhere['userId'] = $users;
            if(!$pkey['isOk']) {
                if($where['hesitateType'] == 0){
                    $tmpWhere['ymdBindCard'] = 0;
                }elseif($tmpWhere['hesitateType'] ==1) {
                    $tmpWhere['ymdFirstBuy'] = 0;
                }
            }

            $rs  = $db->getPair(\Rpt\Tbname::tb_user_final, 'userId', 'phone', $tmpWhere);
            if(empty($rs)){
                return $this->returnError('没有可以发送的对象');
            }
        }
        $frm                            = \Sooh\Base\Form\Broker::getCopy('default')
            ->init(\Sooh\Base\Tools::uri(), 'get', empty($where) ? \Sooh\Base\Form\Broker::type_c : \Sooh\Base\Form\Broker::type_u);
        $frm->addItem('num', form_def::factory('短信条数', sizeof($rs), form_def::constval, [], ['data-rule' => 'required']))
            ->addItem('content', form_def::factory('短信内容', $content, form_def::mulit, [], ['data-rule' => 'required']))
            ->addItem('_pkey_val_', $this->_request->get('_pkey_val_'));
        $frm->fillValues();
        //表单提交

        if ($frm->flgIsThisForm)
        {
            $dt = \Sooh\Base\Time::getInstance();

            $fields = $frm->getFields();
            $failed = [];
            $success = [];
            foreach($rs as  $uid => $phone){
                $result = $this->sendMsg($phone, $fields['content']);
                if($result) {
                    $tmp = [
                        'ymd' => $dt->YmdFull,
                        'his' => $dt->his,
                        'userId' => $uid,
                        'hesitateType' => $pkey['hesitateType'],
                        'marketType' => 0,
                        'msg' => $fields['content'],
                    ];
                    $db->addRecord(\Rpt\Tbname::tb_secondmarket, $tmp);
                    $success[] = $uid;
                    var_log('send sms success, phone:' .$phone .' content:'.$fields['content']);
                }else {
                    $failed[] = $uid;
                }
            }
            return $this->returnOK('成功了'.count($success).'条,失败了'.count($failed).'条!');
        }else{

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


    public function callAction () {
        $pkey = \Prj\Misc\View::decodePkey($this->_request->get('_pkey_val_'));
        $db = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_p2prpt);
        $userId = $pkey['userId'];
        $historyRecords = $db->getRecords(\Rpt\Tbname::tb_secondmarket, 'ymd,his,msg,note,marketType,hesitateType,callStatus,bonus', ['userId'=>$userId]);
        $history= '';
        if(!empty($historyRecords)) {
            foreach($historyRecords as $r) {
                if($r['marketType'] == 0) {
                    $history .= '短信时间：'. date('Y-m-d H:i:s', strtotime($r['ymd'].sprintf('%06d', $r['his']))).' 营销类型：'.$this->hesitateType[$r['hesitateType']]."\n";
                    $history .= '      短信内容：'.$r['msg']."\n";
                }elseif($r['marketType'] == 1){
                    $history .= '电话时间：'.date('Y-m-d H:i:s', strtotime($r['ymd'].sprintf('%06d', $r['his']))). '营销类型：'.$this->hesitateType[$r['hesitateType']]."\n";
                    $history .= '      激励:'.$r['bonus']."\n";
                    $history .= '      备注：'.$r['note']."\n";
                }
            }
        }

        $formEdit = \Sooh\Base\Form\Broker::getCopy('default')
            ->init(\Sooh\Base\Tools::uri(), 'get', \Sooh\Base\Form\Broker::type_u);
        $formEdit
            ->addItem('userId', form_def::factory('用户ID', $userId, form_def::constval))
            ->addItem('msgHistory', form_def::factory('历史记录', $history, form_def::mulit))
            ->addItem('hesitateType', form_def::factory('营销类型', '', form_def::select, $this->hesitateType))
            ->addItem('ymdCall', form_def::factory('拨打时间',  date('YmdHis'), 'timepicker', [], ['data-rule' => 'required']))
            ->addItem('callStatus', form_def::factory('拨打结果', 0, form_def::select, $this->callStatus))
            ->addItem('note', form_def::factory('拨打备注', '', form_def::text))
            ->addItem('bonus', form_def::factory('激励', '', form_def::text));
        $formEdit->fillValues();
        $fields = $formEdit->getFields();
        if($formEdit->flgIsThisForm) {
            $tmp['userId'] = $fields['userId'];
            $tmp['marketType'] = 1;
            $tmp['hesitateType'] = $fields['hesitateType'];
            $tmp['ymd'] = date('Ymd', strtotime($fields['ymdCall']));
            $tmp['his'] = date('His', strtotime($fields['ymdCall']))-0;
            $tmp['callStatus'] = $fields['callStatus'];
            $tmp['note'] = $fields['note'];
            $tmp['bonus'] = $fields['bonus'];
            $db->addRecord(\Rpt\Tbname::tb_secondmarket, $tmp);
            $this->returnOk('保存成功');
//            $this->closeAndReloadPage('index');
        }
//        $this->returnOk('保存成功');
        $this->_view->assign('history', $history);

    }
}