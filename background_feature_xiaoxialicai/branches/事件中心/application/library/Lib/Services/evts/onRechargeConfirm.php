<?php
namespace Lib\Services\evts;
/**
 * 用户充值有结果以后做哪些任务 （可能成功充值，可能失败）
 *
 * @author wang.ning
 */
class onRechargeConfirm {
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
