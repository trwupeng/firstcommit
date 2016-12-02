<?php
namespace Lib\Services\evts;
/**
 * 用户发起充值请求以后做哪些任务
 *
 * @author wang.ning
 */
class onRechargeRequest {
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
