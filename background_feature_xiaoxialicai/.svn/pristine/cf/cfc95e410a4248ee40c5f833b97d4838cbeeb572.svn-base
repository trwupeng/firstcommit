<?php

/**
 * 新版的用户相关接口
 * @author simon.wang
 */
class UsernewController extends \Prj\UserCtrl
{
    protected $logMark = ''; //日志标记

    protected $logRand = 0; //日志随机数

    protected function varLog($msg){
        if($this->logRand == 0)$this->logRand = mt_rand(1000,9999);
        error_log("#varlog#[$this->logRand]".$this->logMark.'#'.$msg);
    }

    //初始化
    public function init(){
        parent::init();
        $this->user->load();
    }

    protected function returnError($msg = '' , $code = 400){
        $this->varLog("[$code]$msg");
        parent::returnError($msg , $code);
    }

    protected $tmp = [];   //保存一些临时参数

    /**
     * 实名认证接口
     * @input idCard string  身份证号
     * @input nickname string  真实姓名
     * @output code:200 成功
     * @errors server_busy 服务器忙
     * @errors gw_error 网关错误
     */
	public function certificationAction(){
        $userId = $this->user->userId;
        $this->_view->assign('userId',$userId);
        $this->logMark = 'certification#'.$userId;
        $user = $this->user;
        $user->load();
        $phone = $user->getField('phone');
        $idCard = $this->_request->get('idCard');
        $nickname = $this->_request->get('nickname');
        $oldIdCard = $user->getField('idCard');
        if(!empty($oldIdCard)){
            $this->varLog('have_certify');
            return $this->returnOK('have_certify');
        }
        //检查实名信息
        $this->realNameCheck($nickname , $idCard);
        $data = [$userId,$phone,$nickname,$idCard];
        try{
            $ret = \Lib\Services\PayGWCmd::sendToPayGWCmd('register',$data);
            $this->varLog('send gw success');
        }catch (\ErrorException $e){
            $this->varLog('#ERROR#'.$e->getMessage());
            return $this->returnError($e->getMessage());
        }

        if($ret['code'] == 200){
            $user->setField('idCard',$idCard);
            $user->setField('nickname',$nickname);
            //todo oauth 那边可能也要写数据...
            try{
                $user->update();
                $this->varLog('user update success');
            }catch (\ErrorException $e){
                $this->varLog('#ERROR#'.$e->getMessage());
                return $this->returnError('server_busy');
            }
            //保存身份证信息
            if(\Prj\Data\Config::get('idcardUnique')){
                if($this->tmp['idCard']){
                    try{
                        $this->tmp['idCard']->update();
                        $this->varLog('idCard update success');
                    }catch (\ErrorException $e){
                        $this->varLog('#ERROR#'.$e->getMessage());
                    }
                }
            }
            return $this->returnOK('success');
        }else{
            if($ret['code'] == 500)$ret['msg'] = '系统正忙,请稍后重试';
            $this->varLog($ret['msg']?$ret['msg']:'gw_error');
            return $this->returnError($ret['msg']?$ret['msg']:'gw_error');
        }
    }
    //表单验证
    protected function realNameCheck($nickname , $idCard){
        if(empty($idCard))$this->throwError('args_miss');
        if(empty($nickname))$this->throwError('args_miss');
        if(\Prj\Data\Config::get('idcardUnique')){
            $id = \Prj\Data\IdCard::check($idCard , $this->user->userId);
            if(empty($id))$this->throwError('该身份证已经被使用');
            $id->setField('statusCode',-1);
            $this->tmp['idCard'] = $id;
        }
        $age = \Prj\IdCard::getAge($idCard);
        if(!\Prj\IdCard::verify($idCard) || $age <= 0 || $age>=100){
            $this->throwError('证件号不匹配');
        }
        if($age < 18){
            $this->throwError('您的年龄未满18岁，无法接受小虾的服务');
        }

    }

    protected function getUrl($func,$data){
        try{
            $ret = \Lib\Services\PayGWCmd::sendToPayGWCmd($func,$data);
            $this->varLog('send gw success');
        }catch (\ErrorException $e){
            $this->varLog('#ERROR#'.$e->getMessage());
            return $this->returnError($e->getMessage());
        }

        if($ret['code'] == 200){
            $this->_view->assign($func,['redirectUrl'=>$ret['data']['redirectUrl']]);
            return $this->returnOK('success');
        }else{
            if($ret['code'] == 500)$ret['msg'] = '系统正忙,请稍后重试';
            $this->varLog($ret['msg']?$ret['msg']:'gw_error');
            return $this->returnError($ret['msg']?$ret['msg']:'gw_error');
        }
    }

    protected function getReturnUrl($args = []){
        $returnUrl = $this->_request->get('returnUrl');
        if(empty($returnUrl)){
            return \Sooh\Base\Ini::getInstance()->get('uriBase')['www'].\Sooh\Base\Tools::uri([],'sinaReturnUrl','public');
        }else{
            $args = http_build_query($args);
            if(strpos($returnUrl,'?')===false){
                return $returnUrl.'?'.$args;
            }else{
                return $returnUrl.'&'.$args;
            }
        }
    }

    /**
     * 设置支付密码
     * @input returnUrl 回调页面
     * @output redirectUrl 跳转地址
     * @errors gw_error 网关错误
     *
     */
    public function setPayPwdAction(){
        $userId = $this->user->userId;
        $returnUrl = $this->_request->get('returnUrl',$this->getReturnUrl());
        var_log($returnUrl , 'returnUrl >>> ');
        $this->logMark = 'setPayPwd#'.$userId;
        $data = [$userId,$returnUrl];
        return $this->getUrl('setPayPwd',$data);
    }
    /**
     * 修改支付密码
     * @input
     * @output redirectUrl 跳转地址
     * @errors gw_error 网关错误
     */
    public function modifyPayPwdAction(){
        $userId = $this->user->userId;
        $returnUrl = $this->_request->get('returnUrl',$this->getReturnUrl());
        var_log($returnUrl , 'returnUrl >>> ');
        $this->logMark = 'modifyPayPwd#'.$userId;
        $data = [$userId,$returnUrl];
        return $this->getUrl('modifyPayPwd',$data);
    }
    /**
     * 找回支付密码
     * @input
     * @output redirectUrl 跳转地址
     * @errors gw_error 网关错误
     */
    public function findPayPwdAction(){
        $userId = $this->user->userId;
        $returnUrl = $this->_request->get('returnUrl',$this->getReturnUrl());
        var_log($returnUrl , 'returnUrl >>> ');
        $this->logMark = 'findPayPwd#'.$userId;
        $data = [$userId,$returnUrl];
        return $this->getUrl('findPayPwd',$data);
    }

