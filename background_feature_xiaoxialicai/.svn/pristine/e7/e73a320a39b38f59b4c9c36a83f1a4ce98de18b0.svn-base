<?php
namespace Lib\Services;
/**
 * rpc 服务器管理
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class PayGWCmd {
	protected static $_instance=null;
    protected $loger = null;
    protected $arr = [];

    public static function sendToPayGWCmd($method , $data){
        $rpc = \Sooh\Base\Ini::getInstance()->get('noGW') ? null : \Sooh\Base\Rpc\Broker::factory('PayGWCmd');
        $sys = \Lib\Services\PayGWCmd::getInstance($rpc , true);
        return call_user_func_array([$sys, $method], $data);
    }
	/**
	 * 
	 * @param \Sooh\Base\Rpc\Broker $rpcOnNew
	 * @return PayGWCmd
	 */
	public static function getInstance($rpcOnNew=null , $new = false)
	{
		if(self::$_instance===null || $new){
			$c = get_called_class();
			self::$_instance = new $c;
			self::$_instance->rpc = $rpcOnNew;
		}
		return self::$_instance;
	}
	/**
	 * 
	 * @var \Sooh\Base\Rpc\Broker
	 */
	protected $rpc;

    /**
     * 解绑银行卡
     * @param $userId 用户id
     * @param $paygwId 绑卡时支付网关返回的id
	 *  
     * @return string 错误信息
     */
    public function unbindBankCard($userId,$paygwId){
		error_log("paygw-service:unbindBankCard($userId,$paygwId)");
        if($this->rpc!==null){
			$userIP = \Sooh\Base\Tools::remoteIP();
            return $this->rpc->initArgs(array('identityId'=>$userId,'cardId'=>$paygwId,'clientIp'=>$userIP,'extendParam'=>''))
					->resetServiceName('account')->send(__FUNCTION__);
        }else{
            $rand = rand(1,2);
            switch ($rand){
                case 1: 	return '';
                case 2: 	return 'NO_BANK_CARD_INFO - 无相关银行卡信息';
            }
        }

    }

    /**
     * 解绑银行卡第一步
     * @param $userId
     * @return array|mixed
     * @throws \Sooh\Base\ErrException
     */
    public function unbindCard($userId){
        error_log("paygw-service:unbindBankCard($userId)");
        if($this->rpc!==null){
            $userIP = \Sooh\Base\Tools::remoteIP();
            $data = [
                'otherUserId'=>$userId,
                'userIp'=>$userIP
            ];
            return $this->rpc->initArgs($data)
                ->resetServiceName('platform')->send(__FUNCTION__);
        }else{
            $rand = 1;
            switch ($rand){
                case 1: 	return ['code'=>200,'data'=>['serialNo'=>rand(10000,99999)]];
                case 2: 	return ['code'=>400,'msg'=>'谜之失败'];
            }
        }
    }

    /**
     * 解绑银行卡第一步
     * @param $userId
     * @return array|mixed
     * @throws \Sooh\Base\ErrException
     */
    public function unbind($userId){
        error_log("paygw-service:unbindBankCard($userId)");
        if($this->rpc!==null){
            $userIP = \Sooh\Base\Tools::remoteIP();
            $data = [
                'outUserId'=>$userId,
                'userIp'=>$userIP
            ];
            return $this->rpc->initArgs($data)
                ->resetServiceName('bankCard')->send(__FUNCTION__);
        }else{
            $rand = 1;
            switch ($rand){
                case 1: 	return ['code'=>200,'data'=>['serialNo'=>rand(10000,99999)]];
                case 2: 	return ['code'=>400,'msg'=>'谜之失败'];
            }
        }
    }

    /**
     * 解绑银行卡第二步
     * @param $serialNo
     * @param $smscode
     * @return array|mixed
     * @throws \Sooh\Base\ErrException
     */
    public function unbindCardAdvance($serialNo,$smscode){
        error_log("paygw-service:unbindBankCard($serialNo,$smscode)");
        if($this->rpc!==null){
            $userIP = \Sooh\Base\Tools::remoteIP();
            $data = [
                'serialNo'=>$serialNo,
                'validCode'=>$smscode,
            ];
            return $this->rpc->initArgs($data)
                 ->resetServiceName('platform')->send(__FUNCTION__);
        }else{
            $rand = 1;
            switch ($rand){
                case 1: 	return ['code'=>200];
                case 2: 	return ['code'=>400,'msg'=>'谜之失败'];
            }
        }

    }

    /**
     * 解绑银行卡第二步
     * @param $serialNo
     * @param $smscode
     * @return array|mixed
     * @throws \Sooh\Base\ErrException
     */
    public function unbindAdvance($serialNo,$userId,$smscode){
        error_log("paygw-service:unbindBankCard($serialNo,$userId,$smscode)");
        if($this->rpc!==null){
            $userIP = \Sooh\Base\Tools::remoteIP();
            $data = [
                'serialNo'=>$serialNo,
                'validCode'=>$smscode,
                'outUserId'=>$userId
            ];
            return $this->rpc->initArgs($data)
                ->resetServiceName('bankCard')->send(__FUNCTION__);
        }else{
            $rand = 1;
            switch ($rand){
                case 1: 	return ['code'=>200];
                case 2: 	return ['code'=>400,'msg'=>'谜之失败'];
            }
        }

    }

    /**
     * 解冻用户的金额
     * @param $ordersId
     * @param $userId
     * @return array|mixed
     * @throws \Sooh\Base\ErrException
     */
    public function unfreezeBalance($ordersId,$userId){
        error_log(__METHOD__."#paygw-service#($ordersId,$userId)");
        $data = [
            'freezeSerialNo'=>$ordersId,
            'otherUserId'=>$userId,
            'summary'=>'购标解冻',
            //'ip'=>\Sooh\Base\Tools::remoteIP()?\Sooh\Base\Tools::remoteIP():0,
        ];
        if($this->rpc!==null){
            return $this->rpc->initArgs($data)
                ->resetServiceName('manager')->send(__FUNCTION__);
        }else{
            $rand = 2;
            switch ($rand){
                case 1: 	return ['code'=>200];
                case 2: 	return ['code'=>400,'msg'=>'谜之失败'];
            }
        }
    }

    /**
     * 扣款-通知网关
     * @param $orderId
     * @param $code
     * @param $msg
     */
    public function refund($orderId, $userId, $reason)
    {
        error_log('refund. args[orderId:' . $orderId . ';userId:' . $userId . ';reason:' . $reason . ']');
        if ($this->rpc !== null) {
            $map = [
                'collectTradeOrderId' => $orderId,
                'otherUserId'         => $userId,
                'explanation'         => $reason,
            ];
            $ret = $this->rpc->initArgs($map)->resetServiceName('manager')->send(__FUNCTION__);
            var_log($ret, '>>>>>>>>>>>>>>>>>>>>>>>.wangguan ret');
            return $ret;
        } else {
            $rand = rand(1, 2);
            switch ($rand) {
                case 1:
                    return [
                        'code' => 200,
                        'msg'  => 'success',
                        'data' => [
                            'serialNo' => 888 . \Sooh\Base\Time::getInstance()->timestamp() . mt_rand(1000, 9999),
                        ],
                    ];
                case 2:
                    return [
                        'code' => 201,
                        'msg'  => 'faile, please retry',
                    ];
            }
        }
    }

    /**
     * 扣款-网关回调
     * @param string $sn      序列号
     * @param string $orderId 订单ID
     * @param string $status  code[SUCCESS, FAIL]
     * @param string $msg     消息
     * @return string success成功，其他失败
     * @throws \ErrorException
     * @throws \Exception
     */
    public function refundResult($sn, $orderId, $status, $msg)
    {
        error_log('refundResult callback result. args[sn:' . $sn . ';orderId:' . $orderId . ';status:' . $status . ';msg:' . $msg . ']');
        $dbChargeback = \Prj\Data\Chargeback::getCopy($orderId);
        $dbChargeback->load();

        if ($dbChargeback->exists()) {
//            if ($sn != $dbChargeback->getField('sn')) {
//                return $this->_returnError('faile');
//            } else {
                $dbChargeback->setField('transTime', \Sooh\Base\Time::getInstance()->ymdhis());
                $dbChargeback->setField('serviceRet', $msg);
                $dbChargeback->setField('serviceCode', $status);
                $dbChargeback->setField('status', 8);
                $dbChargeback->update();

                $dbInvestment = \Prj\Data\Investment::getCopy($orderId);
                $dbInvestment->load();
                if ($dbInvestment->exists()) {
                    if (strtoupper($status) == 'SUCCESS') {
                        $dbInvestment->setField('chargeBackStatus', 8);
                        $dbInvestment->update();
                    } else if (strtoupper($status) == 'FAIL') {
                        $dbInvestment->setField('chargeBackStatus', 4);
                        $dbInvestment->update();
                    }
                }

                return $this->_returnOK('success');
//            }
        }
        return $this->_returnError('faile');
    }

    /**
     * 查询用户的新浪账户
     * @param $userId
     * @return array|mixed
     * @throws \Sooh\Base\ErrException
     */
    public function queryBalance($userId){
        error_log(__METHOD__."#paygw-service#($userId)");
        $data = [
            'otherUserId'=>$userId,
        ];
        if($this->rpc!==null){
            return $this->rpc->initArgs($data)
                ->resetServiceName('account')->send(__FUNCTION__); //注意配套新版的网关
        }else{
            $rand = 2;
            switch ($rand){
                case 1: 	return ['code'=>200];
                case 2: 	return ['code'=>400,'msg'=>'谜之失败'];
            }
        }
    }


    public function trans($sn,$waresId,$amountReal,$amountGift,$amountTotal,$managementTrans,$borrowerId,$borrowerName,$borrowerTunnel = 'auto'){
        error_log("paygw-service:".__CLASS__."($sn,$waresId,$amountReal,$amountGift,$amountTotal,$managementTrans,$borrowerId,$borrowerName,$borrowerTunnel)");
        if($this->rpc!==null){
            $userIP = \Sooh\Base\Tools::remoteIP();
            $data = [
                'sn'=>$sn,
                'waresId'=>$waresId,
                'amountReal'=>round($amountReal),
                'amountGift'=>round($amountGift),
                'amountTotal'=>round($amountTotal-$managementTrans), //todo 减去服务费后的总金额,即打款总金额
                'managementTrans'=>round($managementTrans),
                'borrowerId'=>$borrowerId,
                'borrowerName'=>$borrowerName,
                'borrowerTunnel'=>$borrowerTunnel,
            ];
            return $this->rpc->initArgs($data)
                ->resetServiceName('platform')->send(__FUNCTION__);
        }else{
            $rand = 1;
            switch ($rand){
                case 1: 	return ['code'=>200,'data'=>['serialNo'=>rand(10000,99999)]];
                case 2: 	return ['code'=>400,'msg'=>'谜之失败'];
            }
        }
    }

    public function getInvestList($waresId){
        $list = \Prj\Data\Investment::loopFindRecords(['waresId'=>$waresId,'orderStatus'=>\Prj\Consts\OrderStatus::waiting]);
        $keys = ['ordersId','userId','amount','amountExt'];
        foreach($list as $v){
            $tmp = [];
            foreach($keys as $vv){
                $tmp[$vv] = $v[$vv];
            }
            $newList[] = $tmp;
        }
        $this->_assign('list',$newList);
        return $this->_returnOK();
    }

    //todo ============================================= 新版接口 =======================================================

    protected $logMark = ''; //日志标记

    protected $logRand = 0; //日志随机数

    protected function varLog($msg){
        if($this->logRand == 0)$this->logRand = mt_rand(1000,9999);
        error_log("[$this->logRand]".$this->logMark.'#'.$msg);
    }

    protected function send($model,$func,$data){
        error_log($func.'#'.current($data).'#send data ...#'.json_encode($data));
        if($this->rpc!==null){
            $ret = $this->rpc->initArgs($data)
                ->resetServiceName($model)->send($func);
            error_log($func.'#'.current($data).'#get data ...#'.json_encode($ret));
            return $ret;
        }else{
            $rand = 1;
            $ok = ['code'=>200];
            if($func == 'bindAdvance'){
                $ok = ['code'=>200 , 'data'=>['cardId'=>888888]];
            }
            if($func == 'recharge'){
                $ok = ['code'=>200 , 'data'=>['serialNo'=>'123456']];
            }
            switch ($rand){
                case 1: 	return $ok;
                case 2: 	return ['code'=>400,'msg'=>'谜之失败'];
            }
        }
    }
    /**
     * 实名认证
     * @param $userId
     * @param $phone
     * @param $nickname
     * @param $idCard
     * @return array|mixed
     * @throws \Sooh\Base\ErrException
     */
    public function register($userId,$phone,$nickname,$idCard){
        $model = 'account';
        $data = [
            "outUserId" => $userId,
            "regPhone" => $phone,
            "regAccountName" => $nickname,
            "regAccountNo" => $idCard,
            "regIp" => \Sooh\Base\Tools::remoteIP(),
        ];
        return $this->send($model,__FUNCTION__,$data);
    }

    /**
     * 设置支付密码
     * @param $userId
     * @param $returnUrl
     * @return array|mixed
     * @throws \Sooh\Base\ErrException
     */
    public function setPayPwd($userId , $returnUrl){
        $desc = ['outUserId','returnUrl'];
        $model = 'account';
        $args = func_get_args();
        $data = [];
        array_walk($args,function($v,$k) use (&$data , $desc){
            $data[$desc[$k]] = $v;
        });
        return $this->send($model,__FUNCTION__,$data);
    }

    public function modifyPayPwd($userId , $returnUrl){
        $desc = ['outUserId','returnUrl'];
        $model = 'account';
        $args = func_get_args();
        $data = [];
        array_walk($args,function($v,$k) use (&$data , $desc){
            $data[$desc[$k]] = $v;
        });
        return $this->send($model,__FUNCTION__,$data);
    }

    public function findPayPwd($userId , $returnUrl){
        $desc = ['outUserId','returnUrl'];
        $model = 'account';
        $args = func_get_args();
        $data = [];
        array_walk($args,function($v,$k) use (&$data , $desc){
            $data[$desc[$k]] = $v;
        });
        return $this->send($model,__FUNCTION__,$data);
    }

    public function bind($userId,$bankId,$realName,$bankCard,$phone,$province = '',$city = '',$bankBranch = ''){
        $desc = ['outUserId','bankCode','bankAccountName','bankAccountNo','bankPhone','province','city','bankBranch','userIp'];
        $model = 'bankCard';
        $args = func_get_args();
        $data = [];
        array_walk($args,function($v,$k) use (&$data , $desc){
            $data[$desc[$k]] = $v;
        });
        $data['province'] = $data['province']?$data['province']:'上海市';
        $data['city'] = $data['city']?$data['city']:'上海市';
        $data['bankBranch'] = $data['bankBranch']?$data['city']:'上海市';
        $data['userIp'] = \Sooh\Base\Tools::remoteIP();
        return $this->send($model,__FUNCTION__,$data);
    }

    public function bindAdvance($userId,$sn,$smsCode){
        $desc = ['outUserId','serialNo','validCode'];
        $model = 'bankCard';
        $args = func_get_args();
        $data = [];
        array_walk($args,function($v,$k) use (&$data , $desc){
            $data[$desc[$k]] = $v;
        });
        return $this->send($model,__FUNCTION__,$data);
    }

    public function proxyAuth_query($userId){
        $desc = ['outUserId'];
        return $this->dealData($desc,func_get_args(),__FUNCTION__);
    }

    public function proxyAuth_set($userId , $returnUrl){
        $desc = ['outUserId','returnUrl'];
        return $this->dealData($desc,func_get_args(),__FUNCTION__);
    }

    public function trade_recharge($outTradeNo,$outUserId,$amount,$returnUrl){
        $desc = ['outTradeNo','outUserId','amount','returnUrl'];
        $this->otherArgs = ['userIp'=>\Sooh\Base\Tools::remoteIP(),"summary"=>'充值'];
        return $this->dealData($desc,func_get_args(),__FUNCTION__);
    }

    public function trade_withdraw($outTradeNo,$outUserId,$amount,$poundage,$returnUrl){
        $desc = ['outTradeNo','outUserId','amount','userFee','returnUrl'];
        $this->otherArgs = ['userIp'=>\Sooh\Base\Tools::remoteIP(),"summary"=>'提现'];
        return $this->dealData($desc,func_get_args(),__FUNCTION__);
    }

    public function trade_investment($ordersId,$waresId,$userId,$realAmount,$amountExt){
        $desc = ['outTradeNo','waresId','outUserId','realAmount','giftAmount'];
        $this->otherArgs = ['userIp'=>\Sooh\Base\Tools::remoteIP(),"summary"=>'投资_'.$waresId];
        return $this->dealData($desc,func_get_args(),__FUNCTION__);
    }

    public function trade_trans($sn,$waresId,$amountReal,$amountGift,$amountTotal,$managementTrans,$borrowerId,$borrowerName,$borrowerTunnel = 'true'){
        $desc = ['sn','waresId','amountReal','amountGift','amountTotal','managementTrans','borrowerId','borrowerName','borrowerTunnel'];
        return $this->dealData($desc,func_get_args(),__FUNCTION__);
    }

    public function repayment_confirm($sn,$waresId,$realAmount,$giftAmount,$servicePay){
        $desc = ['outTradeNo','waresId','realAmount','giftAmount','serviceAmount'];
        return $this->dealData($desc,func_get_args(),__FUNCTION__);
    }

    public function trade_payment($outTradeNo,$outUserId,$amount,$type = 'rebate'){
        $desc = ['outTradeNo','outUserId','amount','type'];
        $this->otherArgs = ['summary'=>'返利'];
        return $this->dealData($desc,func_get_args(),__FUNCTION__);
    }

    public function trade_unfreeze($waresId , $ordersId){
        $desc = ['waresId','outTradeNo'];
        return $this->dealData($desc,func_get_args(),__FUNCTION__);
    }

    public function funding_userIncome($ymd){
        $desc = ['fundingDate'];
        return $this->dealData($desc,func_get_args(),__FUNCTION__);
    }

    /**
     * 满哥太累了,接口两用,补贴息也用此接口
     * @param $batchId
     * @param $waresId
     * @param $confirmSN
     * @return array
     */
    public function repayment_returnFundBatch($batchId , $waresId , $confirmSN){
        $desc = ['batchId'];
        $this->otherArgs = ['list'=>['waresId'=>$waresId,'confirmSN'=>$confirmSN]];
        return $this->dealData($desc,func_get_args(),__FUNCTION__);
    }

    /**
     * 查询支付密码设置情况
     * @param $userId
     * @return array|mixed
     */
    public function queryPayPwdStatus($userId){
        $desc = ['outUserId'];
        $model = 'account';
        $args = func_get_args();
        $data = [];
        array_walk($args,function($v,$k) use (&$data , $desc){
            $data[$desc[$k]] = $v;
        });
        return $this->send($model,__FUNCTION__,$data);
    }

    protected function dealData($desc,$args,$funcs){
        list($model,$func) = explode('_',$funcs);
        $data = [];
        array_walk($args,function($v,$k) use (&$data , $desc){
            if($desc[$k])$data[$desc[$k]] = $v;
        });
        $data = array_merge($data,$this->otherArgs);
        return $this->send($model,$func,$data);
    }

    /**
     * 提现成功回调
     * withdrawResult
     * @input string orderId 订单号
     * @input string amount 金额
     * @input string ret 结果 sucess/failed
     * @input string msg 失败详情
     * @error [code:200,data:[code:400,msg='']]
     */
    public function withdrawResult($orderId='',$amount='',$status='',$msg='未知错误')
    {
        $ordersId = $orderId;
        $this->logMark = 'withdrawResult#'.$ordersId;
        $this->varLog("amount:$amount ret:$status msg:$msg");
        $withdraw = \Prj\Data\Recharges::getCopy($ordersId);
        $withdraw->load();
        if(!$withdraw->exists())return $this->_returnError('orderId_void');
        if($amount!=$withdraw->getField('amountAbs'))return $this->_returnError('error_amount');
        if(!in_array($status,['success','failed','accept']))return $this->_returnError('error_ret');
        if(\Prj\Consts\OrderStatus::done==$withdraw->getField('orderStatus'))return $this->_returnOK('order_finish');
        $amount = $withdraw->getField('amountAbs');
        $poundage = $withdraw->getField('poundage');
        $userId = $withdraw->getField('userId');
        $user = \Prj\Data\User::getCopy($userId);
        $user->load();
        $info = 'recharge failed orderId:'.$ordersId;
        if($status=='success')
        {
            //说明:解冻冻结金额,订单置为完成,发送消息
            if(\Prj\Consts\OrderStatus::waitingGW!=$withdraw->getField('orderStatus'))return $this->_returnError('error_status');
            //订单置为完成
            $withdraw->setField('orderStatus',\Prj\Consts\OrderStatus::done);
            $withdraw->setField('payTime',\Sooh\Base\Time::getInstance()->ymdhis());
            $withdraw->setField('exp','订单完成');
            //解冻冻结金额
            $this->_unfreezeTally($ordersId);
            //发送消息
            $user = \Prj\Data\User::getCopy($withdraw->getField('userId'));
            $user->load();
            $phone = $user->getField('phone');
            try {
                \Prj\Message\Message::run(
                    ['event' => 'bank_ok', 'time_baby' => date('Y年m月d日H:i分', strtotime($withdraw->getField('orderTime'))), 'money_now' => $amount / 100],
                    ['userId' => $withdraw->getField('userId'), 'phone' => $phone]
                );
            } catch (\ErrorException $e) {

            }
            //入库
            try{
                $this->varLog('withdraw update begin...');
                $withdraw->update();
            }catch (\ErrorException $e){
                return $this->_returnError('db_error');
            }
        }elseif($status=='failed') {
            //说明:如果扣除了次数则返回次数,订单置为失败
            if (\Prj\Consts\OrderStatus::unusual == $withdraw->getField('orderStatus')) return $this->_returnOK('');
            if (\Prj\Consts\OrderStatus::created != $withdraw->getField('orderStatus')) {
                if(\Prj\Consts\OrderStatus::waitingGW == $withdraw->getField('orderStatus')){
                    //银行处理失败--退款
                    $this->varLog('withraw failed and return...');
                    $where = ['id'=>$orderId];
                    $withdraw = \Prj\Data\Recharges::getCopy($where['id']);
                    $withdraw->load();
                    if (!$withdraw->exists()) {
                        return $this->_returnError('不存在的提现');
                    }
                    if ($withdraw->getField('orderStatus') != \Prj\Consts\OrderStatus::waitingGW) {
                        return $this->_returnError('非法的订单状态');
                    }
                    $amount = $withdraw->getField('amountAbs');
                    $poundage = $withdraw->getField('poundage') - 0;
                    $userId = $withdraw->getField('userId');
                    $user = \Prj\Data\User::getCopy($userId);
                    $user->load();
                    if (!$user->exists()) {
                        return $this->_returnError('不存在的用户');
                    }
                    $retryNum = 3;
                    while (!$user->lock(date('H:i:s') . '#withdrawReturn#odersId:' . $where['id']) && $retryNum >= 0) {
                        if ($retryNum == 0) {
                            return $this->_returnError('系统正忙,请稍候重试');
                        }
                        $this->varLog('lock retry...');
                        sleep(1);
                        $user->reload();
                        $retryNum--;
                    }

                    $tally = \Prj\Data\WalletTally::addTally($userId, $user->getField('wallet'), $amount + $poundage, 0, $where['id'], \Prj\Consts\OrderType::manualReturn);
                    $tally->setField('poundage', $poundage);
                    $tally->setField('descCreate','提现失败退款:'.($msg?$msg:'未知错误'));
                    $tally->setField('statusCode', \Prj\Consts\Tally::status_new);

                    $withdraw->setField('orderStatus', \Prj\Consts\OrderStatus::unusual);
                    $withdraw->setField('exp', $msg?$msg:'未知错误');

                    if($poundage == 0){
                        $this->varLog('return free times...');
                        $item = new \Prj\Items\ItemGiver($userId);
                        $item->add('Withdraw', 1)->give([]);
                        $withNum = \Prj\Data\WithdrawNum::add($userId, 1, date('Ym'), $where['id'] . '#提现退还', 'system');
                    }

                    $user->setField('wallet', $user->getField('wallet') + $amount + $poundage);

                    $oldTally = \Prj\Data\WalletTally::getCopy($userId);
                    $oldTally->load();
                    $oldTallyArr = $oldTally->db()->getRecord($oldTally->tbname(),'*',['orderId'=>$where['id'],'tallyType'=>\Prj\Consts\OrderType::withdraw]);

                    if($oldTallyArr){
                        $oldTallyy = \Prj\Data\WalletTally::getCopy($oldTallyArr['tallyId']);
                        $oldTallyy->load();
                        $oldTallyy->setField('freeze',0);
                        try{
                            $this->varLog('oldtally unfreeze begin...');
                            $oldTallyy->update();
                        }catch (\ErrorException $e){
                            var_log($where['id'].'#原流水解冻失败');
                        }
                    }

                    try {
                        $this->varLog('tally update begin...');
                        $tally->update();
                    } catch (\ErrorException $e) {
                        $user->unlock();
                        return $this->_returnError('流水更新失败');
                    }

                    try {
                        $this->varLog('withdraw update begin...');
                        $withdraw->update();
                    } catch (\ErrorException $e) {
                        $user->unlock();
                        $tally->setField('statusCode', \Prj\Consts\Tally::status_abandon);
                        $tally->update();
                        return $this->_returnError('订单更新失败');
                    }

                    try {
                        if($withNum){
                            $this->varLog('withdraw times update begin...');
                            $withNum->update();
                        }
                    } catch (\ErrorException $e) {
                        $user->unlock();
                        $tally->setField('statusCode', \Prj\Consts\Tally::status_abandon);
                        $tally->update();
                        $withdraw->setField('statusCode', \Prj\Consts\OrderStatus::waitingGW);
                        $withdraw->update();
                        return $this->_returnError('提现次数流水更新失败');
                    }

                    try {
                        $this->varLog('user update begin...');
                        $user->update();
                    } catch (\ErrorException $e) {
                        $user->unlock();
                        $tally->setField('statusCode', \Prj\Consts\Tally::status_abandon);
                        $tally->update();
                        $withdraw->setField('statusCode', \Prj\Consts\OrderStatus::waitingGW);
                        $withdraw->update();
                        $withNum->setField('statusCode', \Prj\Consts\Tally::status_abandon);
                        $withNum->update();
                        return $this->_returnError('用户更新失败');
                    }
                    return $this->_returnOK('success');
                }else{
                    return $this->_returnError('error_status');
                }
            }
            //订单置为失败
            $withdraw->setField('orderStatus',\Prj\Consts\OrderStatus::unusual);
            $withdraw->setField('payTime',\Sooh\Base\Time::getInstance()->ymdhis());
            $withdraw->setField('exp',$msg?$msg:'未知错误');
            //如果扣除了次数则返回次数
            if($poundage == 0){
                $wn = \Prj\Data\WithdrawNum::add($userId,1,date('Ym'),'',$userId);
                $wn->setField('exp','#提现失败返还#orderId:'.$orderId);
                $item = new \Prj\Items\ItemGiver($userId);
                $item->add('Withdraw',1)->give();
            }
            //入库
            try{
                if($wn){
                    $wn->update();
                    $rollGroup['wn'] = $wn;
                }
                $withdraw->update();
                $rollGroup['withdraw'] = $withdraw;
                $rollGroup['withdrawStatus'] = \Prj\Consts\OrderStatus::created;
                if($poundage == 0)$user->update();
            }catch (\ErrorException $e){
                $this->rollBack($rollGroup);
                return $this->_returnError($e->getMessage());
            }
        }elseif($status=='accept'){
            //说明:扣除用户的账户余额,添加流水记录,订单置为新浪已受理
            if(\Prj\Consts\OrderStatus::waitingGW==$withdraw->getField('orderStatus'))return $this->_returnOK('');
            if(\Prj\Consts\OrderStatus::created!=$withdraw->getField('orderStatus')){return $this->_returnError('error_status');}
            if(!$user->lock($info)){
                usleep(500000);
                $user->reload();
                if(!$user->lock($info)){
                    return $this->_returnError('lock_failed');
                }
            }
            //添加流水记录
            \Prj\Misc\OrdersVar::$introForUser = '提现申请';
            \Prj\Misc\OrdersVar::$introForCoder = 'withdraw_'.$orderId;
            $tally = \Prj\Data\WalletTally::addTally($userId, $user->getField('wallet'), -$amount - $poundage, 0, $orderId, \Prj\Consts\OrderType::withdraw);
            $tally->setField('poundage',$poundage-0);
            //扣除用户的余额
            $user->setField('wallet',$user->getField('wallet') - $amount - $poundage);
            if(empty($tally)){
                $user->unlock();
                return $this->_returnError('db_error');
            }
            $tally->setField('freeze',1);
            $tally->setField('statusCode',\Prj\Consts\Tally::status_new);
            //订单置为新浪已受理
            $withdraw->setField('orderStatus',\Prj\Consts\OrderStatus::waitingGW);
            $withdraw->setField('payTime',\Sooh\Base\Time::getInstance()->ymdhis());
            $withdraw->setField('exp','#新浪已受理#');
            $rollGroup['user'] = $user;
            try{
                $tally->update();
                $rollGroup['tally'] = $tally;
                $withdraw->update();
                $rollGroup['withdraw'] = $withdraw;
                $rollGroup['withdrawStatus'] = \Prj\Consts\OrderStatus::created;
                $user->update();
            }catch (\ErrorException $e){
                $this->rollBack($rollGroup);
                return $this->_returnError($e->getMessage());
            }
        }
        if($status=='success'){
            //todo 记录对账
            try{
                $data = $withdraw->dump();
                $data['sn'] = $ordersId;
                $ret = \Prj\Check\DayWithdraw::addRecords($data);
            }catch (\ErrorException $e){

            }
        }

        return $this->_returnOK('success');
    }

    protected function rollBack($backGroup){
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
            $backGroup['withdraw']->setField('orderStatus',$backGroup['withdrawStatus']!==null?$backGroup['withdrawStatus']:\Prj\Consts\OrderStatus::abandon);
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
    }

    protected function _unfreezeTally($ordersId){
        //解冻流水
        try{
            $rs = \Prj\Data\WalletTally::loopFindRecords(['orderId'=>$ordersId,'freeze'=>1]);
            if(!empty($rs)){
                $tallyId = $rs[0]['tallyId'];
                $tally = \Prj\Data\WalletTally::getCopy($tallyId);
                $tally->load();
                //var_log($tally,'tally rs >>>');
                if($tally->exists()){
                    var_log('[warning]解冻了一笔资金流水 tallyId:'.$tallyId);
                    $tally->setField('freeze',0);
                    $tally->update();
                    return $tally;
                }
            }
        }catch (\ErrorException $e){
            var_log('[error]解冻流水失败 tallyId:'.$tallyId);
        }
    }

    protected $otherArgs = [];

    //todo =============================================================================================================

    protected function _assign($key,$value)
    {
        $this->arr[$key] = $value;
        return $this->arr;
    }

    protected function _returnError($msg='',$str = '',$code=400)
    {
        $this->arr['msg'] = $msg;
        $this->arr['code'] = $code;
        $this->varLog($str.'[error400]'.$msg);
        return $this->arr;
    }

    protected function _returnOK($msg='')
    {
        $this->arr['msg'] = $msg;
        $this->arr['code'] = 200;
        $this->varLog('#[success200]'.$msg);
        return $this->arr;
    }
}
