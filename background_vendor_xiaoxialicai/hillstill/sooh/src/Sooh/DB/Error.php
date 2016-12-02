<?php
namespace Sooh\DB;
/**
 * @author Simon Wang <sooh_simon@163.com> 
 */
class Error extends \Sooh\Base\ErrException
{
	public $strLastCmd;
	const connectError=1;//检查配置；检查应用服务和网络的状态
	const dbExists=2;
	const dbNotExists=4;
	const tableExists=8;
	const tableNotExists=16;
	const fieldExists=32;
	const fieldNotExists=64;
	const duplicateKey=128;//键冲突
	const otherError=1073741824;
	public function __construct($code,$errOriginal,$lastSql) {
		$this->strLastCmd = is_string($lastSql)?$lastSql:  json_encode($lastSql);
		parent::__construct($errOriginal, $code);
		//$this->getTrace();
	}
	public $keyDuplicated=null;//冲突的键名
	public static $maskSkipTheseError=0;	
/*
	public function error_log($msgPrefix='ErrorException:')
	{
		if($this->isWroteToErrorLog===false){
			$this->isWroteToErrorLog=true;
			error_log($msgPrefix.$this->getMessage()."\n".\Sooh\DB\Broker::lastCmd()."\n".$this->getTraceAsString());
		}
	}
*/
}
