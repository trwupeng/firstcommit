<?php
namespace Lib\Services\evts;
/**
 * 标的按月付息以后做哪些任务 （可能成功可能失败）
 *
 * @author wang.ning
 */
class onWaresMonthly {
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
