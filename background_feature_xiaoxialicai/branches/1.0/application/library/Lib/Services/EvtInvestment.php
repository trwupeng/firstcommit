<?php
namespace Lib\Services;
/**
 * 投资事件
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class EvtInvestment {
	protected static $_instance=null;
	/**
	 * 
	 * @param \Sooh\Base\Rpc\Broker $rpcOnNew
	 * @return EvtInvestment
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
	 * 下单结果
	 * @param int $userId 用户id
	 * @param int $waresId 标的id
	 * @param int $orderId 订单号
	 * @param int $amount 投资额
	 * @param int $success 成功失败
	 * @param int $clientType 客户端类型
	 * @param int $firstTime 是否首次投资
	 * @return boolean
	 */
	public function result($userId,$waresId,  $orderId,$amount, $success,$clientType,$firstTime)
	{
		if($this->rpc!==null){
			return $this->rpc->initArgs(['userId'=>$userId,'waresId'=>$waresId,
								'orderId'=>$orderId,'amount'=>$amount,
								'success'=>$success,'clientType'=>$clientType,'firstTime'=>$firstTime])->send(__FUNCTION__);
		}else{
			
			return true;
		}
	}

	/**
	 * 首购送券
	 * @param string $userId 用户ID
	 * @param number $amout 投资额（包含红包）
	 * @return array
	 */
    public function firstBuyGetVouchers($userId,$amout){
        if(!\Prj\Data\Config::get('ORDER_FIRST_RED_TYPE')){
            var_log('[warn]首购送红包被关闭');
            return [];
        }

	    try {
		    $toUserId = \Prj\Data\User::getMineInvitedTree($userId)['parent'];
		    var_log($toUserId,'邀请人ID>>>');

		    $itemGiverFirstBuy = new \Prj\Items\ItemGiver($userId);
		    if (empty($toUserId)) {
			    $itemGiverRet = $itemGiverFirstBuy->add('NewFirstBuyRedPacket', 1)->give(['amount' => $amout, 'noInvite' => true]);
		    } else {
			    $itemGiverRet = $itemGiverFirstBuy->add('NewFirstBuyRedPacket', 1)->give(['amount' => $amout, 'noInvite' => false]);
		    }

		    if(!empty($toUserId) && $userId != $toUserId){
			    $itemGiverFirstBuyForInvite = new \Prj\Items\ItemGiver($toUserId);
			    $_itemGiverRet = $itemGiverFirstBuyForInvite->add('NewFirstBuyForInviteRedPacket', 1)->give(['redAmount' => $itemGiverRet[0][1]]);
			    $itemGiverFirstBuyForInvite->onUserUpdated();
		    }
	    } catch (\Exception $e) {
		    error_log($e->getMessage()."\n".$e->getTraceAsString());
	    }

	    if (isset($itemGiverRet)) {
		    $ret = ['type' => $itemGiverRet[0][0], 'amount' => $itemGiverRet[0][1], 'voucherId' => $itemGiverRet[0][3]];
	    }
	    return isset($ret) ? $ret : [];

//        $red = new \Prj\Items\RedPacketForFirstBuy();
//        $toUserId = \Prj\Data\User::getMineInvitedTree($userId)['parent'];
//        var_log($toUserId,'邀请人ID>>>');
//        if($toUserId){
//            $redInvite = new \Prj\Items\RedPacketForFirstBuyForInvite();
//        }
//        try{
//            if(empty($toUserId))
//	            $red->haveNoInvite();
//            $ret = $red->give($userId,$amout);
//            if(!empty($redInvite) && $userId!=$toUserId){
//                $redInvite->give($toUserId,$amout);
//            }
//        } catch (\ErrorException $e){
//            error_log($e->getMessage()."\n".$e->getTraceAsString());
//        }
//        return $ret;
    }

    /**
     * 购买指定金额送一个分享券
     */
    public function buyAssignGetVouchers($userId, $investment, $investmentExpires){
        var_log($userId.' '.$investment.' '.$investmentExpires,'warning');
	    return (new \Prj\Items\RedPacketForShare())->giveParent($userId, $investment, $investmentExpires);
    }


}
