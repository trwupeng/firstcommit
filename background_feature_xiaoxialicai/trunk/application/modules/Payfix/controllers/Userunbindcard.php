<?php
use Sooh\Base\Form\Item as form_def;
use Prj\Data\User as User;
use Prj\Consts\OrderStatus as OrderStatus;

/**
 * Created by PhpStorm.
 * User: tang.gaohang
 * Date: 2016/4/27
 * Time: 21:38
 */
class UserunbindcardController extends \Prj\ManagerCtrl
{

    public function unbindcardAction()
    {
        $where                          = \Prj\Misc\View::decodePkey($this->_request->get('_pkey_val_'));
        $type                           = $this->_request->get('_type');
        $frm                            = \Sooh\Base\Form\Broker::getCopy('default')
            ->init(\Sooh\Base\Tools::uri(), 'get', empty($where) ? \Sooh\Base\Form\Broker::type_c : \Sooh\Base\Form\Broker::type_u);

        $frm->addItem('uid', form_def::factory('用户ID/手机号', '', form_def::text, [], ['data-rule' => 'required,length[~15]']))
            ->addItem('idCard', form_def::factory('身份证号', '', form_def::text, [], ['data-rule' => 'required,length[~20]']))
            ->addItem('_type', $type)
            ->addItem('_pkey_val_', $this->_request->get('_pkey_val_'));

        //todo 构造表单数据

        $frm->fillValues();
        //表单提交
        if ($frm->flgIsThisForm)
        {
            $fields = $frm->getFields();
            var_log($fields,'fields >>> ');
            $uid = $fields['uid'];
            if(empty($uid))return $this->returnWarn('查无此用户');
            if(strlen($uid)==11){
                $user = \Prj\Data\User::getByPhone($uid);
                if(empty($user))return $this->returnWarn('查无此用户');
            }else{
                $user = \Prj\Data\User::getCopy($uid);
                $user->load();
            }
            if(!$user->exists())return $this->returnWarn('查无此用户');
            if($fields['idCard']!=$user->getField('idCard'))return $this->returnWarn('不匹配的身份证号');
            $userDump = $user->dump();
            $userId = $userDump['userId'];
            $userInfo = [
                '用户ID'=>$userId,
                '手机号'=>$userDump['phone'],
                '用户名'=>\Prj\IdCard::hideName($userDump['nickname']),
                '首次绑卡日期'=>$userDump['ymdBindcard']?date('Y-m-d',strtotime($userDump['ymdBindcard'])):'',
                '身份证号'=>\Prj\IdCard::hideId($userDump['idCard']),
                '余额(元)'=>$userDump['wallet']/100
            ];
            $this->_view->assign('userInfo',$userInfo);

            $cardList = \Prj\Data\BankCard::getList($userId , ['statusCode'=>\Prj\Consts\BankCard::enabled]);
            array_walk($cardList,function(&$v,$k){
                $v['_pkey_val_'] = \Prj\Misc\View::encodePkey([
                    'userId'=>$v['userId'],
                    'orderId'=>$v['orderId']
                ]);
                $v['bankCard'] = \Prj\IdCard::hideId($v['bankCard']);
                $v['bankId'] = \Prj\Consts\Banks::$enums[$v['bankId']][0]?\Prj\Consts\Banks::$enums[$v['bankId']][0]:$v['bankId'];
            });
            $this->_view->assign('cardList',$cardList);

            //审核通过
            if ($type == 'check') {

            }
            $where = \Prj\Misc\View::decodePkey($_REQUEST['_pkey_val_']);
            if (!empty($where)) {
                $frm->switchType(\Sooh\Base\Form\Broker::type_u);
            } else {
                $frm->switchType(\Sooh\Base\Form\Broker::type_c);
            }
            $op = '';
            try {
                $fields = $frm->getFields();
                try{
                    //todo 字段过滤
                }catch (\ErrorException $e){
                    return $this->returnWarn($e->getMessage());
                }

                if ($frm->type() == \Sooh\Base\Form\Broker::type_c) //add
                {
                    $op                   = "新增";
                    //todo 插入数据库
                } else { // update
                    $op   = '更新';
                    //todo 更新数据库

                }
            } catch (\ErrorException $e) {
                return $this->returnWarn($op . '失败：冲突，相关记录已经存在？');
            }


        }

        if ($frm->type() == \Sooh\Base\Form\Broker::type_u) //update show
        {
            //todo 字段展示 设置item的value
            /*
            $??? = \Prj\Data\???::getCopy($where['id']);
            $???->load();
            $arr = $???->dump();
            foreach($frm->items as $k=>$v){
                if(array_key_exists($k,$arr))$frm->items[$k]->value = $arr[$k];
            }
            */
        }

        //var_dump($fields);
        //die();
        $this->_view->assign('FormOp', $op = '添加商品');
        $this->_view->assign('type', $type);
        $this->_view->assign('_pkey_val_', $this->_request->get('_pkey_val_'));
    }

