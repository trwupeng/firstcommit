<?php

use Sooh\Base\Form\Item as form_def;
use Prj\Data\User as User;
use Prj\Consts\OrderStatus as OrderStatus;
use Sooh\DB\Pager;

/**
 * Created by PhpStorm.
 * User: tang.gaohang
 * Date: 2016/6/3
 * Time: 17:59
 */
class UsercheckController extends \Prj\ManagerCtrl {

    public function indexAction(){
        $where                          = \Prj\Misc\View::decodePkey($this->_request->get('_pkey_val_'));
        $type                           = $this->_request->get('_type');
        $frm                            = \Sooh\Base\Form\Broker::getCopy('default')
            ->init(\Sooh\Base\Tools::uri(), 'get', empty($where) ? \Sooh\Base\Form\Broker::type_c : \Sooh\Base\Form\Broker::type_u);

        $frm->addItem('uid', form_def::factory('用户ID/手机号', '', form_def::text, [], ['data-rule' => 'required,length[~15]']))
            ->addItem('_type', $type)
            ->addItem('_pkey_val_', $this->_request->get('_pkey_val_'));

        //todo 构造表单数据

        $frm->fillValues();
        //表单提交
        if ($frm->flgIsThisForm)
        {
            $fields = $frm->getFields();
            $uid = $fields['uid'];
            $user = \Prj\Data\User::getUserByPhoneOrUserId($uid);
            if(empty($user)){
                $error = '用户不存在';
            }else{
                $userId = $user->userId;
                $userInfo = $user->dump();
                $this->_view->assign('userInfo',$userInfo);
                $data = [$userId];
                try{
                    $ret = \Lib\Services\PayGWCmd::sendToPayGWCmd('queryBalance',$data);
                    if($ret['code'] == 200){
                        $sinaInfo = $ret['data'];
                        $this->_view->assign('sinaInfo',$sinaInfo);
                    }else{
                        $error = '新浪:'.$ret['msg'];
                    }
                }catch (\ErrorException $e){
                    $error = '网关未响应:'.$e->getMessage();
                }
            }
        }
        $this->_view->assign('error',$error);
        $this->_view->assign('FormOp', $op = '添加商品');
        $this->_view->assign('type', $type);
        $this->_view->assign('_pkey_val_', $this->_request->get('_pkey_val_'));
    }

}
