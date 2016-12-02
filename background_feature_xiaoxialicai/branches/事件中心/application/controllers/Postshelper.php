<?php

/**
 * 公告并入站内信用的
 * @author simon.wang
 */
class PostshelperController extends \Prj\BaseCtrl
{
	/**
	 * 给系统的所有的用户发站内信
	 * @input int postsId 公告的ID
	 * @input string title 公告的标题
	 * @input string content 公告的内容

     * @output {code: 200,msg:"request accept"}
	 */
	public function sendallAction()
	{
		$postsId = $this->_request->get('postsId',0);
		$title = $this->_request->get('title','title_test');
		$txt = $this->_request->get('content','content_test');
		//$rpc = \Prj\BaseCtrl::getRpcDefault('Poststransfer');
		\Lib\Services\Poststransfer::getInstance(null)
				->sendPostsToAll($postsId,$title,$txt,1);		
		$this->loger->target=$postsId;
		$this->returnOK('request accept');
	}

}