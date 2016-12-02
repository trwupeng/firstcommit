<?php
namespace Lib\Services;
/**
 * rpc 服务器管理
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class PayGW {
	protected static $_instance=null;
    protected $loger = null;

	/**
	 * 
	 * @param \Sooh\Base\Rpc\Broker $rpcOnNew
	 * @return PayGW
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
     * 流标处理
     * @param $sn
     * @param $waresId
     * @return array|mixed
     * @throws \Sooh\Base\ErrException
     */
    public function abort($sn,$waresId){
        if($this->rpc!==null){
            return $this->rpc->initArgs(array('SN'=>$sn,'waresId'=>$waresId))->send(__FUNCTION__);
        }else{
            $rand = 1;
            switch ($rand){
                case 1: 	return ["status"=>1,"payCorp"=>111];
                case 2: 	return ["status"=>\Prj\Consts\PayGW::failed,'reason'=>'debug'];
            }
        }

    }

    protected $voucherArr = [];
    protected $user;
    protected $ware;

    public function abortResult($ordersId,$amount,$status,$msg = ''){
       // \Prj\Tool\Func::debug_log();
        var_log('[warning]流标回调开始>>>');
        $redAmount = 0;
       if(empty($ordersId)){
           return $this->_returnError('no_args_ordersId');
       }elseif(empty($amount)){
           return $this->_returnError('no_args_amount');
       }elseif(!in_array($status,['success','failed'])){
           return $this->_returnError('no_args_status');
       }else{


           if($status=='success'){
               $invest = \Prj\Data\Investment::getCopy($ordersId);
               $invest->load();
               if(!$invest->exists()){
                   return $this->_returnError('订单不存在');
               }else{
                   $orderStatus = $invest->getField('orderStatus');
                   if($amount!=$invest->getField('amount')){
                       return $this->_returnError('错误的金额');
                   }
                   if($orderStatus==\Prj\Consts\OrderStatus::flow){
                       return $this->_returnOK();
                   }else{
                       if(!in_array($orderStatus,[\Prj\Consts\OrderStatus::waiting])){
                           return $this->_returnError('非法的订单状态');
                       }else{
                           $userId = $invest->getField('userId');
                           $user = \Prj\Data\User::getCopy($userId);
                           $user->load();
                           if(!$user->exists()){
                               return $this->_returnError('用户不存在');
                           }
                           var_log('[warning]流标回调#标的号:'.$invest->getField('waresId'));
                           $ware = \Prj\Data\Wares::getCopy($invest->getField('waresId'));
                           $ware->load();
                           if(!$ware->exists()){
                               return $this->_returnError('标的不存在');
                           }

                           $this->user = $user;
                           $this->ware = $ware;
                           if(!$this->user->lock('流标锁定')){
                               sleep(1);
                               $this->user->reload();
                               if(!$this->user->lock('流标锁定')){
                                   $this->$ware->unlock();
                                   return $this->_returnError('用户锁定失败');
                               }
                           }
                            $this->ware->reload();
                           if(!$this->ware->lock('流标锁定')){
                               sleep(1);
                               $this->ware->reload();
                               if(!$this->ware->lock('流标锁定')){
                                   $this->user->unlock();
                                   $this->ware->unlock();
                                   return $this->_returnError('标的锁定失败');
                               }
                           }

                           $userInit = $this->user->dump(); //记录用户初始值
                           $wareInit = $this->ware->dump(); //记录标的初始值

                           //返还红包
                           $vouchers = $invest->getField('vouchers');
                           if(!empty($vouchers)){
                               $vouchersArr = explode(',',$vouchers);
                               if(!empty($vouchersArr)){
                                   foreach($vouchersArr as $voucherId){
                                       $voucher = \Prj\Data\Vouchers::getCopy($voucherId);
                                       $voucher->load();
                                       if(!$voucher->exists()){
                                           var_log('[error]流标回调 红包激活失败 voucherId:'.$voucherId);
                                       }else{
                                            if($voucher->getField('statusCode')==\Prj\Consts\Voucher::status_unuse)continue;
                                           $tmp = [
                                               'voucherId'=>$voucherId,
                                               'orderId'=>$voucher->getField('orderId'),
                                               'dtUsed'=>$voucher->getField('dtUsed'),
                                               'dtExpired'=>$voucher->getField('dtExpired'),
                                           ];
                                           $voucher->setField('statusCode',\Prj\Consts\Voucher::status_unuse);
                                           $voucher->setField('orderId',0);
                                           $voucher->setField('dtUsed',0);
                                           $voucher->setField('dtExpired',date('Ymd',strtotime('+2 days')).'235959');
                                           $voucher->setField('descCreate','流标补偿红包');
                                           $voucher->setField('codeCreate','liubiaobuchang');

                                           try{
                                               $voucher->update();
                                               if($voucher->getField('voucherType')==\Prj\Consts\Voucher::type_real)$redAmount+=$voucher->getField('amount');
                                               $this->voucherArr[$voucherId] = $tmp;
                                           }catch (\ErrorException $e){
                                               var_log('[error]流标错误 券激活失败 voucherId:'.$voucherId);
                                               $ret = $this->roll_back();
                                               if(!empty($ret)){
                                                   return $this->_returnError($ret);
                                               }else{
                                                   return $this->_returnError('券更新失败');
                                               }
                                           }
                                       }
                                   }
                               }
                           }
                           //返还红包额度
                           if($invest->getField('amountExt')!=0){
                               $user->setField('redPacket',$user->getField('redPacket')+$redAmount);
                           }
                           //返还钱包额度
                           \Prj\Misc\OrdersVar::$introForUser = "标的_".$invest->getField('waresId')."_流标";
                           \Prj\Misc\OrdersVar::$introForCoder = "wares_".$invest->getField('waresId')."_flow";
                           $tally = \Prj\Data\WalletTally::addTally($userId,$user->getField('wallet'),$invest->getField('amount'),0,$invest->getField('ordersId'),\Prj\Consts\OrderType::flow);
                           $tally->setField('statusCode',\Prj\Consts\WalletTally::status_new);
                           $user->setField('wallet',$user->getField('wallet')+$invest->getField('amount'));
                           //标的处理
                           $num = $ware->getField('waitInvestNum');
                           $num--;
                           $num = $num<0?0:$num;
                           var_log('流标>>> '.$num);
                           $ware->setField('waitInvestNum',$num);
                           if($num==0){
                               $ware->setField('payStatus',\Prj\Consts\PayGW::success);
                               $ware->setField('exp','流标处理完成');
                           }else{
                               $ware->setField('exp',"剩余 $num 条订单等待流标");
                           }
                           //订单处理
                           $invest->setField('orderStatus',\Prj\Consts\OrderStatus::flow);
                           $invest->setField('exp','流标成功');

                           try{
                               $tally->update();
                               try{
                                   $user->update();
                                   try{
                                       $ware->update();
                                       try{
                                           $invest->update();
                                       }catch (\ErrorException $e){
                                            $this->roll_back($tally,$userInit,$wareInit);
                                           return $this->_returnError('订单更新失败'.$e->getMessage());
                                       }
                                   }catch (\ErrorException $e){
                                        $this->roll_back($tally,$userInit);
                                       return $this->_returnError('标的更新失败'.$e->getMessage());
                                   }
                               }catch (\ErrorException $e){
                                    $this->roll_back($tally);
                                   return $this->_returnError('用户更新失败'.$e->getMessage());
                               }
                           }catch (\ErrorException $e){
                               $this->roll_back();
                               return $this->_returnError('流水更新失败'.$e->getMessage());
                           }

                           $this->_unfreezeTally($invest->getField('ordersId'));
                       }
                   }
               }
           }else{
               //status failed
               $invest = \Prj\Data\Investment::getCopy($ordersId);
               $invest->load();
               if(!$invest->exists()) {
                   return $this->_returnError('订单不存在');
               }
               $orderStatus = $invest->getField('orderStatus');
               if($orderStatus==\Prj\Consts\OrderStatus::flow){
                   return $this->_returnOK();
               }
               if($orderStatus!=\Prj\Consts\OrderStatus::waiting){
                   return $this->_returnError('非法的订单状态');
               }
               try{
                  $invest->setField('exp','网关返回失败:'.$msg);
                  // $invest->setField('orderStatus',\Prj\Consts\PayGW::failed);
                   $invest->update();
               }catch (\ErrorException $e){
                    return $this->_returnError($e->getMessage());
               }
           }

           return $this->_returnOK();
       }
    }

    protected function roll_back($tally = null,$userInit = [],$wareInit = []){
        if(!$userInit)$this->user->unlock();
        if(!$wareInit)$this->ware->unlock();
        if($tally){
            $tally->updStatus(\Prj\Consts\WalletTally::status_abandon);
            $tally->update();
        }
        if($userInit){
            $this->user->setField('redPacket',$userInit['redPacket']);
            $this->user->setField('wallet',$userInit['wallet']);
            $this->user->update();
        }
        if($wareInit){
            $this->ware->setField('waitInvestNum',$wareInit['waitInvestNum']);
            $this->ware->setField('payStatus',$wareInit['payStatus']);
            $this->ware->setField('exp',$wareInit['exp']);
            $this->ware->update();
        }

        if(!empty($this->voucherArr)){
            foreach($this->voucherArr as $v){
                $tmpVoucher = \Prj\Data\Vouchers::getCopy($v['voucherId']);
                $tmpVoucher->setField('statusCode',\Prj\Consts\Voucher::status_used);
                $tmpVoucher->setField('orderId',$v['orderId']);
                $tmpVoucher->setField('dtUsed',$v['dtUsed']);
                $tmpVoucher->setField('dtExpired',$v['dtExpired']);
                try{
                    $tmpVoucher->update();
                }catch (\ErrorException $e){
                    var_log('[error]流标错误 券激活回滚失败 回滚券:voucherId:'.$v['voucherId']);
                    return '系统错误:券回滚失败!'.$e->getMessage();
                }
            }
        }
    }

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
			return $this->rpc->initArgs(array('SN'=>$sn,'userId'=>$userId,'bankId'=>$bankId,'bankCard'=>$bankCard,'amount'=>$amount,'userIP'=>$userIP,'wwwway'=>$wwwway,'ip'=>\Sooh\Base\Tools::remoteIP()))->send(__FUNCTION__);
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
                'ip'=>\Sooh\Base\Tools::remoteIP(),
            ];
            var_log($arr,'[购买通知'.$arr["ordersId"].']>>>>>>>>>>>>>>>>>>>>>');
            return $this->rpc->initArgs($arr)->send(__FUNCTION__);
        }else{
            $rand = rand(1,2);
            switch ($rand){
                case 1: 	return ["code"=>200];
                case 2: 	return ["code"=>400];
            }
        }
    }
    public function withdrawFreeze($withdrawId,$userId,$amont,$poundage)
    {
        if($this->rpc!==null){
            $arr = [
                'withdrawId'=>$withdrawId,
                'userId'=>$userId,
                'amount'=>$amont,
                'poundage'=>$poundage,
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
     * 手动转账
     */
    public function manualTrans($sn,$amount,$fromUid,$toUid)
    {
        if($this->rpc!==null){
            $data = [
                $sn,
                $amount,
                $fromUid,
                $toUid,
            ];
            var_log("[manualTrans]发给网关的参数 >>>");
            return $this->rpc->initArgs($data)->send(__FUNCTION__);
        }else{
            $rand = 2;
            switch ($rand){
                case 1: 	return ["status"=>1,"payCorp"=>111];
                case 2: 	return ["status"=>4,'reason'=>'测试#error'];
            }
        }
    }

    /**
     * 转账（募集满后确认付款到企业户）
     * 管理员操作后台时触发，从用户子账户或平台账户转到企业户
     * 以新浪支付来说，先比较标的投资订单总金额，如果正确，从各
     * 个用户账（平台赠送的部分）上归集资金到借款人在新浪的子账户，注意打标记，重复发送的情况的处理
     */
    public function trans($sn,$waresId,$amountReal,$amountGift,$amountTotal,$managementTrans,$borrowerId,$borrowerName,$borrowerTunnel = 'auto')
    {
        if($this->rpc!==null){
            return $this->rpc->initArgs([
                'SN'=>$sn,
                'waresId'=>$waresId,
                'amountReal'=>round($amountReal),
                'amountGift'=>round($amountGift),
                'amountTotal'=>round($amountTotal-$managementTrans), //todo 减去服务费后的总金额,即打款总金额
                'managementTrans'=>round($managementTrans),
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
        //set_time_limit(2);
        var_log('[warning]满标转账回调: ordersId:'.$ordersId.' status:'.$status.' msg:'.$msg);
        $invest = \Prj\Data\Investment::getCopy($ordersId);
        $invest->load();
        if(!$invest->exists())return $this->_returnError('void_invest');
        if($invest->getField('orderStatus')==\Prj\Consts\OrderStatus::payed){
            goto RETRY;
        }
        if($invest->getField('orderStatus')!=\Prj\Consts\OrderStatus::waiting && $invest->getField('orderStatus')!=\Prj\Consts\OrderStatus::waitingGW){
            return $this->_returnError('error_status');
        }
        if(!in_array($status,['success','failed']))return $this->_returnError('unknown_status');
        if($status=='success'){
            $invest->updStatus(\Prj\Consts\OrderStatus::payed);
            $invest->setField('exp',$status);
            $invest->setField('transTime',date('YmdHis'));

            /*
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
            */

            //解冻流水
            $this->_unfreezeTally($ordersId);
        }else{
            $invest->setField('transTime',date('YmdHis'));
            $invest->updStatus(\Prj\Consts\OrderStatus::failed);
            $invest->setField('exp',$msg);
        }

        try{
            $invest->update();

	        if ($status == 'success') {
                    try {
                        $_dbUser = \Prj\Data\User::getCopy($invest->getField('userId'));
                        $_dbUser->load();
                        \Prj\ReadConf::run(
                            [
                                'event'    => 'look_ok',
                                'pro_name' => $invest->getField('waresName'),
                                'time_all' => 24,
                                'brand'    => \Prj\Message\Message::MSG_BRAND,
                                'cont_ok'  => '投资记录'
                            ],
                            ['phone' => $_dbUser->getField('phone'), 'userId' => $invest->getField('userId')]
                        );
                        \Prj\ReadConf::run(
                            [
                                'event'       => 'money_start',
                                'pro_name'    => $invest->getField('waresName'),
                                'touzi_money' => ($invest->getField('amount') + $invest->getField('amountExt')) / 100,
                                'money_back'  => \Prj\Consts\Wares::$shilfIdName[$invest->getField('shelfId')],
                            ],
                            ['userId' => $invest->getField('userId')]
                        );
                    } catch (\ErrorException $e) {

                    }
                            }

        }catch (\ErrorException $e){
            return $this->_returnError($e->getMessage());
        }

        /*
        $waresId = $invest->getField('waresId');
        $ware = \Prj\Data\Wares::getCopy($waresId);
        $ware->load();
        */

        if($status==='success'){
            /*
            $waitInvestNum = $ware->getField('waitInvestNum')-1?$ware->getField('waitInvestNum')-1:0;
            $ware->setField('waitInvestNum',$waitInvestNum);
            if($waitInvestNum==0){
                $ware->setField('payStatus',\Prj\Consts\PayGW::success);
                $ware->setField('payYmd',date('YmdHis'));
                //todo 更新标的还款计划
                try{
                    $returnPlan = \Prj\ReturnPlan\All01\ReturnPlan::calendar($ware);
                    $ware->setField('nextConfirmYmd',$returnPlan->getYmdNext());
                    $ware->setField('returnPlan',$returnPlan->decode());
                }catch (\ErrorException $e){
                    var_log('[error]transResult create returnPlan failed '.$e->getMessage());
                }

            }
            */

            /*
            if($waitInvestNum>0){
                $ware->setField('exp',"剩余".$ware->getField('waitInvestNum')."条订单等待处理");
            }else{
                $ware->setField('exp',"订单全部处理完成");
                //todo 记录对账资料
                try{
                    var_log('>>> 记录放款对账资料 >>> ');
                    $data = $ware->dump();
                    $data['sn'] = $data['paySn'];
                    \Prj\Check\DayLoan::addRecords($data);
                }catch (\ErrorException $e){
                    var_log('[warning]记录对账数据失败');
                }
            }
            */

            /*
            try{
                $ware->update();
            }catch (\ErrorException $e){
                var_log("[error]更新标的备注失败");
                var_log($e->getMessage());
                var_log($e->getTraceAsString());
            }
            */
        }

        //记录对账数据
        if($status=='success'){
            $data = $invest->dump();
            $data['sn'] = $ordersId;
            try{
                \Prj\Check\DayBuy::addRecords($data);
            }catch (\ErrorException $e){
                var_log($e->getMessage(),'[error] >>> ');
            }

            /*
            if($waitInvestNum==0){
                //todo 批量更新还款计划
                try{
                    $ret = \Prj\Wares\Wares::getCopy()->updateReturnPlan($waresId);
                }catch (\ErrorException $e){
                    var_log('[error]满标转账更新还款计划 >>> '.$e->getMessage());
                }
                var_log('[warning]还款计划更新情况 >>> '.$ret);
            }
            */

            try{
                //todo 激活返利
                $rebateId = $invest->getField('rebateId');
                if(!empty($rebateId)){
                    try{
                        $ret = \Prj\Items\Rebate::openRebate($ordersId);
                    }catch (\ErrorException $e){
                        var_log($e->getTraceAsString());
                    }
                    var_log($ret,'[warning]激活返利网关处理结果:');
                }
            }catch (\ErrorException $e){
                var_log('[error]'.$e->getMessage());
            }
            RETRY:  //todo 应对还款计划跑不完的情况
            //todo 满标转账结束 更新还款计划
            try{
                \Prj\Wares\Wares::updatePlanWhenTransOver($invest->getField('waresId'));
            }catch (\ErrorException $e){

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
    public function rebate($sn,$amount,$userId,$rebateId,$ordersId){
        if($this->rpc!==null){
            return $this->rpc->initArgs([
                'SN'=>$sn,
                'amount'=>round($amount),
                'userId'=>$userId,
                'rebateId'=>$rebateId,
                'ordersId'=>$ordersId,
            ])->send(__FUNCTION__);
        }else{
            $rand = 2;
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
                'amountPlan'=>round($amountPlan),
                'amountReal'=>round($amountReal),
                'amountLeft'=>round($amountLeft),
                'borrowerId'=>$borrowerId,
                'borrowerName'=>$borrowerName,
                'borrowerTunnel'=>$borrowerTunnel,
                //新增两个参数
                'servicePay'=>round($servicePay), //借款人需要支付的佣金
                'giftPay'=>round($giftPay), //借款人无力偿还,平台代为垫付

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
     * 打款给借款人
     * @param $sn
     * @param $waresId
     * @param $id
     * @param $amountPlan
     * @param $interestPlan
     * @param $servicePay
     * @param $realPay
     * @param $borrowerId
     * @param $borrowerName
     * @return array|mixed
     * @throws \Sooh\Base\ErrException
     */
    public function remit($sn,$waresId,$id,$amountPlan,$interestPlan,$servicePay,$realPay,$borrowerId,$borrowerName){
        if($this->rpc!==null){
            $data = [
                'SN' => $sn,
                'waresId' => $waresId,
                'id'=>$id,
                'amountPlan' => round($amountPlan),
                'interestPlan' => round($interestPlan),
                'servicePay' => round($servicePay), //佣金
                'realPay' =>round( $realPay), //实际打款
                'borrowerId' => $borrowerId,
                'borrowerName' => $borrowerName,
                'borrowerTunnel'=>'auto',
            ];
            return $this->rpc->initArgs($data)->send(__FUNCTION__);
        }else{
            $rand = 1;
            switch ($rand){
                case 1: 	return ['status'=>1,'reason'=>''];
                case 2: 	return ['status'=>4,'reason'=>'失败啦！'];
            }
        }
    }

    public function remitResult($sn,$waresId,$borrowerId,$amount,$status,$msg = ''){
        $error = '';
        switch(true){
            case empty($sn) : $error = 'no_sn';break;
            case empty($waresId) : $error = 'no_waresId';break;
            case empty($amount) : $error = 'no_amount';break;
            case empty($borrowerId) : $error = 'no_borrowerId';break;
            case !in_array($status,['success','failed']) : $error = 'no_status';break;
        }
        if(!empty($error))return $this->_returnError($error);

        $ware = \Prj\Data\Wares::getCopy($waresId);
        $ware->load();
        if(!$ware->exists())return $this->_returnError('void_ware');
        $rp = \Prj\ReturnPlan\All01\ReturnPlan::createPlan($ware->getField('returnPlan'));
        $plan = current($rp->getPlan(['remitSN'=>$sn]));
        if(empty($plan))return $this->_returnError('void_sn');
        if($plan['remitAmount']!=$amount)return $this->_returnError('void_amount');
        if($plan['remitStatus']==\Prj\Consts\PayGW::success)return $this->_returnOK('成功啦');
        if($plan['remitStatus']!=\Prj\Consts\PayGW::accept)return $this->_returnError('void_status');
        if($ware->getField('borrowerId')!=$borrowerId)return $this->_returnError('vouid_borrowerId');
        if($status=='success'){
            $tally = \Prj\Data\Systally::addTally($sn,-$amount,$borrowerId,$waresId,\Prj\Consts\Systally::pay_status);
            $tally->setField('type',\Prj\Consts\PayGW::tally_remit);
            $tally->setField('exp','第'.$plan['id'].'期还款');
            $tally->setField('payYmd',date('YmdHis'));

            $rp->updatePlanById('remitStatus',\Prj\Consts\PayGW::success,$plan['id']);
            if($plan['ahead']){
                $rp->clearPlanWhenAhead($plan['id']);
                $rp->updatePlanById('exp','提前还款',$plan['id']);
            }
        }else{
            $rp->updatePlanById('remitStatus',\Prj\Consts\PayGW::failed,$plan['id']);
            $rp->updatePlanById('exp','打款:'.$msg,$plan['id']);
        }
        try{
            if($tally){
                $tally->update();
            }
        }catch (\ErrorException $e){
            var_log('[error]remitResult tally update failed :'.$e->getMessage());
            return $this->_returnError('db_error');
        }

        try{
            $ware->setField('returnPlan',$rp->decode());
            $ware->update();
        }catch (\ErrorException $e){
            var_log('[error]remitResult ware update failed :'.$e->getMessage());
            $tally->setField('statusCode',\Prj\Consts\Systally::cancel_status);
            $tally->update();
            return $this->_returnError('db_error');
        }

        return $this->_returnOK('成功啦');
    }



    public function confirmResult($sn,$waresId,$status,$msg = '')
    {
        var_log('[回款回调]#confirmResult#sn:'.$sn.' status:'.$status.'>>>>>>>>>>>>>>>>>>>');
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
            $returnPlan->updatePlan('retryBtnShow',0,['sn'=>$sn]);
            $returnPlan->updatePlan('isPay',1,['sn'=>$sn]);
            $ware->setField('nextConfirmYmd',$returnPlan->getYmdNext());
            $returnPlan->updatePlan('realDateYmd',date('Ymd'),['sn'=>$sn]);
            $msg = $this->noticeArr[$msg]? $this->noticeArr[$msg]:$msg;
            $returnPlan->updatePlan('exp',$msg,['sn'=>$sn]);
            if($plan['ahead']==1){ //提前回款
                $returnPlan->clearPlanWhenAhead($plan['id']);
                $returnPlan->updatePlan('exp','提前还款#'.$msg,['sn'=>$sn]);
                $ware->setField('statusCode',\Prj\Consts\Wares::status_ahead);
            }elseif($plan['amount']>0){
                $ware->setField('statusCode',\Prj\Consts\Wares::status_close);
            }
            if($plan['giftPay']>0){  //平台垫付
                $ware->setField('confirmGift',$ware->getField('confirmGift')+$plan['giftPay']);
            }
        }else{
            $returnPlan->updatePlan('status',\Prj\Consts\PayGW::failed,['sn'=>$sn]);
            $returnPlan->updatePlan('exp',$msg,['sn'=>$sn]);
        }
        try{
            $returnPlanStr = $returnPlan->decode();
            if(empty($returnPlanStr['calendar']))throw new \ErrorException('还款计划异常');
            $ware->setField('returnPlan',$returnPlanStr);
            $ware->update();

            if($status == 'success'){
	            if($ware->getField('autoReturnFund')==1){
	                //todo 接批量回款
	                try{
	                    var_log('[warning]自动回款关闭>>>');
	                     $ret = \Prj\Wares\Wares::getCopy()->returnFundAll($plan['id'],$waresId,$sn,$plan['ahead']);
	                }catch (\ErrorException $e){
	                    var_log('[error]confirmResult returnFundAll failed:'.$e->getMessage());
	                }
	               var_log('[warning]批量回款结果:'.$ret);
	            }
            }

        }catch (\ErrorException $e){
            return $this->_returnError($e->getMessage());
        }

        if($status=='success'){
            try{
                var_log('>>> 记录还款对账资料 >>>');
                $data = $plan;
                $data['amount'] = $plan['realPay'];
                \Prj\Check\DayPayback::addRecords($data);
            }catch (\ErrorException $e){
                var_log('>>> 记录还款对账资料:失败>>>'.$e->getMessage());
            }
        }
        return $this->_returnOK();
    }

    /**
     * 还本还息
     * 管理员操作后台时触发，用于指定产品回款到用户（本息）
     * 就新浪来说，检查还款批次对应的余额，够就按指定的本金总额转到用户账户
     * @param $sn
     * @param $confirmSN
     * @param $ordersId
     * @param $waresId
     * @param $userId
     * @param $amount
     * @param $interest
     * @param $interestSub
     * @param int $borrowerId
     * @param string $borrowerName
     * @return array|mixed
     * @throws \Sooh\Base\ErrException
     */
    public function returnFund($sn,$confirmSN,$ordersId,$waresId,$userId,$amount,$interest,$interestSub,$borrowerId = 0,$borrowerName = '')
    {
        $data = [
            'cmd'=>'returnFund',
            'SN'=>$sn,
            'confirmSN'=>$confirmSN,
            'ordersId'=>$ordersId,
            'waresId'=>$waresId,
            'userId'=>$userId,
            'amount'=>round($amount),
            'interest'=>round($interest),
            'interestSub'=>round($interestSub),
            'borrowerId'=>$borrowerId,
            '$borrowerName'=>$borrowerName,
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

    public function returnFundBatch($batchId,$list){
        if(is_array($list)){
            $list = json_encode($list);
        }
        $data = [
            'batchId'=>$batchId,
            'list'=>$list,
            //'isPost'=>1,
        ];
        var_log('[returnFundBatch]>>>');
        var_log($data,'给returnFund发送的参数>>>>>>>>>>>>');
        if($this->rpc!==null ){
            return $this->rpc->initArgs($data)->send(__FUNCTION__);
        }else{
            var_log('returnFundBatch 测试代码 >>>');
            $rand = 1;
            switch ($rand){
                case 1: 	return ['status'=>1,'reason'=>''];
                case 2: 	return ['status'=>4,'reason'=>'失败啦！'];
            }
        }
    }

    public function showReturnFund($waresId,$confirmSN){
        $ware = \Prj\Data\Wares::getCopy($waresId);
        $ware->load();
        $wareRp = \Prj\ReturnPlan\All01\ReturnPlan::createPlan($ware->getField('returnPlan'),$ware);
        $warePlan = current($wareRp->getPlan(['sn'=>$confirmSN]));
        if(empty($warePlan))return $this->_returnError('error_confirmSN');
        $id = $warePlan['id'];
        var_log($warePlan,'warePlan >>> ');
        $investList = \Prj\Data\Investment::loopFindRecords(['waresId' => $waresId, 'orderStatus]' => \Prj\Consts\OrderStatus::payed]);
        $list = [];
        if($investList){
            foreach($investList as $v){
                $invest = \Prj\Data\Investment::getCopy($v['ordersId']);
                $invest->load();
                $ordersId = $v['ordersId'];
                $userId = $v['userId'];
                if(empty($v['returnPlan'])) {
                    $rp = \Prj\ReturnPlan\Std01\ReturnPlan::calendar($v['ordersId']);
                } else {
                    $rp = \Prj\ReturnPlan\Std01\ReturnPlan::createPlan($v['returnPlan']);
                }
                $plan = $rp->getPlanById($id);
                if(empty($plan)){
                    $failedData[] = $ordersId;
                    continue;
                }else {
                    if ($plan['isPay'] == 1 ) { //
                        continue;
                    } else {
                        $tmp = [
                            'cmd'=>'returnFund',
                            'SN'=>$plan['sn'],
                            'confirmSN'=>$confirmSN,
                            'ordersId'=>$plan['ordersId'],
                            'waresId'=>$plan['waresId'],
                            'userId'=>$userId,
                            'amount'=>$plan['realPayAmount'],
                            'interest'=>$plan['realPayInterest'],
                            'interestSub'=>$plan['interestSub'] - 0,
                            'borrowerId'=>$ware->getField('borrowerId'),
                            'isPay'=>$plan['isPay'],
                            'status'=>$plan['status'],
                            'exp'=>$plan['exp']
                        ];
                        $list[] = $tmp;
                    }
                }
            }
        }
        $this->_assign('list',$list);
        return $this->_returnOK('ok');
    }

    public function returnInterestSub($waresId){
        $data = [
            'waresId'=>$waresId
        ];

        if($this->rpc!==null ){
            return $this->rpc->initArgs($data)->send(__FUNCTION__);
        }else{
            var_log('returnInterestSub 测试代码 >>>');
            $rand = 1;
            switch ($rand){
                case 1: 	return ['status'=>1,'reason'=>''];
                case 2: 	return ['status'=>4,'reason'=>'失败啦！'];
            }
        }
    }

    public function returnInterestSubResult($ordersId , $interestSub = 0 , $sn ,$status){
        if(empty($sn))return $this->_returnError('no_sn');
        $invest = \Prj\Data\Investment::getCopy($ordersId);
        $invest->load();
        if(!$invest->exists())return $this->_returnError('ordersId_error');
        $userId = $invest->getField('userId');
        $user = \Prj\Data\User::getCopy($userId);
        $user->load();
        if(!$user->lock('lock by returnInterestSubResult #'.$ordersId)){
            return $this->_returnError('lock_failed');
        }
        $invest->reload();

        $returnPlan = \Prj\ReturnPlan\Std01\ReturnPlan::createPlan($invest->getField('returnPlan'));
        $returnPlanInit = $invest->getField('returnPlan');
        $plan = current(array_slice($returnPlan->calendar,-1));
        if(empty($plan['amount'])){
            $user->unlock();
            return $this->_returnError('error_plan');
        }
        if($plan['isPay']!=1){
            $user->unlock();
            return $this->_returnError('error_isPay');
        }
        if($plan['interestSub']!=$interestSub){
            $user->unlock();
            return $this->_returnError('error_interestSub');
        }
        if(!empty($plan['realPayinterestSub'])){
            $user->unlock();
            return $this->_returnOK('no_need_repay');
        }

        if($plan['repay']==8){
            $user->unlock();
            return $this->_returnOK('have_pay');
        }

        if($plan['repay']==4){
            $user->unlock();
            return $this->_returnError('have_failed');
        }

        $key = end(array_keys($returnPlan->calendar));
        if(empty($returnPlan->calendar[$key])){
            $user->unlock();
            return $this->_returnError(__LINE__.'#代码错误');
        }
        if($returnPlan->calendar[$key]['amount'] == 0){
            $user->unlock();
            return $this->_returnError(__LINE__.'#代码错误');
        }

        if($status=='success'){

            \Prj\Misc\OrdersVar::$introForUser = '您投资的 '.$invest->getField('waresName').' 第'.ceil($plan['id']).'期 补发贴息';
            \Prj\Misc\OrdersVar::$introForCoder = 'return_fund_'.$sn;

            $tallySub = \Prj\Data\WalletTally::addTally($userId,$user->getField('wallet')+$interestSub,$interestSub,0,$ordersId,\Prj\Consts\OrderType::giftInterest);
            $tallySub ->setField('sn',$sn);
            $wallet = $user->getField('wallet');
            $user->setField('wallet',$user->getField('wallet')+$interestSub);

            $returnPlan->calendar[$key]['realPayinterestSub'] = $interestSub;
            $returnPlan->calendar[$key]['repaySN'] = $sn;
            $returnPlan->calendar[$key]['repay'] = 8;
            try{
                $tallySub->updStatus(\Prj\Consts\WalletTally::status_new);
                $tallySub->update();
            }catch (\ErrorException $e){
                $user->unlock();
                return $this->_returnError($e->getMessage());
            }

            try{
                $invest->setField('returnPlan',$returnPlan->decode());
                $invest->update();
            }catch (\ErrorException $e){
                $user->unlock();
                $tallySub->updStatus(\Prj\Consts\Tally::status_abandon)->update();
                return $this->_returnError($e->getMessage());
            }

            try{
                $user->update();
            }catch (\ErrorException $e){
                $tallySub->updStatus(\Prj\Consts\Tally::status_abandon)->update();
                $invest->setField('returnPlan',$returnPlanInit);
                $invest->update();
                $user->unlock();
                return $this->_returnError($e->getMessage());
            }

            return $this->_returnOK();
        }elseif($status=='failed'){
            $returnPlan->calendar[$key]['repay'] = 4;
            try{
                $invest->setField('returnPlan',$returnPlan->decode());
                $invest->update();
            }catch (\ErrorException $e){
                $user->unlock();
                return $this->_returnError($e->getMessage());
            }
            $user->unlock();
            return $this->_returnOK();
        }else{
            $user->unlock();
            return $this->_returnError('error_give_status');
        }
    }

    public function showInterestSub($waresId){
        $where = [
            'orderStatus'=>\Prj\Consts\OrderStatus::done,
            'waresId'=>$waresId,
        ];
        $where['LEFT(transTime,8)-LEFT(orderTime,8)>'] = 1;
        $list = \Prj\Data\Investment::loopFindRecords($where);
        $newList = [];
        if($list){
            array_walk($list,function(&$v,$k)use(&$newList){
                $ordersId = $v['ordersId'];
                $returnPlan = \Prj\ReturnPlan\Std01\ReturnPlan::calendar($v['ordersId']);
                //
                if($plans = $returnPlan->calendar){
                    $oldReturnPlan = \Prj\ReturnPlan\Std01\ReturnPlan::createPlan($v['returnPlan']);
                    $oldPlan = current(array_slice($oldReturnPlan->calendar,-1));
                    if($oldPlan['interestSub']>0){
                        if($oldPlan['isPay']!=1)return;
                        // var_log($oldPlan);
                        $oldPlan['userId'] = $v['userId'];
                        //$oldPlan['repay'] = 1;
                        $newList[] = $oldPlan;
                    }else{

                    }
                }else{

                }
            });
        }
        return $this->_assign('list',$newList);
    }

    public function test($batchId,$list){
        if(is_array($list)){
            $list = json_encode($list);
        }
        $data = [
            'batchId'=>$batchId,
            'list'=>$list,
        ];
        var_log($this->rpc,'this->rpc >>> ');
        if($this->rpc!==null ){
            return $this->rpc->initArgs($data)->send(__FUNCTION__);
        }else{
            var_log('returnFundBatch 测试代码 >>>');
            $rand = 1;
            switch ($rand){
                case 1: 	return ['status'=>1,'reason'=>'hahahahahahaha'];
                case 2: 	return ['status'=>4,'reason'=>'失败啦！'];
            }
        }
    }

    public function testResult($ordersId){
        $rpc = \Sooh\Base\Rpc\Broker::factory('PayGW');
        var_log($rpc,' >>> ');
        $sys = \Lib\Services\PayGW::getInstance($rpc , true);
        try{
            $ret = $sys->test('123456','654321');
        }catch(\Sooh\Base\ErrException $e){
            $this->loger->error('send order to gw failed where addorder '.$e->getMessage());
            $code = $e->getCode();
            if($code==400){
                $this->_view->assign('retAll',['ret'=>$e->getMessage(),'got'=>$e->customData]);
            }elseif($code==500){
                $this->_view->assign('retAll',['ret'=>$e->getMessage(),'got'=>$e->customData]);
            }
        }

        var_log($ret,' >>> ');
    }

    /**
     * 逾期还款
     * @param $sn
     * @param $waresId
     * @param $borrowerId
     * @param $repay
     * @return array|mixed
     * @throws \Sooh\Base\ErrException
     */
    public function delayConfirm($sn,$waresId,$borrowerId,$repay){
        $data = [
            'SN'=>$sn,
            'waresId'=>$waresId,
            'borrowerId'=>$borrowerId,
            'realRepay'=>round($repay),
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

    protected function delayConfirmResult($sn,$waresId,$borrowerId,$amount,$status,$msg = ''){
        $tally = \Prj\Data\Systally::getCopy($sn);
        $tally->load();
        if(!$tally->exists())return $this->_returnError('void_sn');
        if($tally->getField('statusCode')==\Prj\Consts\Systally::pay_status)return $this->_returnOK('成功啦');
        if($tally->getField('statusCode')!=\Prj\Consts\Systally::wait_status)return $this->_returnError('error_statusCode');
        if($tally->getField('waresId')!=$waresId)return $this->_returnError('void_waresId');
        if($tally->getField('userId')!=$borrowerId)return $this->_returnError('void_borrowerId');
        if($tally->getField('amount')!=$amount)return $this->_returnError('void_amount');
        if(!in_array($status,['success','failed']))return $this->_returnError('void_status');

        if($status=='success'){
            $ware = \Prj\Data\Wares::getCopy($waresId);
            $ware->load();
            if(!$ware->exists())return $this->_returnError('void_ware');
            $tally->setField('statusCode',\Prj\Consts\Systally::pay_status);
            $repayInit = $ware->getField('repay');
            $ware->setField('repay',$repayInit+$amount);

            try{
                $ware->update();
            }catch (\ErrorException $e){
                var_log('[error]delayConfirmResult ware update failed '.$e->getMessage());
                return $this->_returnError($e->getMessage());
            }
        }else{
            $tally->setField('statusCode',\Prj\Consts\Systally::failed_status);
            $tally->setField('exp',$msg);
        }

        try{
            $tally->update();
        }catch (\ErrorException $e){
            if($status=='success'){
                $ware->setField('repay',$repayInit);
                $ware->update();
            }
            var_log('[error]delayConfirmResult tally update failed '.$e->getMessage());
            return $this->_returnError($e->getMessage());
        }

        return $this->_returnOK('成功啦');
    }

    /**
     * 扣平台账回调
     * @param $dowhat  动作名称 'confirm','trans','managementTrans','managementConfirm','rebate'
     * @param $sn  流水号
     * @param $amount 金额 正的是加钱 负的是减钱
     * @param $userId 用户ID,借款人ID
     * @param $status 'success','failed'
     * @param string $msg 出错的时候 错误原因
     * @param int $waresId 标的ID
     * @return array
     */
    public function giftPayResult($dowhat,$sn,$amount,$userId = 0,$status,$msg = '',$waresId = 0,$rebateId = 0){
        if(empty($dowhat))return $this->_returnError('args_do_miss');
        if(empty($sn))return $this->_returnError('args_sn_miss');
        if(empty($amount))return $this->_returnError('args_amount_miss');
        if(empty($userId))return $this->_returnError('args_userId_miss');
        if(empty($status))return $this->_returnError('args_status_miss');
        $tally = \Prj\Data\Systally::addTally($sn,$amount,$userId,$waresId);
        if(empty($tally))return $this->_returnError('repeat_request');
        switch($dowhat){
            case 'confirm':$tally->setField('type',\Prj\Consts\PayGW::tally_confirm);$tally->setField('amount',abs($amount)*-1);break;
            case 'trans':$tally->setField('type',\Prj\Consts\PayGW::tally_trans);$tally->setField('amount',abs($amount)*-1);break;
            case 'managementTrans': $tally->setField('type',\Prj\Consts\PayGW::tally_managementTrans);break;
            case 'managementConfirm': $tally->setField('type',\Prj\Consts\PayGW::tally_managementConfirm);break;
            case 'rebate':$tally->setField('type',\Prj\Consts\PayGW::tally_rebate);$tally->setField('amount',abs($amount)*-1);break;
            case 'interestSub': $tally->setField('type',\Prj\Consts\PayGW::tally_interestSub);$tally->setField('amount',abs($amount)*-1);break;
            default : return $this->_returnError('error_do');
        }
        $tally->setField('payYmd',date('YmdHis'));
        if($status=='success'){
            $tally->setField('statusCode',\Prj\Consts\Systally::pay_status);
            $tally->setField('rebateId',$rebateId);
            $tally->setField('exp',$status);
        }else{
            $tally->setField('statusCode',\Prj\Consts\Systally::failed_status);
            $tally->setField('exp',$msg);
        }
        try{
            $tally->update();
        }catch (\ErrorException $e){
            return $this->_returnError($e->getMessage());
        }
        return $this->_returnOK();
    }

    public $noticeArr = [
        'TRADE_FINISHED'=>'交易成功',
        'TRADE_FAILED'=>'交易失败',
        'PAY_FINISHED'=>'支付成功',
    ];

    /**
     * 还本还息回调
     *
     */
    public function returnFundResult($sn,$ordersId,$realPayAmount,$realPayInterest,$realPayinterestSub,$status,$msg='')
    {
        //set_time_limit(2);
        var_log('[warning]回款回调开始#'.$sn.'#>>>');
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
                return $this->_returnOK('have_pay');
            }
        }else{
            return $this->_returnError('void_sn');
        }

        if($realPayAmount!=$plan['realPayAmount'])return $this->_returnError('error_realPayAmount');
        if($realPayInterest!=$plan['realPayInterest'])return $this->_returnError('error_realPayInterest');
        if($realPayinterestSub-0!=$plan['realPayinterestSub']-0)return $this->_returnError('error_realPayinterestSub');

        if($status=='success'){
            $waresId = $invest->getField('waresId');
            $ware = \Prj\Data\Wares::getCopy($waresId);
            $ware->load();
            $wareRetry = 3;
            //160428 tgh 添加锁商品的功能 防止并发回调
            while($wareRetry >= 0 && !$ware->lock('returnFundResult#ordersId:'.$ordersId,30)){
                return $this->_returnError('lock_failed');
            }
            //订单状态复检
            $invest->reload();
            $returnPlan = \Prj\ReturnPlan\Std01\ReturnPlan::createPlan($invest->getField('returnPlan'));
            $where = ['sn'=>$sn];
            if($plans = $returnPlan->getPlan($where)){
                $plan = current($plans);
                if($plan['isPay']){
                    var_log($plan,'plan>>>>>>>>>>>>>>>>>>>>>');
                    $ware->unlock();
                    return $this->_returnOK('have_pay');
                }
            }else{
                $ware->unlock();
                return $this->_returnError('void_sn');
            }

            $wareRP = \Prj\ReturnPlan\All01\ReturnPlan::createPlan($ware->getField('returnPlan'));
            $warePlan = $wareRP->getPlanById($plan['id']);
            if($warePlan['waitNum']!=null){
                $warePlan['waitNum']--;
                $warePlan['waitNum'] = $warePlan['waitNum']>0?$warePlan['waitNum']:0;
                $wareRP->updatePlan('waitNum',$warePlan['waitNum'],['id'=>$plan['id']]);
                if($warePlan['waitNum']==0){
                    $wareRP->updatePlan('returnFundStatus',\Prj\Consts\PayGW::success,['id'=>$plan['id']]);
                    $wareRP->updatePlan('retryBtnShow1',0,['id'=>$plan['id']]);
                }
            }

            $invest->setField('lastReturnFundYmd',date('Ymd'));
            $returnPlan->updatePlan('isPay','1',$where);
            $returnPlan->updatePlan('status',\Prj\Consts\PayGW::success,$where);
            $returnPlan->updatePlan('realPayAmount',$realPayAmount,$where);
            $returnPlan->updatePlan('realPayInterest',$realPayInterest,$where);
            $returnPlan->updatePlan('realDateYmd',date('Ymd'),$where);
            $num = $returnPlan->updatePlan('exp','ok',$where);
            if($plan['ahead']){
                $returnPlan->updatePlan('exp','提前还款#ok',$where);
                $returnPlan->clearPlanWhenAhead($plan['id']);
                $returnPlan->splitAhead($plan['id']);
            }
        }else{
            $returnPlan->updatePlan('status',\Prj\Consts\PayGW::failed,$where);
            $num = $returnPlan->updatePlan('exp',$msg,$where);
        }
        if(!$num){
            $ware->unlock();
            return $this->_returnError('void_sn');
        }

        $lockInfo = 'returnFund#sn:'.$sn.' ordersId:'.$ordersId.' userId:'.$userId;
        if(!$user->lock($lockInfo)){
            sleep(1);
            $user->reload();
            if(!$user->lock($lockInfo)){
                $ware->unlock();
                return $this->_returnError('lock_failed');
            }
        }

        try{
            $invest->setField('returnPlan',$returnPlan->decode());
            $invest->setField('returnNext',$returnPlan->getYmdNext());
            if($plan['ahead']){
                //订单提前还款
                $invest->setField('returnNext',0);
                $invest->setField('orderStatus',\Prj\Consts\OrderStatus::done);
            }elseif(!empty($realPayAmount)){
                //订单结束
                $invest->setField('returnNext',0);
                $invest->setField('orderStatus',\Prj\Consts\OrderStatus::done);
            }
            $invest->setField('returnPlan',$returnPlan->decode());
            $newPlan = current($returnPlan->getPlan($where));
            try{
                $tmp = \Prj\Data\ReturnPlan::updateFields($ordersId,$plan['id'],$newPlan['status'],$newPlan['isPay'],$newPlan['exp']);
            }catch (\ErrorException $e){
                $ware->unlock();
                $user->unlock();
                var_log(__LINE__.'#'.$sn.'#'.$e->getMessage());
                return $this->_returnError('ReturnPlan_updateFailed');
            }
            $invest->update();
        }catch (\ErrorException $e){
            $ware->unlock();
            $user->unlock();
            $tmp->updateFields_rollBack();
            return $this->_returnError($e->getMessage());
        }

        \Prj\Misc\OrdersVar::$introForUser = '您投资的 '.$invest->getField('waresName').' 第'.ceil($plan['id']).'期 已成功回款';
        \Prj\Misc\OrdersVar::$introForCoder = 'return_fund_'.$sn;


        $tally = \Prj\Data\WalletTally::addTally($userId,$user->getField('wallet'),$realPayInterest,0,$ordersId,\Prj\Consts\OrderType::paysplit);

        if(!empty($realPayAmount)){
            $type = $plan['ahead']?(\Prj\Consts\OrderType::advPaysplit):(\Prj\Consts\OrderType::payAmount);
            $tallyAmount = \Prj\Data\WalletTally::addTally($userId,$user->getField('wallet')+$realPayInterest,$realPayAmount,0,$ordersId,$type);
            $tallyAmount->setField('sn',$sn);
        }
        // $realPayinterestSub
        if(!empty($realPayinterestSub)){
            $tallySub = \Prj\Data\WalletTally::addTally($userId,$user->getField('wallet')+$realPayInterest+$realPayAmount,$realPayinterestSub,0,$ordersId,\Prj\Consts\OrderType::giftInterest);
            $tallySub ->setField('sn',$sn);
        }


        $tally->setField('sn',$sn);

        if(!$tally){
            $user->unlock();
            $ware->unlock();
            $tmp->updateFields_rollBack();
            return $this->_returnError('add_tally_failed');
        }
        try{
            $tally->updStatus(\Prj\Consts\Tally::status_new);
            if(!empty($realPayAmount)){
                $tallyAmount->updStatus(\Prj\Consts\Tally::status_new);
                $tallyAmount->update();
            }
            if(!empty($tallySub)){
                $tallySub->updStatus(\Prj\Consts\Tally::status_new);
                $tallySub->update();
            }
            $tally->update();
        }catch (\ErrorException $e){
            $user->unlock();
            $ware->unlock();
            $tmp->updateFields_rollBack();
            return $this->_returnError($e->getMessage());
        }
        try{
            $user->setField('interestTotal',$user->getField('interestTotal')+$realPayInterest);
            $user->setField('wallet',$user->getField('wallet')+$realPayAmount+$realPayInterest+$realPayinterestSub);
            $user->update();
        }catch (\ErrorException $e){
            if($tally){
                $tally->updStatus(\Prj\Consts\Tally::status_abandon);
                $tally->update();
            }
            if($tallyAmount){
                $tallyAmount->updStatus(\Prj\Consts\Tally::status_abandon);
                $tallyAmount->update();
            }
            if($tallySub){
                $tallySub->updStatus(\Prj\Consts\Tally::status_abandon);
                $tallySub->update();
            }
            $user->unlock();
            $ware->unlock();
            $tmp->updateFields_rollBack();
            return $this->_returnError($e->getMessage());
        }

        try{
            $returnPlanStr = $wareRP->decode();
            if(empty($returnPlanStr['calendar']))throw new \ErrorException('还款计划异常');
            $ware->setField('returnPlan',$returnPlanStr);
            $ware->update();
        }catch (\ErrorException $e){
            $ware->unlock();
            var_log('[error]returnFound 回调 标的更新情况 '.$e->getMessage());
        }

        //推送
        $phone = $user->getField('phone');

        //提前还款推送
        if($plan['ahead']){
            try {
                $ret1 = \Prj\ReadConf::run(
                    ['event' => 'tell_data','pro_name'=>$invest->getField('waresName'),'money_full'=>($realPayAmount+$realPayInterest)/100,'capital_ok'=>$realPayAmount/100,'int_ok'=>$realPayInterest/100,'price_ok'=>0 ],
                    ['phone' => $phone, 'userId' => $userId]
                );
            } catch (\ErrorException $e) {

            }
        }else{
            try {
                $ret = \Prj\ReadConf::run(
                    ['event' => 'get_money','pro_name'=>$invest->getField('waresName'),'money_baby'=>($realPayAmount+$realPayInterest)/100, ],
                    ['phone' => $phone, 'userId' => $userId]
                );
            } catch (\ErrorException $e) {

            }
        }

        //记录对账资料
        if($status=='success') {
            try {
                var_log('>>> 记录回款对账资料 >>>');
                $data = $plan;
                $data['userId'] = $userId;
                $data['amount']=$plan['realPayAmount'];
                $data['interest']=$plan['realPayInterest'];
                \Prj\Check\DayPaysplit::addRecords($data);
            } catch (\ErrorException $e) {
                var_log($e->getMessage(), '[error]>>>');
            }
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

    public function unbinding($sn,$userId,$realname,$idType,$idCode,$bankId,$bankCard,$phone,$userIP)
    {
        if($this->rpc!==null){
            return $this->rpc->initArgs(array(
                'SN'=>$sn,
                'userId'=>$userId,
                'realname'=>$realname,
                'idType'=>$idType,
                'idCode'=>$idCode,
                'bankId'=>$bankId,
                'bankCard'=>$bankCard,
                'phone'=>$phone,
                'userIP'=>$userIP
            ))->send(__FUNCTION__);
        }else{
            $rand = 1;
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
            return $this->rpc->initArgs(array('smsCode'=>$code,'SN'=>$ticket,'userId'=>$userId,'ip'=>\Sooh\Base\Tools::remoteIP()))->send(__FUNCTION__);
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
                    ['file'=>'sina/'.$ymd.'/dayRecharges.csv','ymd'=>$ymd,'SN'=>time().rand(1000,9999),'userId'=>'90003837339748','amount'=>rand(1000,9999),'amountExtra'=>'10','waresId'=>'123','paycorp'=>'101'],
                    ['file'=>'sina/'.$ymd.'/dayRecharges.csv','ymd'=>$ymd,'SN'=>time().rand(1000,9999),'userId'=>'90003837339750','amount'=>rand(1000,9999),'amountExtra'=>'10','waresId'=>'123','paycorp'=>'101'],
                    ['file'=>'sina/'.$ymd.'/dayRecharges.csv','ymd'=>$ymd,'SN'=>time().rand(1000,9999),'userId'=>'90003837339748','amount'=>rand(1000,9999),'amountExtra'=>'10','waresId'=>'123','paycorp'=>'101'],
                    ['file'=>'sina/'.$ymd.'/dayRecharges.csv','ymd'=>$ymd,'SN'=>time().rand(1000,9999),'userId'=>'90003837339748','amount'=>rand(1000,9999),'amountExtra'=>'10','waresId'=>'123','paycorp'=>'101'],
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
                    ['file'=>'sina/'.$ymd.'/dayRecharges.csv','ymd'=>$ymd,'SN'=>time().rand(1000,9999),'userId'=>'90003837339748','amount'=>rand(1000,9999),'interest'=>'10','waresId'=>'123','paycorp'=>'101'],
                    ['file'=>'sina/'.$ymd.'/dayRecharges.csv','ymd'=>$ymd,'SN'=>time().rand(1000,9999),'userId'=>'90003837339750','amount'=>rand(1000,9999),'interest'=>'10','waresId'=>'123','paycorp'=>'101'],
                    ['file'=>'sina/'.$ymd.'/dayRecharges.csv','ymd'=>$ymd,'SN'=>time().rand(1000,9999),'userId'=>'90003837339748','amount'=>rand(1000,9999),'interest'=>'10','waresId'=>'123','paycorp'=>'101'],
                    ['file'=>'sina/'.$ymd.'/dayRecharges.csv','ymd'=>$ymd,'SN'=>time().rand(1000,9999),'userId'=>'90003837339748','amount'=>rand(1000,9999),'interest'=>'10','waresId'=>'123','paycorp'=>'101'],

                ];
                case 2: 	return ["code"=>400,'msg'=>'error'];
            }
        }
    }

    protected $arr = [];

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
            return $this->_returnError('db_no_orderOfRecharge#'.$orderId);
        }

        if($orderOfRecharge->getField('orderStatus') == \Prj\Consts\OrderStatus::done){
            return $this->_returnOK('success');
        }

        if($orderOfRecharge->getField('amount')!=$amount)
        {
            var_log($orderOfRecharge,'error_amount>>>>>>>>>>');
            return $this->_returnError('error_amount');
        }
        if($orderOfRecharge->getField('orderStatus')!=\Prj\Consts\OrderStatus::waiting && $orderOfRecharge->getField('orderStatus')!=\Prj\Consts\OrderStatus::unusual){
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
            //todo 验证成功 添加流水
            \Prj\Misc\OrdersVar::$introForUser = '充值单';
            \Prj\Misc\OrdersVar::$introForCoder = 'recharge';

            if(!$this->user->lock('recharge callback from payGW'))
            {
                if(!$this->user->lock('recharge callback from payGW'))
                {
                    return $this->_returnError('lock_user_failed');
                }
            }
            error_log('rechargeResult#'.$orderId.'#lock success...');

            $orderOfRecharge->reload();

            if($orderOfRecharge->getField('orderStatus') == \Prj\Consts\OrderStatus::done){
                $this->user->unlock();
                return $this->_returnOK('success');
            }


            $tally = \Prj\Data\WalletTally::addTally($this->user->userId, $this->user->getField('wallet'), $amount,0,$orderId, \Prj\Consts\OrderType::recharges);
            //var_log($tally,'tally>>>>>>>>>>>>.');
            $tally->setField('descCreate','充值单 '.$orderId.' 支付成功');


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

            //更新流水
            $tally->updStatus(\Prj\Consts\Tally::status_new)->update();

            //更新订单
            try{
                $orderOfRecharge->setField('payTime',\Sooh\Base\Time::getInstance()->ymdhis());
                $orderOfRecharge->setField('exp',$status);
                $orderOfRecharge->update();
            }catch (\ErrException $e){
                $tally->updStatus(\Prj\Consts\Tally::status_abandon)->update();
                return $this->_returnError('updateOrderStatus_failed');
            }

            try {
                $this->user->setField('wallet', $this->user->getField('wallet') + $amount);
                $this->user->update();
            } catch (\ErrException $e) {
                $orderOfRecharge->setField('orderStatus',\Prj\Consts\OrderStatus::unusual);
                $orderOfRecharge->update();
                $tally->updStatus(\Prj\Consts\Tally::status_abandon)->update();
                return $this->_returnError('updateWallet_failed');
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

        //对账资料记录
        if($status=='success'){
            try{
                $tmp = $orderOfRecharge->dump();
                $tmp['sn'] = $orderId;
                \Prj\Check\DayRecharges::addRecords($tmp);
            }catch (\ErrorException $e){
                var_log($e->getMessage(),'[error] >>> ');
            }
        }

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
//	            ['userId' => $withdraw->getField('userId')]
//            );
            $withdraw->setField('orderStatus',\Prj\Consts\OrderStatus::done);
            $withdraw->setField('payTime',\Sooh\Base\Time::getInstance()->ymdhis());
            $withdraw->setField('exp',$msg);

            //解冻流水
            $this->_unfreezeTally($ordersId);
	        try {
		        \Prj\ReadConf::run(
			        ['event' => 'bank_ok', 'time_baby' => date('Y年m月d日H:i分', strtotime($withdraw->getField('orderTime'))), 'money_now' => $amount / 100],
			        ['userId' => $withdraw->getField('userId')]
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
                    return $tally;
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
        var_log($str,'[error400]'.$msg);
        return $this->arr;
    }

    protected function _returnOK($msg='')
    {
        $this->arr['msg'] = $msg;
        $this->arr['code'] = 200;
        return $this->arr;
    }

}