    public function withdrawAction(){
        $userId = $this->user->userId;
        $user = $this->user;
        $perTimeAmount = \Prj\Data\Config::get('WITHDRAW_PER_TIME_LIMIT_AMOUNT');
        $dayAmount = \Prj\Data\Config::get('WITHDRAW_DAY_AMOUNT'); //每日最多
        $monthTimes = \Prj\Data\Config::get('WITHDRAW_MONTH_TIMES');
        $item = new \Prj\Items\Withdraw();
        $left = $item->numLeft($user);
        $alreadyTimes = $item->getUsed($user);
        $dayAmountRemain = $dayAmount - \Prj\Data\Recharges::getAmountWithdrawingByOrderTime($userId, date('Ymd'));
        $dayAmountRemain = $dayAmountRemain > 0 ? $dayAmountRemain : 0; //限额剩余
        $wallet = $user->getField('wallet')-0;
        $inputs['amount'] = $this->_request->get('amount');
        $myCards = $this->_myCards();
        if(empty($myCards))return $this->returnError('no_bindcard');
        $myCard = current($myCards);
        if($inputs['amount'] > 0){
            //提现强制关闭
            $forbid_withdraw = \Prj\Data\Config::get('forbid_withdraw');
            if($forbid_withdraw['forbid'])return $this->_throwError($forbid_withdraw['notice']);
            $this->logMark = '#withdraw#userId:'.$userId;
            //提现逻辑
            //提现受理的回调太慢,故先关闭这条逻辑

            $lastWithdraw = \Prj\Data\Recharges::getLastRedirectUrl($userId,\Prj\Consts\OrderType::withdraw);
            if(1==2 && $lastWithdraw && $lastWithdraw['amount'] == $inputs['amount']){
                $this->varLog('get last redirectUrl');
                $this->_view->assign('withdraw',['redirectUrl'=>$lastWithdraw['redirectUrl'],'orderId'=>$lastWithdraw['ordersId']]);
            }else{

                try{
                    $this->_withdraw($inputs['amount'],$myCard);
                }catch (\ErrorException $e){
                    return $this->returnError($e->getMessage());
                }

            }

        }else{
            $this->_view->assign('wallet',$wallet); //账户余额
            $this->_view->assign('perTimeAmount',$perTimeAmount-0); //单次限额
            $this->_view->assign('dayAmount',$dayAmount-0); //日限额
            $this->_view->assign('dayAmountRemain',$dayAmountRemain-0); //日限额神谕
            $this->_view->assign('awardTimes',$left-0); //免费次数剩余
            $this->_view->assign('alreadyTimes',$alreadyTimes-0); //已经免费次数
            $this->_view->assign('monthTimes',$monthTimes-0); //月免费次数
            $this->_view->assign('poundageInit',\Prj\Data\Config::get('WITHDRAW_POUNDAGE')-0); //提现手续费
            $this->_view->assign('myCard',$myCard); //我的银行卡
        }
        $this->returnOK();
    }

    protected function _throwError($msg,$code = 400){
        throw new \ErrorException($msg,$code);
    }

    protected function _withdraw($amount,$myCard){
        $inputs['amount'] = $amount;
        $user = $this->user;
        $userId = $user->userId;
        $wallet = $user->getField('wallet');
        $perTimeAmount = \Prj\Data\Config::get('WITHDRAW_PER_TIME_LIMIT_AMOUNT');
        $dayAmount = \Prj\Data\Config::get('WITHDRAW_DAY_AMOUNT'); //每日最多
        $item = new \Prj\Items\Withdraw();
        $left = $item->numLeft($user);
        $dayAmountRemain = $dayAmount - \Prj\Data\Recharges::getAmountWithdrawingByOrderTime($userId, date('Ymd'));
        $dayAmountRemain = $dayAmountRemain > 0 ? $dayAmountRemain : 0; //限额剩余
        //借款人金额不受限制
        $borrowerConfig = \Prj\Data\Config::get('borrower');
        if(!is_array($borrowerConfig)){

        }else{
            if(array_key_exists($userId,$borrowerConfig)){
                $this->varLog('this is borrower '.$borrowerConfig[$userId]);
                $perTimeAmount = 1000000000;
                $dayAmountRemain = 1000000000;
            }
        }
        if ($inputs['amount'] > $perTimeAmount) return $this->_throwError('perTime_out');
        if ($inputs['amount'] > $dayAmountRemain) return $this->_throwError('dayAmount_out');
        //计算提现手续费
        if ($left>0) {
            $wn = \Prj\Data\WithdrawNum::add($userId,-1,date('Ym'),'',$userId);
            $item->useit($user,1);
            $poundage = 0;
        }else{
            $poundage = \Prj\Data\Config::get('WITHDRAW_POUNDAGE')-0;
            $this->varLog("need poundage $poundage yuan");
        }
        if ($inputs['amount'] + $poundage > $wallet) {
            $this->_view->assign('amount', $inputs['amount']);
            return $this->_throwError(\Prj\Lang\Broker::getMsg('user.withdraw_over_remain'));
        }
        $withdraw = \Prj\Data\Recharges::addOrders($userId, -$inputs['amount'], $myCard['bankId'], $myCard['bankCard'], \Prj\Consts\OrderType::withdraw);
        if(empty($withdraw)){return $this->_throwError('db_error');}
        $orderId = $withdraw->getPKey()['ordersId'];
        $lockInfo = "lock by $userId when withdraw $orderId"; //锁定信息
        //锁定用户
        //if(!$user->lock($lockInfo)){return $this->_throwError('record_locked');}
        $withdraw->setField('poundage', $poundage);
        $withdraw->setField('payCorp', \Prj\Consts\PayGW::paycorp_sina);
        //获取到账日期 T+1 OR T+2
        $ymd = date('Ymd', strtotime('+1 days'));
        $withdraw->setField('withdrawYmd', $ymd);
        $withdraw->setField('exp', '跳转至收银台');
        //生成钱包流水
        \Prj\Misc\OrdersVar::$introForUser = '提现申请';
        \Prj\Misc\OrdersVar::$introForCoder = 'withdraw_'.$orderId;
        $tally = \Prj\Data\WalletTally::addTally($userId, $user->getField('wallet'), -$inputs['amount'] - $poundage, 0, $orderId, \Prj\Consts\OrderType::withdraw);
        $tally->setField('statusCode', \Prj\Consts\Tally::status_new);
        $tally->setField('poundage', $poundage);
        $tally->setField('freeze', 1); //冻结金额
        //修改用户钱包
        $oldWallet = $user->getField('wallet');
        //$user->setField('wallet', $oldWallet - $inputs['amount'] - $poundage);
        $this->logMark.=('#orderId:'.$orderId);
        //提交网关
        try{
            $this->varLog('send to gw ...');
            $returnUrl = $this->getReturnUrl(['h5orderdetailId'=>$orderId]);
            $data = [$orderId,$userId,$inputs['amount'],$poundage,$returnUrl];
            $ret = \Lib\Services\PayGWCmd::sendToPayGWCmd('trade_withdraw',$data);
            $this->varLog('send to gw success');
        }catch (\ErrorException $e){
            $this->varLog('#ERROR#'.'send to gw ... timeout or server error');
            //超时的情况
            $withdraw->setField('orderStatus',\Prj\Consts\OrderStatus::abandon);
            $withdraw->setField('exp',$e->getMessage());
            try{
                $this->varLog('save error msg ...');
                $withdraw->update();
                $this->varLog('save error msg success');
            }catch (\ErrorException $e){
                $this->varLog('#ERROR#'.'save error msg failed');
            }
            $user->unlock();
            return $this->_throwError($e->getMessage());
        }
        if($ret['code'] == 200){
            $this->varLog('gw return 200 ...');
            //回滚函数
            $rollBack = function($backGroup){
                $this->varLog('begin to rollBack ...');
                if($backGroup['wn']){
                    $backGroup['wn']->setField('statusCode',\Prj\Consts\OrderStatus::abandon);
                    $backGroup['wn']->update();
                    $this->varLog('wn rollBack success');
                }
                if($backGroup['tally']){
                    $backGroup['tally']->setField('statusCode',\Prj\Consts\Tally::status_abandon);
                    $backGroup['tally']->setField('codeCreate',$backGroup['tally']->getField('codeCreate').'_rollBack');
                    $backGroup['tally']->update();
                    $this->varLog('tally rollBack success');
                }
                if($backGroup['withdraw']){
                    $backGroup['withdraw']->setField('orderStatus',\Prj\Consts\OrderStatus::abandon);
                    $backGroup['withdraw']->setField('exp','#更新失败回滚#');
                    $backGroup['withdraw']->update();
                    $this->varLog('withdraw rollBack success');
                }
                if($backGroup['user']){
                    try{
                        $backGroup['user']->unlock();
                        $this->varLog('user unlock success');
                    }catch (\ErrorException $e){
                        $this->varLog('user need not unlock ');
                    }
                }
                $this->varLog('rollBack finished');
            };
            $backGroup['user'] = $user;  //记录需要回滚的对象
            $redirectUrl = \Prj\Tool\Func::getUrlFromSinaHtml($ret['data']['htmlContent']); //解析地址
            try{
                if($wn){
                    $wn->setField('exp',$orderId);
                    $wn->update();
                    $backGroup['wn'] = $wn;
                }
                //$tally->update();
                //$backGroup['tally'] = $tally;
                $withdraw->setField('redirectUrl',$redirectUrl);
                $withdraw->update();
                $backGroup['withdraw'] = $withdraw;
                if($left>0)$user->update();
            }catch (\ErrorException $e){
                $this->varLog('#ERROR#'.$e->getMessage());
                $rollBack($backGroup);
                return $this->_throwError($e->getMessage());
            }
            if(empty($redirectUrl)){
                $this->varLog('#ERROR#'.'url parse failed');
                return $this->_throwError('gw_error');
            }
            $this->_view->assign('withdraw',['redirectUrl'=>$redirectUrl,'orderId'=>$orderId,'poundage'=>$poundage-0]);
        }else{
            if($ret['code'] == 500)$ret['msg'] = '系统正忙,请稍后重试';
            $this->varLog('gw return 400 ...');
            $user->unlock();
            return $this->_throwError($ret['msg']?$ret['msg']:'gw_error');
        }
    }
    /**
     * 查询支付密码设置情况
     * @input
     * @output isSet true 设置过/false 未设置
     * @errors gw_error 网关错误
     *
     */
    /*
    public function queryPayPwdStatusAction(){
        $this->_view->assign('isSetPwd',$this->queryPayPwdStatus()['isSetPwd']);
        return $this->returnOK('success');
    }
    */

