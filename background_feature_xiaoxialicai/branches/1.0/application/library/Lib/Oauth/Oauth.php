<?php

namespace Lib\Oauth;

/**
 * Class Oauth
 * @package Lib\Services
 * @author LiangYanQing <liang.yanqing@yixiutouzi.com>
 */
class Oauth {
    /**
     * 使用的授权类型
     * @var array code调用authrizeForCode方法
     */
    private $response_type = [
	    'code'     => 'authrizeForCode',//授权码模式
	    'appreg'   => 'authrizeForAppreg',//移动端注册模式，客户端需要admin权限
	    'applogin' => 'authrizeForApplogin',//移动端登录模式，客户端需要admin权限
	    'resetPwd' => 'authrizeForCommon',//通用模式，只验证clientId和clientSecret合法性
	    'common'   => 'authrizeForCommon',//通用模式，只验证clientId和clientSecret合法性
        'quickLogin' => 'authrizeForAppQuicklLogin',//快速登录
        'quickReg' => 'authrizeForQuickreg',
    ];
    /**
     * 使用的授权模式
     * @var array
     */
    private $grant_type = [
        'authorization_code',//授权码模式
    ];

    const codeExpiresIn = 600;//临时码过期时间：10分钟
    const accessTokenExpiresIn = 1800;//密钥过期时间：30分钟
    const refreshTokenExpiresIn = 259200;//刷新密钥过期时间：3天

	/**
	 * @var Oauth
	 */
    protected static $_instance = null;

	/**
	 * @var \Sooh\Base\Rpc\Broker
	 */
    protected $rpc = null;

	/**
	 * @var \Sooh\DB\Base\KVObj
	 */
    protected $oauthToken = null;

	protected $oauthResponse;

    /**
     * 获取实例
     * @param \Sooh\Base\Rpc\Broker $rpc
     * @return Oauth
     */
    public static function getInstance($rpc = null) {
        if (self::$_instance === null) {
            $cc = get_called_class();
            self::$_instance = new $cc;
            self::$_instance->rpc = $rpc;
        }
        return self::$_instance;
    }

    /**
     * 授权-检查客户端请求的合法性
     * @param string $responseType 授权类型
     * @param array $arrParam 参数
     * @return mixed
     * @throws \Sooh\Base\ErrException
     */
    public function authrize($responseType, $arrParam) {
        if ($this->rpc !== null) {
            return $this->rpc->initArgs(['responseType' => $responseType, 'arrParam' => $arrParam]);
        }

	    $this->oauthResponse = new OauthResponse();

        if (array_key_exists($responseType, $this->response_type)) {
            return call_user_func([$this, $this->response_type[$responseType]], $arrParam);
        } else {
            throw new \Sooh\Base\ErrException($this->oauthResponse->invalid_request_response_type['error'], $this->oauthResponse->invalid_request_response_type['code']);
        }
    }

    /**
     * 获取临时码Code
     * @param string $accountId 用户ID
     * @param string $scope 权限，用逗号隔开
     * @param string $clientId clientId
     * @param string $redirectUri redirectUri
     * @param string $sesScope sesScope
     * @return array ['code' => '*****', 'redirectUri' => '*****', 'expiresIn' => '***']
     * @throws \Sooh\Base\ErrException
     */
    public function getCode($accountId, $scope, $clientId = null, $redirectUri = null, $sesScope = null) {
        if ($this->rpc !== null) {
            return $this->rpc->initArgs(['accountId' => $accountId, 'scope' => $scope])->send(__FUNCTION__);
        }

	    $this->oauthResponse = new OauthResponse();

        $clientId = $clientId ? : \Sooh\Base\Session\Data::getInstance()->get('clientId');
        $redirectUri = $redirectUri ? : \Sooh\Base\Session\Data::getInstance()->get('redirectUri');
        $sesScope = $sesScope ? : \Sooh\Base\Session\Data::getInstance()->get('scope');
        if (empty($clientId) || empty($redirectUri) || empty($sesScope)) {
            throw new \Sooh\Base\ErrException($this->oauthResponse->failed_get_code['error'], $this->oauthResponse->failed_get_code['code']);
        }
        //验证scope合法性
        $parScope = array_unique(explode(',', $scope));
        if (count($parScope) > count($sesScope)) {
            throw new \Sooh\Base\ErrException($this->oauthResponse->invalid_request_scope['error'], $this->oauthResponse->invalid_request_scope['code']);
        }
        if (count(array_intersect($sesScope, $parScope)) != count($parScope)) {
            throw new \Sooh\Base\ErrException($this->oauthResponse->invalid_request_scope['error'], $this->oauthResponse->invalid_request_scope['code']);
        }

        $code = $this->buildCode($clientId, $redirectUri);
        $oauthCode = \Sooh\DB\Cases\OauthCode::getCopy($code);
        $oauthCode->load();

        //验证code
        $retry = 0;
        while ($oauthCode->exists()) {
            if($retry >= 10) {
                throw new \Sooh\Base\ErrException($this->oauthResponse->failed_get_code['error'], $this->oauthResponse->failed_get_code['code']);
            }
            $code = $this->buildCode($clientId, $redirectUri);
            $oauthCode = \Sooh\DB\Cases\OauthCode::getCopy($code);
            $oauthCode->load();
            $retry++;
        }

	    $expiresIn = \Sooh\Base\Time::getInstance()->timestamp() + self::codeExpiresIn;
        $oauthCode->setField('clientId', $clientId);
        $oauthCode->setField('accountId', $accountId);
        $oauthCode->setField('expiresIn', $expiresIn);
        $oauthCode->setField('scope', implode(',', $parScope));
        $oauthCode->update();

        return ['code' => $code, 'redirectUri' => $redirectUri, 'expiresIn' => $expiresIn];
    }

