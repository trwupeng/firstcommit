<?php
namespace Sooh\Base;
/**
 * ErrorException
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class ErrException extends \ErrorException{
	const msgErrorArg='arg_error';
	const msgServerBusy='server_busy';
	const msgLocked='record_locked';
	const msgNotEnough='num_not_enough';

	public function __construct($message = "", $code = 400, $severity = 1, $filename = __FILE__, $lineno = __LINE__, $previous=null) {
		parent::__construct($message, $code, $severity, $filename, $lineno, $previous);
	}

	public static function factory($message,$code)
	{
		$e = new ErrException($message, $code);
		return $e;
	}
	public $customData=null;
	protected $isWrote=false;
	public function __toString()
	{
		if($this->isWrote===false){
			$this->isWrote=true;
			return $this->getMessage()."[Sooh_Base_Error]".$this->getTraceAsString();
		}else{
			return parent::__toString();
		}
	}
	
}
