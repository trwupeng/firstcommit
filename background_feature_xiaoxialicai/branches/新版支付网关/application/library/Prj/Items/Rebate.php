<?php
/**
 * Created by PhpStorm.
 * User: tang.gaohang
 * Date: 2016/3/4
 * Time: 14:34
 */
namespace Prj\Items;

class Rebate {





    /**
     * 使返利流水生效
     */
    public static function openRebate($orderId){
        $tmp = \Prj\Data\Rebate::getCopy('');
        $rs = $tmp::loopFindRecords(['investId'=>$orderId,'statusCode'=>[\Prj\Consts\OrderStatus::created,\Prj\Consts\OrderStatus::failed,\Prj\Consts\OrderStatus::waitingGW],'amount>'=>0])[0];
        var_log($orderId,'订单号>>>');
        var_log($rs,'相关返利>>>');
        if(empty($rs)){
            return [];
        }else{
            $rebateId = $rs['rebateId'];
            $rebate = \Prj\Data\Rebate::getCopy($rebateId);
            $rebate->load();
            if(!$rebate->exists()){
                throw new \ErrorException('rebate_missing');
            }else{
                $sn = substr($rebateId,0,14).rand(1000,9999);
                var_log($sn,'sn>>>');
                $amount = $rebate->getField('amount');
                $userId = $rebate->getField('userId');
                $rebate->setField('sn',$sn);
                $data = [
                    'sn'=>$sn,
                    'amount'=>$amount,
                    'userId'=>$userId,
                    'rebateId'=>$rebateId,
                    'ordersId'=>$orderId,
                ];
                //调用网关
                $rpc = \Sooh\Base\Ini::getInstance()->get('noGW')?self::getRpcDefault('PayGW'):\Sooh\Base\Rpc\Broker::factory('PayGW');
                $sys = \Lib\Services\PayGW::getInstance($rpc,true);
                $newApi = 1; //注意不要上线到正式服
                try{
                    if($newApi){
                        $data = [$sn,$userId,$amount,'rebate'];
                        $ret = \Lib\Services\PayGWCmd::sendToPayGWCmd('trade_payment',$data);
                    }else{
                        $ret = call_user_func_array([$sys,'rebate'],$data);
                    }
                }catch (\ErrorException $e){
                    if($e->getCode()==400){
                        var_log('返利发送失败'.$e->getTraceAsString());
                    }else{
                        var_log('返利发送失败#网关未响应'.$e->getTraceAsString());
                    }
                    $rebate->setField('snMsg','返利发送失败#网关返回:'.$e->getMessage());
                    $rebate->setField('statusCode',\Prj\Consts\OrderStatus::unusual);
                    try{
                        $rebate->update();
                    }catch (\ErrorException $e){

                    }
                }

                var_log($ret,'ret>>>');
                if($ret['code']==200){
                    $rebate->setField('snMsg','网关已受理');
                    $rebate->setField('statusCode',\Prj\Consts\OrderStatus::waitingGW);
                }else if($ret['code']==400){
                    $msg = $ret['msg']?$ret['msg']:'处理失败,rpc_failed';
                    $rebate->setField('snMsg',$msg);
                    $rebate->setField('statusCode',\Prj\Consts\OrderStatus::failed);
                }
                $rebate->update();
            }
            return $ret;
        }
    }