    /**
     * 获取token
     * @param string $code 临时码
     * @param string $redirectUri 回调地址
     * @return array ['access' => '*****', 'accessTokenExpiresIn' => '****', 'refreshToken' => '*****' 'refreshTokenExpiresIn' => '***']
     * @throws \Sooh\Base\ErrException
     */
    public function getToken($code, $redirectUri) {
        if ($this->rpc !== null) {
            return $this->rpc->initArgs(['code' => $code, 'redirectUri' => $redirectUri]);
        }

	    $this->oauthResponse = new OauthResponse();

        $oauthCode = \Sooh\DB\Cases\OauthCode::getCopy($code);
        $oauthCode->load();
        if ($oauthCode->exists()) {
            $dt = \Sooh\Base\Time::getInstance();
            if ($oauthCode->getField('expiresIn') < $dt->timestamp()) {
                throw new \Sooh\Base\ErrException($this->oauthResponse->error_code_expired['error'], $this->oauthResponse->error_code_expired['code']);
            }

            $token = $this->buildAccessToken();
            $this->getOauthToken($token);
            $this->oauthToken->load();
            $refresh = $this->buildRefreshToken();
            $oauthRefresh = \Sooh\DB\Cases\OauthRefresh::getCopy($refresh);
            $oauthRefresh->load();
            $retry = 0;
            while ($retry < 10) {
                if ($this->oauthToken->exists()) {
                    $token = $this->buildAccessToken();
                    $this->getOauthToken($token);
                    $this->oauthToken->load();
                    $retry++;
                    continue;
                } elseif ($oauthRefresh->exists()) {
                    $refresh = $this->buildRefreshToken();
                    $oauthRefresh = \Sooh\DB\Cases\OauthRefresh::getCopy($refresh);
                    $oauthRefresh->load();
                    $retry++;
                    continue;
                } else {
                    break;
                }
            }
            if ($retry >= 10) {
                throw new \Sooh\Base\ErrException($this->oauthResponse->failed_get_access_token['error'], $this->oauthResponse->failed_get_access_token['code']);
            }

	        $accessTokenExpiresIn = self::accessTokenExpiresIn;
            $this->oauthToken->setField('accountId', $oauthCode->getField('accountId'));
            $this->oauthToken->setField('expiresIn', $dt->timestamp() + $accessTokenExpiresIn);
            $this->oauthToken->setField('scope', $oauthCode->getField('scope'));
	        $this->oauthToken->setField('clientId', $oauthCode->getField('clientId'));
            $this->oauthToken->update();

	        $refreshTokenExpiresIn = self::refreshTokenExpiresIn;
            $oauthRefresh->setField('accessToken', $token);
            $oauthRefresh->setField('expiresIn', $dt->timestamp() + $refreshTokenExpiresIn);
            $oauthRefresh->update();

	        $oauthCode->delete();
            return ['accessToken' => $token, 'accessTokenExpiresIn' => $accessTokenExpiresIn, 'refreshToken' => $refresh, 'refreshTokenExpiresIn' => $refreshTokenExpiresIn];
        } else {
            throw new \Sooh\Base\ErrException($this->oauthResponse->invalid_request_code['error'], $this->oauthResponse->invalid_request_code['code']);
        }
    }

