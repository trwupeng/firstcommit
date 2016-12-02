<?php
namespace Sooh\Base\Log;
/**
 * 日志模块，用法：
 *		in dispatcher before ctrl->action start
			\Sooh\Base\Log\Data::addWriter(new \Sooh\Base\Log\Writers\TextAll(),'trace');
			\Sooh\Base\Log\Data::addWriter(new \Sooh\Base\Log\Writers\TextAll(),'error');
			\Sooh\Base\Log\Data::addWriter(new \Sooh\Base\Log\Writers\Database('dbgrpForLog', 2),'evt');
			$l = \Sooh\Base\Log\Data::getInstance('c');
			$l->evt = mod/ctr;/act....;

 *		in ctrl->action or lib
			$l = \Sooh\Base\Log\Data::getInstance();
			$l->clientType=900;
			$l->deviceId = \Lib\Session::getSessId();
			$l->appendResChange('gold', 1, 1);
			$l->appendResChange('silver', 10, 20);
 * 
			//$l->nextOne();
 * 
 *		in dispatcher after ctrl->action done
			\Sooh\Base\Log\Data::onShutdown();
 * 
 * 
 * @author Simon Wang <hillstill_simon@163.com>
 */
class Data {
	protected function newLogId($plan='c')
	{
		$dt = microtime(true);
		list($s,$ms)=  explode('.', $dt);
		$s = $s-1435680000;
			//18446744073709551616
			//17------------------  18组服务器
			//--1234567890--------  时间戳
			//------------sss-----  毫秒
			//---------------12345  进程
		switch ($plan){
			case 'a'://plan-a 180组服务器，30几年后溢出,(每个请求需要一个进程处理至少1个毫秒，)
				$this->plan='a';
				return sprintf("%03d%09d%03d%05d", \Sooh\Base\Ini::getInstance()->get('ServerId',0),$s,substr($ms,0,3),  getmypid());
			case 'b'://plan-b 18组服务器， 300年后溢出, (每个请求需要一个进程处理至少1个毫秒，)
				$this->plan='b';
				return sprintf("%02d%010d%03d%05d", \Sooh\Base\Ini::getInstance()->get('ServerId',0),$s,substr($ms,0,3),  getmypid());
			case 'c'://plan-c 18组服务器， 30年后溢出,  (每个请求需要一个进程处理至少0.1个毫秒，)
				$this->plan='c';
				return sprintf("%02d%09d%04d%05d", \Sooh\Base\Ini::getInstance()->get('ServerId',0),$s,substr($ms,0,4),  getmypid());
			default:
				throw new \Sooh\Base\ErrException('unknown support log-guid-generate');
		}
	}
	const type_evt = 'evt';
	const type_trace = 'trace';
	const type_error = 'error';
	/**
	 *
	 * @var  \Sooh\Base\Log\Data
	 */
	private static $_instance=null;
	/**
	 * $guidtype 在构造时使用一次
	 *   a 180组服务器，30几年后溢出,(每个请求需要一个进程处理至少1个毫秒，)
	 *   b 18组服务器， 300年后溢出, (每个请求需要一个进程处理至少1个毫秒，)
	 *   c 18组服务器， 30年后溢出,  (每个请求需要一个进程处理至少0.1个毫秒，)
	 * @param string $guidtype 
	 * @return \Sooh\Base\Log\Data
	 */
	public static function getInstance($guidtype='c')
	{
		if(self::$_instance==null){
			self::$_instance = new Data;
			$dt = \Sooh\Base\Time::getInstance();
			self::$_instance->ymd=$dt->YmdFull;
			self::$_instance->hhiiss=$dt->his;
			self::$_instance->ip = \Sooh\Base\Tools::remoteIP();
			self::$_instance->logGuid = self::$_instance->newLogId($guidtype);
			\Sooh\Base\Ini::registerShutdown(get_called_class().'::onShutdown', 'logOnShutdown');
		}
		return self::$_instance;
	}
	/**
	 * @var  VARCHAR(64)
	 */
	public $deviceId='';
	/**
	 * @var  VARCHAR(64)
	 */
	public $userId=''; 
	/**
	 * @var  tinyint [ 0 | 1 ]
	 */
	public $isLogined=0;
	/**
	 * @var  int
	 */	
	public $opcount=0;
	/**
	 * @var  int
	 */		
	public $clientType=0;
	/**
	 * @var  bigint
	 */		
	public $contractId=0;
	/**
	 * @var  VARCHAR(64)
	 */	
	public $evt='';
	/**
	 * @var  VARCHAR(64)
	 */
	public $mainType='';
	/**
	 * @var  VARCHAR(64)
	 */	
	public $subType='';
	/**
	 * @var  VARCHAR(128)
	 */
	public $target='';
	/**
	 * @var  int
	 */
	public $num=0;
	/**
	 * @var  VARCHAR(512)
	 */
	public $ext='';
	/**
	 * @var VARCHAR(16)
	 */
	public $clientVer='';
	/**
	 * @var  VARCHAR(128)
	 */
	public $ret='';
	/**
	 * @var  bigint
	 */	
	public $narg1=0;
	public $narg2=0;
	public $narg3=0;
	/**
	 * @var  VARCHAR(500)
	 */
	public $sarg1='';
	/**
	 * @var  VARCHAR(1000)
	 */
	public $sarg2='';
	/**
	 * @var  VARCHAR(2000)
	 */
	public $sarg3='';

