<?php
namespace Lib\Services\evts;
/**
 * 用户确认购买 以后做哪些任务 （可能购买成功，可能购买失败）
 *
 * @author wang.ning
 */
class onCustomEvt {
	/**
	 * 
	 * @param mixed $data
	 * @return void
	 */
	public function run($data)
	{
		$evtName = $data['evtName'];
		$evtData = $data['evtData'];
		$func = ucfirst($evtName);
		$this->$func($evtData);
	}
	/**
	 * 通知报表中心
	 */
	protected function Poststransfer_sendPostsToAll($data)
	{
		error_log(__CLASS__.'->'.__FUNCTION__.'("'.(is_string($data)?$data:json_encode($data)).'")');
		if(is_string($data)){
			$data=  json_decode($data,true);
		}
		$postsId = $data['postsId'];
		$postsTitle = $data['title'];
		$postsContent = $data['content'];
		$pageId = $data['pageId'];
		\Lib\Services\Poststransfer::getInstance(\Prj\BaseCtrl::getRpcDefault('Poststransfer'))
				->sendPostsToAll($postsId,$postsTitle,$postsContent,$pageId);		
	}
}
