<?php
namespace Sooh\Base\Log\Writers;
/**
 * 默认的写文本的log writer (一个文件中)
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class Database {
	private $dbid;
	private $tbSplit;
	private $useYmd;
	public function __construct($dbid,$tbNum,$useYmd=true) {
		$this->dbid=$dbid;
		$this->tbSplit=$tbNum;
		$this->useYmd=$useYmd;
	}
	/**
	 * 
	 * @param \Sooh\Base\Log\Data $logData
	 */
	public function write($logData)
	{
		$resChg = $logData->resChanged;
		$arr = $logData->toArray();
		unset($arr['resChanged']);
		unset($arr['logGuid']);
		if($this->useYmd){
			\Sooh\DB\Cases\LogStorage::$__YMD= \Sooh\Base\Time::getInstance()->YmdFull;
		}else{
			\Sooh\DB\Cases\LogStorage::$__YMD='';
		}
		\Sooh\DB\Cases\LogStorage::$__id_in_dbByObj=$this->dbid;
		\Sooh\DB\Cases\LogStorage::$__type='a';
		\Sooh\DB\Cases\LogStorage::$__nSplitedBy=$this->tbSplit;
		//\Sooh\DB\Cases\LogStorage::$__fields=array(.....);
		$tmp = \Sooh\DB\Cases\LogStorage::getCopy($logData->logGuid);
		foreach($arr as $k=>$v){
			$tmp->setField($k, $v);
		}
		$ret = $tmp->writeLog();
		if($ret){
			$tbSub = str_replace('_a_', '_b_', $tmp->tbname());
			foreach($resChg as $r){
				$r['logGuid'] = $logData->logGuid;
				try{
					\Sooh\DB\Broker::errorMarkSkip(\Sooh\DB\Error::tableNotExists);
					$tmp->db()->addRecord($tbSub, $r);
				}  catch (\ErrorException $e){
					if(\Sooh\DB\Broker::errorIs($e, \Sooh\DB\Error::tableNotExists)){
						$tmp->db()->ensureObj($tbSub, array(
							'logGuid'=>'bigint unsigned not null  default 0',
							'resName'=>"varchar(36) not null default ''",
							'resChg'=>"int not null default 0",
							'resNew'=>"int not null default 0",
						));
						$tmp->db()->addRecord($tbSub, $r);
					}else{
						error_log("write log failed:".$e->getMessage()."\n". \Sooh\DB\Broker::lastCmd());
					}
				}
			}
		}
	}
	public function free()
	{
		
	}	
}
