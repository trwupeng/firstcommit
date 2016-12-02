<?php
namespace PrjCronds;
/**
 * 检查失败的订单，回复本金券状态
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class FreeSession extends \Sooh\Base\Crond\Task{
	public function init() {
		parent::init();
		$this->toBeContinue=true;
		$this->_iissStartAfter=2200;//每小时22分后启动
		$this->ret = new \Sooh\Base\Crond\Ret();
	}
	public function free() {
		parent::free();
	}

	/**
	 * @param \Sooh\Base\Time $dt
	 */
	protected function onRun($dt) {
		
		if($this->_isManual || $dt->hour == 3 ){
			$this->timeChk = $dt->timestamp()-86400*10;
			error_log('remove_expired_session:start '.date('Y-m-d',$this->timeChk));
			$rpc = \Prj\BaseCtrl::getRpcDefault('SessionStorage');
			if($rpc==null){
				\Lib\Services\SessionStorage::setStorageIni();
			}
			$n = \Lib\Services\SessionStorage::getInstance($rpc)->removeExpire($this->timeChk);
			$this->lastMsg = "removed:".$n;//要在运行日志中记录的信息
			error_log('remove_expired_session:done');
		}else{
			error_log('remove_expired_session:skip');
			$this->lastMsg = 'skip';
		}
		$this->toBeContinue=false;
		return true;
	}
	protected $timeChk = 0;

}