    public function unbindAction(){
        $where = \Prj\Misc\View::decodePkey($this->_request->get('_pkey_val_'));
        var_log($where,'where >>> ');
        if(empty($where))return $this->returnError('参数错误');
        $userId = $where['userId'];
        $user = \Prj\Data\User::getCopy($userId);
        $user->load();
        $bankcardId = $where['orderId'];
        $bankcard = \Prj\Data\BankCard::getCopy($bankcardId);
        $bankcard->load();
        $idCardSN = $bankcard->getField('idCardSN');
        if(!$user->exists() || !$bankcard->exists())return $this->returnError('参数错误');
        if($user->getField('wallet')>0)return $this->returnError('该账户余额不为0,无法进行解绑操作');
        $data = [
            'SN'=>$where['orderId'],
            'userId'=>$userId,
            'realname'=>$user->getField('nickname'),
            'idType'=>$bankcard->getField('idCardType'),
            'idCode'=>$idCardSN,
            'bankId'=>$bankcard->getField('bankId'),
            'bankCard'=>$bankcard->getField('bankCard'),
            'phone'=>$bankcard->getField('phone'),
            'userIP'=>$_SERVER['REMOTE_ADDR'],
        ];

        //todo 通知网关
        $rpc = \Sooh\Base\Ini::getInstance()->get('noGW') ? self::getRpcDefault('PayGWCmd') : \Sooh\Base\Rpc\Broker::factory('PayGWCmd');
        $sys = \Lib\Services\PayGWCmd::getInstance($rpc);
        try{
            $ret = call_user_func_array([$sys, 'unbindBankCard'], $data);
        }catch (\ErrorException $e){
            $code = $e->getCode();
            if($code==400){
                //todo 网关未响应
                error_log('网关未响应#'.$where['orderId']);
                return $this->returnError($e->getMessage());
            }elseif($code == 500){
                return $this->returnError($e->getMessage());
            }else{
                return $this->returnError('gw_error');
            }
        }

        if($this->_request->get('type')=='first'){
            $this->_view->assign('ticket','abcdefg');
            return $this->returnOK('ok');
        }elseif($this->_request->get('type')=='second'){
            if($ret['code']==200){

                if($bankcard->getField('statusCode')==\Prj\Consts\BankCard::disabled){
                    return $this->returnOK('该卡已经解绑');
                }
                if($bankcard->getField('statusCode')!=\Prj\Consts\BankCard::enabled){
                    return $this->returnError('非法的卡状态');
                }

                //解绑银行卡
                try{
                    $bankcard->updStatus(\Prj\Consts\BankCard::disabled);
                    $bankcard->update();
                }catch (\ErrorException $e){
                    return $this->returnError($e->getMessage());
                }
                //删除支付密码
                try{
                    $tradePwd = $user->getField('tradePwd');
                    $salt = $user->getField('salt');
                    $user->setField('tradePwd','');
                    $user->setField('salt','');
                }catch (\ErrorException $e){
                    $bankcard->updStatus(\Prj\Consts\BankCard::enabled);
                    $bankcard->update();
                    return $this->returnError($e->getMessage());
                }
                //删除或者失效身份证信息
                $idCard = \Prj\Data\IdCard::getCopy($idCardSN);
                $idCard->load();
                if($idCard->exists()){
                    $idCardDump = $idCard->dump();
                    if(isset($idCardDump['statusCode'])){
                        //新版身份证表
                        $idCard->setField('statusCode',-1);
                        try{
                            $idCard->update();
                        }catch (\ErrorException $e){
                            $bankcard->updStatus(\Prj\Consts\BankCard::enabled);
                            $bankcard->update();
                            $user->setField('tradePwd',$tradePwd);
                            $user->setField('salt',$salt);
                            $user->update();
                            return $this->returnError($e->getMessage());
                        }
                    }else{
                        //旧版身份证表
                        try{
                            $idCard->delete();
                        }catch (\ErrorException $e){
                            $bankcard->updStatus(\Prj\Consts\BankCard::enabled);
                            $bankcard->update();
                            $user->setField('tradePwd',$tradePwd);
                            $user->setField('salt',$salt);
                            $user->update();
                            return $this->returnError($e->getMessage());
                        }
                    }

                }

                try{
                    \Prj\Data\UserChangeLog::addLog(\Prj\Data\UserChangeLog::type_unbind,$user->getField('phone'),json_encode([
                        'userId'=>$userId,
                        'bankcardId'=>$bankcardId,
                        'bankId'=>$bankcard->getField('bankId'),
                        'bankCard'=>$bankcard->getField('bankCard'),
                        'realname'=>$user->getField('nickname'),
                        'idCode'=>$idCardSN,
                    ]));
                }catch (\ErrorException $e){
                    error_log("unbind#".$where['orderId'].'#'.$e->getMessage());
                }

                return $this->returnOK('解绑成功');
            }elseif($ret['code']==400){
                return $this->returnError($ret['msg']);
            }else{
                return $this->returnError('网关未响应');
            }
        }else{
            return $this->returnError('中断>>>');
        }

        return $this->returnError('中断>>>');
    }

