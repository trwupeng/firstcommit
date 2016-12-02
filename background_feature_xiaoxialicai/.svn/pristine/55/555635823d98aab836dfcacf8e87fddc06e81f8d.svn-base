<?php
namespace Lib\Services\evts;
/**
 * 成功xxxxx以后做哪些任务 【模板】
 *
 * @author wang.ning
 */
class onXXXXXX {
	/**
	 * 
	 * @param \Sooh\Base\Log\Data $data
	 * @return void
	 */
	public function run($data)
	{
		$this->notifyRptCenter();
		\Lib\Services\Bysms::getInstance(\Prj\BaseCtrl::getRpcDefault('Bysms'))
							->sendCode( '130123456789', __CLASS__.'随onXXXXX文');
	}
	/**
	 * 通知报表中心
	 */
	protected function notifyRptCenter()
	{
		
	}
}