    /**
     * 访问资源
     * @param string $token token
     * @return mixed
     * @throws \Sooh\Base\ErrException
     */
    public function getResources($token) {
        if ($this->rpc !== null) {
            return $this->rpc->initArgs(['token' => $token])->send(__FUNCTION__);
        }

	    $this->oauthResponse = new OauthResponse();

        $this->getOauthToken($token);
        $this->oauthToken->load();
        if ($this->oauthToken->exists()) {
            $dt = \Sooh\Base\Time::getInstance();
            if ($this->oauthToken->getField('expiresIn') < $dt->timestamp()) {
                throw new \Sooh\Base\ErrException($this->oauthResponse->error_access_token_expired['error'], $this->oauthResponse->error_access_token_expired['code']);
            }

            $scope = $this->oauthToken->getField('scope');
            $accountId = $this->oauthToken->getField('accountId');

            //get user resources
            $account = \Lib\Services\Account::getInstance(\Prj\BaseCtrl::getRpcDefault('Account'));
            return $account->getResource($accountId);
        } else {
            throw new \Sooh\Base\ErrException($this->oauthResponse->invalid_request_access_token['error'], $this->oauthResponse->invalid_request_access_token['code']);
        }
    }

    /**
     * 更新密钥
     * @param string $refreshToken refreshToken
     * @return array ['accessToken' => '*****', 'accessTokenExpiresIn' => '***', 'refreshToken' => '*****', 'refreshTokenExpiresIn' => '***']
     * @throws \Sooh\Base\ErrException
     */
    public function refreshToken($refreshToken) {
        if ($this->rpc !== null) {
            return $this->rpc->initArgs(['refreshToken' => $refreshToken])->send(__FUNCTION__);
        }

	    $this->oauthResponse = new OauthResponse();

        $oauthRefresh = \Sooh\DB\Cases\OauthRefresh::getCopy($refreshToken);
        $oauthRefresh->load();
        if ($oauthRefresh->exists()) {
            $dt = \Sooh\Base\Time::getInstance();
            if ($oauthRefresh->getField('expiresIn') < $dt->timestamp()) {
                throw new \Sooh\Base\ErrException($this->oauthResponse->error_refresh_token_expired['error'], $this->oauthResponse->error_refresh_token_expired['code']);
            }

            $oldToken = $oauthRefresh->getField('accessToken');
            $token = $this->buildAccessToken();
            $refresh = $this->buildRefreshToken();

            $this->getOauthToken($token);
            $this->oauthToken->load();
            $oauthRefreshNew = \Sooh\DB\Cases\OauthRefresh::getCopy($refresh);
            $oauthRefreshNew->load();
            $retry = 0;
            while ($retry < 20) {
                if ($this->oauthToken->exists()) {
                    $token = $this->buildAccessToken();
                    $this->getOauthToken($token);
                    $this->oauthToken->load();
                    $retry++;
                    continue;
                } elseif ($oauthRefreshNew->exists()) {
                    $refresh = $this->buildRefreshToken();
                    $oauthRefreshNew = \Sooh\DB\Cases\OauthRefresh::getCopy($refresh);
                    $oauthRefreshNew->load();
                    $retry++;
                    continue;
                } else {
                    break;
                }
            }
            if ($retry >= 20) {
                throw new \Sooh\Base\ErrException($this->oauthResponse->failed_get_refresh_token['error'], $this->oauthResponse->failed_get_refresh_token['code']);
            }

            //delete old refresh_token and access_token
            $oauthRefresh->delete();
            $this->getOauthToken($oldToken);
            $this->oauthToken->load();
            $accountId = $this->oauthToken->getField('accountId');
            $scope = $this->oauthToken->getField('scope');
            $this->oauthToken->delete();

	        $refreshTokenExpiresIn = self::refreshTokenExpiresIn;
            $oauthRefreshNew->setField('refreshToken', $refresh);
            $oauthRefreshNew->setField('accessToken', $token);
            $oauthRefreshNew->setField('expiresIn', $dt->timestamp() + $refreshTokenExpiresIn);
            $oauthRefreshNew->update();

	        $accessTokenExpiresIn = self::accessTokenExpiresIn;
            $this->getOauthToken($token);
            $this->oauthToken->load();
            $this->oauthToken->setField('accessToken', $token);
            $this->oauthToken->setField('accountId', $accountId);
            $this->oauthToken->setField('expiresIn', $dt->timestamp() + $accessTokenExpiresIn);
            $this->oauthToken->setField('scope', $scope);
            $this->oauthToken->update();
            return ['accessToken' => $token, 'accessTokenExpiresIn' => $accessTokenExpiresIn, 'refreshToken' => $refresh, 'refreshTokenExpiresIn' => $refreshTokenExpiresIn];
        } else {
            throw new \Sooh\Base\ErrException($this->oauthResponse->invalid_request_refresh_token['error'], $this->oauthResponse->invalid_request_refresh_token['code']);
        }
    }

