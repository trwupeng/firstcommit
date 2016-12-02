<?php
namespace Lib\Services;
/**
 * 给每个用户发公告
 * 因每次有10秒的执行限制，顾逻辑流程上，遍历每个用户表，每张表处理::pageSizeOfUpdate条
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class Poststransfer {
	/**
	 *  获取最新的公告
	 * @return array ([id=>1,title=>xxx,content=>xxx])
	 */
	public static function getRecent()
	{
		$url =\Sooh\Base\Ini::getInstance()->get('NewestPostsServer');
		if(empty($url)){
			error_log("Error : globals 里面没有配置 公告中心的地址");
			return null;
		}
		$url = $url. "/api.php?op=api_announce&aid=0&__VIEW__=json";
		$ret = \Sooh\Base\Tools::httpGet($url);
		$ret = json_decode($ret,true);
		if(empty($ret)|| $ret['code']!=200){
			return null;
		}
		return $ret['pubnotice'];
	}
	protected static $_instance=null;
	// ice 是通过php-fpm调用的，最长执行10秒，所以要注意控制每页数量
	public static $flgUseRPC=true;
	const pageSizeOfUpdate=100;
	/**
	 * 
	 * @param \Sooh\Base\Rpc\Broker $rpcOnNew
	 * @return Poststransfer
	 */
	public static function getInstance($rpcOnNew=null)
	{
		if(self::$_instance===null){
			$c = get_called_class();
			self::$_instance = new $c;
			self::$_instance->rpc = $rpcOnNew;
		}
		return self::$_instance;
	}
	/**
	 * 
	 * @var \Sooh\Base\Rpc\Broker
	 */
	protected $rpc;
	public function sendPostsToAll($postsId,$title,$content,$pageId)
	{
		if($this->rpc!==null){
			error_log("(rpc)公告 sendPostsToAll called[page:$pageId] with ($postsId)$title");
			//return $this->rpc->initArgs(array('postsId'=>$postsId,'title'=>$title,'content'=>$content,'pageId'=>$pageId))->send(__FUNCTION__);
			\Lib\Services\Triggers::getInstance(\Prj\BaseCtrl::getRpcDefault('Triggers'))
							->onCustomEvt('Poststransfer_sendPostsToAll', json_encode(['postsId'=>$postsId,'title'=>$title,'content'=>$content,'pageId'=>$pageId]));
		}else{
			error_log("(run) 公告 sendPostsToAll called[page:$pageId] with ($postsId)");

			$this->mum=0;
			$this->pageId=$pageId=$pageId-0;
			$this->title = $title;
			$this->content = $content;
			$this->postsId = $postsId;
			//ice调用过来，phpproxy那里，是rpc=null的，进来后再次调用，要改rpc，不然不会再通过ice调用的
			//	error_log("(into)sendPostsToAll called[$pageId] with ($postsId)$title");
			if($pageId==0)$pageId=1;
			$this->pageId=$pageId;
			$this->pager = new \Sooh\DB\Pager(self::pageSizeOfUpdate);
			$this->pager->init(1000000000,$this->pageId);
			\Prj\Data\User::loop([$this,'loopSendPosts']);
			if($this->num){
				if(self::$flgUseRPC){
					error_log("(run) 公告 $postsId 通过rpc继续下一页 ".($pageId+1)." 的用户");
					$rpc9 = \Prj\BaseCtrl::getRpcDefault('Triggers');
				}else{
					error_log("(run) 公告 $postsId 直接继续下一页 ".($pageId+1)." 的用户");
					$rpc9=null;
				}
				\Lib\Services\Triggers::getInstance($rpc9)
						->onCustomEvt('Poststransfer_sendPostsToAll', json_encode(['postsId'=>$postsId,'title'=>$title,'content'=>$content,'pageId'=>$pageId+1]));
				error_log("(run) 公告 $postsId 页 ".($pageId+1)." 结束");
			}else{
				error_log("(end) 公告 $postsId,同步结束了，当前页 ".$pageId);
				try{
					$maxid = \Prj\Data\TbConfigItem::getCopy('MaxPostsIdTransfered');
					$maxid->load();
					$maxid->setField('v', $postsId);
					$maxid->update();
				}  catch (\ErrorException $e){
					error_log("更新同步过来的最新的公告id失败(".$postsId.")：".$e->getMessage()."\n".$e->getTraceAsString());
					$maxid=null;
				}
			}
		}
	}
	public $num;
	protected $title;
	protected $content;
	protected $postsId;
	/**
	 *
	 * @var \Sooh\DB\Pager 
	 */
	protected $pager;
	/**
	 * 
	 * @param \Sooh\DB\Interfaces\All $db
	 * @param string $tb
	 */
	public function loopSendPosts($db,$tb)
	{
		$us = $db->getPair($tb, 'userId','maxPostsId', null,'sort userId',$this->pager->page_size,$this->pager->rsFrom());
		if(empty($us)){
			return;
		}
		error_log("send posts to($tb):".  sizeof($us));
		$this->num+=sizeof($us);
		$rpc = \Prj\BaseCtrl::getRpcDefault('Message');
		\Lib\Services\Message::getInstance($rpc);
		foreach($us as $uid=>$lastMaxPostsId){
			if($this->postsId>$lastMaxPostsId){
				error_log("send posts $this->postsId to $uid as $lastMaxPostsId < {$this->postsId} in loopSendPosts");
				
				self::sendToUserOneMsg($uid, $this->title, $this->content, $this->postsId);
			
			}
		}	
	}
	
	public static function sendToUserOneMsg($uid,$title,$content,$postsId)
	{
		error_log("sendToUserOneMsg($uid,$title,....,$postsId)");
		$helper= \Lib\Services\Message::getInstance(null);
		$helper->add(0, $uid, \Prj\Consts\Message::type_posts, $title, $content, "postsId#".$postsId, false);
		$tmp = \Prj\Data\User::getCopy($uid);
		//因此此字段只有这里用，mysql的kvobj是部分更新，所以没通过kvobj模式，直接更新了
		$tmp->db()->updRecords($tmp->tbname(), ['maxPostsId'=>$postsId],['userId'=>$uid,'maxPostsId<'=>$postsId]);
	}
}
