<?php
namespace Prj\Data;
/**
 * 投资订单
 * 订单号19位长度，尾数（4位）同waresId的尾数
 * xx----------------- 订单分类
 * --xxxxxxxxxx------- 时间戳（秒）
 * ------------xxx---- 时间 毫秒
 * ---------------xxxx 分表id,同waresId的尾数
 * @author simon.wang <hillstill_simon@163.com>
 */
class Orders extends \Sooh\DB\Base\KVObj{
	/**
	 * 创建一个购买指定waresId的订单
	 * @param string $waresId
	 * @param string $userId
	 * @param int $amount 金额
	 * @return \Prj\Data\Investment or null on failed
	 */
	public static function addOrders($waresId,$userId,$amount)
	{
		$dt = \Sooh\Base\Time::getInstance();
		$ordersIdBase = substr($waresId,-4);
		for($retry=0;$retry<10;$retry++){
			list($sec,$ms) = explode('.', microtime(true));
			$ordersId = \Prj\Consts\OrderType::investment.$sec.substr($ms,0,3).$ordersIdBase;
			$tmp = parent::getCopy($ordersId);
			$tmp->load();
			if(!$tmp->exists()){
				
				$tmp->setField('waresId', $waresId);
				$tmp->setField('userId', $userId);
				$tmp->setField('amount', $amount);
				$tmp->setField('interest', 0);
				$tmp->setField('amountExt', 0);
				$tmp->setField('amountFake', 0);
				$tmp->setField('interestExt', 0);
				$tmp->setField('extDesc', '');
				$tmp->setField('orderTime', $dt->ymdhis());
				$tmp->setField('payTime', 0);
				$tmp->setField('orderStatus', \Prj\Consts\OrderStatus::created);
				$tmp->setField('vouchers', '');
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
	 * @return \Prj\Data\Investment
	 */
	public static function getCopy($ordersId) {
		return parent::getCopy(['ordersId'=>$ordersId]);
	}

	
	//针对缓存，非缓存情况下具体的表的名字
	protected static function splitedTbName($n,$isCache)
	{
//		if($isCache)return 'tb_test_cache_'.($n % static::numToSplit());
//		else 
		return 'tb_investment_'.($n % static::numToSplit());
	}
//指定使用什么id串定位数据库配置
	protected static function idFor_dbByObj_InConf($isCache)
	{
		return 'default';
	}
	//针对缓存，非缓存情况下具体的表的名字

	//说明分几张表
	protected static function numToSplit(){return 1;}

//	/**
//	 * 是否启用cache机制
//	 * cacheSetting=0：不启用
//	 * cacheSetting=1：优先从cache表读，每次更新都先更新硬盘表，然后更新cache表
//	 * cacheSetting>1：优先从cache表读，每次更新先更新cache表，如果达到一定次数，才更新硬盘表
//	 */
//	protected function initConstruct($cacheSetting=0,$fieldVer='iRecordVerID')
//	{
//		return parent::initConstruct($cacheSetting,$fieldVer);
//	}
}
