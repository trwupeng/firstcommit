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
        $rpc = \Sooh\Base\Ini::getInstance()->get('noGW') ? \Prj\Wares\Wares::getRpcDefault('PayGWCmd') : \Sooh\Base\Rpc\Broker::factory('PayGWCmd');
        $sys = \Lib\Services\PayGWCmd::getInstance($rpc);
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
