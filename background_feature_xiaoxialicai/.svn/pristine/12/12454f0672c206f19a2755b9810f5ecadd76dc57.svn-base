<?php
namespace Lib\Misc;
/**
 * 默认的写文本的log writer (一个文件中)
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class IceLoger {
	protected $iceRegServer;
	public function __construct() {
		$this->iceRegServer = "SzcIceGrid/Locator:tcp -h 127.0.0.1 -p 4061";
	}
	/**
	 * 
	 * @param \Sooh\Base\Log\Data $logData
	 */
	public function write($logData)
	{
		global $_SOOH_REQUEST_INFO;//最后的输出
		//$randId = rand(1000000,9999999);
		//if('loger/applog'==$_REQUEST['__'])return;
		//error_log('[write log start]-time-used-start'.$_REQUEST['__'].'#'.$randId.'#'.  microtime(true)."\n");
		$tmpobj = \Lib\Misc\ZerocIce::getInstance();
		$tmpobj->asy('logcenter','loger','writeAsy',json_encode($logData),  json_encode($_SOOH_REQUEST_INFO));
		//error_log('[write log done]-time-used-ended'.$_REQUEST['__'].'#'.$randId.'#'.  microtime(true)."\n");
	}
	public function free()
	{
		
	}	
}