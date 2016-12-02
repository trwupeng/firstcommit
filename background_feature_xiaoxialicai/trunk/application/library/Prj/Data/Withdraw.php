<?php
namespace Prj\Data;
/**
 * 提现记录
 *
 * @author simon.wang
 */
class Withdraw extends Recharges{
	/**
	 * 创建一个指定userId的充值订单
	 * @param string $userId 
	 * @param string $amount 金额
	 * @param string $bankAbs  银行代码 ICBC
	 * @param string $bankCard 银行卡
	 * @param string $payway 支付通道，默认0
	 * @return \Prj\Data\Withdraw or null on failed
	 */
	public static function addOrders($userId,$amount,$bankAbs,$bankCard,$payway=0,$ext = 0)
	{
		$dt = \Sooh\Base\Time::getInstance();
		$ordersIdBase = substr($userId,-4);
		for($retry=0;$retry<10;$retry++){
			list($sec,$ms) = explode('.', microtime(true));
			$ordersId = \Prj\Consts\OrderType::withdraw.$sec.substr($ms,0,3).$ordersIdBase;
			$tmp = parent::getCopy($ordersId);
			$tmp->load();
			if(!$tmp->exists()){
				if($amount>0){
					$tmp->setField('amount', -$amount);
					$tmp->setField('amountAbs', $amount);
				}else{
					$tmp->setField('amount', $amount);
					$tmp->setField('amountAbs', -$amount);
				}
				$tmp->setField('userId', $userId);
				
				$tmp->setField('amountFlg', 0);
				$tmp->setField('orderTime', $dt->ymdhis());
				$tmp->setField('payTime', 0);
				$tmp->setField('orderStatus', \Prj\Consts\OrderStatus::created);
				$tmp->setField('payCorp', $payway);
				$tmp->setField('bankAbs', $bankAbs);
				$tmp->setField('bankCard', $bankCard);
				return $tmp;
			}
			self::freeAll($tmp->getPKey());
		}
		return null;
	}
	/**
	 * 
	 * @param string $account
	 * @param string $camefrom
	 * @return \Prj\Data\Withdraw
	 */
	public static function getCopy($ordersId) {
		return parent::getCopy(['ordersId'=>$ordersId]);
	}
}
