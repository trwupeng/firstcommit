<?php
use Sooh\Base\Form\Item as form_def;

/**
 * Created by PhpStorm.
 * User: tang.gaohang
 * Date: 2016/6/28
 * Time: 19:11
 */
class PaybackextController extends \Prj\ManagerCtrl
{
    protected $class = '';

    protected function getCompanyUserId(){
        return \Prj\Data\Config::get('company_account')['第三方企业户'];
    }

    public function companyRechargeAction()
    {
        $amount = $this->_request->get('amount');
        $userId           = $this->getCompanyUserId();
        if ($amount) {
            \Sooh\Base\Ini::getInstance()->viewRenderType('echo');
            $fields['amount'] = $amount;
            $fields['amount'] = ceil($fields['amount'] * 100);
            if (empty($userId)) return $this->returnError('配置缺失第三方企业户ID');
            if (empty($fields['amount'])) return $this->returnError('请输入充值金额');
            $user = \Prj\Data\User::getCopy($userId);
            $user->load();
            if(!$user->exists()){
                echo '用户不存在';
                return;
            }
            //todo 入库
            $tmp = \Prj\Data\Recharges::addOrders($userId,$fields['amount'],'xxxx','8888',\Prj\Consts\OrderType::recharges,101);
            $tmp->setField('orderStatus',\Prj\Consts\OrderStatus::waiting);
            $tmp->setField('exp','第三方企业户充值');
            $tmp->update();
            $sn               = $tmp->getPKey()['ordersId'];
            error_log('#companyRecharge#sn:'.$sn);
            try {
                $data = [$sn, $userId, $fields['amount']];
                $ret  = \Lib\Services\PayGW::sendToPayGW('companyRecharge', $data);
            } catch (\ErrorException $e) {
                echo $e->getMessage();
                return;
            }

            if ($ret['code'] == 200) {
                echo $ret['html'];
                return;
                //$this->_view->assign('html',$ret['html']);
                //return $this->returnOK();
            } else {
                $msg = $ret['msg'] ? $ret['msg'] : '网关错误';
                echo $msg ;
                return;
            }
        }else{
            /*
            try{
                $data = [$userId];
                $ret = \Lib\Services\PayGWCmd::sendToPayGWCmd('queryBalance',$data);
            }catch (\ErrorException $e){

            }
            */
        }
    }

    public function transToBorrowerAction()
    {
        $inputConfig = [
            'borrowerId' => ['借款人','',form_def::select,\Prj\Data\Config::get('borrower')+[''=>''],'required'],
            'amount' => ['金额(元)','',form_def::text,[],'number'],
        ];
        $where = \Prj\Misc\View::decodePkey($this->_request->get('_pkey_val_'));
        $type  = $this->_request->get('_type');
        $frm   = \Sooh\Base\Form\Broker::getCopy('default')
            ->init(\Sooh\Base\Tools::uri(), 'get', empty($where) ? \Sooh\Base\Form\Broker::type_c : \Sooh\Base\Form\Broker::type_u);
        $frm->addItem('_type', $type)
            ->addItem('_pkey_val_', $this->_request->get('_pkey_val_'));
        if ($inputConfig) {
            foreach ($inputConfig as $k => $v) {
                $frm->addItem($k, form_def::factory($v[0], $v[1], $v[2], $v[3], ['data-rule' => $v[4]]));
            }
        }
        //todo 构造表单数据
        $frm->fillValues();
        //表单提交
        if ($frm->flgIsThisForm) {
            $where = \Prj\Misc\View::decodePkey($this->_request->get('_pkey_val_'));
            if (!empty($where)) {
                $frm->switchType(\Sooh\Base\Form\Broker::type_u);
            } else {
                $frm->switchType(\Sooh\Base\Form\Broker::type_c);
            }
            $op = '';
            try {
                $fields = $frm->getFields();
                $fields = $this->inFormat($fields);
                try {
                    //todo 字段过滤
                } catch (\ErrorException $e) {
                    return $this->returnError($e->getMessage());
                }

                if ($frm->type() == \Sooh\Base\Form\Broker::type_c) //add
                {
                    $op  = "转账操作已受理";
                    $nickname = $this->manager->getField('nickname');
                    //todo 转账操作
                    $fromUid = $this->getCompanyUserId();
                    $borrowerIds = \Sooh\Base\Ini::getInstance()->get('borrowerIds');
                    $borrowerId = $fields['borrowerId'];
                    $borrowerName = \Prj\Data\Config::get('borrower')[$borrowerId];
                    $company = \Prj\Data\User::getCopy($fromUid);
                    $company->load();
                    if(empty($fromUid) || !$company->exists())return $this->returnError('无效的企业户ID');
                    if(empty($borrowerName))return $this->returnError('非法的借款人ID');
                    if(empty($borrowerIds) || !in_array($borrowerId,$borrowerIds))return $this->returnError('非法的借款人ID');
                    $data = [
                        'fromUid' => $fromUid,
                        'toUid' => $borrowerId,
                        'amount' => $fields['amount'],
                        'statusCode' => \Prj\Consts\OrderStatus::abandon,
                        'exp' => '企业户转账给借款人#'.$borrowerName,
                    ];
                    $trans = \Prj\Data\Trans::add($data,$nickname);
                    if(empty($trans))return $this->returnError('db_error');
                    try{
                        $trans->update();
                    }catch (\ErrorException $e){
                        return $this->returnError($e->getMessage());
                    }
                    $sn = $trans->getPKey()['transId'];
                    try{
                        $tmp = [$sn,$fields['amount'],$fromUid,$borrowerId,$borrowerName];
                        $ret = \Lib\Services\PayGW::sendToPayGW('remit',$tmp);
                    }catch (\ErrorException $e){
                        return $this->returnError($e->getMessage());
                    }

                    if($ret['code'] == 200){
                        $trans->setField('statusCode',\Prj\Consts\OrderStatus::waiting);
                        $trans->update();
                    }else{
                        $error = '网关错误#'.$ret['msg'];
                        $trans->setField('statusCode',\Prj\Consts\OrderStatus::abandon);
                        $trans->setField('exp',$error);
                        $trans->update();
                        return $this->returnError($error);
                    }
                } else { // update
                    return;
                }
            } catch (\ErrorException $e) {
                return $this->returnError($op . '失败：冲突，相关记录已经存在？');
            }

            $this->closeAndReloadPage($this->tabname('index'));
            $this->returnOK($op . '成功');
            return;
        }

        $this->_view->assign('type', $type);
        $this->_view->assign('_pkey_val_', $this->_request->get('_pkey_val_'));
    }

    protected function inFormat($fields){
        $fields['amount']*=100;
        return $fields;
    }
}