    protected function queryPayPwdStatus(){
        $userId = $this->user->userId;
        $this->logMark = 'queryPayPwdStatus#'.$userId;
        try{
            $data = [$userId];
            $ret = \Lib\Services\PayGWCmd::sendToPayGWCmd('queryPayPwdStatus',$data);
            $this->varLog('send gw success');
        }catch (\ErrorException $e){
            $this->varLog('#ERROR#'.$e->getMessage());
            $this->throwError($e->getMessage());
        }

        if($ret['code'] == 200){
            return $ret['data'];
        }else{
            if($ret['code'] == 500)$ret['msg'] = '系统正忙,请稍后重试';
            $msg = $ret['msg']?$ret['msg']:'gw_error';
            $this->varLog($msg);
            $this->throwError($msg);
        }
    }
    /**
     * 查询委托扣款开通情况
     * @input
     * @output isSetProxyAuth true 开通了/false 未开通
     * @errors gw_error 网关错误
     *
     */
    /*
    public function queryProxyAuthAction(){
        $this->_view->assign('isSetProxyAuth',$this->queryProxyAuth()['isSetProxyAuth']);
        return $this->returnOK('success');
    }
    */

    /**
     * 设置委托扣款
     * @input returnUrl 回调页面
     * @output redirectUrl 跳转地址
     * @errors gw_error 网关错误
     *
     */
    public function setProxyAuthAction(){
        $userId = $this->user->userId;
        $returnUrl = $this->_request->get('returnUrl',$this->getReturnUrl());
        $this->logMark = 'proxyAuth_set#'.$userId;
        $data = [$userId,$returnUrl];
        return $this->getUrl('proxyAuth_set',$data);
    }


    protected function queryProxyAuth(){
        $userId = $this->user->userId;
        $this->logMark = __FUNCTION__.'#'.$userId;
        try{
            $data = [$userId];
            $ret = \Lib\Services\PayGWCmd::sendToPayGWCmd('proxyAuth_query',$data);
            $this->varLog('send gw success');
        }catch (\ErrorException $e){
            $this->varLog('#ERROR#'.$e->getMessage());
            $this->throwError($e->getMessage());
        }

        if($ret['code'] == 200){
            return $ret['data'];
        }else{
            if($ret['code'] == 500)$ret['msg'] = '系统正忙,请稍后重试';
            $msg = $ret['msg']?$ret['msg']:'gw_error';
            $this->varLog($msg);
            $this->throwError($msg);
        }
    }

