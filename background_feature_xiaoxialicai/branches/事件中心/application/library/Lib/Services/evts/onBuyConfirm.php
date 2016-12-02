<?php
namespace Lib\Services\evts;
/**
 * 用户确认购买 以后做哪些任务 （可能购买成功，可能购买失败）
 *
 * @author wang.ning
 */
class onBuyConfirm {
	/**
	 * 
	 * @param \Sooh\Base\Log\Data $data
	 * @return void
	 */
	public function run($data)
	{
		$this->notifyRptCenter();
	}
	/**
	 * 通知报表中心
	 */
	protected function notifyRptCenter()
	{
		
	}
}
