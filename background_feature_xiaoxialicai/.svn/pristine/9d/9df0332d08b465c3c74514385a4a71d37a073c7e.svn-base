<?php
namespace PrjCronds;
/**
 * 同步公告，独立进程运行，幂等，每10分钟检查一下，发现后开始同步，同步锁定时间预计半小时
 * 碰上一次需要同步多个公告，有缺陷：更高id的先完成，低的id覆盖，导致重跑，浪费系统资源
 * 发现需要同步的公告后，MaxPostsIdTransfered 改为 -时间戳，2小时内不再公告
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class Poststransfer extends \Sooh\Base\Crond\Task{
	public function init() {
		parent::init();
		$this->toBeContinue=true;
		$this->_iissStartAfter=850;//每小时9分后启动
		$this->_secondsRunAgain=540;//每10分钟
		$this->ret = new \Sooh\Base\Crond\Ret();
		$this->maxid = \Prj\Data\TbConfigItem::getCopy('MaxPostsIdTransfered');
		$this->maxid->load();
		if(!$this->maxid->exists()){
			$this->maxid->setField('v', '0');
			$this->maxid->setField('intro', '同步过来的最新的公告id');
			try{
				$this->maxid->update();
			}  catch (\ErrorException $e){
				error_log("更新同步过来的最新的公告id失败(0)：".$e->getMessage()."\n".$e->getTraceAsString());
				$this->maxid=null;
			}
		}
	}
	public function free() {
		parent::free();
	}
	/**
	 *
	 * @var \Prj\Data\TbConfigItem 
	 */
	private $maxid=null;
	/**
	 * @param \Sooh\Base\Time $dt
	 */
	protected function onRun($dt) {
		if($this->maxid===null){
			return false;
		}
		$this->maxid->reload();
		$cmp = $this->maxid->getField('v');
		if($cmp<0){
			if($dt->timestamp()<abs($cmp)){
				error_log("公告同步任务没有结束,继续等待：".date('Ymd H:i:s',abs($cmp)));
				return true;
			}else{
				error_log("公告同步任务没有结束，过期了，重置再跑：".date('Ymd H:i:s',abs($cmp)));
				$cmp=0;
			}
		}
		$rs = \Lib\Services\Poststransfer::getRecent();
		
		if(!empty($rs)){
			$needsTransfer = array();
			foreach($rs as $r){
				$id = $r['id'];
				if($id>$cmp){
					$needsTransfer[$id] = ['title'=>$r['title'],'content'=>$r['content']];
				}
			}
			ksort($needsTransfer);
			error_log("定时任务找到的公告数：".sizeof($rs).' 认为需要同步的有：'.implode(',',array_keys($needsTransfer)));
			if(!empty($needsTransfer)){
				$this->maxid->setField('v', 0-$dt->timestamp()-1800);//同步锁定时间
				try{
					$this->maxid->update();
				}  catch (\ErrorException $e){
					error_log("设置同步公告id的超时时间戳失败(".$id.")：".$e->getMessage()."\n".$e->getTraceAsString());
					$this->maxid=null;
				}
				foreach($needsTransfer as $id=>$r){
					\Lib\Services\Poststransfer::getInstance(null)
						->sendPostsToAll($id,$r['title'],$r['content'],1);
					break;
				}
			}
		}
		return true;
	}
	protected $timeChk = 0;

}
