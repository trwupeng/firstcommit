<?php
use Sooh\Base\Form\Item as form_def;
use Sooh\Base\Form\Options as options_def;
/**
 * 自定义 券发放
 * @author li.lianqi
 *
 */
class VouchergrantuserdefinedController extends \Prj\ManagerCtrl {

    public function init() {
        parent::init();
    }
    protected $fielsMap = [
        'phone'             => ['电话',70],
        'realname'          => ['姓名', 70],
        'voucherName'       => ['券名称', 80],
        'repeatN'           => ['手机号重复次数', 90],
        'flgVoucher'        => ['是否发放本金券', 90],
        'flgMsg'            => ['是否发送信息', 90],
        'msg'               => ['信息内容', 160],
        'sender'            => ['发送者', 100],
    ];
    protected $msgStatus = [
        -1                  => '提交失败',
        0                   => '用户不存在',
        1                   => '提交成功',
        -2                  => '用户不存在'
    ];
    protected $sendVoucherStatus=[
        -1                  =>'发送失败',
        0                   => '用户不存在',
        1                   =>'发送成功'
    ];

    const MAX_PHONE_COMMIT_NUM = 10000;

    protected $redPacket=['RedPacketOfUserDefined'=>'自定义红包'];
    public function indexAction (){
        
        $voucherTypesArr = [
            \Prj\Consts\Voucher::type_real => '红包',
        ];

        $db = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_p2prpt);
        
        $frm = \Sooh\Base\Form\Broker::getCopy('default')
            ->init(\Sooh\Base\Tools::uri(), 'post', \Sooh\Base\Form\Broker::type_s);
        $frm->addItem('taskId', form_def::factory('taskId', time().rand(10000,99999), form_def::hidden))
            ->addItem('phone', form_def::factory('手机号码', '', form_def::mulit, [], ['data-rule' => 'required;']))
            ->addItem('repeat', form_def::factory('重复手机号码',0, form_def::select)->initMore(new \Sooh\Base\Form\Options([0=>'去重', 1=>'不去重'])))
            ->addItem('msgType', form_def::factory('通知方式', 0, form_def::select)->initMore(new \Sooh\Base\Form\Options([0=>'请选择短信通道类型',1=>'站内信', 2=>'通知短信', 3=>'站内信和通知短信'])))
            ->addItem('desc', form_def::factory('红包名称', '', form_def::text, [], ['data-rule' => 'required;'])) // 红包的名称
            ->addItem('amount', form_def::factory('红包金额(元)', '', form_def::text, [], ['data-rule' => 'required;amount', 'data-rule-amount'=>'/^\d+(?:\.\d{1,2})?$/',]))
            ->addItem('minInvestment', form_def::factory('最低投资限额(元)', '', form_def::text, [], ['data-rule'=>'required;digits']))
            ->addItem('days', form_def::factory('有效天数', '', form_def::text, [], ['data-rule'=>'required;digits']))
            ->addItem('msg', form_def::factory('短信/站内信内容', '', form_def::mulit, [], ['data-rule' => 'required;']));
        