    /**
     * 绑卡
     * By Hand
     * 绑定第一步：
     * @input string bankId 银行代码 icbc 【第一步】
     * @input string bankCard 银行卡号码 【第一步】
     * @input string realName 姓名 【第一步】
     * @input int phone 手机号 【第一步】
     * @input int idCardType 证件类型 1:身份证 【第一步】
     * @input string idCardSn 证件号码 【第一步】
     * @input string returnUrl 回转地址 【第一步】+ 【第二步】
     * @input string cmd 指令   binding:绑定第一步 bindingcode:绑定第二步 发送验证码 【第一步】+ 【第二步】
     * @input string smsCode 短信验证码 【第二步】
     * @input string ticket 第一步得到的神秘代码 【第二步】
     * //返回客户端旧的标识信息
     * @output 【第一步】{returnUrl: "",retAll: {ret: "ok",got: {code: "200",msg: "成功",ticket: "ceb1218c2a1d45ac8d1a87b077d6ea1a"}},code: 400,msg: "null_return"}
     * / 【第二步】{returnUrl: "",retAll: {ret: "ok",got: {cardId: "47495",code: "200",orderId: "1447724571610102103"}},CardBound: {orderId: "1447724571610102103",status: 16},code: 200}
     * //返回客户端新的标识信息
     * @output 【第一步】{returnUrl: "",bindCardretAll: {ret: "ok",got: {code: "200",msg: "成功",ticket: "ceb1218c2a1d45ac8d1a87b077d6ea1a"}},code: 400,msg: "null_return"}
     * / 【第二步】{returnUrl: "",bindCardCoderetAll: {ret: "ok",got: {cardId: "47495",code: "200",orderId: "1447724571610102103"}},CardBound: {orderId: "1447724571610102103",status: 16},code: 200}
     * @errors {"code":505,"msg":"db_error"} 数据库操作异常，比如插入新纪录失败，磁盘满了
     * @errors {"code":400,"msg":"error_name"} 姓名不符
     * @errors {"code":400,"msg":"error_idCardSn"} 身份证不符
     * @errors {"code":400,"msg":"error_cmd"} 错误的指令
     * @errors {"code":400,"msg":"card_exist"} 银行卡已经存在
     * @errors {"code":400,"msg":"addCard_failed"} 添加银行卡失败，系统错误
     * @errors {"code":400,"msg":"gw_error"} 支付网关错误
     * @errors {"code":400,"msg":"null_payCorp"} 支付网关未返回支付渠道
     * @errors {"code":400,"msg":"db_error"} 数据库错误
     * @errors {"code":400,"msg":"no_smsCode"} 为输入短信验证码
     * @errors {"code":400,"msg":"no_ticket"} 为输入神秘代码
     * @errors {"code":400,"msg":"null_cardId"} 支付网关未返回cardId
     * @errors {"code":400,"msg":"null_orderId"} 支付网关未返回orderId
     * @errors {"code":400,"msg":"null_card"} 不存在的银行卡
     * @errors {"code":400,"msg":"already_binding"} 该卡已经绑定过了
     * @errors {"code":400,"msg":"update_card_failed"} 卡信息更新失败，数据库错误
     * @errors {"code":400,"msg":"update_user_failed"} 用户信息更新失败，数据库错误
     * @errors {"code":400,"msg":"rpc_failed:binding"} 网关未响应
     * @errors {"code":400,"msg":"name_out"} 实名长度超过20
     * @errors {"code":400,"msg":"phone_number_is_incorrect"} 手机号码不合法
     */
    public function bindcardAction()
    {
        $this->logMark = '#bindcard#userId:'.$this->user->userId.'#';
        $this->varLog('begin...');
        $forbid_bind = \Prj\Data\Config::get('forbid_bind');
        if($forbid_bind['forbid'])return $this->returnError($forbid_bind['notice']);
        //error_reporting(E_ALL);
        //var_log($this->_checkNickname('mj'), 'mj>>>>>>>>>>');
        //TODO: 加验证码 防止频繁提交
        $this->user->load();
        var_log($this->user->userId, 'userId>>>>>>>>>>');
        $cmd = $this->_request->get('cmd', 'binding');
        $this->_view->assign('returnUrl', $this->_request->get('returnUrl'));
        //$this->_view->assign('returnUrl', $this->_request->get('returnUrl'));
        $frm = \Sooh\Base\Form\Broker::getCopy('default')
            ->init(\Sooh\Base\Tools::uri(), 'get', \Sooh\Base\Form\Broker::type_c);
        $frm->addItem('bankId', \Sooh\Base\Form\Item::factory('银行', 'icbc', \Sooh\Base\Form\Item::text)->initMore(new \Sooh\Base\Form\Options(\Prj\Consts\Banks::$enums, '不限')))
            ->addItem('bankCard', \Sooh\Base\Form\Item::factory('卡号', '6222021304002871932', \Sooh\Base\Form\Item::text))
            ->addItem('realName', \Sooh\Base\Form\Item::factory('姓名', '汤高航', \Sooh\Base\Form\Item::text))
            ->addItem('phone', \Sooh\Base\Form\Item::factory('手机号', '13262798028', \Sooh\Base\Form\Item::text))
            ->addItem('idCardType', \Sooh\Base\Form\Item::factory('证件类型', '1', \Sooh\Base\Form\Item::select)->initMore(new \Sooh\Base\Form\Options(\Prj\Consts\IdCardType::$enums, '不限')))
            ->addItem('idCardSn', \Sooh\Base\Form\Item::factory('证件号码', '340823199311284918', \Sooh\Base\Form\Item::text))
            ->addItem('ticket', \Sooh\Base\Form\Item::factory('标识', '123', \Sooh\Base\Form\Item::text))
            ->addItem('orderId', \Sooh\Base\Form\Item::factory('订单号', '', \Sooh\Base\Form\Item::text))
            ->addItem('smsCode', \Sooh\Base\Form\Item::factory('验证码', '123', \Sooh\Base\Form\Item::text))
            ->addItem('returnUrl', $this->_request->get('returnUrl'))
            ->addItem('cmd', \Sooh\Base\Form\Item::factory('指令', $cmd, \Sooh\Base\Form\Item::text));

        //TODO:oauth 安全校验
        //。。。

        $frm->fillValues();

        $this->_view->assign('returnUrl', $this->_request->get('returnUrl'));
        // $this->_view->assign('returnUrlbindcard', $this->_request->get('returnUrl'));
        $inputs = $frm->getFields();
        if (mb_strwidth($inputs['realName']) > 33) {
            return $this->returnError(\Prj\Lang\Broker::getMsg('user.realname_long_wrong'));
        }

        //提交表单
        if (!empty($inputs['cmd'])) {
            //\Sooh\Base\Ini::getInstance()->viewRenderType('json');
            //如果已经有姓名了再绑第二张卡就不用姓名身份证和支付密码了
            $inputs['realName'] = $nickname = $this->user->getField('nickname');
            $inputs['idCardSn'] = $idCardSn = $this->user->getField('idCard');
            if ($this->_checkNickname($nickname)) {
                var_log($this->_checkNickname($nickname), '$this->_checkNickname($nickname)>>>>>>>>>>>>');
                if (!empty($inputs['realName'])) {
                    if ($inputs['realName'] != $nickname) {
                        var_log($inputs['realName'] . '/' . $nickname, 'error>>>');
                        return $this->returnError(\Prj\Lang\Broker::getMsg('user.error_realname')); //error_name
                    }
                } else {
                    $inputs['realName'] = $nickname;
                }
            }

            if(!empty($idCardSn)){
                if (!empty($inputs['idCardSn'])) {
                    if ($inputs['idCardSn'] != $idCardSn) {
                        return $this->returnError(\Prj\Lang\Broker::getMsg('user.error_idCardSn')); //error_idCardSn
                    }
                } else {
                    $inputs['idCardSn'] = $idCardSn;
                }
            }

            //调用绑卡网关
            if (\Sooh\Base\Ini::getInstance()->get('noGW')) {
                $rpc = self::getRpcDefault('PayGW'); //debug
            } else {
                $rpc = \Sooh\Base\Rpc\Broker::factory('PayGW');
            }
            $sys = \Lib\Services\PayGW::getInstance($rpc);
            //绑卡第一步
            if ($inputs['cmd'] == 'binding') {
                //身份证唯一性验证
                if($inputs['idCardSn']){
                    $idCard = \Prj\Data\IdCard::check($inputs['idCardSn'],$this->user->userId);
                    if(empty($idCard) && \Prj\Data\Config::get('idcardUnique')){
                        return $this->returnError('该身份证已经被使用');
                    }
                }
                $result = $this->_bindCard($inputs, $sys);
                if (!empty($result)) return $this->returnError($result);
            } //绑卡第二步
            elseif ($inputs['cmd'] == 'bindingcode') {
                $result = $this->_bindCardCode($inputs, $sys);
                if (!empty($result)) return $this->returnError($result);
            } else {
                return $this->returnError(\Prj\Lang\Broker::getMsg('system.arg_error'));
            }
        }
        return $this->returnOK();
    }

