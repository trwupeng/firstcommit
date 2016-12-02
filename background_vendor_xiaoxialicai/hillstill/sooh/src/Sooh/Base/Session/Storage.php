<?php
namespace Sooh\Base\Session;
/**
 * Description of Session
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class Storage {
	protected static $_instance=null;
	public static function setStorageIni($dbGrpId='default',$numSplit=2)
	{
		\Sooh\DB\Cases\SessionStorage::$__id_in_dbByObj=$dbGrpId;
		\Sooh\DB\Cases\SessionStorage::$__nSplitedBy=$numSplit;
	}

	/**
	 * 
	 * @param \Sooh\Base\Rpc\Base $rpcOnNew
	 * @return Storage
	 */
	public static function getInstance($rpcOnNew=null)
	{
		if(self::$_instance===null){
			$c = get_called_class();
			self::$_instance = new $c;
			self::$_instance->rpc = $rpcOnNew;
		}
		return self::$_instance;
	}
	/**
	 *
	 * @var \Sooh\Base\Rpc\Base 
	 */	
	protected $rpc=null;
	
	/**
	 * 读出数据并返回：array(
	 *				data=>array(__eXpIrE__=>array(k2=>expire2),k1=>v1,k2=>v2), 
	 *				trans=array(...)
	 *			)
	 * @param type $sessionId
	 * @return array array(data=>array(__eXpIrE__=>array(k2=>expire2),k1=>v1,k2=>v2), trans=array(...))
	 */
	public function load($sessionId)
	{
		if($this->rpc!==null){
			return $this->rpc->initArgs(array('sessionId'=>$sessionId))->send(__FUNCTION__);
		}else{
			$obj = \Sooh\DB\Cases\SessionStorage::getCopy($sessionId);
			$obj->load();
			if(!$obj->exists()){
				return array('data'=>array(),'trans'=>array('sessionId'=>$sessionId));
			}else{
				$data = $obj->getSessionData();
				$this->md5Last = md5(json_encode($data));
				return array('data'=> $data,'trans'=>$obj->getArrayTrans());
			}
		}
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
			return;
		}else{
			$this->dtExpiredChk = $dtExpire;
			\Sooh\DB\Cases\SessionStorage::loop([$this,__FUNCTION__]);
			return $this->numRemoved;
		}
	}
	protected $dtExpiredChk=0;
	protected $numRemoved=0;
	/**
	 * 
	 * @param type $sessionId
	 * @return bool
	 */
	public function createSesssion($sessionId)
	{
		
	}
	protected $md5Last = '';
	public function update($sessionId,$sessDataChg,$trans)
	{
		if(empty($sessDataChg)){
			$ex = new \ErrorException('try save session when nothing changed???');
			error_log($ex->getMessage()."\n".$ex->getTraceAsString());
			return 'done';
		}
		if($this->rpc!==null){
			return $this->rpc->initArgs(array('sessionId'=>$sessionId,'sessDataChg'=>$sessDataChg,'trans'=>$trans))->send(__FUNCTION__);
		}else{
			try{
				if (!empty($trans['sessionId']) && $trans['sessionId']!=$sessionId){
					error_log("SessionSave_{$sessionId}:ERROR on update session with sessionId dismatch: $sessionId"."\n[trans=>".  json_encode($trans)."\ndata=>".  json_encode($sessDataChg)." ]" );
					\Sooh\Base\Log\Data::error("ERROR on update session with sessionId dismatch: $sessionId",array('trans'=>$trans,'data'=>$sessDataChg));
					return 'error';
				}else{
					$obj = \Sooh\DB\Cases\SessionStorage::getCopy($sessionId);
					$obj->load();
					$retry=3;
					while($retry){
						try{
							$obj->setSessionData($sessDataChg);
							$obj->setField('lastUpdate', time());
							$obj->update();
							error_log(\Sooh\DB\Broker::lastCmd());
							break;
						}catch(\ErrorException $e){
							error_log(\Sooh\DB\Broker::lastCmd());
							$obj->reload();
						}
						$retry--;
					}

					error_log("SessionSave_{$sessionId}:ok");
					//error_log(">>>>>>>>>>>session>>>$sessionId\n".  var_export($sessDataChg,true)."\n".  var_export($trans,true));
					return 'done';
				}
			}  catch (\Exception $e){
				\Sooh\Base\Log\Data::error('errorOnUpdateSession',$e);
				error_log("SessionSave_{$sessionId}:".$e->getMessage()."\n".\Sooh\DB\Broker::lastCmd()."\n".$e->getTraceAsString());
				return 'error:'.$e->getMessage();
			}
		}
	}
}