        $frm->fillValues();
        if ($frm->flgIsThisForm) {
            \Sooh\Base\Ini::getInstance()->viewRenderType('json');
            $fields = $frm->getFields();
            $taskId = $fields['taskId'];
            $msgType = $fields['msgType'];
            if($msgType==0){
                return $this->returnError('请选择短信通道类型');
            }elseif ($db->getRecordCount(\Rpt\Tbname::tb_voucher_grant, ['taskId'=>$taskId])) {
                return $this->returnError('为防止重复提交，点击右上角刷新当前页面按钮重试');
            }
            $phones = $fields['phone'];
            if (empty($phones)) {
                return $this->returnError(\Prj\Lang\Broker::getMsg('vouchergrant.phone_not_filled'));
            }
            preg_match_all('/([0-9]+)/', $phones, $match);
            $phones=$match[0];
            if (empty($phones)) {
                return $this->returnError(\Prj\Lang\Broker::getMsg('vouchergrant.phone_number_is_not_valid'));
            }elseif(sizeof($phones) > self::MAX_PHONE_COMMIT_NUM) {
                return $this->returnError('最大提交号码数量'.self::MAX_PHONE_COMMIT_NUM.'个');
            }
            
            $repeat = array_count_values($phones);
            $phones = array_keys($repeat);
            $userIds = \Prj\Data\User::loopFindRecordsByFields(array('phone'=>$phones), null, 'userId, phone, nickname');
            $users = [];
            if(!empty($userIds)) {
                foreach($userIds as $v) {
                    $phone = $v['phone'];
                    unset($v['phone']);
                    $users[$phone]= $v;
                }
                $userIds = null;
            }

            $exsitsUserPhones = array_keys($users);
            $notExsitsUserPhones = array_diff($phones, $exsitsUserPhones);
            // 发券， 发短信 通知短信

            $dtCreate= date('Y-m-d H:i:s');
            $num = 1;
            if (!empty($exsitsUserPhones)) {
                    foreach($exsitsUserPhones as $phone) {
                        $record=[];
                        $record['taskId']           = $taskId;
                        $record['userId']           = $users[$phone]['userId'];
                        $record['realname']         = $users[$phone]['nickname'];
                        $record['phone']            = $phone;
                        $record['msgType']          = $msgType;
                        $record['voucherName']      = 'RedPacketOfUserDefined';
                        $record['amount'] = $fields['amount']*100;
                        $record['timeCreate']       = $dtCreate;
                        $record['sender']           = $this->manager->getField('loginName');
                        $record['msg']              = $fields['msg'];
                        $repeatN = $repeat[$phone];
                        if(!$fields['repeat']) {
                            $repeatN = 1;
                            $record['repeatN'] = $repeat[$phone];
                        }else {
                            $record['repeatN'] = 1;
                        }
                        while($repeatN>0) {
                            $record['orderNumber'] = $num++;
                            $itemGive = new\Prj\Items\ItemGiver($users[$phone]['userId']);
                            $itemGive->add('RedPacketOfUserDefined', 1);

                            $args = [
                                'userId' => $users[$phone]['userId'],
                                'amount' => $fields['amount'],
                                'content' => $fields['msg'],
                                'desc' => $fields['desc'],
                                'minInvestment' => $fields['minInvestment'],
                                'days' => $fields['days'],
                            ];
                            $rs = $itemGive->give($args);

                            if (is_array($rs)) {
                                $user_obj = \Prj\Data\User::getCopy($users[$phone]['userId']);
                                $user_obj->update();
                                $record['flgVoucher'] = 1;

                                if($msgType==1 || $msgType==3) {
                                    $bonusItemClass = '\\Prj\\Items\\' . 'RedPacketOfUserDefined';
                                    $voucherItem = new $bonusItemClass($args);
                                    $rMsg = $voucherItem->sendMsg(); // 站内信
                                }
                                if($msgType == 2 || $msgType ==3) {
                                    $rSms = $this->sendMsg($phone, $fields['msg']); // 通知短信
                                }

                                if ($rMsg || $rSms) {
                                    $record['flgMsg'] = 1;
                                } else {
                                    $record['flgMsg'] = -1;
                                }
                            } else {
                                $record['flgVoucher'] = -1;
                                $record['flgMsg'] = -1;
                            }
                            $db->addRecord(\Rpt\Tbname::tb_voucher_grant, $record);

                            $repeatN--;
                        }
                    }
            }
            if (!empty($notExsitsUserPhones)) {
                foreach($notExsitsUserPhones as $notExitsUserPhone){
                    $record = [
                        'taskId'                    => $taskId,
                        'orderNumber'               => $num++,
                        'phone'                     => $notExitsUserPhone,
                        'voucherName'               => 'RedPacketOfUserDefined',
                        'repeatN'                   =>$repeat[$notExitsUserPhone],
                        'flgMsg'                    =>0,
                        'flgVoucher'                =>0,
                        'timeCreate'                =>$dtCreate,
                        'sender'                    => $this->manager->getField('loginName'),
                    ];

                    $db->addRecord(\Rpt\Tbname::tb_voucher_grant, $record);
                }
            }

            $header = [];
            foreach($this->fielsMap  as $k => $v) {
                $header[$v[0]] = $v[1];
            }
//            $this->_view->assign('headers', $header);
//            $this->_view->assign('records', $records);
//            $where = ['taskId'=>$taskId];
            $this->_view->assign('taskId', $taskId);
//            $this->returnOK('OK');
        }
    }


    protected function sendMsg($phone, $msg) {
        try {
            \Lib\Services\SMS::getInstance()->sendNotice($phone, $msg);
        } catch (\Sooh\Base\ErrException $e) {
            return false;
        }
        return true;
    }


