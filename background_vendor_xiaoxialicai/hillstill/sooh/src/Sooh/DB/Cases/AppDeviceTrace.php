<?php
namespace Sooh\DB\Cases;
/**
 * app 匹配检查
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class AppDeviceTrace extends \Sooh\DB\Base\KVObj{
	protected function log($type,$err=null)
	{
		$msg = $this->getField('appType')."\t".$this->getField('pkey')."\t".$this->getField('skey')."\t".$this->getField('copartnerData')."\t".$this->getField('copartnerUrl');
		$msg = $this->getField('contractId')."\t".$this->getField('copartnerAbs')."\t".$msg;
		if($err!==null){
			error_log("[AppTrace $type Error]\t".$err->getMessage()."\t$msg\n". $err->getTraceAsString());
		}else{
			error_log("[AppTrace $type]\t\t$msg");
		}
	}
	/**
	 * 
	 * @param callback $callback 回调函数($record) 返回错误消息，空串表示成功通知
	 */
	public static function findNeedsNotify($callback)
	{
		self::$func_notifyInstall = $callback;
		\Sooh\DB\Cases\AppDeviceTrace::loop("\\Sooh\\DB\\Cases\\AppDeviceTrace::loop_chk");
	}
	protected static $func_notifyInstall;
	/**
	 * 遍历回调(by self::findNeedsNotify)
	 * @param \Sooh\DB\Interfaces\All $db
	 * @param string $tb
	 */
	public static function loop_chk($db,$tb)
	{
		$retry = new \Sooh\Base\Retrylater(array(1,5,60,120,300,300,300,300),60);
		
		try{
			\Sooh\DB\Broker::errorMarkSkip(\Sooh\DB\Error::tableNotExists);
			$rs = $db->getRecords($tb, '*',array('callbackRetry<'=>$retry->cmpVal,'callbackRetry>'=>0),null,100);
			if(empty($rs)){
				return;
			}
		}catch(\ErrorException $e){
			if(\Sooh\DB\Broker::errorIs($e, \Sooh\DB\Error::tableNotExists)){
				return;
			}else{
				throw $e;
		}
		}
		
		if(is_array(self::$func_notifyInstall)){
			foreach($rs as $r){
				try{
					$r['copartnerRet'] = call_user_func(self::$func_notifyInstall,$r);
				}catch(\ErrorException $e){
					$r['copartnerRet'] = new \Sooh\Base\RetSimple(\Sooh\Base\RetSimple::errDefault,$e->getMessage());
					error_log($e->getMessage()."\n".$e->getTraceAsString());
				}
				self::updNotifiRet($db,$tb,$r,$retry);
			}
		}else{
			$f = self::$func_notifyInstall;
			foreach($rs as $r){
				try{
					$r['copartnerRet'] = $f($r);
				}catch(\ErrorException $e){
					$r['copartnerRet']=new \Sooh\Base\RetSimple(\Sooh\Base\RetSimple::errDefault,$e->getMessage());
					error_log($e->getMessage()."\n".$e->getTraceAsString());
				}
				self::updNotifiRet($db,$tb,$r,$retry);
			}
		}
	}
	/**
	 * 更新回调通知结果
	 * @param \Sooh\DB\Interfaces\All $db
	 * @param string $tb
	 * @param array $r
	 * @param \Sooh\Base\RetSimple $__ignore__
	 */	
	protected static function updNotifiRet($db,$tb,$r,$retry,$__ignore__=null)
	{
		$__ignore__ = $r['copartnerRet'];
		if($__ignore__->ret==\Sooh\Base\RetSimple::ok){
			$r['callbackRetry']=$retry->maxForDone;
		}else{
			$retry->parse($r['callbackRetry']);
			$retry->needsRetry();
			$r['callbackRetry']=$retry->toNumStr();
		}
		$r['copartnerRet'] = json_encode($__ignore__);
		$ret = $db->updRecords($tb, array('copartnerRet'=>$r['copartnerRet'],'callbackRetry'=>$r['callbackRetry'],'iRecordVerID'=>$r['iRecordVerID']+1),array('appType'=>$r['appType'],'pkey'=>$r['pkey'],'skey'=>$r['skey'],'iRecordVerID'=>$r['iRecordVerID']));

		return $ret===1;
	}
	
	protected static function idFor_dbByObj_InConf($isCache)
	{
		return 'AppUniqueDevice';
	}
	protected static function splitedTbName($n,$isCache)
	{
		return 'tblog_device_'.($n%static::numToSplit());
	}
	protected static function indexForSplit($pkey)
	{
		$s = md5(json_encode($pkey['pkey']));
		$s = substr($s,-3);
		$n = base_convert($s, 16, 10);
		$n = $n % 100;
		return $n ;
	}

	/**
	 * 
	 * @param type $data
	 * @param type $url
	 * @return \Sooh\DB\Cases\AppDeviceTrace
	 */
	public function setData($data,$url=null)
	{
		$this->setField('copartnerData', $data);
		$this->setField('copartnerUrl', $url);
		return $this;
	}

	/**
	 * 记录合作方发起的通知
	 * @param int $expire 有效期，默认259200（3天）
	 * @return Sooh\DB\Cases\AppDeviceTrace
	 */
	public function setCopartnerAndSave($contractId,$copartnerAbs,$expire=259200)
	{
		$this->setField('appType', $this->pkey['appType']);
		$this->setField('pkey', $this->pkey['pkey']);
		$this->setField('skey', $this->pkey['skey']);
		$this->setField('contractId', $contractId);
		$this->setField('chk', '');
		$this->setField('copartnerAbs', $copartnerAbs);
		$dt = \Sooh\Base\Time::getInstance();
		$this->setField('ymd', $dt->YmdFull);
		$this->setField('hhiiss', $dt->his);
		$this->setField('expired', $dt->timestamp()+$expire);
		try{
			\Sooh\DB\Broker::errorMarkSkip(\Sooh\DB\Error::tableNotExists);
			\Sooh\DB\Broker::errorMarkSkip(\Sooh\DB\Error::duplicateKey);
			$this->update();
			$this->log('trace_init');
		}  catch (\ErrorException $e){
			if(\Sooh\DB\Broker::errorIs($e, \Sooh\DB\Error::tableNotExists)){
				$this->createTable($this->db(),$this->tbname());
				$this->update();
				$this->log('trace_init');
			}elseif(\Sooh\DB\Broker::errorIs($e, \Sooh\DB\Error::duplicateKey)){
				$data = $this->getField('copartnerData');
				$url = $this->getField('copartnerUrl');
				$this->load('*');
				if($this->getField('callbackRetry')=='0'){
					$this->setField('copartnerData', $data);
					$this->setField('copartnerUrl', $url);
					$this->setField('contractId', $contractId);
					$this->setField('chk', '');
					$this->setField('copartnerAbs', $copartnerAbs);
					$dt = \Sooh\Base\Time::getInstance();
					$this->setField('ymd', $dt->YmdFull);
					$this->setField('hhiiss', $dt->his);
					$this->setField('expired', $dt->timestamp()+$expire);
					$this->update();
					$this->log('trace_init');
				}else $this->log('trace_init');
			}else $this->log('trace_init',$e);
		}
	}
	/**
	 * 建表
	 * @param \Sooh\DB\Interfaces\All $db
	 * @param string $tb
	 */
	protected function createTable($db,$tb)
	{
		$db->ensureObj($tb, array(
			'appType'=>'varchar(10) not null',
			'pkey'=>'varchar(50) not null',
			'skey'=>'varchar(50) not null',
			'chk'=>'varchar(36) not null',
			'contractId'=>'bigint not null default 0',
			'copartnerAbs'=>'varchar(16)',
			'ymd'=>'int not null default 0',
			'hhiiss'=>'int not null default 0',
			'expired'=>'int not null default 0',
			'copartnerData'=>'varchar(128)',
			'copartnerUrl'=>'varchar(512)',
			'copartnerRet'=>'varchar(512)',
			'callbackRetry'=>'bigint unsigned not null default 0',
			$this->fieldName_verid=>'int not null default 0',
		),array('appType','pkey','skey'),array('dtretry'=>array('callbackRetry')));
	}
	/**
	 * 
	 * @param type $idfa
	 * @param type $mac
	 * @return \Sooh\DB\Cases\AppDeviceTrace
	 */
	public static function startIOS($idfa,$mac=null)
	{
		if(empty($idfa)){
			if(empty($mac)) throw new \ErrorException('pkey for ios invalid');
			$pkey = array('appType'=>'ios','pkey'=>'','skey'=>$mac);
		}else{
			$pkey = array('appType'=>'ios','pkey'=>$idfa,'skey'=>'');
		}
		$tmp = parent::getCopy($pkey);
		return $tmp;
	}
	/**
	 * 
	 * @param string $imei
	 * @return \Sooh\DB\Cases\AppDeviceTrace
	 */	
	public static function startAndroid($imei)
	{
		$pkey=array('appType'=>'android','pkey'=>$imei,'skey'=>'');
		$tmp = parent::getCopy($pkey);
		return $tmp;
	}

	/**
	 * 
	 * @param string $idfa
	 * @param string $mac
	 * @return int64 contractId
	 */
	public static function getContractIdOfIOS($idfa,$mac=null)
	{
		if(!empty($idfa)){
			$tmp = self::startIOS($idfa, null);
		}else{
			$tmp = self::startIOS(null, $mac);
		}
		try{
			\Sooh\DB\Broker::errorMarkSkip(\Sooh\DB\Error::tableNotExists);
			$pkey = $tmp->load();
			if($pkey){
				$dt = \Sooh\Base\Time::getInstance()->timestamp();
				if($dt<$tmp->getField('expired') ){
					$contractId = $tmp->getField('contractId');
					try{
						$tmp->update(array($tmp,'markInstalled'));
						return $contractId;
					}  catch (\ErrorException $e){
						error_log($e->getMessage()."\n".$e->getTraceAsString());
						return 0;
					}
				}else {
					return 0;
				}
			}else{ 
				return 0;
			}
		}  catch (\ErrorException $e){
			if(\Sooh\DB\Broker::errorIs($e,\Sooh\DB\Error::tableNotExists)){
				return 0;
			}else{
				error_log($e->getMessage()."\n".$e->getTraceAsString());
				return 0;
			}
		}
	}
	
	/**
	 * 
	 * @param \Sooh\DB\Base\KVObj $obj
	 * @param int $retry
	 */
	public function markInstalled($obj,$retry)
	{
		$obj->setField('callbackRetry',1);
	}
	/**
	 * 
	 * @param string $imei
	 * @return int64 contractId
	 */	
	public static function getContractIdOfAndroid($imei)
	{
		$tmp = self::startAndroid($imei);
		try {
			\Sooh\DB\Broker::errorMarkSkip(\Sooh\DB\Error::tableNotExists);
			$pkey = $tmp->load();
			if($pkey){
				$dt = \Sooh\Base\Time::getInstance()->timestamp();
				if($dt<$tmp->getField('expired') ){
					$contractId = $tmp->getField('contractId');
					try{
						$tmp->update(array($tmp,'markInstalled'));
						return $contractId;
					}  catch (\ErrorException $e){
						error_log($e->getMessage()."\n".$e->getTraceAsString());
						return 0;
					}
				}else {
					return 0;
				}
			}else {
				return 0;
			}
		}  catch (\ErrorException $e){
			if(\Sooh\DB\Broker::errorIs($e,\Sooh\DB\Error::tableNotExists)){
				return 0;
			}else{
				error_log($e->getMessage()."\n".$e->getTraceAsString());
				return 0;
			}
		}	
	}
}