	/**
	 * 更新nickname
	 * @param string $token    accessToken
	 * @param string $nickname nickname
	 * @return bool
	 * @throws \Sooh\Base\ErrException
	 */
	public function updNickname($token, $nickname) {
		if ($this->rpc !== null) {
			return $this->rpc->initArgs(['token' => $token])->send(__FUNCTION__);
		}

		$this->oauthResponse = new OauthResponse();

		$this->getOauthToken($token);
		$this->oauthToken->load();
		if ($this->oauthToken->exists()) {
			$dt = \Sooh\Base\Time::getInstance();
			if ($this->oauthToken->getField('expiresIn') < $dt->timestamp()) {
				throw new \Sooh\Base\ErrException($this->oauthResponse->error_access_token_expired['error'], $this->oauthResponse->error_access_token_expired['code']);
			}

			$scope     = $this->oauthToken->getField('scope');
			$accountId = $this->oauthToken->getField('accountId');

			$account = \Lib\Services\Account::getInstance(\Prj\BaseCtrl::getRpcDefault('Account'));
			return $account->updFields($accountId, ['nickname' => $nickname]);
		} else {
			throw new \Sooh\Base\ErrException($this->oauthResponse->invalid_request_access_token['error'], $this->oauthResponse->invalid_request_access_token['code']);
		}
	}

    /**
     * 生成临时码code
     * @param $clientId
     * @param $redirectUri
     * @return string code
     */
    private function buildCode($clientId, $redirectUri) {
        return md5(uniqid() . self::getRand(14));
    }

    /**
     * 生成密钥Token
     * @return string accessToken
     */
    private function buildAccessToken() {
        return md5(uniqid() . self::getRand(14));
    }

    /**
     * 生成refreshToken
     * @return string refreshToken
     */
    private function buildRefreshToken() {
        return md5(microtime() . self::getRand(14));
    }

    /**
     * 生成一个指定长度的随机字符串
     * @param int $length 目标字符串长度
     * @param string $strLib 随机字符串库，默认为：2345678abcdefhijkmnpqrstuvwxyzABCDEFGHJKLMNPQRTUVWXY
     * @return string 目标随机字符串
     */
    private function getRand($length = 20, $strLib = '') {
        if (empty($strLib)) {
            $strLib = '2345678abcdefhijkmnpqrstuvwxyzABCDEFGHJKLMNPQRTUVWXY';
        }

        $ret = '';
        for($i = 0; $i < $length; $i++) {
            $ret .= $strLib[rand(0, strlen($strLib) - 1)];
        }

        return $ret;
    }