//'phone' => ['电话',70],
//'realname' => ['姓名', 70],
//'voucherName' => ['券名称', 80],
//'repeatN' => ['手机号重复次数', 90],
//'flgVoucher' => ['是否发放本金券', 90],
//'flgMsg' => ['是否发送短信', 90],
//'msg' => ['短信内容', 160]

    protected $pageSizeEnum = [50, 100, 150];
    public function dialogAction() {

        $taskId = $this->_request->get('taskId');
        $isExcel = $this->_request->get('__EXCEL__');

        $pageid = $this->_request->get('pageId', 1) - 0;
        $pagesize = $this->_request->get('pageSize', current($this->pageSizeEnum)) - 0;
        $pager = new \Sooh\DB\Pager($pagesize, $this->pageSizeEnum, false);
        $formEdit = \Sooh\Base\Form\Broker::getCopy('default')
            ->init(\Sooh\Base\Tools::uri(), 'get', \Sooh\Base\Form\Broker::type_s);
        $formEdit->addItem('pageid', $pageid)
            ->addItem('pagesize', $pager->page_size);

        $db = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_p2prpt);
        $fields = 'phone,userId,realname,voucherName,flgVoucher,flgMsg,msg,repeatN,timeCreate,sender';
        if($isExcel){
            $records = $db->getRecords(\Rpt\Tbname::tb_voucher_grant, $fields,['taskId'=>$taskId]);
        }else {
            $count = $db->getRecordCount(\Rpt\Tbname::tb_voucher_grant, ['taskId'=>$taskId]);
            $pager->init($count, $pageid);
            $records = $db->getRecords(\Rpt\Tbname::tb_voucher_grant, $fields,['taskId'=>$taskId], null, $pager->page_size, $pager->rsFrom());
        }
        if(!empty($records)) {
            foreach($records as $k => $v) {
                $tmp['phone'] = $v['phone'];
                $tmp['realname'] = $v['realname'];
                $tmp['voucherName'] = $this->redPacket[$v['voucherName']];
                $tmp['repeatN']= $v['repeatN'];
                $tmp['flgVoucher'] = $this->sendVoucherStatus[$v['flgVoucher']];
                $tmp['flgMsg'] = $this->msgStatus[$v['flgMsg']];
                $tmp['msg'] = $v['msg'];
                $manager = \Prj\Data\Manager::getCopy();
                $sendName = $manager->db()->getOne($manager->tbname(), 'nickname', ['loginName'=>$v['sender']]);
                $manager->free();
                $tmp['sender'] = $sendName;
                $records[$k] = $tmp;
            }
        }
        $header = [];
        foreach($this->fielsMap  as $k => $v) {
            $header[$v[0]] = $v[1];
        }
        if($isExcel) {
            return $this->downExcel($records, array_keys($header));
        }

        $this->_view->assign('headers', $header);
        $this->_view->assign('records', $records);
        $this->_view->assign('taskId', $taskId);
        $this->_view->assign('pager',$pager);
    }
    
    
}