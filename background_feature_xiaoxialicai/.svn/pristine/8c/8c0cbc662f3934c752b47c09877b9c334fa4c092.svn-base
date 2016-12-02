<?php
namespace Prj\Copartner;
/**
 * 喵叽自己的推广
 */
class Miaoji extends Copartner0
{
	/**
	 * 系统启动后，通知合作方(暂不实现)
	 * 
	 * @param type $dt
	 * @return bool 通知失败返回false,通知成功或不需要通知返回true
	 */
	public function onInstalled($dt,$extraData)
	{
		
	}
	/**
	 * 注册后，通知合作方
	 * 
	 * @param type $dt
	 * @param type $userId
	 * @return bool 通知失败返回false,通知成功或不需要通知返回true
	 */
	public function onRegister($dt,$userId,$extraData)
	{
		return true;
	}
	/**
	 * 首次绑卡的情况下，通知合作方
	 * 
	 * @param type $dt
	 * @param type $userId
	 * @return bool 通知失败返回false,通知成功或不需要通知返回true
	 */
	public function onFirstBind($dt,$userId,$extraData)
	{
		return true;
	}
	/**
	 * 首次购买的情况下，通知合作方
	 * 
	 * @param int $dt 购买的时间戳
	 * @param string $userId 用户id
	 * @param string $wareId 标的id
	 * @param int $amountReal 实际投资额，单位分
	 * @param int $amountFake 代币（红包）金额，单位分
	 * @return bool 通知失败返回false,通知成功或不需要通知返回true
	 */
	public function onFirstBuy($dt,$userId,$wareId, $amountReal,$amountFake,$extraData)
	{
		return true;
	}
	/**
	 * 再次购买的情况下，通知合作方
	 * 
	 * @param int $dt 购买的时间戳
	 * @param string $userId 用户id
	 * @param string $wareId 标的id
	 * @param int $amountReal 实际投资额，单位分
	 * @param int $amountFake 代币（红包）金额，单位分
	 * @return bool 通知失败返回false,通知成功或不需要通知返回true
	 */
	public function onBuyMore($dt,$userId,$wareId, $amountReal,$amountFake,$extraData)
	{
		return true;
	}
}
