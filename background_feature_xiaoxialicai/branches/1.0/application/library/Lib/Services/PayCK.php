<?php
namespace Lib\Services;
/**
 * rpc 服务器管理
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class PayCK {
	protected static $_instance=null;
    protected $loger = null;
	/**
	 * 
	 * @param \Sooh\Base\Rpc\Broker $rpcOnNew
	 * @return PayGW
	 */
	public static function getInstance($rpcOnNew=null)
	{
        //set_time_limit(2);
		if(self::$_instance===null){
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
	 * 充值
	 * @param type $sn 充值的流水记录
	 * @param type $cardRecordId 绑卡记录的表的id
	 * @param type $wwwway 网页支付模式none:默认，auto:支付网关决定，shft:盛付通
	 * @return mix
	 * @throws \Sooh\Base\ErrException
	 */
	public function recharge($sn,$userId,$bankId,$bankCard,$amount,$userIP,$wwwway='none')
	{
		if($this->rpc!==null){
			return $this->rpc->initArgs(array('SN'=>$sn,'userId'=>$userId,'bankId'=>$bankId,'bankCard'=>$bankCard,'amount'=>$amount,'userIP'=>$userIP,'wwwway'=>$wwwway))->send(__FUNCTION__);
		}else{
			$rand = 1;
            \Sooh\Base\Session\Data::getInstance()->set('tghOrderId',$sn);
			switch ($rand){
				case 1: 	return ["code"=>  200,"payCorp"=>111];
				case 2: 	return ["status"=>\Prj\Consts\PayGW::failed,'reason'=>'debug'];
			}
		}

    }
    /**
     * 实名认证
     */
    public function setRealName($userId,$realname,$idCard)
    {
        if($this->rpc!==null){
            return $this->rpc->initArgs(array('userId'=>$userId,'realname'=>$realname,'idCard'=>$idCard))->send(__FUNCTION__);
        }else{
            $rand = rand(1,2);
            switch ($rand){
                case 1: 	return ["code"=>200];
                case 2: 	return ["code"=>400];
            }
        }
    }

    /**
     * 通知支付网关订单完成
     */
    public function addOrder($orderId,$waresId,$userId,$amount,$amountExt,$amountFake,$orderTime,$orderStatus)
    {
        if($this->rpc!==null){
            $arr = [
                'ordersId'=>$orderId,
                'waresId'=>$waresId,
                'userId'=>$userId,
                'amount'=>$amount,
                'amountExt'=>$amountExt,
                'amountFake'=>$amountFake,
                'orderTime'=>$orderTime,
                'orderStatus'=>$orderStatus,
                'key'=>md5($orderId.$amount.'asfrysfasfcxgvretfddf'),
            ];
            return $this->rpc->initArgs($arr)->send(__FUNCTION__);
        }else{
            $rand = rand(1,2);
            switch ($rand){
                case 1: 	return ["code"=>200];
                case 2: 	return ["code"=>400];
            }
        }
    }
    /**
     * 发送提现通知
     */
    public function sendWithdraw($batchId,$list)
    {
        if($this->rpc!==null){
            return $this->rpc->initArgs(['batchId'=>$batchId,'list'=>$list])->send(__FUNCTION__);
        }else{
            $rand = 1;
            switch ($rand){
                case 1: 	return ["code"=>200,"payCorp"=>111];
                case 2: 	return ["code"=>400,'msg'=>'error'];
            }
        }
    }

    /**
     * 转账（募集满后确认付款到企业户）
     * 管理员操作后台时触发，从用户子账户或平台账户转到企业户
     * 以新浪支付来说，先比较标的投资订单总金额，如果正确，从各
     * 个用户账（平台赠送的部分）上归集资金到借款人在新浪的子账户，注意打标记，重复发送的情况的处理
     */
    public function trans($sn,$waresId,$amountReal,$amountGift,$amountTotal,$borrowerId,$borrowerName,$borrowerTunnel = 'auto')
    {
        if($this->rpc!==null){
            return $this->rpc->initArgs([
                'SN'=>$sn,
                'waresId'=>$waresId,
                'amountReal'=>$amountReal,
                'amountGift'=>$amountGift,
                'amountTotal'=>$amountTotal,
                'borrowerId'=>$borrowerId,
                'borrowerName'=>$borrowerName,
                'borrowerTunnel'=>$borrowerTunnel,

            ])->send(__FUNCTION__);
        }else{
            $rand = 1;
            switch ($rand){
                case 1: 	return ['status'=>1,'reason'=>''];
                case 2: 	return ['status'=>4,'reason'=>'失败啦！'];
            }
        }
    }

    /**
     * 满标转账的回调
     */
    public function transResult($ordersId,$status,$msg = '')
    {
        //set_time_limit(5);
	var_log('[warning]满标转账回调: ordersId:'.$ordersId.' status:'.$status.' msg:'.$msg);
        $invest = \Prj\Data\Investment::getCopy($ordersId);
        $invest->load();
        if(!$invest->exists())return $this->_returnError('void_invest');
        if($invest->getField('orderStatus')==\Prj\Consts\OrderStatus::payed){
            return $this->_returnError('repeat_request');
        }
        if($invest->getField('orderStatus')!=\Prj\Consts\OrderStatus::waiting && $invest->getField('orderStatus')!=\Prj\Consts\OrderStatus::waitingGW){
            return $this->_returnError('error_status');
        }
        if(!in_array($status,['success','failed']))return $this->_returnError('unknown_status');
        if($status=='success'){
            $invest->updStatus(\Prj\Consts\OrderStatus::payed);
            $invest->setField('exp',$status);
            $invest->setField('transTime',date('YmdHis'));

            //更新还款计划
            try{
                $returnPlan = \Prj\ReturnPlan\Std01\ReturnPlan::calendar($ordersId);
                $nextYmd = $returnPlan->getYmdNext();
                $invest->setField('returnPlan',$returnPlan->decode());
                $invest->setField('orderStatus',\Prj\Consts\OrderStatus::going); //正常回款中
                if($nextYmd)$invest->setField('returnNext',$nextYmd);
            }catch (\ErrorException $e){
                var_log('更新还款计划失败');
                var_log($e->getTraceAsString());
            }

            //解冻流水
            $this->_unfreezeTally($ordersId);
            try{
                //todo 激活返利
                $rebateId = $invest->getField('rebateId');
                if(!empty($rebateId)){
                    try{
                        $ret = \Prj\Items\Rebate::openRebate($rebateId);
                    }catch (\ErrorException $e){
                        var_log($e->getTraceAsString());
                    }
                    var_log($ret,'[warning]激活返利网关处理结果:');
                }
            }catch (\ErrorException $e){
                var_log('[error]'.$e->getMessage());
            }
	        try {
		        $_dbUser = \Prj\Data\User::getCopy($invest->getField('userId'));
		        $_dbUser->load();
		        \Prj\ReadConf::run(
			        [
				        'event' => 'look_ok',
				        'pro_name' => $invest->getField('waresName'),
				        'time_all' => 24,
				        'brand' => \Prj\Message\Message::MSG_BRAND,
				        'cont_ok' => '投资记录'
			        ],
			        ['phone' => $_dbUser->getField('phone'), 'userId' => $invest->getField('userId')]
		        );
		        \Prj\ReadConf::run(
			        [
				        'event' => 'money_start',
				        'pro_name' => $invest->getField('waresName'),
				        'touzi_money' => ($invest->getField('amount') + $invest->getField('amountExt')) / 100,
				        'money_back' => \Prj\Lang\Broker::getMsg('wares.name_of_house'),
			        ],
			        ['userId' => $invest->getField('userId')]
		        );

	        } catch (\ErrorException $e) {

	        }

        }else{
            $invest->updStatus(\Prj\Consts\OrderStatus::failed);
            $invest->setField('exp',$msg);
        }

        try{
            $invest->update();
        }catch (\ErrorException $e){
            return $this->_returnError($e->getMessage());
        }

        $waresId = $invest->getField('waresId');
        $ware = \Prj\Data\Wares::getCopy($waresId);
        $ware->load();
        if(!$ware->exists()){
            var_log("[error]标的不存在");
        }else{
            $num = $this->transNum($waresId);
            if($num>0){
                $ware->setField('exp',"网关处理了 $num 条订单");
                try{
                    $ware->update();
                }catch (\ErrorException $e){
                    var_log("[error]更新标的备注失败");
                    var_log($e->getMessage());
                    var_log($e->getTraceAsString());
                }
            }
        }

        return $this->_returnOK('回调成功');
    }

    protected function transNum($waresId){
        $records = \Prj\Data\Investment::loopFindRecords(['waresId'=>$waresId,'orderStatus'=>[\Prj\Consts\OrderStatus::payed,\Prj\Consts\OrderStatus::going,\Prj\Consts\OrderStatus::failed]]);
        return $records?count($records):0;
    }

    /**
     * 返利调用
     * @param $sn
     * @param $amount
     * @param $userId
     * @return array|mixed
     * @throws \Sooh\Base\ErrException
     */
    public function rebate($sn,$amount,$userId){
        if($this->rpc!==null){
            return $this->rpc->initArgs([
                'SN'=>$sn,
                'amount'=>$amount,
                'userId'=>$userId,
            ])->send(__FUNCTION__);
        }else{
            $rand = 1;
            switch ($rand){
                case 1: 	return ['code'=>200,'msg'=>'成功啦'];
                case 2: 	return ['code'=>400,'msg'=>'失败啦！'];
            }
        }
    }

    /**
     * 返利回调
     * @param $sn
     * @param $amount
     * @param $userId
     * @param $status
     * @param string $msg
     * @return array
     */
    public function rebateResult($sn,$amount,$userId,$status,$msg=''){
        var_log("[warning]返利回调开始 sn:".$sn.' amount:'.$amount.' userId:'.$userId.' status:'.$status.' msg:'.$msg);
        if(empty($sn)){
            return $this->_returnError('no_sn');
        }elseif(empty($amount)){
            return $this->_returnError('no_amount');
        }elseif(empty($userId)){
            return $this->_returnError('no_userId');
        }else{
            try{
                $ret = \Prj\Items\Rebate::rebateResult($sn,$amount,$userId,$status,$msg);
            }catch (\ErrorException $e){
                return $this->_returnError($e->getMessage());
            }
            return $this->_returnOK();
        }
    }


    /**
     * 回款确认 借款人还钱
     * 管理员操作后台时触发，用于将资金从借款人账户转账到中间户。
     * 就新浪支付来说，只要记录返回就行（表中增加记录return操作余额）
     */
    public function confirm($sn,$waresId,$amountPlan,$amountReal,$amountLeft,$borrowerId,$borrowerName,$borrowerTunnel = 'auto',$servicePay = 0,$giftPay = 0)
    {
        if($this->rpc!==null){
            return $this->rpc->initArgs([
                'SN'=>$sn,
                'waresId'=>$waresId,
                'amountPlan'=>$amountPlan,
                'amountReal'=>$amountReal,
                'amountLeft'=>$amountLeft,
                'borrowerId'=>$borrowerId,
                'borrowerName'=>$borrowerName,
                'borrowerTunnel'=>$borrowerTunnel,
                //新增两个参数
                'servicePay'=>$servicePay, //借款人需要支付的佣金
                'giftPay'=>$giftPay, //借款人无力偿还,平台代为垫付

            ])->send(__FUNCTION__);
        }else{
            $rand = 1;
            switch ($rand){
                case 1: 	return ['status'=>1,'reason'=>''];
                case 2: 	return ['status'=>4,'reason'=>'失败啦！'];
            }
        }
    }

    public function confirmResult($sn,$waresId,$status,$msg = '')
    {
        $ware = \Prj\Data\Wares::getCopy($waresId);
        $ware->load();
        if(!$ware->exists())return $this->_returnError('void_wares_id');
        $returnPlan = \Prj\ReturnPlan\All01\ReturnPlan::createPlan($ware->getField('returnPlan'));
        $rs = $returnPlan->getPlan(['sn'=>$sn]);
        if(empty($rs))return $this->_returnError('void_sn');
        $plan = current($rs);
        if($plan['status']==\Prj\Consts\PayGW::success)return $this->_returnError('repeat_request');
        if($plan['status']!=\Prj\Consts\PayGW::accept)return $this->_returnError('error_status');
        if(!in_array($status,['success','failed']))return $this->_returnError('unknown_status');
        if($status=='success'){
            $ware->setField('lastPaybackYmd',date('Ymd'));
            $returnPlan->updatePlan('status',\Prj\Consts\PayGW::success,['sn'=>$sn]);
            $returnPlan->updatePlan('isPay',1,['sn'=>$sn]);
            $returnPlan->updatePlan('realDateYmd',date('Ymd'),['sn'=>$sn]);
            $returnPlan->updatePlan('exp',$msg,['sn'=>$sn]);
        }else{
            $returnPlan->updatePlan('status',\Prj\Consts\PayGW::failed,['sn'=>$sn]);
            $returnPlan->updatePlan('exp',$msg,['sn'=>$sn]);
        }
        try{
            $ware->setField('returnPlan',$returnPlan->decode());
            $ware->update();
        }catch (\ErrorException $e){
            return $this->_returnError($e->getMessage());
        }
        return $this->_returnOK();
    }

    /**
     * 还本还息
     * 管理员操作后台时触发，用于指定产品回款到用户（本息）
     * 就新浪来说，检查还款批次对应的余额，够就按指定的本金总额转到用户账户
     */
    public function returnFund($sn,$confirmSN,$ordersId,$waresId,$userId,$amount,$interest)
    {
        $data = [
            'cmd'=>'returnFund',
            'SN'=>$sn,
            'confirmSN'=>$confirmSN,
            'ordersId'=>$ordersId,
            'waresId'=>$waresId,
            'userId'=>$userId,
            'amount'=>$amount,
            'interest'=>$interest,
        ];
        var_log($data,'给returnFund发送的参数>>>>>>>>>>>>');
        if($this->rpc!==null){
            return $this->rpc->initArgs($data)->send(__FUNCTION__);
        }else{
            $rand = 1;
            switch ($rand){
                case 1: 	return ['status'=>1,'reason'=>''];
                case 2: 	return ['status'=>4,'reason'=>'失败啦！'];
            }
        }
    }

    /**
     * 扣平台账回调
     * @param $dowhat  动作名称 'confirm','trans'
     * @param $sn  流水号
     * @param $amount 金额 正的是加钱 负的是减钱
     * @param $userId 用户ID,借款人ID
     * @param $status 'success','failed'
     * @param string $msg 出错的时候 错误原因
     * @param int $waresId 标的ID
     * @return array
     */
    public function giftPayResult($dowhat,$sn,$amount,$userId,$status,$msg = '',$waresId = 0){
        if(empty($dowhat))return $this->_returnError('args_do_miss');
        if(empty($sn))return $this->_returnError('args_sn_miss');
        if(empty($amount))return $this->_returnError('args_amount_miss');
        if(empty($userId))return $this->_returnError('args_userId_miss');
        if(empty($status))return $this->_returnError('args_status_miss');
        $tally = \Prj\Data\Systally::addTally($sn,$amount,$userId,$waresId);
        if(empty($tally))return $this->_returnError('repeat_request');
        switch($dowhat){
            case 'confirm':$tally->setField('type',\Prj\Consts\PayGW::tally_confirm);break;
            case 'trans':$tally->setField('type',\Prj\Consts\PayGW::tally_trans);break;
            case 'rebate':$tally->setField('type',\Prj\Consts\PayGW::tally_rebate);break;
            default:return $this->_returnError('error_do');
        }
        if($status=='success'){
            $tally->setField('statusCode',\Prj\Consts\Tally::status_new);
            $tally->setField('exp',$status);
        }else{
            $tally->setField('exp',$msg);
        }
        try{
            $tally->update();
        }catch (\ErrorException $e){
            return $this->_returnError($e->getMessage());
        }
        return $this->_returnOK();
    }

    /**
     * 还本还息回调
     *
     */
    public function returnFundResult($sn,$ordersId,$realPayAmount,$realPayInterest,$status,$msg='')
    {
        //set_time_limit(2);
        $invest = \Prj\Data\Investment::getCopy($ordersId);
        $invest->load();
        if(!$invest->exists())return $this->_returnError('void_orders_id');
        $userId = $invest->getField('userId');
        $user = \Prj\Data\User::getCopy($userId);
        $user->load();
        if(!$user->exists())return $this->_returnError('void_user');
        $returnPlan = \Prj\ReturnPlan\Std01\ReturnPlan::createPlan($invest->getField('returnPlan'));
        if(!in_array($status,['success','failed']))return $this->_returnError('void_status');
        $where = ['sn'=>$sn];
        if($plans = $returnPlan->getPlan($where)){
            $plan = current($plans);
            if($plan['isPay']){
                var_log($plan,'plan>>>>>>>>>>>>>>>>>>>>>');
                return $this->_returnError('have_pay');
            }
        }else{
            return $this->_returnError('void_sn');
        }
        if($status=='success'){
            $returnPlan->updatePlan('isPay','1',$where);
            $returnPlan->updatePlan('status',\Prj\Consts\PayGW::success,$where);
            $returnPlan->updatePlan('realPayAmount',$realPayAmount,$where);
            $returnPlan->updatePlan('realPayInterest',$realPayInterest,$where);
            $returnPlan->updatePlan('realDateYmd',date('Ymd'),$where);
            $num = $returnPlan->updatePlan('exp','ok',$where);
        }else{
            $returnPlan->updatePlan('status',\Prj\Consts\PayGW::failed,$where);
            $num = $returnPlan->updatePlan('exp',$msg,$where);
        }
        if(!$num)return $this->_returnError('void_sn');
        try{
            $invest->setField('returnPlan',$returnPlan->decode());
            if(!empty($realPayAmount)){
                //订单结束
                $invest->setField('orderStatus',\Prj\Consts\OrderStatus::done);
            }
            $invest->update();
        }catch (\ErrorException $e){
            return $this->_returnError($e->getMessage());
        }
        $lockInfo = 'returnFund#sn:'.$sn.' ordersId:'.$ordersId.' userId:'.$userId;
        if(!$user->lock($lockInfo)){
            $user->reload();
            if(!$user->lock($lockInfo)){
                return $this->_returnError('lock_failed');
            }
        }
        \Prj\Misc\OrdersVar::$introForUser = '您投资的 '.$invest->getField('waresName').' 已成功回款';
        \Prj\Misc\OrdersVar::$introForCoder = 'return_fund_'.$sn;
        $tally = \Prj\Data\WalletTally::addTally($userId,$user->getField('wallet'),$realPayInterest,0,$ordersId,\Prj\Consts\OrderType::paysplit);
        $tally->setField('sn',$sn);
        if(!empty($realPayAmount)){
            $tallyAmount = \Prj\Data\WalletTally::addTally($userId,$user->getField('wallet')+$realPayInterest,$realPayAmount,0,$ordersId,\Prj\Consts\OrderType::payAmount);
            $tallyAmount->setField('sn',$sn);
        }
        if(!$tally){
            $user->unlock();
            return $this->_returnError('add_tally_failed');
        }
        try{
            $tally->updStatus(\Prj\Consts\Tally::status_new);
            if(!empty($realPayAmount)){
                $tallyAmount->updStatus(\Prj\Consts\Tally::status_new);
                $tallyAmount->update();
            }
            $tally->update();
        }catch (\ErrorException $e){
            return $this->_returnError($e->getMessage());
        }
        try{
            $user->setField('wallet',$user->getField('wallet')+$realPayAmount+$realPayInterest);
            $user->update();
        }catch (\ErrorException $e){
            return $this->_returnError($e->getMessage());
        }
        //推送
        $phone = $user->getField('phone');
        try {
        $ret = \Prj\ReadConf::run(
            ['event' => 'get_money','pro_name'=>$invest->getField('waresName'),'money_baby'=>($realPayAmount+$realPayInterest)/100, ],
            ['phone' => $phone, 'userId' => $userId]
        );
        } catch (\ErrorException $e) {

        }
        //提前还款推送
        try {
        if(date('Ymd')<$plan['planDateYmd']){
            $ret1 = \Prj\ReadConf::run(
                ['event' => 'tell_data','pro_name'=>$invest->getField('waresName'),'money_full'=>($realPayAmount+$realPayInterest)/100,'capital_ok'=>$realPayAmount/100,'int_ok'=>$realPayInterest/100,'price_ok'=>0 ],
			        ['phone' => $phone, 'userId' => $userId]
            );
        }
        } catch (\ErrorException $e) {

        }
        return $this->_returnOK();
    }


	/**
	 * 绑卡
	 * @param type $sn bankcard记录id
	 * @return type
	 */
	public function binding($sn,$userId,$realname,$idType,$idCode,$bankId,$bankCard,$phone,$userIP)
	{
		if($this->rpc!==null){
			return $this->rpc->initArgs(array('SN'=>$sn,'userId'=>$userId,'realname'=>$realname,'idType'=>$idType,'idCode'=>$idCode,'bankId'=>$bankId,'bankCard'=>$bankCard,'phone'=>$phone,'userIP'=>$userIP))->send(__FUNCTION__);
		}else{
			$rand = 1;
            \Sooh\Base\Session\Data::getInstance()->set('tghOrderId',$sn);
			switch ($rand){
				case 1: 	return ["code"=>200,'payCorp'=>'111','orderId'=>$sn];
				case 2: 	return ["code"=>400,'msg'=>'error'];

			}
		}
	}

    /**
     * 绑卡验证码验证
     * By Hand
     */
    public function bindingCode($code,$ticket = '',$userId,$orderId = '110')
    {
        if($this->rpc!==null){
            return $this->rpc->initArgs(array('smsCode'=>$code,'SN'=>$ticket,'userId'=>$userId))->send(__FUNCTION__);
        }else{
            $rand = 1;
            $orderId = \Sooh\Base\Session\Data::getInstance()->get('tghOrderId');
            switch ($rand){
                case 1: 	return ["code"=>200,'cardId'=>'12345','orderId'=>$orderId];
                case 2: 	return ["code"=>400,'msg'=>'error'];
            }
        }
    }

    /**
     * 充值验证码验证
     * By Hand
     */
    public function rechargeCode($code,$ticket,$userId,$orderId = '110')
    {
        if($this->rpc!==null){
            return $this->rpc->initArgs(array('smsCode'=>$code,'SN'=>$ticket,'userId'=>$userId))->send(__FUNCTION__);
        }else{
            $orderId = \Sooh\Base\Session\Data::getInstance()->get('tghOrderId');
            $rand = 1;
            switch ($rand){
                case 1: 	return ["code"=>200,'cardId'=>12345,'orderId'=>$orderId];
                case 2: 	return ["code"=>400,'msg'=>'error'];
            }
        }
    }

    /**
     * 查询订单的状态
     */
	public function check($sn)
	{
        $dt = time();
        $sign = md5($dt.'dasfijogysaudaihilgjakojd');
		if($this->rpc!==null){
			return $this->rpc->initArgs([
                'SN'=>$sn,
                'dt'=>$dt,
                'sign'=>$sign
            ])->send(__FUNCTION__);
		}else{
			$rand = 1;
			switch ($rand){
                case 1: 	return ["code"=>200,"payCorp"=>111];
                case 2: 	return ["code"=>400,'msg'=>'error'];
			}
		}
	}

    /**
     * 充值流水（对账）
     */
    public function dayRecharges($ymd)
    {
        $dt = time();
        $sign = md5($dt.'dasfijogysaudaihilgjakojd');
        if($this->rpc!==null){
            return $this->rpc->initArgs([
                'ymd'=>$ymd,
                'dt'=>$dt,
                'sign'=>$sign,
            ])->send(__FUNCTION__);
        }else{
            $rand = 1;
            switch ($rand){
                case 1: 	return [
                    ['file'=>'sina/'.$ymd.'/dayRecharges.csv','ymd'=>$ymd,'SN'=>123456,'userId'=>'90003837339748','amount'=>rand(1000,9999),'paycorp'=>'101'],
                    ['file'=>'sina/'.$ymd.'/dayRecharges.csv','ymd'=>$ymd,'SN'=>time().rand(1000,9999),'userId'=>'90003837339750','amount'=>rand(1000,9999),'paycorp'=>'101'],
                    ['file'=>'sina/'.$ymd.'/dayRecharges.csv','ymd'=>$ymd,'SN'=>time().rand(1000,9999),'userId'=>'90003837339748','amount'=>rand(1000,9999),'paycorp'=>'101'],
                    ['file'=>'sina/'.$ymd.'/dayRecharges.csv','ymd'=>$ymd,'SN'=>time().rand(1000,9999),'userId'=>'90003837339748','amount'=>rand(1000,9999),'paycorp'=>'101'],
                ];
                case 2: 	return ["code"=>400,'msg'=>'error'];
            }
        }
    }

    /**
     * 充值流水（对账）
     */
    public function dayManage($ymd)
    {
        $dt = time();
        $sign = md5($dt.'dasfijogysaudaihilgjakojd');
        if($this->rpc!==null){
            return $this->rpc->initArgs([
                'ymd'=>$ymd,
                'dt'=>$dt,
                'sign'=>$sign,
            ])->send(__FUNCTION__);
        }else{
            $rand = 1;
            switch ($rand){
                case 1: 	return [
                    ['file'=>'sina/'.$ymd.'/dayManage.csv','ymd'=>$ymd,'SN'=>123456,'waresId'=>'90003837339748','amount'=>rand(1000,9999),'paycorp'=>'101'],
                    ['file'=>'sina/'.$ymd.'/dayManage.csv','ymd'=>$ymd,'SN'=>time().rand(1000,9999),'waresId'=>'90003837339750','amount'=>rand(1000,9999),'paycorp'=>'101'],
                    ['file'=>'sina/'.$ymd.'/dayManage.csv','ymd'=>$ymd,'SN'=>time().rand(1000,9999),'waresId'=>'90003837339748','amount'=>rand(1000,9999),'paycorp'=>'101'],
                    ['file'=>'sina/'.$ymd.'/dayManage.csv','ymd'=>$ymd,'SN'=>time().rand(1000,9999),'waresId'=>'90003837339748','amount'=>rand(1000,9999),'paycorp'=>'101'],
                ];
                case 2: 	return ["code"=>400,'msg'=>'error'];
            }
        }
    }

    /**
     * 提现流水（对账）
     */
    public function dayWithdraw($ymd)
    {
        $dt = time();
        $sign = md5($dt.'dasfijogysaudaihilgjakojd');
        if($this->rpc!==null){
            return $this->rpc->initArgs([
                'ymd'=>$ymd,
                'dt'=>$dt,
                'sign'=>$sign,
            ])->send(__FUNCTION__);
        }else{
            $rand = 1;
            switch ($rand){
                case 1: 	return [
                    ['file'=>'sina/'.$ymd.'/dayRecharges.csv','ymd'=>$ymd,'SN'=>time().rand(1000,9999),'userId'=>'90003837339748','amount'=>rand(1000,9999),'paycorp'=>'101','poundage'=>1],
                    ['file'=>'sina/'.$ymd.'/dayRecharges.csv','ymd'=>$ymd,'SN'=>time().rand(1000,9999),'userId'=>'90003837339750','amount'=>rand(1000,9999),'paycorp'=>'101','poundage'=>1],
                    ['file'=>'sina/'.$ymd.'/dayRecharges.csv','ymd'=>$ymd,'SN'=>time().rand(1000,9999),'userId'=>'90003837339748','amount'=>rand(1000,9999),'paycorp'=>'101','poundage'=>1],
                    ['file'=>'sina/'.$ymd.'/dayRecharges.csv','ymd'=>$ymd,'SN'=>time().rand(1000,9999),'userId'=>'90003837339748','amount'=>rand(1000,9999),'paycorp'=>'101','poundage'=>1],
                ];
                case 2: 	return ["code"=>400,'msg'=>'error'];
            }
        }
    }

    /**
     * 购买对账
     */
    public function dayBuy($ymd)
    {
        $dt = time();
        $sign = md5($dt.'dasfijogysaudaihilgjakojd');
        if($this->rpc!==null){
            return $this->rpc->initArgs([
                'ymd'=>$ymd,
                'dt'=>$dt,
                'sign'=>$sign,
            ])->send(__FUNCTION__);
        }else{
            $rand = 1;
            switch ($rand){
                case 1: 	return [
                    ['file'=>'sina/'.$ymd.'/dayRecharges.csv','ymd'=>$ymd,'SN'=>'1014614745801739748','userId'=>'90003837339748','amount'=>'136','amountExtra'=>'9864','waresId'=>'1461474573767928','paycorp'=>'101'],
                ];
                case 2: 	return ["code"=>400,'msg'=>'error'];
            }
        }
    }

    /**
     * 回款对账
     */
    public function dayPayback($ymd)
    {
        $dt = time();
        $sign = md5($dt.'dasfijogysaudaihilgjakojd');
        if($this->rpc!==null){
            return $this->rpc->initArgs([
                'ymd'=>$ymd,
                'dt'=>$dt,
                'sign'=>$sign,
            ])->send(__FUNCTION__);
        }else{
            $rand = 1;
            switch ($rand){
                case 1: 	return [
                    ['file'=>'sina/'.$ymd.'/dayRecharges.csv','ymd'=>$ymd,'SN'=>time().rand(1000,9999),'borrowerId'=>'90003837339748','amount'=>rand(1000,9999),'waresId'=>'123','paycorp'=>101],
                    ['file'=>'sina/'.$ymd.'/dayRecharges.csv','ymd'=>$ymd,'SN'=>time().rand(1000,9999),'borrowerId'=>'90003837339750','amount'=>rand(1000,9999),'waresId'=>'123','paycorp'=>101],
                    ['file'=>'sina/'.$ymd.'/dayRecharges.csv','ymd'=>$ymd,'SN'=>time().rand(1000,9999),'borrowerId'=>'90003837339748','amount'=>rand(1000,9999),'waresId'=>'123','paycorp'=>101],
                    ['file'=>'sina/'.$ymd.'/dayRecharges.csv','ymd'=>$ymd,'SN'=>time().rand(1000,9999),'borrowerId'=>'90003837339748','amount'=>rand(1000,9999),'waresId'=>'123','paycorp'=>101],
                ];
                case 2: 	return ["code"=>400,'msg'=>'error'];
            }
        }
    }

    /**
     * 放款对账
     */
    public function dayLoan($ymd)
    {
        $dt = time();
        $sign = md5($dt.'dasfijogysaudaihilgjakojd');
        if($this->rpc!==null){
            return $this->rpc->initArgs([
                'ymd'=>$ymd,
                'dt'=>$dt,
                'sign'=>$sign,
            ])->send(__FUNCTION__);
        }else{
            $rand = 1;
            switch ($rand){
                case 1: 	return [
                    ['file'=>'sina/'.$ymd.'/dayRecharges.csv','ymd'=>$ymd,'SN'=>'1461474621334082','borrowerId'=>'46061253494017','amount'=>'18000','waresId'=>'1461474573767928','paycorp'=>101],
                ];
                case 2: 	return ["code"=>400,'msg'=>'error'];
            }
        }
    }

    /**
     * 返用户本息对账
     */
    public function dayPaysplit($ymd)
    {
        $dt = time();
        $sign = md5($dt.'dasfijogysaudaihilgjakojd');
        if($this->rpc!==null){
            return $this->rpc->initArgs([
                'ymd'=>$ymd,
                'dt'=>$dt,
                'sign'=>$sign,
            ])->send(__FUNCTION__);
        }else{
            $rand = 1;
            switch ($rand){
                case 1: 	return [
                    ['file'=>'sina/'.$ymd.'/dayRecharges.csv','ymd'=>$ymd,'SN'=>time().rand(1000,9999),'userId'=>'90003837339748','amount'=>rand(1000,9999),'interest'=>'10','waresId'=>'123','paycorp'=>'101'],
                    ['file'=>'sina/'.$ymd.'/dayRecharges.csv','ymd'=>$ymd,'SN'=>time().rand(1000,9999),'userId'=>'90003837339750','amount'=>rand(1000,9999),'interest'=>'10','waresId'=>'123','paycorp'=>'101'],
                    ['file'=>'sina/'.$ymd.'/dayRecharges.csv','ymd'=>$ymd,'SN'=>time().rand(1000,9999),'userId'=>'90003837339748','amount'=>rand(1000,9999),'interest'=>'10','waresId'=>'123','paycorp'=>'101'],
                    ['file'=>'sina/'.$ymd.'/dayRecharges.csv','ymd'=>$ymd,'SN'=>time().rand(1000,9999),'userId'=>'90003837339748','amount'=>rand(1000,9999),'interest'=>'10','waresId'=>'123','paycorp'=>'101'],
                ];
                case 2: 	return ["code"=>400,'msg'=>'error'];
            }
        }
    }

    /**
     * 公司账户运营资金流水（包括分润）
     */
    public function dayCompany($ymd)
    {
        $dt = time();
        $sign = md5($dt.'dasfijogysaudaihilgjakojd');
        if($this->rpc!==null){
            return $this->rpc->initArgs([
                'ymd'=>$ymd,
                'dt'=>$dt,
                'sign'=>$sign,
            ])->send(__FUNCTION__);
        }else{
            $rand = 1;
            switch ($rand){
                case 1: 	return ["code"=>200,"payCorp"=>111];
                case 2: 	return ["code"=>400,'msg'=>'error'];
            }
        }
    }

    /**
     * 存钱罐账户日收益
     */
    public function dayInterest($ymd)
    {
        $dt = time();
        $sign = md5($dt.'dasfijogysaudaihilgjakojd');
        if($this->rpc!==null){
            return $this->rpc->initArgs([
                'ymd'=>$ymd,
                'dt'=>$dt,
                'sign'=>$sign,
            ])->send(__FUNCTION__);
        }else{
            $rand = 1;
            switch ($rand){
                case 1: 	return [
                    ['file'=>'sina/'.$ymd.'/dayRecharges.csv','ymd'=>$ymd,'SN'=>time().rand(1000,9999),'userId'=>'90003837339748','amount'=>rand(1000,9999),'paycorp'=>'101'],
                    ['file'=>'sina/'.$ymd.'/dayRecharges.csv','ymd'=>$ymd,'SN'=>time().rand(1000,9999),'userId'=>'90003837339750','amount'=>rand(1000,9999),'paycorp'=>'101'],
                    ['file'=>'sina/'.$ymd.'/dayRecharges.csv','ymd'=>$ymd,'SN'=>time().rand(1000,9999),'userId'=>'90003837339748','amount'=>rand(1000,9999),'paycorp'=>'101'],
                    ['file'=>'sina/'.$ymd.'/dayRecharges.csv','ymd'=>$ymd,'SN'=>time().rand(1000,9999),'userId'=>'90003837339748','amount'=>rand(1000,9999),'paycorp'=>'101'],

                ];
                case 2: 	return ["code"=>400,'msg'=>'error'];
            }
        }
    }

    protected $arr = [];
    protected $user = null;

    /**
     * 充值成功回调
     *
     */
    public function rechargeResult($orderId='',$amount='',$status='',$msg='')
    {
        var_log("warning:充值回调 orderId:$orderId amount:$amount status:$status msg:$msg ");
        $this->loger = \Sooh\Base\Log\Data::getInstance();
        $this->loger->userId = 'payGW';
        $this->loger->ext = $orderId;
        if(empty($orderId)||empty($amount)){
            return $this->_returnError('args_error');
        }
        $orderOfRecharge = \Prj\Data\Recharges::getCopy($orderId);
        $orderOfRecharge->load();
        if(!$orderOfRecharge->exists())
        {
            return $this->_returnError('db_no_orderOfRecharge',$orderId);
        }
        if($orderOfRecharge->getField('amount')!=$amount)
        {
            var_log($orderOfRecharge,'error_amount>>>>>>>>>>');
            return $this->_returnError('error_amount');
        }
        if($orderOfRecharge->getField('orderStatus')!=\Prj\Consts\OrderStatus::waiting){
            var_log($orderId,'orderId>>>>>>>>>>>>>>>>>>>>>>>>>>');
            var_log($orderOfRecharge->getField('orderStatus'),'error_status>>>>>>>>>>>>>>>>');
            return $this->_returnError('error_status');
        }
        $userId = $orderOfRecharge->getField('userId');
        $this->user = \Prj\Data\User::getCopy($userId);
        $this->user->load();
        //支付成功
        if($status=='success')
        {
            //成功后一系列操作
                //是否首充
            $ymdFirstCharge = $this->user->getField('ymdFirstCharge');
            if (empty($ymdFirstCharge) || $this->user->getField('isSuperUser')) {
                $this->user->setField('ymdFirstCharge', \Sooh\Base\Time::getInstance()->YmdFull);
                //todo 赠送奖励 首充送10元红包
                $vouchersGiftArr = \Lib\Services\EvtRecharges::getInstance('')->giveVouchersWherFirstCharge($userId); //调用松泉服务

                //...
            }
            //更新订单 nextUpdateUserWallet
            $orderOfRecharge->setField('orderStatus',\Prj\Consts\OrderStatus::done);

            //todo 验证成功 添加流水
            \Prj\Misc\OrdersVar::$introForUser = '充值单';
            \Prj\Misc\OrdersVar::$introForCoder = 'recharge';
            $tally = \Prj\Data\WalletTally::addTally($this->user->userId, $this->user->getField('wallet'), $amount,0,$orderId, \Prj\Consts\OrderType::recharges);
            //var_log($tally,'tally>>>>>>>>>>>>.');
            $tally->setField('descCreate','充值单 '.$tally->getPKey()['tallyId'].' 支付成功');
            //更新流水
            $tally->updStatus(\Prj\Consts\Tally::status_new)->update();
            if(!$this->user->lock('recharge callback from payGW'))
            {
                if(!$this->user->lock('recharge callback from payGW'))
                {
                    return $this->_returnError('lock_user_failed');
                }
            }
            try {
                $this->user->setField('wallet', $this->user->getField('wallet') + $amount);
                $this->user->update();
            } catch (\ErrException $e) {
                $tally->updStatus(\Prj\Consts\Tally::status_abandon);
                return $this->_returnError('updateWallet_failed');
            }
            //更新订单
            try{
                $orderOfRecharge->setField('payTime',\Sooh\Base\Time::getInstance()->ymdhis());
                $orderOfRecharge->setField('exp',$status);
                $orderOfRecharge->update();
            }catch (\ErrException $e){
                return $this->_returnError('updateOrderStatus_failed');
            }

        }
        elseif($status=='failed')
        {
            try{
                $orderOfRecharge->setField('payTime',\Sooh\Base\Time::getInstance()->ymdhis());
                $orderOfRecharge->setField('orderStatus',\Prj\Consts\OrderStatus::failed);
                $orderOfRecharge->setField('exp',$msg);
                $orderOfRecharge->update();
            }catch (\ErrException $e){
                return $this->_returnError('updateOrderStatus_failed');
            }
        }
        else
        {
            error_log('error>>>>>>>>>>>>>>>>>>错误的status指令 success/failed:'.$status);
            return $this->_returnError('error_cmd');
        }
        //推送
        $phone = $this->user->getField('phone');
        try {
        $ret = \Prj\ReadConf::run(
            ['event' => 'recharge_ok','money_recharge'=>$amount/100],
            ['phone' => $phone]
        );
        } catch (\ErrorException $e) {

        }
        var_log('#充值回调#>>>>>>>>>>>>>>>>>>>>>>>>');
        return $this->_returnOK('success');
    }

    /**
     * 购买成功回调
     * addOrderResult
     * @input string orderId 订单号
     * @input string amount 金额
     * @input string ret 处理结果 success/failed
     * @input string msg 错误描述
     * @error [code:200,data:[code:400,msg='']]
     */
    public function addOrderResult($orderId='',$amount='',$ret='',$msg='')
    {
        var_log("warning:购买回调 orderId:$orderId amount:$amount ret:$ret msg:$msg");
        switch(true)
        {
            case empty($orderId):return $this->_returnError('no_orderId');
            case empty($amount):return $this->_returnError('no_amount');
            case empty($ret):return $this->_returnError('no_ret');
        }
        $invest = \Prj\Data\Investment::getCopy($orderId);
        $invest->load();
        if(!$invest->exists()){
            return $this->_returnError('orderId_error',$orderId);
        }

        if(\Prj\Consts\OrderStatus::waiting!=$invest->getField('orderStatus'))return $this->_returnError('status_error',$invest->getField('orderStatus'));
        if($invest->getField('amount')!=$amount)return $this->_returnError('amount_error',$invest->getField('amount').'/'.$amount);
        if(!in_array($ret,['success','failed']))return $this->_returnError('ret_error',$ret);

        if($ret==='success')
        {
            $invest->setField('orderStatus',\Prj\Consts\OrderStatus::payed);
            $invest->setField('exp',$ret);
        }
        if($ret==='failed')
        {
            $invest->setField('exp',$msg);
        }

        try{
            $invest->update();
        }catch (\ErrorException $e){
            return $this->_returnError('db_error',$e->getMessage());
        }
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
    public function withdrawResult($ordersId='',$amount='',$status='',$msg='')
    {
        var_log("warning:提现回调 orderId:$ordersId amount:$amount ret:$status msg:$msg");
        $withdraw = \Prj\Data\Recharges::getCopy($ordersId);
        $withdraw->load();
        if(!$withdraw->exists())return $this->_returnError('orderId_void');
        if(\Prj\Consts\OrderStatus::waitingGW!=$withdraw->getField('orderStatus'))return $this->_returnError('error_status');
        if($amount!=$withdraw->getField('amountAbs'))return $this->_returnError('error_amount');
        if(!in_array($status,['success','failed']))return $this->_returnError('error_ret');

        if($status=='success')
        {
            try{
                $user = \Prj\Data\User::getCopy($withdraw->getField('userId'));
                $user->load();
                $phone = $user->getField('phone');
            }catch (\ErrorException $e){
                
            }
//            $ret = \Prj\ReadConf::run(
//                ['event' => 'bank_ok','time_baby'=>date('Y-m-d H:i:s',strtotime($withdraw->getField('orderTime'))),'money_now'=>$amount/100 ],
//                ['userId' => $withdraw->getField('userId'), 'phone' => $phone]
//            );
            $withdraw->setField('orderStatus',\Prj\Consts\OrderStatus::done);
            $withdraw->setField('payTime',\Sooh\Base\Time::getInstance()->ymdhis());
            $withdraw->setField('exp',$msg);

            //解冻流水
            $this->_unfreezeTally($ordersId);
	        try {
		        \Prj\ReadConf::run(
			        ['event' => 'bank_ok', 'time_baby' => date('Y年m月d日H:i分', strtotime($withdraw->getField('orderTime'))), 'money_now' => $amount / 100],
			        ['userId' => $withdraw->getField('userId'), 'phone' => $phone]
		        );
	        } catch (\ErrorException $e) {

        }
        }
        if($status=='failed')
        {
            $withdraw->setField('orderStatus',\Prj\Consts\OrderStatus::failed);
            $withdraw->setField('payTime',\Sooh\Base\Time::getInstance()->ymdhis());
            $withdraw->setField('exp',$msg);
        }

        try{
            $withdraw->update();
        }catch (\ErrorException $e){
            return $this->_returnError('db_error');
        }
    }

    protected function _unfreezeTally($ordersId){
        //解冻流水
        try{
            $rs = \Prj\Data\WalletTally::loopFindRecords(['orderId'=>$ordersId]);
            if(!empty($rs)){
                $tallyId = $rs[0]['tallyId'];
                $tally = \Prj\Data\WalletTally::getCopy($tallyId);
                $tally->load();
                //var_log($tally,'tally rs >>>');
                if($tally->exists()){
                    var_log('[warning]解冻了一笔资金流水 tallyId:'.$tallyId);
                    $tally->setField('freeze',0);
                    $tally->update();
                }
            }
        }catch (\ErrorException $e){
            var_log('[error]解冻流水失败 tallyId:'.$tallyId);
        }
    }

    protected function _assign($key,$value)
    {
        $this->arr[$key] = $value;
        return $this->arr;
    }

    protected function _returnError($msg='',$str = '',$code=400)
    {
        $this->arr['msg'] = $msg;
        $this->arr['code'] = $code;
        var_log($str,$msg.':');
        return $this->arr;
    }

    protected function _returnOK($msg='')
    {
        $this->arr['msg'] = $msg;
        $this->arr['code'] = 200;
        return $this->arr;
    }

}