    public function unlockUserAction(){
        $where                          = \Prj\Misc\View::decodePkey($this->_request->get('_pkey_val_'));
        $type                           = $this->_request->get('_type');
        $frm                            = \Sooh\Base\Form\Broker::getCopy('default')
            ->init(\Sooh\Base\Tools::uri(), 'get', empty($where) ? \Sooh\Base\Form\Broker::type_c : \Sooh\Base\Form\Broker::type_u);

        $frm->addItem('uid', form_def::factory('用户ID/手机号', '', form_def::text, [], ['data-rule' => 'required,length[~15]']))
            ->addItem('_type', $type)
            ->addItem('_pkey_val_', $this->_request->get('_pkey_val_'));


        //todo 构造表单数据

        $frm->fillValues();
        if($frm->isThisFormSubmited()){
            $fields = $frm->getFields();
            var_log($fields,'fields >>> ');
            if($fields['uid']){
                $user = \Prj\Data\User::getUserByPhoneOrUserId($fields['uid']);
                if($user){
                    $fields['userId'] = $user->userId;
                }else{
                    $fields['userId'] = '';
                }
            }
        }

        //锁定用户列表
        $where = ['sLockData!'=>''];
        if($fields['userId']!==null){
            $where['userId'] = $fields['userId'];
        }
        $records = \Prj\Data\User::loopFindRecords($where);
        $header = [
            'userId'=>['用户ID','100'],
            'nickname'=>['用户名','100'],
            'phone'=>['手机号','100'],
            'sLockData'=>['锁定详情','500'],
        ];
        $userList = [];
        if($records){
            $userList = array_map(function($value) use ($header){
                foreach($header as $k=>$v){
                    $tmp[$k] = $value[$k];
                }
                if(isset($tmp['nickname']))$tmp['nickname']=\Prj\IdCard::hideName($tmp['nickname']);
                if(isset($tmp['phone']))$tmp['phone']=\Prj\IdCard::hideId($tmp['phone']);
                return $tmp;
            },$records);
        }
        $this->_view->assign('userList',$userList);
        $this->_view->assign('header',$header);
        //var_log($userList,'userList >>> ');
        //todo 构造表单数据

        $userId = $this->_request->get('userId');
        if($userId){
//=========================================================================
            $user = \Prj\Data\User::getCopy($userId);
            $user->load();
            $userDump = $user->dump();
            $userInfo = [
                '用户ID'=>$userId,
                '手机号'=>$userDump['phone'],
                '用户名'=>\Prj\IdCard::hideName($userDump['nickname']),
                '首次绑卡日期'=>$userDump['ymdBindcard']?date('Y-m-d',strtotime($userDump['ymdBindcard'])):'',
                '身份证号'=>\Prj\IdCard::hideId($userDump['idCard']),
                '余额(元)'=>$userDump['wallet']/100
            ];
            $this->_view->assign('userInfo',$userInfo);

            var_log($userId,'userId >>>>>>>>>>');
            $tallyHeader = [
                'tallyId'    => ['资金ID', '100'],
                'tallyType'  => ['类别', '50'],
                'userId'     => ['用户ID', '100'],
                'orderId'    => ['订单ID', '100'],
                'sn'         => ['支付ID', '100'],
                'nOld'       => ['变更前余额(元)', '100'],
                'nAdd'       => ['账户变动(元)', '100'],
                'nNew'       => ['变更后余额(元)', '100'],
                'timeCreate' => ['流水时间', '100'],
                'descCreate' => ['备注', '200'],
            ];
            $tally = \Prj\Data\WalletTally::getCopy($userId);
            $tally->load();
            $tallyList = $tally->db()->getRecords($tally->tbname(),'*',['userId'=>$userId],'rsort timeCreate','10',0);
            array_walk($tallyList,function(&$v,$k) use ($tallyHeader){
                $v['tallyType'] =  \Prj\Consts\OrderType::$enum[$v['tallyType']]?\Prj\Consts\OrderType::$enum[$v['tallyType']]:$v['tallyType'];
                $v['timeCreate'] = date('Y-m-d H:i:s',strtotime( $v['timeCreate']));
                $v['nOld']/=100;
                $v['nAdd']/=100;
                $v['nNew']/=100;
                foreach($tallyHeader as $kk=>$vv){
                    $tmp[$kk] = $v[$kk];
                }
                $v = $tmp;
            });
            $this->_view->assign('tallyHeader',$tallyHeader);
            $this->_view->assign('tallyList',$tallyList);

            $investHeader = [
                'ordersId'=>['订单号','160'],
                'waresId'=>['标的号','137'],
                'waresName'=>['标的名称','120'],
                'userId'=>['用户ID','120'],
                'amount'=>['实投金额/红包','90'],
                //'amountExt'=>['红包','35'],
                //'amountFake'=>['券金','35'],
                'yieldStatic'=>['固定年化/活动加息','115'],
                //'yieldStaticAdd'=>['活动加息','60'],
                //'interest'=>['本金收益/奖励收益','115'],
                //'interestExt'=>['奖励收益','60'],
                //'extDesc'=>['赠送说明','105'],
                'orderTime'=>['下单时间/下次还款日','210'],
                'orderStatus'=>['订单状态','120'],
                //'returnType'=>['还款方式','112'],
                //'returnNext'=>['下次还款日','60'],
            ];
            $invest = \Prj\Data\Investment::getCopy($userId);
            $invest->load();
            $investList = $invest->db()->getRecords($invest->tbname(),'*',['userId'=>$userId,'orderStatus!'=>\Prj\Consts\OrderStatus::abandon],'rsort orderTime',10,0);
            array_walk($investList,function(&$v,$k) use ($investHeader){
                $v['orderStatus'] = \Prj\Consts\OrderStatus::$enum[$v['orderStatus']];
                $v['amount'] = ($v['amount']/100).'/'.$v['amountExt']/100;
                $v['yieldStatic'] = ($v['yieldStatic']).'/'.$v['yieldStaticAdd'];
                $v['orderTime'] = date('Y-m-d H:i:s',strtotime($v['orderTime']));
                $v['returnNext'] = $v['returnNext']?date('Y-m-d  ',strtotime($v['returnNext'])):'';
                $v['orderTime'] = $v['orderTime'].'/'.$v['returnNext'];
                foreach($investHeader as $kk=>$vv){
                    $tmp[$kk] = $v[$kk];
                }
                $v = $tmp;
            });
            $this->_view->assign('investHeader',$investHeader);
            $this->_view->assign('investList',$investList);
//=========================================================================
        }


        if ($frm->type() == \Sooh\Base\Form\Broker::type_u) //update show
        {
            //todo 字段展示 设置item的value
            /*
            $??? = \Prj\Data\???::getCopy($where['id']);
            $???->load();
            $arr = $???->dump();
            foreach($frm->items as $k=>$v){
                if(array_key_exists($k,$arr))$frm->items[$k]->value = $arr[$k];
            }
            */
        }

        //var_dump($fields);
        //die();
        $this->_view->assign('FormOp', $op = '添加商品');
        $this->_view->assign('type', $type);
        $this->_view->assign('_pkey_val_', $this->_request->get('_pkey_val_'));
    }

    public function doUnlockUserAction(){
        $userId = $this->_request->get('userId');
        $user = \Prj\Data\User::getCopy($userId);
        $user->load();
        if(!$user->exists())return $this->returnError('不存在的用户');
        $sLockData = $user->getField('sLockData');
        try{
            $user->unlock();
        }catch (\ErrorException $e){
            return $this->returnError($e->getMessage());
        }
        try{
            \Prj\Data\UserChangeLog::addLog('unlockUser',$user->getField('phone'),json_encode([
                'userId'=>$userId,
                'sLockData'=>$sLockData,
            ]));
        }catch (\ErrorException $e){

        }
        return $this->returnOK('解锁成功!');
    }

    protected function returnWarn($msg){
        $this->_view->assign('error',$msg);
    }
}