    /**
     * 授权码模式
     * @param $arrParam
     * @return string
     * @throws \Sooh\Base\ErrException
     */
    private function authrizeForCode($arrParam) {
        $oauthClient = $this->verifyClient($arrParam['clientId']);

	    $this->oauthResponse = new OauthResponse();

	    if ($oauthClient->getField('redirectUri') != $arrParam['redirectUri']) {
		    throw new \Sooh\Base\ErrException($this->oauthResponse->invalid_request_redirect_uri['error'], $this->oauthResponse->invalid_request_redirect_uri['code']);
	    }

	    $parScope = '';
        if (isset($arrParam['scope'])) {
            $scope = $oauthClient->getField('scope');
            $dbScope = explode(',', $scope);
            $parScope = array_unique(explode(',', $arrParam['scope']));
            if (count($parScope) > count($dbScope)) {
                throw new \Sooh\Base\ErrException($this->oauthResponse->invalid_request_scope['error'], $this->oauthResponse->invalid_request_scope['code']);
            }

            if (count(array_intersect($dbScope, $parScope)) != count($parScope)) {
                throw new \Sooh\Base\ErrException($this->oauthResponse->invalid_request_scope['error'], $this->oauthResponse->invalid_request_scope['code']);
            }
        }

        if ($oauthClient->getField('secret') === md5($arrParam['clientSecret'])) {
            \Sooh\Base\Session\Data::getInstance()->set('clientId', $arrParam['clientId']);
            \Sooh\Base\Session\Data::getInstance()->set('redirectUri', $arrParam['redirectUri']);
            \Sooh\Base\Session\Data::getInstance()->set('scope', $parScope);
            return $arrParam;
        } else {
            throw new \Sooh\Base\ErrException($this->oauthResponse->invalid_request_client_secret['error'], $this->oauthResponse->invalid_request_client_secret['code']);
        }
    }

    /**
     * 可信任客户端注册模式
     * @param $arrParam
     * @return bool
     * @throws \Sooh\Base\ErrException
     */
    private function authrizeForAppreg($arrParam) {

	    $this->oauthResponse = new OauthResponse();

        $oauthClient = $this->verifyClient($arrParam['clientId']);
	    if ($oauthClient->getField('redirectUri') != $arrParam['redirectUri']) {
		    throw new \Sooh\Base\ErrException($this->oauthResponse->invalid_request_redirect_uri['error'], $this->oauthResponse->invalid_request_redirect_uri['code']);
	    }

        $scope = $oauthClient->getField('scope');
        $dbScope = explode(',', $scope);
        $parScope = array_unique(explode(',', $arrParam['scope']));
        if (count($parScope) > count($dbScope)) {
            throw new \Sooh\Base\ErrException($this->oauthResponse->invalid_request_scope['error'], $this->oauthResponse->invalid_request_scope['code']);
        }

        if (count(array_intersect($dbScope, $parScope)) != count($parScope)) {
            throw new \Sooh\Base\ErrException($this->oauthResponse->invalid_request_scope['error'], $this->oauthResponse->invalid_request_scope['code']);
        }

        //拥有admin权限的客户端才能使用此方法
        if ($oauthClient->getField('secret') === md5($arrParam['clientSecret']) && in_array('admin', explode(',', $oauthClient->getField('scope')))) {
            \Sooh\Base\Session\Data::getInstance()->set('clientId', $arrParam['clientId']);
            \Sooh\Base\Session\Data::getInstance()->set('redirectUri', $arrParam['redirectUri']);
            \Sooh\Base\Session\Data::getInstance()->set('scope', $parScope);
            return true;
        } else {
            throw new \Sooh\Base\ErrException($this->oauthResponse->error_client_illegal['error'], $this->oauthResponse->error_client_illegal['code']);
        }
    }

	/**
	 * 快捷登录
	 * @param $arrParam
	 * @return bool
	 * @throws \ErrorException
	 * @throws \Sooh\Base\ErrException
	 */
	private function authrizeForQuickreg($arrParam) {

		$this->oauthResponse = new OauthResponse();

		$oauthClient = $this->verifyClient($arrParam['clientId']);
		if ($oauthClient->getField('redirectUri') != $arrParam['redirectUri']) {
			throw new \Sooh\Base\ErrException($this->oauthResponse->invalid_request_redirect_uri['error'], $this->oauthResponse->invalid_request_redirect_uri['code']);
		}

		$scope = $oauthClient->getField('scope');
		$dbScope = explode(',', $scope);
		$parScope = array_unique(explode(',', $arrParam['scope']));
		if (count($parScope) > count($dbScope)) {
			throw new \Sooh\Base\ErrException($this->oauthResponse->invalid_request_scope['error'], $this->oauthResponse->invalid_request_scope['code']);
		}

		if (count(array_intersect($dbScope, $parScope)) != count($parScope)) {
			throw new \Sooh\Base\ErrException($this->oauthResponse->invalid_request_scope['error'], $this->oauthResponse->invalid_request_scope['code']);
		}

		if ($oauthClient->getField('secret') === md5($arrParam['clientSecret']) && in_array('admin', explode(',', $oauthClient->getField('scope')))) {
			\Sooh\Base\Session\Data::getInstance()->set('clientId', $arrParam['clientId']);
			\Sooh\Base\Session\Data::getInstance()->set('redirectUri', $arrParam['redirectUri']);
			\Sooh\Base\Session\Data::getInstance()->set('scope', $parScope);


			$account = \Lib\Services\Account::getInstance(\Prj\BaseCtrl::getRpcDefault('Account'));
			$regTmp = $account->registerFromSmscode([[$arrParam['phone'], 'phone']], $arrParam['customArgs']);
			$accountInfo = $account->loginFromSmscode($arrParam['phone'], $arrParam['smscode']);
			$code = $this->getCode($accountInfo['accountId'], $arrParam['scope'], $arrParam['clientId'], $arrParam['redirectUri'], array_unique(explode(',', $arrParam['scope'])));
			$code['accountId'] = $accountInfo['accountId'];
			$code['resource'] = $account->getResource($accountInfo['accountId']);
			$code['_isQuick_'] = 1;
			return $code;
		} else {
			throw new \Sooh\Base\ErrException($this->oauthResponse->error_client_illegal['error'], $this->oauthResponse->error_client_illegal['code']);
		}
	}