    //实名认证过：true    没有认证：false
    protected function _checkNickname($nickname)
    {
        $preg = "/^[\x{4e00}-\x{9fa5}]+$/u"; //中文汉字匹配 utf-8
        return (preg_match($preg, $nickname)) ? true : false;
    }

    /**
     * 绑卡第一步 提交资料
     * By Hand
     */
    protected function _bindCard($inputs, $sys)
    {
        \Sooh\Base\Session\Data::getInstance()->set('snMark',['bindCard'=>[]]);
        if (\Prj\IdCard::getAge($inputs['idCardSn']) < 18) {
            return '您的年龄未满18岁，无法接受小虾的服务';
        }

        $userId = $this->user->userId;

        if(empty($inputs['bankId']))return 'bankId_miss';
        if(empty($inputs['bankCard']))return 'bankCard_miss';

        //手机号验证
        $rules = [
            'phone' => [\Lib\Misc\InputValidation::$define['phone'], \Prj\Lang\Broker::getMsg('account.phone_number_is_incorrect')],
        ];
        if (\Lib\Misc\InputValidation::validateParams(['phone'=>$inputs['phone']], $rules) === false) {
            return \Lib\Misc\InputValidation::$errorMsg;
        }


        //卡是否已经存在
        $rs = \Prj\Data\BankCard::repeatCheck($userId, $inputs['bankId'], $inputs['bankCard']);
        if (!empty($rs)) {
            var_log($rs, 'repeatCard>>>>>>>>>>');
            return \Prj\Lang\Broker::getMsg('user.card_exist');//'card_exist';
        }
        //添加流水
        $payCorp = 0;
        $o = \Prj\Data\BankCard::addCard($this->user->userId, $inputs['bankId'], $inputs['bankCard'], $payCorp, $inputs['realName'], $inputs['phone'], $inputs['idCardType'], $inputs['idCardSn']);
        if (empty($o)) {
            return \Prj\Lang\Broker::getMsg('user.addCard_failed');//'addCard_failed';
        }

        $orderId = $o->getPKey()['orderId'];


        //添加注册手机号
        $regPhone = $this->user->getField('phone');
        try {
            $data = [$userId,$inputs['bankId'],$inputs['realName'],$inputs['bankCard'],$inputs['phone']];
            $ret = \Lib\Services\PayGWCmd::sendToPayGWCmd('bind',$data);
            $this->_view->assign('retAll', ['ret' => 'ok', 'got' => $ret]);//出现多个assign中的retAll字段重复，修改如下突出唯一识别
            $this->_view->assign('bindCardretAll', ['ret' => 'ok', 'got' => $ret]);

        } catch (\Sooh\Base\ErrException $e) {
            $this->loger->error('visit gateway failed when bind-card:' . $e->getMessage());
            $code = $e->getCode();
            if ($code == 400) {
                $this->_view->assign('retAll', ['ret' => $e->getMessage(), 'got' => $e->customData]);
                try{
                    $o->setField('resultMsg','rpc:'.$e->getMessage());
                    $o->update();
                }catch (\ErrorException $e){
                    //todo nothing
                }
                return $e->getMessage();
            } elseif ($code == 500) {
                $this->_view->assign('retAll', ['ret' => $e->getMessage(), 'got' => $e->customData]);
                error_log('error:' . '500');
                return $e->getMessage();
            }
            return \Prj\Lang\Broker::getMsg('system.gw_error');//'gw_error';
        }

        if($ret['code']!=200){
            if($ret['code'] == 500)$ret['msg'] = '系统正忙,请稍后重试';
            $error = $ret['msg']?$ret['msg']:'网关未知错误';
            $o->setField('resultMsg','rpc:'.$error);
            $o->update();
            return $ret['msg'];
        }else{
            \Sooh\Base\Session\Data::getInstance()->set('snMark',['bindCard'=>$ret['data']['serialNo'],'orderId'=>$orderId]);
        }

        if (empty($ret['payCorp'])) {
            $ret['payCorp'] = 101;
        }
        try {
            $o->setField('payCorp', $ret['payCorp']);
            $o->update();
        } catch (\ErrException $e) {
            $this->loger->error('update payCorp failed when bind-card:' . $e->getMessage());
            return \Prj\Lang\Broker::getMsg('system.db_error');
        }
        return;
    }