    public static function rebateResult($sn,$amount,$userId,$status,$msg=''){
        $tmp = \Prj\Data\Rebate::getCopy('');
        $where = ['sn'=>$sn];
        $rs = $tmp::loopFindRecords($where)[0];
        if(empty($rs)){
            throw new \ErrorException('不存在的返利');
        }elseif($amount!=$rs['amount']){
            throw new \ErrorException('不同步的金额');
        }elseif($userId!=$rs['userId']){
            throw new \ErrorException('不同步的用户ID');
        }else{
            $rebate = \Prj\Data\Rebate::getCopy($rs['rebateId']);
            $rebate->load();
            if(!$rebate->exists()){
                throw new \ErrorException('不存在的返利');
            }else{
                if($rebate->getField('statusCode')==\Prj\Consts\OrderStatus::done){
                    throw new \ErrorException('订单已经完成');
                }
                if($rebate->getField('statusCode')!=\Prj\Consts\OrderStatus::waitingGW && $rebate->getField('statusCode')!=\Prj\Consts\OrderStatus::unusual){
                    throw new \ErrorException('错误的订单状态');
                }
                $rebate->setField('updateYmd',date('YmdHis'));
                if($status=='failed'){
                    $exp = $msg?$msg:'回调失败,原因未知';
                    $rebate->setField('snMsg',$exp);
                    $rebate->setField('statusCode',\Prj\Consts\OrderStatus::failed);
                    $rebate->update();
                }elseif($status=='success'){
                    $exp = '回调成功';
                    $rebate->setField('snMsg',$exp);
                    $rebate->setField('sumAmount',\Prj\Data\Rebate::getSumAcount($userId,$rebate->getField('childUserId'))+$amount);
                    $rebate->setField('statusCode',\Prj\Consts\OrderStatus::done);
                    $rebate->update();
                    //更新钱包 添加流水
                    $user = \Prj\Data\User::getCopy($userId);
                    $user->load();
                    if(!$user->exists()){
                        self::rollback(null,$rebate,null,'用户不存在userId:'.$userId);
                    }else{
                        //todo 返利加钱
                        if(!$user->lock(__CLASS__.' '.__FILE__.' '.'sn:'.$sn)){
                            $user->load();
                            if(!$user->lock(__CLASS__.' '.__FILE__.' '.'sn:'.$sn)){
                                throw new \ErrorException('系统错误:锁定用户失败');
                            }
                        }
                        \Prj\Misc\OrdersVar::$introForUser = "返利号:".$rs['rebateId'];
                        \Prj\Misc\OrdersVar::$introForCoder = "rebate_".$rs['rebateId'];
                        $tally = \Prj\Data\WalletTally::addTally($userId,$user->getField('wallet'),$amount,0,$rebate->getField('investId'),\Prj\Consts\OrderType::invite);
                        if(empty($tally)){
                            $user->unlock();
                            throw new \ErrorException('系统错误:添加流水失败');
                        }else{
                            $tally->setField('statusCode',\Prj\Consts\Tally::status_new);
                            //更新流水
                            try{
                                $tally->update();
                            }catch (\ErrorException $e){
                                self::rollback($user,$rebate,null,'系统错误:更新流水失败');
                            }
                            //更新用户
                            try{
                                $user->setField('wallet',$user->getField('wallet')+$amount);
                                $user->setField('rebate',$user->getField('rebate')+$amount);
                                $user->setField('rebating',$user->getField('rebating')-$amount?$user->getField('rebating')-$amount:0);
                                //throw new \ErrorException('打断');
                                $user->update();
                            }catch (\ErrorException $e){
                                self::rollback($user,$rebate,$tally,'系统错误:更新用户失败');
                            }
                        }
                    }

	                try {
		                \Prj\Message\Message::run(
			                ['event' => 'return_money', 'money_re' => $amount / 100],
			                ['userId' => $userId]
		                );
	                } catch (\ErrorException $e) {

	                }

                }else{
                    throw new \ErrorException('错误的参数值status:'.$status);
                }
            }

        }
    }



    public static function rollback($user = null,$rebate = null,$tally = null,$msg = ''){
        if(!empty($user)){
            $user->unlock();
        }
        if(!empty($rebate)){
            $rebate->setField('snMsg','回滚:'.$msg);
            $rebate->setField('statusCode',\Prj\Consts\OrderStatus::unusual);
            $rebate->update();
        }
        if(!empty($tally)){
            $tally->setField('statusCode',\Prj\Consts\Tally::status_abandon);
            $tally->update();
        }
        throw new \ErrorException($msg);
    }

