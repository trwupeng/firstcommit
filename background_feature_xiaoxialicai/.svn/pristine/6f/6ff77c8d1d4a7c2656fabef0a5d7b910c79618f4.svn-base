<?php
namespace Lib\Services;
/**
 * rpc 服务器管理
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class Loger
{
	/**
	 * @var SMS
	 */
	protected static $_instance = null;

	protected function useRpcForEvtTriggers(){
		error_log("线上版本，日志接着直接记录evt，不需要通过rpc，改为false");
		return false;
	}
	/**
	 *
	 * @param \Sooh\Base\Rpc\Broker $rpcOnNew
	 * @return \Lib\Services\SMS
	 */
	public static function getInstance($rpcOnNew = null)
	{
		if (self::$_instance === null) {
			$c = get_called_class();
			self::$_instance = new $c;
			self::$_instance->rpc = $rpcOnNew;
		}
		return self::$_instance;
	}
	public function writeSyn($logdata,$reqdata)
	{
		if ($this->rpc !== null) {
			return $this->rpc->initArgs(array('logdata' => $logdata, 'reqdata' => $reqdata))->send(__FUNCTION__);
		} else {
			
			$tmp = new \Sooh\Base\Log\Data();
			$tmp->fromArray(json_decode($logdata,true));
			$this->fixDeviceId($tmp);
			if($this->writeEventlog($tmp)){
				$this->triggers($tmp);
				$this->writeRequestlog($reqdata);
				return '{"code":200,"msg":"ok-'.$tmp->evt.'"}';
			}else{
				$this->writeRequestlog($reqdata);
				error_log(__CLASS__.'->'.__FUNCTION__."_failed($logdata, $reqdata) ");
				return '{"code":300,"msg":"failed-'.$tmp->evt.'"}';
			}
		}
	}
	/**
	 * @param \Sooh\Base\Log\Data $tmp
	 */
	protected function fixDeviceId($tmp)
	{
		if(!empty($tmp->deviceId)){
			$pos = strpos($tmp->deviceId, ':');
			if($pos===false){
				$len = strlen($tmp->deviceId);
				if($len<32){
					$tmp->deviceId='imei:'.$tmp->deviceId;
				}elseif($len==32){
					$tmp->deviceId='md5:'.$tmp->deviceId;
				}else{
					$tmp->deviceId='idfa:'.str_replace('-','',$tmp->deviceId);
				}
			}else{
				$tmp->deviceId=str_replace('-','',$tmp->deviceId);
			}
			
		}
	}
	/**
	 * 
	 * @param \Sooh\Base\Log\Data $logData
	 * @return bool
	 */	
	protected function triggers($logData,$evtFunc=null){
		if($evtFunc===null){
			//var_log($logData,'##CHECK-TRIGGERS-IN-EVTLOG');
			switch ($logData->evt){
				//case 'Index/Oauth/applogin':return $this->triggers($logData,'onLogin');
                case 'Index/Orders/add':{
                    if($logData->sarg3 == 'finallyOrder' && $logData->ret == 'ok'){
                        return $this->triggers($logData,'onBuyRequest'); //发放兜底活动奖励
                    }
                }
				case 'Index/Passport/login':
				case 'Index/Passport/quickReg':
				case 'Index/Passport/quickLogin':
					if($logData->mainType==1){
//						return $this->triggers($logData,'onRegister');
						$this->triggers($logData,'onRegister');
					}else{
//						return $this->triggers($logData,'onLogin');
						$this->triggers($logData,'onLogin');
					}
                case 'Index/Passport/sendInvalidcode':
                    return $this->triggers($logData, 'onSendSmsCodeForUpdPwd');1111
                case 'Index/Passport/sendSmscodeForQuickLogin':
                    return $this->triggers($logData, 'onSendSmsCodeForQuickLogin');1111
                case 'Index/Passport/login':
                    return $this->triggers($logData, 'onPassportLogin');1111
                case 'Index/Passport/quickReg':
                    return $this->triggers($logData, 'onPassportQuickReg');1111
                case 'Index/Passport/quickLogin':
                    return $this->triggers($logData, 'onPassportQuickLogin');1111
                case 'Index/Passport/resetPwd':
                    return $this->triggers($logData, 'onPassportResetPwd');1111
                case 'Index/Oauth/appreg':
                    return $this->triggers($logData, 'onOauthAppreg');11111
                case 'Index/Oauth/webReg':
                    return $this->triggers($logData, 'onOauthWebreg');11111
                case 'Index/Oauth/quickReg':
                    return $this->triggers($logData, 'onOauthQuickReg');1111
                case 'Index/Oauth/sendInvalidcode':
                    return $this->triggers($logData, 'onOauthSendInvalidcode');1111
                case 'Index/Public/receiveVoucher':
                    return $this->triggers($logData, 'onPublicReceiveVoucher');1111
                case 'Index/Actives/checkin':
                    return $this->triggers($logData, 'onActivesCheckin');11111111
                case 'Index/Exchangecode/getbonus':
                    return $this->triggers($logData, 'onExchangecodeGetbonus');111111
                case 'Index/Orders/Add';
                    return $this->triggers($logData, 'onOrdersAdd');1111
                case 'Index/User/bindcard':
                    return $this->triggers($logData, 'onUserBindcard');1111
                case 'Index/User/recharge':
                    return $this->triggers($logData, 'onUserRecharge');1111
                case 'Index/User/withdraw':
                    return $this->triggers($logData, 'onUserWithdraw');1111
                case 'Index/User/sendSmsCode':
                    return $this->triggers($logData, 'onUserSendSmscode');1111
                case 'Index/User/unbindCardAction':
                    return $this->triggers($logData, 'onUserUnBindCard');1111
                case 'Index/Weekactive/fetch':
                    return $this->triggers($logData, 'onWeekactiveFetch');

				//default:return $this->triggers($logData,'onLogin');
			}
		}else{
			//内部做了个小优化，如果通过rpc，转换成json-string，否则直接传data,
			if($this->useRpcForEvtTriggers()){
				\Lib\Services\Triggers::getInstance(\Prj\BaseCtrl::getRpcDefault('Triggers'))
							->$evtFunc( json_encode($logData->toArray()));		
			}else{
				\Lib\Services\Triggers::getInstance(null)
							->$evtFunc($logData);
			}
		}
	}

	private $dbid='dbgrpForLog';
	private $tbSplit=2;
	private $useYmd=true;	
	protected function writeRequestlog($reqdata)
	{
		error_log("[RequestLog]\n".$reqdata);
	}
	/**
	 * 
	 * @param \Sooh\Base\Log\Data $logData
	 * @return bool
	 */	
	protected function writeEventlog($logData)
	{
		//error_log('##################################'.__CLASS__.'->'.__FUNCTION__."\n".json_encode($logData->toArray()));
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
							'logGuid'=>'bigint unsigned not null default 0',
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
			return true;
		}else{
			return false;
		}
	}
}