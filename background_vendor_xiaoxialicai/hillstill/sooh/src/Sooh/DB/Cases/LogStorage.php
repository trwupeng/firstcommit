<?php
namespace Sooh\DB\Cases;

/**
 * 记录日志的控制类
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class LogStorage extends \Sooh\DB\Base\KVObj {
	public static $__YMD=20150401;
	public static $__id_in_dbByObj='use_db_log';
	public static $__type='a';
	public static $__nSplitedBy=1;
	public static $__fields=array(
			'logGuid'=>"bigint unsigned NOT NULL DEFAULT 0",
			'deviceId'=>'varchar(64) not null',
			'userId'=>'varchar(64) not null',
			'isLogined'=>'smallint not null default 0',
			'opcount'=>'int not null default 0',
			'clientType'=>'int not null default 0',
			'clientVer'=>'VARCHAR(16)',
			'contractId'=>'bigint not null default 0',
			'evt'=>'varchar(64)',
			'mainType'=>'varchar(64)',
			'subType'=>'varchar(64)',
			'target'=>'varchar(128)',
			'num'=>'int not null default 0',
			'ext'=>'varchar(512)',
			'ret'=>'varchar(2000)',
			'narg1'=>'int not null default 0',
			'narg2'=>'int not null default 0',
			'narg3'=>'int not null default 0',
			'sarg1'=>'varchar(500)',
			'sarg2'=>'varchar(1000)',
			'sarg3'=>'varchar(2000)',
			'ip'=>'varchar(32)',
			'ymd'=>'int not null default 0',
			'hhiiss'=>'int not null default 0',
		);
	public function writeLog()
	{
		try{
			\Sooh\DB\Broker::errorMarkSkip(\Sooh\DB\Error::tableNotExists);
			$this->update();
			return true;
		} catch ( \Sooh\DB\Error $e) {
			if(\Sooh\DB\Broker::errorIs($e,\Sooh\DB\Error::tableNotExists)){
				$this->createTable ();
				$this->update();
				return true;
			}else {
				error_log("ErrorOnWriteLog:".$e->getMessage()."\n".\Sooh\DB\Broker::lastCmd()."\n".$e->getTraceAsString());
				return false;
			}
		}
		
	}
	
	protected static function idFor_dbByObj_InConf($isCache)
	{
		return static::$__id_in_dbByObj;
	}
	/**
	 * 
	 * @return  LogStorage
	 */
	public static function getCopy($guid)
	{
		return parent::getCopy(array('logGuid'=>$guid));
	}
	protected static function splitedTbName($n,$isCache)
	{
		return 'tblog_'.static::$__YMD.'_'.self::$__type.'_'.($n%static::numToSplit());
	}

	/**
	 * 创建每天的日志表
	 */
	protected function createTable()
	{
		$fields = self::$__fields;
		$fields['iRecordVerID']='int not null default 0';//'iRecordVerID'=>'int not null default 0'
		$this->db()->ensureObj($this->tbname(), $fields);
	}
}