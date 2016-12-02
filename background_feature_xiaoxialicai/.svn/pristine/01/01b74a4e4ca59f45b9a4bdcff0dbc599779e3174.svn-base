<?php
namespace Lib\Services;
/**
 * session storage
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class SessionStorage extends \Sooh\Base\Session\Storage{
	public static function setStorageIni($dbGrpId='session',$numSplit=1)
	{
		\Sooh\DB\Cases\SessionStorage::$__id_in_dbByObj=$dbGrpId;
		\Sooh\DB\Cases\SessionStorage::$__nSplitedBy=$numSplit;
	}
	/**
	 * 删除过期的 调用时第一个参数是过期时间点，内部回调是第一个参数是db
	 * @param \Sooh\DB\Interfaces\All $dtExpire  调用时是过期时间点，内部回调是db
	 * @param string $_ignore_ 回调时的tbname
	 */
	public function removeExpire($dtExpire,$_ignore_=null)
	{
		if($_ignore_!=null){
			$this->numRemoved+=$dtExpire->delRecords($_ignore_,['lastUpdate<'=>  $this->dtExpiredChk]);
			error_log('remove_expired_session:'.\Sooh\DB\Broker::lastCmd());
			return;
		}else{
			$this->dtExpiredChk = $dtExpire;
			\Sooh\DB\Cases\SessionStorage::loop([$this,__FUNCTION__]);
			return $this->numRemoved;
		}
	}
}