	public $ip;
	public $ymd=0;
	public $hhiiss=0;
	
	public $logGuid;
	public $resChanged=array();
	private $plan='c';
	private $justNew=true;
	
	public static function trace($msg,$var=null)
	{
		$log = self::getInstance();
		$log->ret = $msg.(empty($var)?'':' - '.  json_encode($var));
		$log->write(self::type_trace);
	}
	
	public static function error($msg,$var=null)
	{
		$log = self::getInstance();
		$log->ret = $msg.(empty($var)?'':' - '.  json_encode($var));
		$log->write(self::type_error);
	}
	/**
	 * 
	 * @param \Sooh\Base\Log\Writers\Writer $_ignore_
	 */
	public static function onShutdown($_ignore_=null)
	{
		$log = self::getInstance();
		if($log->justNew){
			$log->write(self::type_evt);
			$log->justNew=false;
		}
		$ks = array_keys(self::$writer);
		foreach($ks as $k){
			foreach(self::$writer[$k] as $_ignore_){
				$_ignore_->free();
			}
			unset(self::$writer[$k]);
		}
	}
	/**
	 * 
	 * @param string $asType
	 * @param \Sooh\Base\Log\Writers\Writer $_ignore_
	 */
	protected function write($asType=self::type_evt,$_ignore_=null)
	{
		if($asType===self::type_evt){
			if($this->justNew){
				$this->justNew=false;
				$fs = self::$writer[self::type_evt];
				if(!empty($fs)){
					foreach($fs as $_ignore_){
						$_ignore_->write($this);
					}	
				}else{
					error_log("log-func undefined twice??");
				}
			}else{
				error_log("write twice??");
			}
		}else{
			$fs = self::$writer[$asType];
			if(!empty($fs)){
				foreach($fs as $_ignore_){
					$_ignore_->write($this);
				}
			}else{
				error_log("log-func undefined ". http_build_query($_GET));
			}
		}
	}
	private static $writer = array();
	/**
	 * 
	 * @param \Sooh\Base\Log\Writers\Writer $writor
	 * @param type $type
	 */
	public static function addWriter($writor,$type=self::type_error,$writerIndex=null)
	{
		if($writerIndex===null){
			self::$writer[$type][]=$writor;
		}else{
			self::$writer[$type][$writerIndex]=$writor;
		}
	}

	public function appendResChange($res,$chg,$new)
	{
		$this->resChanged[]=array('resName'=>$res,'resChg'=>$chg,'resNew'=>$new);
	}
	
	/**
	 * 
	 * @param string $mainType
	 * @param string $subType
	 * @param string $target
	 * @param int $num
	 * @param string $ext
	 * @return \Sooh\Base\Log\Data
	 */
	public function setThese($mainType,$subType,$target,$num,$ext)
	{
		$this->mainType = $mainType;
		$this->subType = $subType;
		$this->target = $target;
		$this->num = $num;
		$this->ext = $ext;
		return $this;
	}
	
	public function nextOne()
	{
		if($this->justNew){
			$this->write();
			$this->resChanged=array();
		}
		$old = substr($this->logGuid,-9);
		$new= $old+100000;
		if($new<1000000000){
			$this->logGuid = substr($this->logGuid,0,-9).sprintf("%09d",$new);
		}else{
			$this->logGuid = substr($this->logGuid,0,-15).(substr($this->logGuid,-15,6)+1).sprintf("%09d",$new);
		}
	}
	public function toArray()
	{
		return array(
			'logGuid'=>$this->logGuid,
			'ip'=>$this->ip,
			'ymd'=>$this->ymd,
			'hhiiss'=>$this->hhiiss,
			'deviceId'=>$this->deviceId,
			'userId'=>$this->userId,
			'isLogined'=>$this->isLogined,
			'opcount'=>$this->opcount,
			'clientType'=>$this->clientType,
			'contractId'=>$this->contractId,
			'evt'=>$this->evt,
			'mainType'=>$this->mainType,
			'subType'=>$this->subType,
			'target'=>$this->target,
			'num'=>$this->num,
			'ext'=>$this->ext,
			'ret'=>$this->ret,
			'clientVer'=>$this->clientVer,
			'narg1'=>$this->narg1,
			'narg2'=>$this->narg2,
			'narg3'=>$this->narg3,
			'sarg1'=>$this->sarg1,
			'sarg2'=>$this->sarg2,
			'sarg3'=>$this->sarg3,
			
			'resChanged'=>$this->resChanged,
		);
	}
	public function fromArray($arr)
	{
		foreach($arr as $k=>$v){
			$this->$k = $v;
		}
	}
}
