<?php
namespace Sooh\Base\Acl;

/**
 * Account Service with login() and register()
 * 默认 storage: \Sooh\DB\Cases\AccountStorage(default库的2张tb_accounts_?表)
 * 重写 setAccountStorage()替换默认的storage
 * 必要字段：
`cameFrom` varchar(36) NOT NULL,
`loginname` varchar(16) NOT NULL,
`passwd` varchar(32) DEFAULT NULL,
`passwdSalt` varchar(4) DEFAULT NULL,
`accountId` bigint(20) unsigned NOT NULL DEFAULT '0',
`regYmd` int(11) NOT NULL DEFAULT '0',
`regHHiiss` int(11) NOT NULL DEFAULT '0',
`regClient` tinyint(4) NOT NULL DEFAULT '0',
`regIP` varchar(16) NOT NULL DEFAULT '',
`dtForbidden` int(11) NOT NULL DEFAULT '0' COMMENT '状态（0表示正常）',
`loginFailed` bigint(36) unsigned NOT NULL DEFAULT '0' COMMENT '密码错误后的CD',
`nickname` varchar(36) DEFAULT NULL,
`lastIP` varchar(16) NOT NULL DEFAULT '' COMMENT '最后访问IP',
`lastDt` int(11) NOT NULL DEFAULT '0' COMMENT '最后访问时间',
`iRecordVerID` int(20) unsigned DEFAULT '0',
PRIMARY KEY (`cameFrom`,`loginname`),
UNIQUE KEY `accountId` (`accountId`)
 *
 * 可自行增加一些字段，诸如contractId,rights
 *
 * 遗留问题，由最早的一个table（包含cameFrom,loginName,password）切换到两个表（表A：cameFrom,loginName,AccuntId 与表B：AccountId,password）,
 * 原来的setAccountStorage等，没有相应的彻底重构。
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class Account {
	const errAccountLock            = '帐号已被锁定';
	const errPasswdLock             = '密码错误次数过多';
	const errPasswdEmpty            = '请输入密码';
	const errPhoneEmpty             = '请输入手机号';
	const errPasswdIllegality       = '密码不合法，请重新输入';
	const errFailedCD               = '失败次数过多，请%s分钟后重试';
	const errInviteCodeError        = '邀请码错误';
	const errSmscodeError           = '验证码不正确';
	protected static $_instance = null;
	/**
	 *
	 * @param \Sooh\Base\Rpc\Base $rpcOnNew
	 * @return Account
	 */
	public static function getInstance($rpcOnNew=null)
	{
		if(self::$_instance===null){
			$cc = get_called_class();
			self::$_instance = new $cc;
			self::$_instance->rpc = $rpcOnNew;
		}
		return self::$_instance;
	}
	/**
	 *
	 * @var \Sooh\Base\Rpc\Broker
	 */
	protected $rpc=null;
	/**
	 * switch to the tb_account_X table(X 用于分库分表)
	 * @var \Sooh\DB\Cases\AccountStorage;
	 */
	protected $account=null;
	protected function setAccountStorage($accountId)
	{
		$this->account = \Sooh\DB\Cases\AccountStorage::getCopy($accountId);
	}
	/**
	 * 获取符合条件的账号的数量
	 * @param array $where
	 * @return int
	 */
	public function getAccountNum($where,$camefrom='local')
	{
		if ($this->rpc!==null) {
			return $this->rpc->initArgs(array('where'=>$where,))->send(__FUNCTION__);
		} else {
			$this->setAccountStorage('', $camefrom);
			return \Sooh\DB\Cases\AccountStorage::loopGetRecordsCount($where);
		}
	}

	/**
	 * 设置登入名--注册前准备(锁定)
	 * @param string $accountId
	 * @param string $loginName
	 * @param string $cameFrom
	 * @return boolean true:一切正常；false:已存在
	 * @throws Exception on update-db failed
	 */
	public function loginPrepare($accountId, $loginName, $cameFrom)
	{
		$objLogin = \Sooh\DB\Cases\AccountAlias::getCopy([$loginName, $cameFrom]);//switch to the tb_loginname_alias_X table(x 是 2的余数)
		$objLogin->load();
		if ($objLogin->exists()) {
			return false;
		} else {
			$objLogin->setField('accountId', $accountId);
			$objLogin->setField('loginName', $loginName);
			$objLogin->setField('cameFrom', $cameFrom);
			$objLogin->setField('flgStatus', 0);
			$objLogin->update();
			return true;
		}
	}

	/**
	 * 设置登入别名--rollback
	 * @param array $arrLoginName
	 * @param integer $key
	 */
	public function loginRollback($arrLoginName, $key) {
		foreach($arrLoginName as $_key => $_val) {
			if($_key <= $key) {
				$alias = \Sooh\DB\Cases\AccountAlias::getCopy([$_val[0], $_val[1]]);
				$alias->load();
				if ($alias->getField('flgStatus') == 0) {
					$alias->delete(true);
				}
			}
		}
	}

	/**
	 * 设置登入别名--commit
	 * @param string $aliasName
	 * @return boolean
	 * @throws Exception on update-db failed*
	 */
	protected function loginCommit($arrLoginName)
	{
		foreach($arrLoginName as $_val) {
			$alias = \Sooh\DB\Cases\AccountAlias::getCopy([$_val[0], $_val[1]]);
			$alias->load();
			$alias->setField('flgStatus', 1);
			$alias->update();
		}
	}

	/**
	 * @param string $loginName 账户ID
	 * @param string $cameFrom  登录类型
	 * @param string $newPwd    新密码
	 * @param string $deviceId  唯一设备ID
	 * @return boolean true
	 * @throws \ErrorException
	 * @throws \Sooh\Base\ErrException
	 */
	public function resetPwd($loginName, $cameFrom, $newPwd, $deviceId) {
		if ($this->rpc !== null) {
			return $this->rpc->initArgs(['loginName' => $loginName, 'cameFrom' => $cameFrom, 'newPwd' => $newPwd, 'deviceId' => $deviceId])->send(__FUNCTION__);
		} else {
			$dbAccount = \Sooh\DB\Cases\AccountAlias::getCopy([$loginName, $cameFrom]);
			$dbAccount->load();
			if($dbAccount->exists()) {
				$this->setAccountStorage($dbAccount->getField('accountId'),$cameFrom);
				$this->account->load();
				$pwdSalt = substr(uniqid(), -4);
				$this->account->setField('passwdSalt', $pwdSalt);
				$this->account->setField('passwd', md5($newPwd . $pwdSalt));
				$this->account->setField('deviceId', $deviceId);
				$this->account->setField('pwdVer', $this->account->getField('pwdVer') + 1);
				try {
					$this->account->update();
					$ret = [
						'pwdVer' => $this->account->getField('pwdVer'),
						'accountId' => $dbAccount->getField('accountId'),
					];
					return $ret;
				} catch (\Exception $e) {
					throw new \Sooh\Base\ErrException(\Prj\Lang\Broker::getMsg('account.reset_password_error'));
				}
			} else {
				throw new \Sooh\Base\ErrException(\Prj\Lang\Broker::getMsg('account.account_is_not_existing'));
			}
		}
	}

	/**
	 * 获取指定用户需要的字段（指定用户的方式：[aliasName:xxx] or [accountId:xxxx] or [loginName:xxxx,cameFrom:local]）
	 * @param array $fields
	 * @param array $where [aliasName:xxx] or [accountId:xxxx] or [loginName:xxxx,cameFrom:local]
	 * @param \Sooh\DB\Interfaces\All $_ignore_
	 * @return Account
	 */
	public function getFieldsBy($fields,$where,$_ignore_=null)
	{
		if($this->rpc!==null){
			return $this->rpc->initArgs(array('fields'=>$fields,'where'=>$where,))->send(__FUNCTION__);
		}else{
			$where = $this->getAccountWhereFinal($where);
			$this->setAccountStorage($where['loginName'], $where['cameFrom']);

			$this->account->load();
			$ret = array();
			if(is_string($fields)){
				$fields = explode(',', $fields);
			}
			foreach($fields as $k){
				$ret[$k] = $this->account->getField($k);
			}
			return $ret;
		}
		return parent::getCopy();
	}

	/**
	 * 获取最终的where：[aliasName:xxx] or [accountId:xxxx] => [loginName:xxxx,cameFrom:local]
	 * @param type $where
	 * @return type
	 * @throws \Sooh\Base\ErrException
	 */
	protected function getAccountWhereFinal($where)
	{
		if(isset($where['aliasName'])){
			$alias = \Sooh\DB\Cases\AccountAlias::getCopy($where['aliasName']);
			$alias->load();
			if($alias->exists()){
				unset($where['aliasName']);
				$where['loginName'] = $alias->getField('loginName');
				$where['cameFrom'] = $alias->getField('cameFrom');
			}else{
				throw new \Sooh\Base\ErrException(\Prj\Lang\Broker::getMsg('account.account_or_password_input_error'));
			}

		}elseif(isset($where['accountId'])){
			$this->setAccountStorage($where['accountId'], (empty($where['accountId'])?'local':$where['accountId']));
			//TODO
			$this->__accountId = $where['accountId'];
			$classname = get_class($this->account);
			$classname::loop(array($this,'__getPkeyByAccountId'));

			if(is_array($this->__accountId)){
				$where['loginName'] = $this->__accountId['loginName'];
				$where['cameFrom'] = $this->__accountId['cameFrom'];
			}else{
				throw new \Sooh\Base\ErrException(\Prj\Lang\Broker::getMsg('account.account_or_password_input_error'));
			}
		}
		return $where;
	}

	/**
	 * 设定字段（指定用户的方式：[aliasName:xxx] or [accountId:xxxx] or [loginName:xxxx,cameFrom:local]）
	 * @param array $fields
	 * @param array $where [aliasName:xxx] or [accountId:xxxx] or [loginName:xxxx,cameFrom:local]
	 * @param string  $aliasField 可选的登入名
	 * @return boolean
	 */
	public function setFieldsBy($fields,$where,$aliasField=null)
	{
		if($this->rpc!==null){
			return $this->rpc->initArgs(array('fields'=>$fields,'where'=>$where,))->send(__FUNCTION__);
		}else{
			$where = $this->getAccountWhereFinal($where);
			$this->setAccountStorage($where['loginName'], $where['cameFrom']);

			$this->account->load();
			if($aliasField!==null){
				$oldAlias = $this->account->getField($aliasField);
			}
			if($aliasField!==null){
				$alias = \Sooh\DB\Cases\AccountAlias::getCopy($fields[$aliasField]);
				$alias->load();
				if($alias->exists() && $alias->getField('flgStatus')==1){
					throw new \Sooh\Base\ErrException(\Prj\Lang\Broker::getMsg('account.accounts_existing'));
				}
				$alias->setField('loginName', $this->account->getField('loginName'));
				$alias->setField('cameFrom', $this->account->getField('cameFrom'));
				$alias->setField('flgStatus', 1);
				$alias->update();

				if($oldAlias && $oldAlias!=$fields[$aliasField]){
					$alias = \Sooh\DB\Cases\AccountAlias::getCopy($oldAlias);
					$alias->load();
					$alias->setField('cameFrom', '__removed__');
					$alias->setField('flgStatus', 0);
					$alias->update();
				}
			}
			foreach($fields as $k=>$v){
				$this->account->setField($k,$v);
			}
			$this->account->update();
			return true;
		}
	}
	private $__accountId;

	/**
	 *
	 * @param \Sooh\DB\Interfaces\All $db
	 * @param string $tb
	 */
	public function __getPkeyByAccountId($db,$tb)
	{
		if(!is_array($this->__accountId)){
			$r = $db->getRecord($tb, 'loginName,cameFrom',array('accountId'=>$this->__accountId));
			if(!empty($r)){
				$this->__accountId = $r;
			}
		}
	}

	/**
	 * 账号登入, 失败抛出异常(密码错误，账号找不到等等)
	 * @param $loginName
	 * @param $cameFrom
	 * @param $password
	 * @param array $customArgs
	 * @return mixed
	 * @throws \ErrorException
	 * @throws \Sooh\Base\ErrException
	 * @throws array
	 */
	public function login($loginName, $cameFrom, $password, $customArgs = ['contractId']) {
		if ($this->rpc !== null) {
			return $this->rpc->initArgs(['loginName' => $loginName, 'cameFrom' => $cameFrom, 'password' => $password, 'customArgs' => $customArgs])->send(__FUNCTION__);
		} else {
			$objLogin = \Sooh\DB\Cases\AccountAlias::getCopy([$loginName, $cameFrom]);
			$objLogin->load();
			if ($objLogin->exists()) {
				$accountId = $objLogin->getField('accountId');
				$this->setAccountStorage($accountId,$cameFrom);
				$this->account->load();
				if($this->account->exists()) {
                    $limitStatus = $this->account->getField('limitStatus');
                    if ($limitStatus == 1) {
                        throw new \Sooh\Base\ErrException(\Prj\Lang\Broker::getMsg('account.account_is_forbidden'), 400);
                    }

					$dt = \Sooh\Base\Time::getInstance();
					$cmp = md5($password . $this->account->getField('passwdSalt'));
					if ($cmp != $this->account->getField('passwd')) {
						if ($dt->timestamp() <= $this->account->getField('dtForbidden')) {
							$ret = $this->account->getField('loginFailed') + 1;
							$this->account->setField('loginFailed', $ret);
						} else {
							$ret = 1;
							$this->account->setField('loginFailed', $ret);
						}
						//限制时间为最后一次错误输入的30分钟之后
						$this->account->setField('dtForbidden', $dt->timestamp() + 1800);
						$this->account->update();
						throw new \Sooh\Base\ErrException(json_encode(['errorCount' => $ret, 'msg' => \Prj\Lang\Broker::getMsg('account.account_or_password_input_error')]), 401);
					}

					$nickname = $this->account->getField('nickname');
					$ret = array('accountId' => $this->account->getField('accountId'), 'nickname' => $nickname,);
					if (!empty($customArgs)) {
						if (is_string($customArgs)) {
							$customArgs = explode(',', $customArgs);
						}
						foreach ($customArgs as $k) {
							$ret[$k] = $this->account->getField('contractId');
						}
					}
					$this->account->setField('dtForbidden', 0);
					$this->account->setField('loginFailed', 0);
					$this->account->setField('lastIP', \Sooh\Base\Tools::remoteIP());
					$this->account->setField('lastDt', $dt->timestamp());

					try {
						$this->account->update();
					} catch (\ErrorException $ex) {
						\Sooh\Base\Log\Data::error("error on update account when login:" . $ex->getMessage() . "\n" . \Sooh\DB\Broker::lastCmd() . "\n" . $ex->getTraceAsString());
					}
					if (is_array($ret)) {
						return $ret;
					}
				} else {
					throw new \Sooh\Base\ErrException(\Prj\Lang\Broker::getMsg('account.account_or_password_input_error'), 400);
				}
			} else {
				throw new \Sooh\Base\ErrException(\Prj\Lang\Broker::getMsg('account.account_or_password_input_error'), 400);
			}
		}
	}

	/**
	 * 通过短信验证码登录
	 * @param integer $loginName 手机号
	 * @param integer $smscode 短信验证码
	 * @param array $customArgs 附加参数
	 * @return array|mixed
	 * @throws \ErrorException
	 * @throws \Sooh\Base\ErrException
	 */
	public function loginFromSmscode($loginName, $smscode, $customArgs = ['contractId'])
	{
		if ($this->rpc !== null) {
			return $this->rpc->initArgs(['loginName' => $loginName, 'smscode' => $smscode, 'customArgs' => $customArgs])->send(__FUNCTION__);
		} else {
			//check smscode
			if (\Sooh\DB\Cases\SMSCode::getCopy($loginName)->chkCode($smscode) === false) {
				$this->loger->mainType = 'appreg';
				$this->loger->target   = ['phone' => $loginName];
				$this->loger->sarg1    = ['smscode' => $smscode];
				return $this->returnError(msgDefine::$define['smsCode_incorrect']);
				throw new \Sooh\Base\ErrException(\Prj\Lang\Broker::getMsg('passport.smscode_not_correct_or_timed_out'), 400);
			}

			$objLogin = \Sooh\DB\Cases\AccountAlias::getCopy([$loginName, 'phone']);
			$objLogin->load();
			if ($objLogin->exists()) {
				$accountId = $objLogin->getField('accountId');
				$this->setAccountStorage($accountId);
				$this->account->load();
				if($this->account->exists()) {
                    $limitStatus = $this->account->getField('limitStatus');
                    if ($limitStatus == 1) {
                        throw new \Sooh\Base\ErrException(\Prj\Lang\Broker::getMsg('account.account_is_forbidden'), 400);
                    }

					$nickname = $this->account->getField('nickname');
					$ret = array('accountId' => $this->account->getField('accountId'), 'nickname' => $nickname,);

					if (!empty($customArgs)) {
						if (is_string($customArgs)) {
							$customArgs = explode(',', $customArgs);
						}
						foreach ($customArgs as $k) {
							$ret[$k] = $this->account->getField($k);
						}
					}
					$this->account->setField('lastIP', \Sooh\Base\Tools::remoteIP());
					$this->account->setField('lastDt', \Sooh\Base\Time::getInstance()->timestamp());

					try {
						$this->account->update();
					} catch (\ErrorException $ex) {
						\Sooh\Base\Log\Data::error("error on update account when login:" . $ex->getMessage() . "\n" . \Sooh\DB\Broker::lastCmd() . "\n" . $ex->getTraceAsString());
					}
					if (is_array($ret)) {
						return $ret;
					}
				} else {
					throw new \Sooh\Base\ErrException(\Prj\Lang\Broker::getMsg('account.account_or_smscode_input_error'), 400);
				}
			} else {
				throw new \Sooh\Base\ErrException(\Prj\Lang\Broker::getMsg('account.account_or_smscode_input_error'), 400);
			}
		}
	}

	/**
	 * 注册账号，失败抛出异常（账号已经存在等等）
	 * @param string $password 密码
	 * @param array $arrLoginName [loginName, cameFrom]
	 * @param array $customArgs [field1, field2, field3]
	 * @return array [accountId, nickname]
	 * @throws \ErrorException
	 * @throws \Exception
	 * @throws \Sooh\Base\ErrException
	 * @throws \Sooh\Base\ErrException
	 */
	public function register($password, $arrLoginName = [], $customArgs = [])
	{
		if ($this->rpc !== null) {
			return $this->rpc->initArgs(array('password'=>$password, 'arrLoginName'=>$arrLoginName, 'customArgs'=>$customArgs))->send(__FUNCTION__);
		} else {
			if($arrLoginName !== null){
				//生成AccountId
				for ($retry = 0; $retry < 10; $retry++) {
					$accountHead = substr(\Sooh\Base\Time::getInstance()->timestamp(), 1, 5);

					$accountId = $accountHead . sprintf('%05d', rand(0, 99999)) . sprintf('%04d', rand(0, 9999));
					$this->setAccountStorage($accountId,'local');
					if ($this->account->exists()) {
						\Sooh\Base\Log\Data::getInstance()->ret = 'account exists';
						error_log("[try accountId:{$accountId}]");
						continue;
					} else {
						break;
					}
					if ($retry >= 10) {
						throw new \Sooh\Base\ErrException(\Prj\Lang\Broker::getMsg('system.server_busy'), 400);
					}
				}

				//检查loginName是否存在
				foreach($arrLoginName as $_key => $_val) {
					if ($this->loginPrepare($accountId, $_val[0], $_val[1]) === false) {
						$this->loginRollback($arrLoginName, $_key);
						throw new \Sooh\Base\ErrException(\Prj\Lang\Broker::getMsg('account.accounts_existing'), 400);
					}
				}
			} else {
				throw new \Sooh\Base\ErrException(\Prj\Lang\Broker::getMsg('account.account_is_not_existing'), 400);
			}

			$this->account->load();
			$passwdSalt = substr(uniqid(), -4);
			if (!empty($customArgs['nickname'])) {
				$nickname = $customArgs['nickname'];
			} else {
				$nickname = $accountId;
			}

			$i = 0;
			for ($i = 0; $i < count($arrLoginName); $i++) {
				if ($arrLoginName[$i][1] === 'phone'){
					$this->account->setField('phone', $arrLoginName[$i][0]);
					break;
				}
			}
			if ($i == count($arrLoginName)) {
				$this->account->setField('phone' , 0);
			}
			$this->account->setField('passwdSalt', $passwdSalt);
			$this->account->setField('loginFailed', 0);
			$this->account->setField('limitStatus', 0);
			$this->account->setField('dtForbidden', 0);
			$cmp = md5($password . $passwdSalt);
			$this->account->setField('passwd', $cmp);
			$this->account->setField('pwdVer', 0);
			$this->account->setField('nickname', $nickname);
			if ($customArgs['invitationCode']) {
				$customData['invitationCode'] = $customArgs['invitationCode'];
				unset($customArgs['invitationCode']);
			}
			if ($customArgs['protocol']) {
				$customData['protocol'] = $customArgs['protocol'];
				unset($customArgs['protocol']);
			}
			if ($customData) {
				$this->account->setField('customData', json_encode($customData));
			} else {
				$this->account->setField('customData', '');
			}

			$customArgs['regClient'] = $customArgs['clientType'] - 0;
			unset($customArgs['clientType']);
			$customArgs['regYmd'] = \Sooh\Base\Time::getInstance()->YmdFull;
			$customArgs['regHHiiss'] = \Sooh\Base\Time::getInstance()->his;
			foreach ($customArgs as $k => $v) {
				$this->account->setField($k, $v);
			}

			$this->account->update();
			$tmp = array('accountId' => $accountId, 'nickname' => $nickname);
			foreach ($customArgs as $k => $v) {
				$tmp[$k] = $v;
			}
			$this->loginCommit($arrLoginName);
			return $tmp;
		}
	}

	/**
	 * 不设置密码的注册【通过短信验证码】
	 * @param array $arrLoginName [loginName, cameFrom]
	 * @param array $customArgs [field1, field2]
	 * @return array [accountId, nickname]
	 * @throws \ErrorException
	 * @throws \Exception
	 * @throws \Sooh\Base\ErrException
	 */
	public function registerFromSmscode($arrLoginName = [], $customArgs = [])
	{
		if ($this->rpc !== null) {
			return $this->rpc->initArgs(array('arrLoginName'=>$arrLoginName, 'customArgs'=>$customArgs))->send(__FUNCTION__);
		} else {
			$password = '';

			if($arrLoginName !== null){
				//生成AccountId，并检查是否存在
				for ($retry = 0; $retry < 10; $retry++) {
					$accountId = rand(10000, 99999) . sprintf('%05d', rand(0, 99999)) . sprintf('%04d', rand(0, 9999));
					$this->setAccountStorage($accountId);
					if ($this->account->exists()) {
						\Sooh\Base\Log\Data::getInstance()->ret = 'account exists';
						error_log("[try accountId:{$accountId}]");
						continue;
					} else {
						break;
					}
					if ($retry >= 10) {
						throw new \Sooh\Base\ErrException(\Prj\Lang\Broker::getMsg('system.server_busy'), 400);
					}
				}

				//检查loginName是否存在
				foreach($arrLoginName as $_key => $_val) {
					if ($this->loginPrepare($accountId, $_val[0], $_val[1]) === false) {
						$this->loginRollback($arrLoginName, $_key);
						throw new \Sooh\Base\ErrException(\Prj\Lang\Broker::getMsg('account.accounts_existing'), 400);
					}
				}
			} else {
				throw new \Sooh\Base\ErrException(\Prj\Lang\Broker::getMsg('account.account_is_not_existing'), 400);
			}

			//          $this->account->load();
			//          $passwdSalt = substr(uniqid(), -4);
			if (!empty($customArgs['nickname'])) {
				$nickname = $customArgs['nickname'];
			} else {
				$nickname = $accountId;
			}

			$i = 0;
			for ($i = 0; $i < count($arrLoginName); $i++) {
				if ($arrLoginName[$i][1] === 'phone'){
					$this->account->setField('phone', $arrLoginName[$i][0]);
					break;
				}
			}
			if ($i == count($arrLoginName)) {
				$this->account->setField('phone' , 0);
			}
            $this->account->setField('passwdSalt', '');
			$this->account->setField('loginFailed', 0);
			$this->account->setField('limitStatus', 0);
			$this->account->setField('dtForbidden', 0);
			//          $cmp = md5($password . $passwdSalt);
			//          $this->account->setField('passwd', $cmp);
			$this->account->setField('passwd', '');
			$this->account->setField('pwdVer', 0);
			$this->account->setField('nickname', $nickname);

			if ($customArgs['invitationCode']) {
				$customData['invitationCode'] = $customArgs['invitationCode'];
				unset($customArgs['invitationCode']);
			}
			if ($customArgs['protocol']) {
				$customData['protocol'] = $customArgs['protocol'];
				unset($customArgs['protocol']);
			}
			if ($customData) {
				$this->account->setField('customData', json_encode($customData));
			} else {
				$this->account->setField('customData', '');
			}

			$customArgs['regClient'] = $customArgs['clientType'] - 0;
			unset($customArgs['clientType']);
			$customArgs['regYmd'] = \Sooh\Base\Time::getInstance()->YmdFull;
			$customArgs['regHHiiss'] = \Sooh\Base\Time::getInstance()->his;
			foreach ($customArgs as $k => $v) {
				$this->account->setField($k, $v);
			}

			$this->account->update();
			$tmp = array('accountId' => $accountId, 'nickname' => $nickname);
			foreach ($customArgs as $k => $v) {
				$tmp[$k] = $v;
			}
			$this->loginCommit($arrLoginName);
			return $tmp;
		}
	}

    /**
     * 调用同盾接口
     * @param string $userId userId
     * @param array  $params params
     * @return bool
     * @throws \ErrorException
     * @throws \Exception
     */
    public function checkSecureForTongDun($params)
    {
        if (!empty($params['tongDunStr'])) {
            $tongDunParams = [
                "partner_code"   => "xiaoxialicai",
                'account_login'  => $params['phone'],
                'account_mobile' => $params['phone'],
                'ip_address'     => \Sooh\Base\Tools::remoteIP(),
            ];
            switch ($params['clientType']) {
                case \Prj\Consts\ClientType::appstore:
                    $tongDunParams['event_id']   = 'register_professional_ios';
                    $tongDunParams['secret_key'] = \Sooh\Base\Ini::getInstance()->get('TongDun_Ios_Secret_Key');
                    $tongDunParams['black_box']  = $params['tongDunStr'];
                    break;
                case \Prj\Consts\ClientType::android:
                    $tongDunParams['event_id']   = 'register_professional_android';
                    $tongDunParams['secret_key'] = \Sooh\Base\Ini::getInstance()->get('TongDun_Android_Secret_Key');
                    $tongDunParams['black_box']  = $params['tongDunStr'];
                    break;
                default :
                    $tongDunParams['event_id']   = 'register_professional_web';
                    $tongDunParams['secret_key'] = \Sooh\Base\Ini::getInstance()->get('TongDun_Web_Secret_Key');
                    $tongDunParams['token_id']   = $params['tongDunStr'];
            }
            var_log($tongDunParams, '================TONG DUN PARAMS==============');
            $tongDunRet = \Prj\Secure\TongDun::invoke_fraud_api($tongDunParams);
            var_log($tongDunRet, '================TONG DUN RET==============');

            \Prj\Data\Tongdun::newAdd($tongDunParams['account_login'], 'phone', $tongDunRet);

//            $this->setAccountStorage($userId, 'local');
//            $this->account->load();
//            if ($this->account->exists() && !empty($tongDunRet)) {
//                $this->account->setField('tongDunRet', $tongDunRet);
//                $this->account->update();
//            }

            $tConf = \Sooh\Base\Ini::getInstance()->get('TONGDUN');
            if ($tConf['INTERCEPT'] && json_decode($tongDunRet, true)) {
                $arrRet = json_decode($tongDunRet, true);
                if (is_array($arrRet) && isset($arrRet['final_decision']) && strtoupper($arrRet['final_decision']) == 'REJECT') {
                    return false;//高风险注册，应当积极拒绝
                }
            }
        }
        return true;
    }

	/**
	 * 获取用户资源
	 * @param string $accountId
	 * @return ['accountId' => '', 'contractId' => '', 'phone' => '', 'invitationCode' => '']
	 * @throws \ErrorException
	 * @throws \Sooh\Base\ErrException
	 */
	public function getResource($accountId) {
		$this->setAccountStorage($accountId, 'local');
		$this->account->load();
		if ($this->account->exists()) {
			$customData = $this->account->getField('customData');
			if (is_string($customData)) {
				$_parseJson = json_decode($customData, true);
				if ($_parseJson !== false) {
					$customData = $_parseJson;
				}
			}

			$ret        = [
				'accountId'      => $accountId,
				'contractId'     => $this->account->getField('contractId'),
				'phone'          => $this->account->getField('phone'),
				'invitationCode' => isset($customData['invitationCode']) ? $customData['invitationCode'] : '',
				'protocol'       => isset($customData['protocol']) ? $customData['protocol'] : '',
				'clientType'     => $this->account->getField('regClient'),
				'pwdVer'         => $this->account->getField('pwdVer'),
				'nickname'       => $this->account->getField('nickname'),
			    'hasPwd'         => $this->account->getField('passwd') ? 1 : 0,
			];

			if (!is_numeric($ret['phone'])) {
				$ret['phone'] = 0;
			}

			$tbUser = \Prj\Data\User::getCopy($accountId);
			$tbUser->load();
			if ($tbUser->exists()) {
				$ret['invitationCode'] = $tbUser->getField('myInviteCode');
			}
			return $ret;
		} else {
			throw new \Sooh\Base\ErrException(\Prj\Lang\Broker::getMsg('account.account_is_not_existing'));
		}
	}

	/**
	 * 更新字段
	 * @param string $userId 用户ID
	 * @param array $fields 字段集
	 * @return bool
	 * @throws \ErrorException
	 * @throws \Exception
	 * @throws \Sooh\Base\ErrException
	 */
	public function updFields($userId, $fields) {
		$this->setAccountStorage($userId, 'local');
		$this->account->load();
		if ($this->account->exists()) {
			foreach($fields as $k => $v) {
				$this->account->setField($k, $v);
			}
			$this->account->update();
			return true;
		} else {
			throw new \Sooh\Base\ErrException(\Prj\Lang\Broker::getMsg('account.account_is_not_existing'));
		}
	}
}