    /**
     * 可信任客户端登录模式
     * @param array $arrParam
     * @return array ['code' => '***', 'redirectUri' => '***', 'expiresIn' => '***', 'accountId' => '***']
     * @throws \Sooh\Base\ErrException
     */
    private function authrizeForApplogin($arrParam) {

	    $this->oauthResponse = new OauthResponse();

        $oauthClient = $this->verifyClient($arrParam['clientId']);
	    if ($oauthClient->getField('redirectUri') != $arrParam['redirectUri']) {
		    throw new \Sooh\Base\ErrException($this->oauthResponse->invalid_request_redirect_uri['error'], $this->oauthResponse->invalid_request_redirect_uri['code']);
	    }

        $scope = $oauthClient->getField('scope');
        $dbScope = explode(',', $scope);
        $parScope = array_unique(explode(',', $arrParam['scope']));
        if (count($parScope) > count($dbScope)) {
            throw new \Sooh\Base\ErrException($this->oauthResponse->invalid_request_scope['error'], $this->oauthResponse->invalid_request_scope['code']);
        }

        if (count(array_intersect($dbScope, $parScope)) != count($parScope)) {
            throw new \Sooh\Base\ErrException($this->oauthResponse->invalid_request_scope['error'], $this->oauthResponse->invalid_request_scope['code']);
        }

        //拥有admin权限的客户端才能使用此方法
        if ($oauthClient->getField('secret') === md5($arrParam['clientSecret']) && in_array('admin', explode(',', $oauthClient->getField('scope')))) {
            $account = \Lib\Services\Account::getInstance(\Prj\BaseCtrl::getRpcDefault('Account'));
            $accountInfo = $account->login($arrParam['phone'], 'phone', $arrParam['password']);
            $code = $this->getCode($accountInfo['accountId'], $arrParam['scope'], $arrParam['clientId'], $arrParam['redirectUri'], array_unique(explode(',', $arrParam['scope'])));
            $code['accountId'] = $accountInfo['accountId'];
            return $code;
        } else {
            throw new \Sooh\Base\ErrException($this->oauthResponse->error_client_illegal['error'], $this->oauthResponse->error_client_illegal['code']);
        }
    }

