<?php
namespace Prj\Copartner;
/**
 * 合作方
 *
 * @author simon.wang
 */
class Copartner0 {
	/**
	 * 根据contractId获取对应的copartner
	 * @return \Prj\Copartner\Copartner0
	 */
	public static function getByContractId($contractId)
	{
		$sys = \Prj\Data\Contract::getCopy(['contractId'=>$contractId]);
		$sys->load();
		return self::getByAbsOrId($sys->getField('copartnerAbs'));
	}
	/**
	 * 根据Id或简称获取对应的copartner
	 * @return \Prj\Copartner\Copartner0
	 */
	public static function getByAbsOrId($absOrId,$contractId=null)
	{
		if(is_numeric($absOrId)){
			$first4 = substr($absOrId,0,4);
			$tmp = \Prj\Data\Copartner::getCopy(['copartnerId'=>$first4]);
			$tmp->load('copartnerAbs');
			$absOrId = $tmp->getField('copartnerAbs');
		}

		$absOrId = '\\Prj\\Copartner\\'.ucfirst($absOrId);
		$o = new $absOrId;
		return $o->initInner($absOrId,$contractId);
	}
	protected $contractDefault=null;
	protected $className;
	/**
	 * 构造时内部初始化用
	 * @param string $className 类名
	 * @param string $contractId 协议id
	 * @return \Prj\Copartner\Copartner0
	 */
	protected function initInner($className,$contractId=null)
	{
		$className = explode('\\', $className);
		$this->className = array_pop($className);
		$this->contractDefault = $contractId;
		return $this;
	}
	public function getContractId()
	{
		if($this->contractDefault===null){
			$this->contractDefault= \Prj\Data\Contract::getDefaultFor($this->className);
		}
		return $this->contractDefault;
	}
	/**
	 * 宣称指定设备接下来的注册属于本合作方
	 * @param string $deviceType 设备号类型:idfa，imei,....
	 * @param string $deviceId 设备号的值
	 * @param string $extraData 额外数据
	 * @param string $deviceTypeAlias 设备号2类型:idfa，imei,....
	 * @param string $deviceIdAlias 设备号2的值
	 */
	public function hold($deviceType,$deviceId,$extraData,$deviceTypeAlias,$deviceIdAlias)
	{
		$tmp = \Lib\Logs\Device::ensureOne($deviceType, $deviceId,null,$this->getContractId(),$extraData,$deviceTypeAlias,$deviceIdAlias);
		if($tmp){
			return true;
		}else{
			return false;
		}
	}
	/**
	 * 关联指定设备到合作方后的返回值
	 *   (如果返回的不是json，请内部自行变更输出方式为echo)
	 * @return array 如果要的是json，可以直接返回数组，否则返回null
	 */
	public function onReturnForHold()
	{
		return ['code'=>0,'message'=>'success'];
	}
	/**
	 * 系统启动后，通知合作方
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
