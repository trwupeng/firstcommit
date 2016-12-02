<?php
namespace Lib\Services;
/**
 * 绑卡事件
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class EvtBinding {
	protected static $_instance=null;
	/**
	 * 
	 * @param \Sooh\Base\Rpc\Broker $rpcOnNew
	 * @return \Lib\Services\EvtInvestment
	 */
	public static function getInstance($rpcOnNew=null)
	{
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
	 * 绑卡申请
	 * @param int $userId 用户id
	 * @param int $orderId 订单流水号
	 * @param int $bankId 银行
	 * @param int $clientType 客户端类型
	 * @return boolean
	 */
	public function apply($userId,$orderId,$bankId,$clientType)
	{
		if($this->rpc!==null){
			return $this->rpc->initArgs(['userId'=>$userId,'waresId'=>$waresId,'amount'=>$amount,'orderId'=>$orderId])->send(__FUNCTION__);
		}else{
			error_log("[EvtBindingApply]$userId,$bankId,$clientType");
			return true;
		}
	}
	/**
	 * 绑卡结果
	 * @param int $userId 用户id
	 * @param int $bankId 银行
	 * @param int $orderId 订单流水号
	 * @param int $clientType 客户端类型
	 * @param int $success 成功失败
	 * @return boolean
	 */
	public function result($userId,$bankId,$orderId,$clientType,$success)
	{
		if($this->rpc!==null){
			return $this->rpc->initArgs(['userId'=>$userId,'bankId'=>$bankId,'orderId'=>$orderId,'clientType'=>$clientType,'success'=>$success])->send(__FUNCTION__);
		}else{
			error_log("[EvtBindingResult]$userId,$bankId,$orderId,$clientType,$success");
			return true;
		}
	}

    /**
     * 首绑送红包
     */
    public function giveVouchersWherFirstBind($userId){
	    try {
		    $itemGiverFirstBind = new \Prj\Items\ItemGiver($userId);
		    $itemGiverRet = $itemGiverFirstBind->add('NewFirstBindRedPacket', 1)->give(['userId' => $userId]);
	    } catch (\Exception $e) {
		    var_log($e->getTraceAsString());
		    var_log('[warning]'.$e->getMessage());
	    }

	    return isset($itemGiverRet) ? ['type' => $itemGiverRet[0][0], 'amount' => $itemGiverRet[0][1], 'voucherId' => $itemGiverRet[0][3]] : [];

//        $red = new \Prj\Items\RedPacketForFirstBind();
//        try{
//            $ret = $red->give($userId);
//        }catch (\ErrorException $e){
//            var_log($e->getTraceAsString());
//            var_log('[warning]'.$e->getMessage());
//        }
//        return $ret;
    }
}