    /**
     * 绑卡第二步 短信验证码提交
     * @param $inputs
     * @param string $sys
     * @return mixed|void
     * @throws ErrorException
     * @throws Exception
     * @throws \Sooh\Base\ErrException
     */
    protected function _bindCardCode($inputs, $sys = '')
    {
        $sn = \Sooh\Base\Session\Data::getInstance()->get('snMark')['bindCard'];
        $orderId = \Sooh\Base\Session\Data::getInstance()->get('snMark')['orderId'];
        $this->logMark = 'bindCard#userId:'.$this->user->userId.'#orderId:'.$orderId;
        $this->user->load();
        if (empty($inputs['smsCode'])) {
            return \Prj\Lang\Broker::getMsg('user.no_smsCode');
        }
        //var_log($sn,'绑卡获取的sn>>>');
        error_log('warning#'.$orderId.'#从session里面获取卡号成功...');
        if(empty($orderId)){
            return '系统正忙,请重新获取验证码';
        }

        //身份证检查
        $bindCard = \Prj\Data\BankCard::getCopy($orderId);
        $bindCard->load();
        if(!$bindCard->exists())return $this->returnError('系统错误,请稍后重试');
        $inputs['idCardSn'] = $bindCard->getField('idCardSN');

        $idCard = \Prj\Data\IdCard::check($inputs['idCardSn'],$this->user->userId);
        if(empty($idCard) && \Prj\Data\Config::get('idcardUnique')){
            return '该身份证已经被使用';
        }
        if($idCard)$idCard->setField('statusCode',0);

        $card = \Prj\Data\BankCard::getCopy($orderId);
        $card->load();
        if (!$card->exists()) return 'null_card';

        try {
            $data = [$this->user->userId,$sn,$inputs['smsCode']];
            $ret = \Lib\Services\PayGWCmd::sendToPayGWCmd('bindAdvance',$data);
            $ret['orderId'] = $orderId;
            $this->_view->assign('retAll', ['ret' => 'ok', 'got' => $ret]);//出现多个assign中的retAll字段重复，修改如下突出唯一识别
            $this->_view->assign('bindCardCoderetAll', ['ret' => 'ok', 'got' => $ret]);
            $this->varLog('send to gw success');
        } catch (\Sooh\Base\ErrException $e) {
            $bindCard->setField('resultMsg',$e->getMessage());
            $bindCard->update();
            $this->varLog('#ERROR#'.'send to gw failed');
            $this->loger->error('visit gateway failed when bind-card-smscode:' . $e->getMessage());
            $code = $e->getCode();
            if ($code == 400) {
                $this->_view->assign('retAll', ['ret' => $e->getMessage(), 'got' => $e->customData]);
                return $e->getMessage();
            } elseif ($code == 500) {
                $this->_view->assign('retAll', ['ret' => $e->getMessage(), 'got' => $e->customData]);
                return $e->getMessage();
            }
            return \Prj\Lang\Broker::getMsg('system.gw_error');
        }

        if($ret['code']!=200){
            if($ret['code'] == 500)$ret['msg'] = '系统正忙,请稍后重试';
            if($ret['msg'] == '已绑卡成功'){

            }else{
                $error = $ret['msg']?$ret['msg']:'rpc_failed:bindingCode';
                $bindCard->setField('resultMsg',$error);
                $bindCard->update();
                return $error;
            }
        }

        //验证以后
        $ret['cardId'] = $ret['data']['cardId'];
        if (empty($ret['cardId'])) {
            $this->loger->error('get cardId failed when bindcard');
            $this->varLog('gw return cardId miss');
            return \Prj\Lang\Broker::getMsg('system.gw_error');
        }

        if (empty($ret['orderId'])) {
            $this->loger->error('get orderId failed when bindcard');
            return \Prj\Lang\Broker::getMsg('system.gw_error');
        }

        $cardId = $card->getField('cardId');
        if ($card->getField('statusCode') == \Prj\Consts\BankCard::enabled && !empty($cardId)) {
            return \Prj\Lang\Broker::getMsg('user.already_binding');//return 'already_binding';
        }
        $card->setField('statusCode', \Prj\Consts\BankCard::enabled);
        $card->setField('cardId', $ret['cardId']);
        $card->setField('resultTime', \Sooh\Base\Time::getInstance()->ymdhis());
        try {
            $card->update();
            $this->varLog('save to db success');
        } catch (\ErrException $e) {
            $this->loger->error('update bindcard failed when bind-card:' . $e->getMessage());
            $this->varLog('#ERROR#'.'save to db failed');
            return \Prj\Lang\Broker::getMsg('system.update_card_failed');//return 'update_card_failed';
        }
        //存身份证
        if(\Prj\Data\Config::get('idcardUnique') && isset($idCard)){
            try{
                $idCard->update();
                $this->varLog('save idCard success');
            }catch (\ErrorException $e){
                $this->varLog('#ERROR#'.'save idCard failed');
                $this->loger->error("[error]身份证表更新失败 id:".$inputs['idCardSn']);
            }
        }

        $error = $this->_updateUser($card); //更新用户信心
        if (!empty($error)) {
            $card->updStatus(\Prj\Consts\BankCard::abandon)->update();
            $this->loger->error('update user failed when bindcard');
            if(\Prj\Data\Config::get('idcardUnique') && isset($idCard)){
                $idCard->setField('statusCode',-1);
                $idCard->update();
            }
            return $error;
        }
        error_log('warning#'.$ret['orderId'].'#绑卡结束 The End ...');
        \Sooh\Base\Session\Data::getInstance()->set('snMark',[]);
        return;
    }

    /**
     * 绑卡成功后更新用户信息
     */
    protected function _updateUser(\Prj\Data\BankCard $card)
    {
        $this->varLog('user update begin...');
        $this->user->load();
        $nickname = $this->user->getField('nickname');
        $userChanged = false;

        if (!$this->_checkNickname($nickname)) {
            $this->user->setField('nickname', $card->getField('realName'));
            $userChanged = true;
        }
        if ($card->getField('idCardType') == \Prj\Consts\IdCardType::shenFenZheng && $this->user->getField('idCard') == '') {
            $this->user->setField('idCard', $card->getField('idCardSN'));
            var_log('[warning]idCard:'.$card->getField('idCardSN'));
            $userChanged = true;
        }

        if (($this->user->getField('ymdBindcard') == 0 && \Prj\Consts\BankCard::enabled == $card->getField('statusCode'))||$this->user->getField('isSuperUser')) {
            $userChanged = true;
            $this->user->setField('ymdBindcard', \Sooh\Base\Time::getInstance()->YmdFull);
            $this->varLog('set ymdBindcard...');
            //首绑送红包
            $ret = \Lib\Services\EvtBinding::getInstance(self::getRpcDefault('EvtBinding'))->giveVouchersWherFirstBind($this->user->userId);
        }

        try {
            if ($userChanged) {
                $this->user->update();
                $this->varLog('user update success');
            }
        } catch (Exception $ex) {
            $this->loger->error('update user failed when bind-card:' . $ex->getMessage());
            $this->varLog('#ERROR#'.'user update failed');
            return \Prj\Lang\Broker::getMsg('system.update_user_failed');//return 'update_user_failed';
        }

        if(!empty($ret)){
            $this->varLog('send firstBind redPacket success');
            try {
                \Prj\Message\Message::run(
                    ['event' => 'red_invate_packet', 'num_packet' => 1, 'private_gift' => $ret['amount']/100, 'num_deadline' => 48, 'brand' => \Prj\Message\Message::MSG_BRAND],
                    ['userId' => $this->user->userId, 'phone' => $this->user->getField('phone')]
                );
                $this->varLog('redPacket push success');
            } catch (\ErrorException $e) {
                $this->varLog('#ERROR#'.'redPacket push failed');
            }

            $this->_view->assign('award',$ret);
            $this->_view->assign('redPacket',$ret);
        }

        $this->_view->assign('CardBound', ["orderId" => current($card->getPkey()), "status" => \Prj\Consts\BankCard::enabled]);
        return;
    }

