<?php
namespace Sooh\Base\Crond;
/**
 * 记录计划任务日志
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class Log {
	protected $dbConfID;
	protected $tbName;
	public function __construct($dbConfId='default',$tbName='db_log.tb_crond_log') {
		$this->dbConfID=$dbConfId;
		$this->tbName=$tbName;
	}
	/**
	 * 写日志
	 * @param type $taskid
	 * @param type $msg
	 */
	public function writeCrondLog($taskid,$msg)
	{
		error_log("\tCrond ".  getmypid()."#\t$taskid\t$msg");
	}
	/**
	 * 更新状态
	 * @param int $ymd yyyymmdd
	 * @param int $hour
	 * @param string $taskid  哪个任务
	 * @param string $lastStatus  本轮最后执行结果
	 * @param int $isOkFinal 是否正常结束（预定的跳过也算正常）
	 * @param int $isManual  是自动还是手动
	 * @throws \ErrorException
	 */
	public function updCrondStatus($ymd,$hour,$taskid,$lastStatus,$isOkFinal,$isManual=0)
	{
		try{
			if(strlen($lastStatus)>250){
				error_log('updCrondStatus_msgTooLong:'.$lastStatus);
				$lastStatus = substr($lastStatus,0,250)."...";
			}
			\Sooh\DB\Broker::errorMarkSkip();
			\Sooh\DB\Broker::getInstance($this->dbConfID)->addRecord($this->tbName, array('ymdh'=>$ymd*100+$hour,'taskid'=>$taskid,'lastStatus'=>$lastStatus,'ymdhis'=>date('YmdHis'),'lastRet'=>$isOkFinal,'isManual'=>$isManual));
		} catch (\ErrorException $e) {
			if(\Sooh\DB\Broker::errorIs($e)){
				\Sooh\DB\Broker::getInstance($this->dbConfID)->updRecords($this->tbName, array('lastStatus'=>$lastStatus,'ymdhis'=>date('YmdHis'),'lastRet'=>$isOkFinal,'isManual'=>$isManual),array('ymdh'=>$ymd*100+$hour,'taskid'=>$taskid,));
			}else throw $e;
		}
	}
	/**
	 * 建库表
	 */
	public function ensureCrondTable()
	{
		\Sooh\DB\Broker::getInstance($this->dbConfID)->ensureObj($this->tbName, array(
			'ymdh'=>'bigint not null default 0','taskid'=>'varchar(64) not null',
			'lastStatus'=>'varchar(512)','lastRet'=>'tinyint not null default 0','isManual'=>'tinyint not null default 0',
			'ymdhis'=>'bigint not null default 0'
		),array('ymdh','taskid'));
	}
	/**
	 * 删除过期日志
	 * @param int $dayExpired （默认删除190天前）
	 */
	public function remoreCrondLogExpired($dayExpired=190)
	{
		$dt = \Sooh\Base\Time::getInstance()->getInstance()->timestamp(-$dayExpired);
		\Sooh\DB\Broker::getInstance($this->dbConfID)->delRecords($this->tbName, array('ymdh<'=>date('YmdH',$dt)));
	}
}