    public static function getRpcDefault($serviceName)
    {
        if($serviceName==='Rpcservices' ||$serviceName==='SessionStorage'){
            return null;
        }
        $flg = \Sooh\Base\Ini::getInstance()->get('RpcConfig.force');
        if($flg!==null){
            if($flg){
                error_log('force rpc for '.$serviceName);
                return \Sooh\Base\Rpc\Broker::factory($serviceName);
            }else{
                error_log('no rpc for '.$serviceName);
                return null;
            }
        }else{
            error_log('try rpc for '.$serviceName);
            return \Sooh\Base\Rpc\Broker::factory($serviceName);
        }
    }

    /**
     * 注册一年内返利  判断日期是否有效
     * @param $userId
     * @return bool
     * @throws \ErrorException
     */
    protected static function isOkDate($userId){
        //判断日期
        $user = \Prj\Data\User::getCopy($userId);
        $user->load();
        if(!$user->exists()){
            var_log("[error]用户不存在");
            return false;
        }
        $regYmd = $user->getField('ymdReg');
        $overYmd = ($regYmd+10000).'235959';
        var_log('[warning]返利过期日:'.$overYmd);
        if(time()>strtotime($overYmd)){
            var_log("[warning]超过一年不在返利 ".$overYmd);
            return false;
        }
        return true;
    }


    /**
     * 给邀请人返利 返RMB
     * @param $userId
     * @param $amount
     * @param $waresId
     * @param $investId
     * @return bool
     * @throws \ErrorException
     * @throws \Sooh\Base\ErrException
     */
    public static function doGiveRebateWhenBuy($userId,$amount,$waresId,$investId)
    {
        if(!self::isOkDate($userId))return false;

        $user = \Prj\Data\User::getCopy($userId);
        $user->load();
        if(!$user->exists()){
            var_log('[error]用户不存在 userId:'.$userId);
            throw new \ErrorException('user_missing');
        }
        $childNickname = $user->getField('nickname');
        $newChildNickname = \Prj\IdCard::getCall($user->getField('idCard'),$childNickname);
        $childPhone = $user->getField('phone');

        $toUserId = \Prj\Data\User::getMineInvitedTree($userId)['parent'];
        var_log($toUserId,'toUserId>>>>>>>>>');
        if($toUserId){
            if($toUserId==$userId)throw new \ErrorException('[error]邀请人是自己');
            $rebateAmount = self::getAmountWhenBuy($amount,$waresId);
            if($rebateAmount==0){
                var_log('[warning]金额为0,不予返利');
                return false;
            }
            $rebate = \Prj\Data\Rebate::addRebate($rebateAmount,$toUserId,$userId,$investId,$waresId);
            if($rebate){

                try{
                    $rebate->setField('exp','购买订单'.$investId);

                    $rebate->setField('childNickname',$newChildNickname);
                    $rebate->setField('childPhone',$childPhone);

                    $rebate->update();
                }catch (\ErrorException $e){
                    var_log('[error]db_failed_'.$e->getMessage());
                    throw new \ErrorException('db_failed');
                }

                $toUser = \Prj\Data\User::getCopy($toUserId);
                $toUser->load();
                if($toUser->exists()){
                    $toUser->setField('rebating',$toUser->getField('rebating')+$rebateAmount);
                    try{
                        $toUser->update();
                    }catch (\ErrorException $e){

                    }
                }

                return $rebate->getPKey()['rebateId'];
            }else{
                var_log('[error]give_failed');
                throw new \ErrorException('give_faild');
            }
        }else{
            var_log('[warning]没有邀请人');
            return false;
        }
    }

    /**
     * 返利金额计算
     * @param $amount
     * @param $waresId
     * @return float
     * @throws \ErrorException
     */
    public static function getAmountWhenBuy($amount,$waresId){
        $ware = \Prj\Data\Wares::getCopy($waresId);
        $ware->load();
        if($ware->exists()){
            $waresDays = $ware->getField('dlUnit')=='天'?$ware->getField('deadLine'):$ware->getField('deadLine')*30;
            return floor($amount*$waresDays/360*self::getScale());
        }else{
            var_log('[error]ware_missing');
            throw new \ErrorException('ware_missing');
        }
    }

    /**
     * 返利率
     * @return float
     */
    public static function getScale(){
        return 0.001 ;  //todo 测试专用
    }
}