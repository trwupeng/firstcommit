<?php
namespace Lib\Services\evts;
/**
 * 标的满标转账以后做什么事情（可能成功，可能失败）
 *
 * @author wang.ning
 */
class onWaresFull {
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