    /**
     * 充值接口
     * @input amount int  充值金额
     * @input returnUrl string  回调页面
     * @output code:200  orderId:充值单号  redirectUrl:收银台页面
     * @errors no_bindcard 未绑卡
     * @errors addOrder_failed 服务器忙
     * @errors server_busy 服务器忙
     * @errors url_parse_failed 系统错误
     * @errors gw_error 网关错误
     */
    public function rechergeAction(){
        $forbid_recharge = \Prj\Data\Config::get('forbid_recharge');
        if($forbid_recharge['forbid'])return $this->returnError($forbid_recharge['notice']);
        $myCards = $this->_myCards();
        if(empty($myCards))return $this->returnError('no_bindcard');
        $myCard = current($myCards);

        $frm = \Sooh\Base\Form\Broker::getCopy('default')
            ->init(\Sooh\Base\Tools::uri(), 'get', \Sooh\Base\Form\Broker::type_c);
        $frm->addItem('amount', \Sooh\Base\Form\Item::factory('充值额', '', \Sooh\Base\Form\Item::text))
            ->addItem('returnUrl', \Sooh\Base\Form\Item::factory('回调页面', '', \Sooh\Base\Form\Item::text));
        $frm->fillValues();

        $fields = $frm->getFields();
        //提交表单
        if($fields){
            $amount = $fields['amount'] - 0;
            $userId = $this->user->userId;
            $this->logMark = 'recharge#userId:'.$userId.'#amount:'.$amount;

            //验证表单
            if($amount<1){return $this->returnError('单笔充值最低0.01元');}
            $ordersOfCharge = \Prj\Data\Recharges::addOrders($this->user->userId, $amount, $myCard['bankId'], $myCard['bankCard']);
            if(empty($ordersOfCharge))return $this->returnError('addOrder_failed');
            $orderId = current($ordersOfCharge->getPKey());
            $this->logMark.="#orderId:".$userId;
            //发送到网关
            try{
                $returnUrl = $this->getReturnUrl(['h5orderdetailId'=>$orderId]);
                $data = [$orderId,$userId,$amount,$returnUrl];
                $ret = \Lib\Services\PayGWCmd::sendToPayGWCmd('trade_recharge',$data);
                $this->varLog('gw return success ...');
            }catch (\ErrorException $e){
                $this->varLog('#ERROR#'.'gw failed '.$e->getMessage());
                $this->saveErrorMsg($ordersOfCharge,$e->getMessage());
                return $this->returnError($e->getMessage());
            }
            //处理网关结果
            if($ret['code']!=200){
                if($ret['code'] == 500)$ret['msg'] = '系统正忙,请稍后重试';
                $this->varLog('gw tell failed '.$ret['msg']);
                $this->saveErrorMsg($ordersOfCharge,$ret['msg']?$ret['msg']:'gw_error');
                return $this->returnError($ret['msg']?$ret['msg']:'gw_error');
            }else{
                $this->varLog('gw tell success ');
                $url = \Prj\Tool\Func::getUrlFromSinaHtml($ret['data']['htmlContent']);
                if(empty($url)){
                    $this->saveErrorMsg($ordersOfCharge,'url_parse_failed');
                    return $this->returnError('url_parse_failed');
                }
                try{
                    $ordersOfCharge->setField('orderStatus',\Prj\Consts\OrderStatus::waiting);
                    $ordersOfCharge->setField('exp','跳转至收银台');
                    $ordersOfCharge->setField('redirectUrl',$url);
                    $ordersOfCharge->update();
                    $this->varLog('orders update success');
                }catch (\ErrorException $e){
                    $this->varLog('#ERROR#'.'orders update failed '.$e->getMessage());
                    return $this->returnError($e->getMessage());
                }
                $data = [
                    'orderId'=>$orderId,
                    'redirectUrl'=>$url
                ];
                $this->_view->assign('recharge',$data);
                return $this->returnOK('success');
            }
        }else{
            $this->returnOK();
        }
    }

    /**
     * 用户解绑银行卡(复制过来的)
     * @input string cmd first:解绑第一步获取验证码/second:解绑第二步
     * @input string smscode 第一步获取的验证码
     * @errors {code:400,msg:'error_time'} 非服务时间
     * @errors {code:400,msg:'wallet_not_null'} 钱包余额不为0
     * @errors {code:400,msg:'orders_not_null'} 在投资金不为0
     * @errors {code:400,msg:'freezeAmount_not_null'} 冻结金额不为0
     * @errors {code:400,msg:'bankcard_miss'} 未知的银行卡
     * @errors {code:400,msg:'idCard_miss'} 未知的身份证
     * @errors {code:400,msg:'void_smscode'} 无效的验证码
     * @errors {code:400,msg:'rpc_error'} 网关错误
     * @errors {code:400,msg:'db_error'} 数据库错误
     */
    public function unbindCardAction(){
        $cmd = $this->_request->get('cmd');
        $unbindTime = \Prj\Data\Config::get('unbind_time');
        $this->_view->assign('unbindTime',$unbindTime);
        if(empty($unbindTime) || !is_array($unbindTime))return $this->returnError('error_time');

        $beginhi = sprintf('%04d',$unbindTime[0]);
        $endhi = sprintf('%04d',$unbindTime[1]);
        if(date('Hi')<$beginhi || date('Hi')>$endhi){
            return $this->returnError('error_time');
        }
        error_log('unbindCard#time#userId:'.$this->user->userId.'#'.$beginhi.'<'.date('Hi').'<'.$endhi);

        $userId = $this->user->userId;
        $this->user->load();
        $user = $this->user;
        $cards = \Prj\Data\BankCard::getList($userId,['statusCode'=>\Prj\Consts\BankCard::enabled]);
        var_log($cards , 'cards >>> ');
        $cardArr = current($cards);
        if(empty($cardArr))return $this->returnError('no_card');
        if(in_array($cmd,['first','second'])){
            $wallet = $user->getField('wallet');
            $holdingAssets = \Prj\Data\Investment::getHoldingAssetsByUserId($userId);
            $freezeAmount = $this->freezeAmount();
            if($wallet>0){
                $this->_view->assign('error',['wallet'=>$wallet]);
                return $this->returnError('wallet_not_null');
            }
            if($holdingAssets > 0){
                $this->_view->assign('error',['holdingAssets'=>$holdingAssets,'freezeAmount'=>$freezeAmount]);
                return $this->returnError('orders_not_null');
            }
            if($freezeAmount > 0 ){
                $this->_view->assign('error',['holdingAssets'=>$holdingAssets,'freezeAmount'=>$freezeAmount]);
                return $this->returnError('freezeAmount_not_null');
            }
            error_log('unbindCard#'.$userId.'#'.$cmd.'#>>>');
            try{
                if($cmd == 'first'){
                    error_log('unbindCard# first ...');
                    $this->unbindCardFirst();
                }elseif($cmd == 'second'){
                    error_log('unbindCard# second ...');
                    $this->tmp['smscode'] = $this->_request->get('smscode');
                    $this->tmp['cardOrderId'] = $cardArr['orderId'];
                    $this->tmp['idCardId'] = $cardArr['idCardSN'];
                    $this->unbindCardSecond();
                    //todo 多设备踢人...
                    error_log('unbindCard# push begin ...');
                    $pushContent = json_encode(['type' => 'kickoutByUnbind', 'msg' => '您的银行卡绑定信息已更改，请重新登录！']);
                    $this->loger->sarg1 = json_encode(['all', $userId, null, $pushContent]);
                }else{
                    return $this->returnError('db_error');
                }
            }catch (\ErrorException $e){
                return $this->returnError($e->getMessage());
            }
            return $this->returnOK();
        }else{
            $cardArr['bankCard'] = substr_replace($cardArr['bankCard'],str_pad('',strlen($cardArr['bankCard'])-8,'*'),4,-4);
            $unsetArr = ['idCardSN','realName','userId','orderId','payCorp','isDefault','cardId'];
            foreach($unsetArr as $v){
                unset($cardArr[$v]);
            }
            $this->_view->assign('cardInfo',$cardArr);
        }
        return $this->returnOK();
    }