	/**
	 * 快速登录模式
	 * @param array $arrParam
	 * @return array ['code' => '***', 'redirectUri' => '***', 'expiresIn' => '***', 'accountId' => '***']
	 * @throws \Sooh\Base\ErrException
	 */
	private function authrizeForAppQuicklLogin($arrParam) {

		$this->oauthResponse = new OauthResponse();

		$oauthClient = $this->verifyClient($arrParam['clientId']);
		if ($oauthClient->getField('redirectUri') != $arrParam['redirectUri']) {
			throw new \Sooh\Base\ErrException($this->oauthResponse->invalid_request_redirect_uri['error'], $this->oauthResponse->invalid_request_redirect_uri['code']);
		}

		$scope = $oauthClient->getField('scope');
		$dbScope = explode(',', $scope);
		$parScope = array_unique(explode(',', $arrParam['scope']));
		if (count($parScope) > count($dbScope)) {
			throw new \Sooh\Base\ErrException($this->oauthResponse->invalid_request_scope['error'], $this->oauthResponse->invalid_request_scope['code']);
		}

		if (count(array_intersect($dbScope, $parScope)) != count($parScope)) {
			throw new \Sooh\Base\ErrException($this->oauthResponse->invalid_request_scope['error'], $this->oauthResponse->invalid_request_scope['code']);
		}

		//拥有admin权限的客户端才能使用此方法
		if ($oauthClient->getField('secret') === md5($arrParam['clientSecret']) && in_array('admin', explode(',', $oauthClient->getField('scope')))) {
			$account = \Lib\Services\Account::getInstance(\Prj\BaseCtrl::getRpcDefault('Account'));
			$accountInfo = $account->loginFromSmscode($arrParam['phone'], $arrParam['smscode']);
			$code = $this->getCode($accountInfo['accountId'], $arrParam['scope'], $arrParam['clientId'], $arrParam['redirectUri'], array_unique(explode(',', $arrParam['scope'])));
			$code['accountId'] = $accountInfo['accountId'];
			$code['resource'] = $account->getResource($accountInfo['accountId']);
			$code['_isQuick_'] = 1;
			return $code;
		} else {
			throw new \Sooh\Base\ErrException($this->oauthResponse->error_client_illegal['error'], $this->oauthResponse->error_client_illegal['code']);
		}
	}

	/**
	 * 通用模式，只验证clientId和clientSecret合法性
	 * @param array $arrParam ['clientId' => '', 'clientSecret' => '', 'scope' => '']
	 * @return array arrParam arrParam原样返回
	 * @throws \ErrorException
	 * @throws \Sooh\Base\ErrException
	 */
	private function authrizeForCommon($arrParam) {

		$this->oauthResponse = new OauthResponse();

		$oauthClient = $this->verifyClient($arrParam['clientId']);
		if ($oauthClient->getField('redirectUri') != $arrParam['redirectUri']) {
			throw new \Sooh\Base\ErrException($this->oauthResponse->invalid_request_redirect_uri['error'], $this->oauthResponse->invalid_request_redirect_uri['code']);
		}

		$parScope = '';
		if (isset($arrParam['scope'])) {
			$scope    = $oauthClient->getField('scope');
			$dbScope  = explode(',', $scope);
			$parScope = array_unique(explode(',', $arrParam['scope']));
			if (count($parScope) > count($dbScope)) {
				throw new \Sooh\Base\ErrException($this->oauthResponse->invalid_request_scope['error'], $this->oauthResponse->invalid_request_scope['code']);
			}

			if (count(array_intersect($dbScope, $parScope)) != count($parScope)) {
				throw new \Sooh\Base\ErrException($this->oauthResponse->invalid_request_scope['error'], $this->oauthResponse->invalid_request_scope['code']);
			}
		}

		if ($oauthClient->getField('secret') === md5($arrParam['clientSecret'])) {
			\Sooh\Base\Session\Data::getInstance()->set('clientId', $arrParam['clientId']);
			\Sooh\Base\Session\Data::getInstance()->set('redirectUri', $arrParam['redirectUri']);
			\Sooh\Base\Session\Data::getInstance()->set('scope', $parScope);
			return $arrParam;
		} else {
			throw new \Sooh\Base\ErrException($this->oauthResponse->invalid_request_client_secret['error'], $this->oauthResponse->invalid_request_client_secret['code']);
		}
	}

    /**
     * 验证客户端合法性
     * @param string $clientId 客户端ID
     * @return \Sooh\DB\Base\KVObj tb_oauth_client
     * @throws \Sooh\Base\ErrException ErrClientNotExist
     */
    private function verifyClient($clientId) {

	    $this->oauthResponse = new OauthResponse();

        $oauthClient = \Sooh\DB\Cases\OauthClient::getCopy($clientId);
        $oauthClient->load();
        if ($oauthClient->exists()) {
            return $oauthClient;
        } else {
            throw new \Sooh\Base\ErrException($this->oauthResponse->invalid_request_client_id['error'], $this->oauthResponse->invalid_request_client_id['code']);
        }
    }

    /**
     * 获取tb_oauth_token
     * @param string $token 密钥
     * @return \Sooh\DB\Base\KVObj
     */
    private function getOauthToken($token = '') {
        $this->oauthToken = \Sooh\DB\Cases\OauthToken::getCopy($token);
    }
}