    protected function unbindCardFirst(){
        $userId = $this->user->userId;
        $method = 'unbind';
        $data = [
            $userId
        ];
        try{
            $ret = \Lib\Services\PayGWCmd::sendToPayGWCmd($method,$data);
        }catch (\ErrorException $e){
            $this->throwError($e->getMessage());
        }
        if($ret['code']==200){
            $serialNo = $ret['data']['serialNo'];
            if(empty($serialNo))$this->throwError('rpc_error');
            \Sooh\Base\Session\Data::getInstance()->set('snMark',['unbindCard_serialNo'=>$serialNo]);
        }else{
            if($ret['code'] == 500)$ret['msg'] = '系统正忙,请稍后重试';
            $this->throwError($ret['msg']);
        }
    }

    protected function unbindCardSecond(){
        $user = $this->user;
        $userId = $user->userId;
        $cardOrderId = $this->tmp['cardOrderId'];
        $log = '>>>#unbindCard#userId:'.$userId.'#cardOrderId:'.$cardOrderId.'#';
        error_log($log.'beging>>>');
        $idCardId = $this->tmp['idCardId'];
        $serialNo = \Sooh\Base\Session\Data::getInstance()->get('snMark')['unbindCard_serialNo'];
        if(empty($serialNo))$this->throwError('void_smscode');
        if(empty($this->tmp['smscode']))$this->throwError('void_smscode');
        $method = 'unbindAdvance';
        $data = [
            $serialNo ,$userId , $this->tmp['smscode']
        ];
        try{
            $ret = \Lib\Services\PayGWCmd::sendToPayGWCmd($method,$data);
        }catch (\ErrorException $e){
            $this->throwError($e->getMessage());
        }
        if($ret['code']==200){
            $bankcard = \Prj\Data\BankCard::getCopy($cardOrderId);
            $bankcard->load();
            if(!$bankcard->exists())$this->throwError('bankcard_miss');
            $idCard = \Prj\Data\IdCard::getCopy($idCardId);
            $idCard->load();
            if(!$idCard->exists()){
                $noId = 1;  //没有身份证
            }
            if(!$noId)$idCard->setField('statusCode',-1);
            $bankcard->setField('statusCode',\Prj\Consts\BankCard::disabled);
            $bankcard->setField('resultMsg','serialNo_'.$serialNo);
            $bankcard->setField('unBindTime',date('YmdHis'));

            $userInit = ['tradePwd'=>$user->getField('tradePwd'),'salt'=>$user->getField('salt')];
            $user->setField('tradePwd','');
            $user->setField('salt','');

            try{
                if(!$noId)$idCard->update();
                error_log($log.'idCard update >>>');
                try{
                    $bankcard->update();
                    error_log($log.'bankcard update >>>');
                    try{
                        $user->update();
                        error_log($log.'user update >>>');
                    }catch (\ErrorException $e){
                        if(!$noId)$idCard->setField('statusCode',0);
                        if(!$noId)$idCard->update();
                        $bankcard->update('statusCode',\Prj\Consts\BankCard::enabled);
                        $bankcard->update();
                        error_log('unbindCard#'.$userId.'#'.$e->getMessage());
                        $this->throwError($e->getMessage());
                    }
                }catch (\ErrorException $e){
                    if(!$noId)$idCard->setField('statusCode',0);
                    if(!$noId)$idCard->update();
                    error_log('unbindCard#'.$userId.'#'.$e->getMessage());
                    $this->throwError($e->getMessage());
                }
            }catch (\ErrorException $e){
                error_log('unbindCard#'.$userId.'#'.$e->getMessage());
                $this->throwError($e->getMessage());
            }
            error_log($log.'success >>>');
        }else{
            if($ret['code'] == 500)$ret['msg'] = '系统正忙,请稍后重试';
            $this->throwError($ret['msg']);
        }
    }

    /**
     * 冻结金额
     */
    protected function freezeAmount(){
        $userId = $this->user->userId;
        $tally = \Prj\Data\WalletTally::getCopy($userId);
        $db = $tally->db();
        $tbname = $tally->tbname();
        $rs = $db->getRecord($tbname,'sum(nAdd) as amount,sum(ext) as ext,sum(poundage) as poundage',['freeze'=>1,'statusCode'=>\Prj\Consts\Tally::status_new,'userId'=>$userId]);
        return abs($rs['amount']-0)+abs($rs['ext']) ;
    }

    protected function saveErrorMsg($order,$msg){
        $order->setField('orderStatus',\Prj\Consts\OrderStatus::abandon);
        $order->setField('exp',$msg);
        try{
            $order->update();
            $this->varLog('error msg save');
        }catch (\ErrorException $e){
            $this->varLog('#ERROR#'.$e->getMessage());
        }
    }

    protected function _myCards()
    {
        $myCards = \Prj\Data\BankCard::loopAll(['userId' => $this->user->userId, 'statusCode' => \Prj\Consts\BankCard::enabled]);
        return $myCards;
    }

    protected function throwError($message,$code = 400){
        throw new \ErrorException($message,$code);
    }

}
