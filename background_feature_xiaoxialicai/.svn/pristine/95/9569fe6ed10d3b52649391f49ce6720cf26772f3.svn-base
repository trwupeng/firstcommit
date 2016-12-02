<?php

use \Lib\Misc\InputValidation as inputValidation;
use \Prj\Consts\MsgDefine as msgDefine;

/**
 * Class Oauth
 * @author LiangYanQing <605415184@qq.com>
 */
class OauthController extends \Prj\BaseCtrl {

    protected $oauthResponse;

    /**
     *
     * @uses BaseCtrl::init()
     */
    public function init() {
        parent::init();
//		$this->oauthResponse = new \Lib\Oauth\OauthResponse();
    }

    /**
     * 客户端授权
     * @input string responseType 授权类型
     * @input string clientId 客户端标识
     * @input string clientSecret 客户端密钥
     * @input string redirectUri 回调地址
     * @input string scope 拥有的权限，用逗号隔开
     * @input string state 状态位，原样返回
     * //返回客户端旧标识信息
     * @output {"code":200, "info":"...", "msg":"成功"}
     * //返回客户端旧标识信息
     * @output {"code":200, "infoauthrize":"...", "msg":"成功"}
     * @errors {"code":400,"msg":"error"}
     */
    public function authrizeAction() {
        $responseType = $this->_request->get('responseType');
        $arrParam = [
            'clientId' => $this->_request->get('clientId'),
            'clientSecret' => $this->_request->get('clientSecret'),
            'redirectUri' => $this->_request->get('redirectUri'),
            'scope' => $this->_request->get('scope'),
            'state' => $this->_request->get('state'),
        ];
        $this->oauthResponse = new \Lib\Oauth\OauthResponse();

        $rules = [
            'clientId' => [
                inputValidation::$define['clientId'],
                $this->oauthResponse->invalid_request_client_id['error'],
                $this->oauthResponse->invalid_request_client_id['code'],
            ],
            'clientSecret' => [
                inputValidation::$define['clientSecret'],
                $this->oauthResponse->invalid_request_client_secret['error'],
                $this->oauthResponse->invalid_request_client_secret['code'],
            ],
        ];

        if (empty($responseType)) {
            return $this->returnError($this->oauthResponse->invalid_request_response_type['error'], $this->oauthResponse->invalid_request_response_type['code']);
        }
        if (inputValidation::validateParams($arrParam, $rules) === false) {
            return $this->returnError(inputValidation::$errorMsg, inputValidation::$errorCode);
        }

        try {
            $oauth = $this->getOauth();
            $ret = $oauth->authrize($responseType, $arrParam);
            $this->_view->assign('info', $ret); //出现多个assign中的info字段重复，修改如下突出唯一识别
            $this->_view->assign('infoauthrize', $ret);
            $this->returnOK(\Prj\Lang\Broker::getMsg('oauth.success'));
        } catch (Exception $e) {
            $this->loger->target = $arrParam['clientId'];
            $this->loger->sarg1 = ['clientSecret' => $arrParam['clientSecret']];
            $this->returnError($e->getmessage(), $e->getCode());
        }
    }

    /**
     * 用户登录页面
     * @input string loginName 登录名
     * @input string password 密码
     * @input string cameFrom 来源类型，例如：phone..
     * @input string scope 权限，用逗号隔开
     * @output {"code":200,"msg":"success"}
     * //返回客户端旧的标识信息
     * @errors {"code":400,"msg":"error","errorCount":"***"}
     * //返回客户端新的标识信息
     * @errors {"code":400,"msg":"error","errorCountlogin":"***"}
     */
    public function loginAction() {
        $loginName = $this->_request->get('loginName');
        $password = $this->_request->get('password');
        $cameFrom = $this->_request->get('cameFrom');
        $scope = $this->_request->get('scope');

        $this->oauthResponse = new \Lib\Oauth\OauthResponse();

        if (is_array($scope)) {
            $scope = implode(',', $scope);
        }
        if (empty($loginName) || empty($password) || empty($cameFrom) || $cameFrom != 'phone') {
            return $this->returnError($this->oauthResponse->invalid_request_loginname_password['error'], $this->oauthResponse->invalid_request_loginname_password['code']);
        } elseif (empty($scope)) {
            return $this->returnError($this->oauthResponse->invalid_request_scope['error'], $this->oauthResponse->invalid_request_scope['code']);
        }

        $params = [
            'phone' => $loginName,
        ];

        //单独验证密码
        $pwd_len = mb_strlen($params['password']);
        if ($pwd_len < 6 || $pwd_len > 20) {
            return $this->returnError(\Prj\Lang\Broker::getMsg('oauth.account_or_pwd_error'));
        }
        $rules = [
            'phone' => [
                inputValidation::$define['phone'],
                $this->oauthResponse->invalid_request_loginname_password['error'],
                $this->oauthResponse->invalid_request_loginname_password['code'],
            ],
        ];
        if (inputValidation::validateParams($params, $rules) === false) {
            return $this->returnError(inputValidation::$errorMsg, inputValidation::$errorCode);
        }

        try {
            $account = \Lib\Services\Account::getInstance(\Prj\BaseCtrl::getRpcDefault('Account'));
            $accountInfo = $account->login($loginName, $cameFrom, $password, []);
        } catch (Exception $e) {
            if ($e->getCode() == 401) {
                $exceptionMsg = json_decode($e->getMessage(), true);
                $this->_view->assign('errorCount', $exceptionMsg['errorCount']); //出现多个assign中的errorCount字段重复，修改如下突出唯一识别
                $this->_view->assign('errorCountlogin', $exceptionMsg['errorCount']);
                return $this->returnError($exceptionMsg['msg'], 401);
            } else {
                return $this->returnError($e->getMessage(), $e->getCode());
            }
        }

        try {
            $oauth = $this->getOauth();
            $ret = $oauth->getCode($accountInfo['accountId'], $scope);
            if (strstr($ret['redirectUri'], '?') === false) {
                header('Location: ' . $ret['redirectUri'] . '?code=' . $ret['code']);
            } else {
                header('Location: ' . $ret['redirectUri'] . '&code=' . $ret['code']);
            }
            return $this->returnOK(\Prj\Lang\Broker::getMsg('oauth.success'));
        } catch (Exception $e) {
            $this->loger->sarg1 = ['loginName' => $loginName];
            return $this->returnError($e->getMessage(), $e->getCode());
        }
    }

    /**
     * 可信任客户端登录
     * @input integer phone 手机号
     * @input string password 密码
     * @input string clientId 客户端ID
     * @input string clientSecret 客户端KEY
     * @input string redirectUri 回调地址
     * //返回客户端旧标识信息
     * @output {"code":200,"info":{"code":"*****","redirectUri":"*****","accountId":"accountId"},"msg":"成功"}
     * //返回客户端新标识信息
     * @output {"code":200,"infoapplogin":{"code":"*****","redirectUri":"*****","accountId":"accountId"},"msg":"成功"}
     * @errors {"code":400,"msg":"error"}
     */
    public function apploginAction() {
//		$_start_time = microtime(true);
        $params = [
            'phone' => $this->_request->get('phone'),
            'password' => $this->_request->get('password'),
            'clientId' => $this->_request->get('clientId'),
            'clientSecret' => $this->_request->get('clientSecret'),
            'redirectUri' => $this->_request->get('redirectUri'),
            'scope' => 'admin',
        ];
        //单独验证密码
        $pwd_len = mb_strlen($params['password']);
        if ($pwd_len < 6 || $pwd_len > 20) {
            return $this->returnError(\Prj\Lang\Broker::getMsg('passport.password_is_not_valid'));
        }
        $rules = [
            'phone' => [inputValidation::$define['phone'], \Prj\Lang\Broker::getMsg('passport.phone_number_is_not_valid')],
            'clientId' => [inputValidation::$define['clientId'], \Prj\Lang\Broker::getMsg('passport.clientId_is_not_valid')],
            'clientSecret' => [inputValidation::$define['clientSecret'], \Prj\Lang\Broker::getMsg('passport.clientSecret_is_not_valid')],
        ];
        if (inputValidation::validateParams($params, $rules) === false) {
            return $this->returnError(inputValidation::$errorMsg);
        }

        $verifyCode = $this->_request->get('verifyCode');
        if (!empty($verifyCode)) {
            if ($this->isValidCodeOK($verifyCode) === false) {
                return $this->returnError(msgDefine::$define['invalidcode_incorrect']);
            }
        }

        try {
            $oauth = $this->getOauth();
            $ret = $oauth->authrize('applogin', $params);
            $this->_view->assign('info', $ret); //出现多个assign中的data字段重复，修改如下突出唯一识别
            $this->_view->assign('infoapplogin', $ret);
//			$_end_time = microtime(true);
//			var_log($_end_time - $_start_time, '============Oauth run time from applogin:');
            $this->returnOK(\Prj\Lang\Broker::getMsg('passport.success'));
        } catch (Exception $e) {
            $this->loger->target = $params['phone'];
            $this->loger->sarg1 = ['clientId' => $params['clientId']];
            $this->loger->sarg2 = ['redirectUri' => $params['redirectUri']];
            $this->loger->sarg3 = ['scope' => $params['scope']];

            if ($e->getCode() == 401) {
                $exceptionMsg = json_decode($e->getMessage(), true);
                $this->_view->assign('errorCount', $exceptionMsg['errorCount']); ////出现多个assign中的errorCount字段重复，修改如下突出唯一识别
                $this->_view->assign('errorCountapplogin', $exceptionMsg['errorCount']);
                return $this->returnError($exceptionMsg['msg'], 402);
            } else {
                return $this->returnError($e->getMessage(), $e->getCode());
            }
        }
    }

    /**
     * 快速登录，不用填写密码
     * @input integer phone 手机号
     * @input string smscode 短信验证码
     * @input string clientId 客户端ID
     * @input string clientSecret 客户端KEY
     * @input string redirectUri 回调地址
     * //返回客户端旧标识信息
     * @output {"code":200,"info":{"code":"*****","redirectUri":"*****","accountId":"accountId"},"msg":"成功"}
     * //返回客户端新标识信息
     * @output {"code":200,"infoquicklogin":{"code":"*****","redirectUri":"*****","accountId":"accountId"},"msg":"成功"}
     * //返回客户端旧的标志信息
     * @errors {"code":400,"msg":"error","errorCount":"***"}
     * //返回客户端新的标志信息
     * @errors {"code":400,"msg":"error","errorCountquicklogin":"***"}
     */
    public function quickLoginAction() {
//		$_start_time = microtime(true);
        $params = [
            'phone' => $this->_request->get('phone'),
            'smscode' => $this->_request->get('smscode'),
            'clientId' => $this->_request->get('clientId'),
            'clientSecret' => $this->_request->get('clientSecret'),
            'redirectUri' => $this->_request->get('redirectUri'),
            'scope' => 'admin',
        ];

        $rules = [
            'phone' => [inputValidation::$define['phone'], \Prj\Lang\Broker::getMsg('passport.phone_number_is_not_valid')],
            'smscode' => [inputValidation::$define['smscode'], \Prj\Lang\Broker::getMsg('passport.smscode_is_not_valid')],
            'clientId' => [inputValidation::$define['clientId'], \Prj\Lang\Broker::getMsg('passport.clientId_is_not_valid')],
            'clientSecret' => [inputValidation::$define['clientSecret'], \Prj\Lang\Broker::getMsg('passport.clientSecret_is_not_valid')],
        ];
        if (inputValidation::validateParams($params, $rules) === false) {
            return $this->returnError(inputValidation::$errorMsg);
        }

        if (\Sooh\DB\Cases\SMSCode::getCopy($params['phone'])->chkCode($params['smscode']) === false) {
            $this->loger->mainType = 'appreg';
            $this->loger->target = ['phone' => $params['phone']];
            $this->loger->sarg1 = ['smscode' => $params['smscode']];
            return $this->returnError(\Prj\Lang\Broker::getMsg('passport.smscode_not_correct_or_timed_out'));
        }

        try {
            $oauth = $this->getOauth();
            $ret = $oauth->authrize('quickLogin', $params);
            $this->_view->assign('info', $ret); //出现多个assign中的info字段重复，修改如下突出唯一识别
            $this->_view->assign('infoquicklogin', $ret);
//			$_end_time = microtime(true);
//			var_log($_end_time - $_start_time, '============Oauth run time from quicklogin:');
            $this->returnOK(\Prj\Lang\Broker::getMsg('passport.success'));
        } catch (Exception $e) {
            $this->loger->target = $params['phone'];
            $this->loger->sarg1 = ['clientId' => $params['clientId']];
            $this->loger->sarg2 = ['redirectUri' => $params['redirectUri']];
            $this->loger->sarg3 = ['scope' => $params['scope']];

            if ($e->getCode() == 401) {
                $exceptionMsg = json_decode($e->getMessage(), true);
                $this->_view->assign('errorCount', $exceptionMsg['errorCount']); //出现多个assign中的errorCount字段重复，修改如下突出唯一识别
                $this->_view->assign('errorCountquicklogin', $exceptionMsg['errorCount']);
                return $this->returnError($exceptionMsg['msg'], 401);
            } else {
                return $this->returnError($e->getMessage(), $e->getCode());
            }
        }
    }

    /**
     * 检查手机号或者邀请码是否正确
     * @input string cameFrom 目前固定为phone
     * @input string phone phone
     * @input string inviteCode 邀请码
     * @input string clientId clientId
     * @input string clientSecret clientSecret
     * @input string redirectUri redirectUri
     * @output {"code":200,"msg":"success"}
     * @errors {"code":400,"msg":"手机号或者邀请码错误"}
     */
    public function validForRegAction() {
        $cameFrom = $this->_request->get('cameFrom');

        $this->oauthResponse = new \Lib\Oauth\OauthResponse();

        if ($cameFrom !== 'phone') {
            return $this->returnError(\Prj\Lang\Broker::getMsg('account.params_error'));
        }
        $params = [
            'phone' => $this->_request->get('phone'),
            'inviteCode' => strtoupper($this->_request->get('inviteCode')),
            'clientId' => $this->_request->get('clientId'),
            'clientSecret' => $this->_request->get('clientSecret'),
            'redirectUri' => $this->_request->get('redirectUri'),
        ];
        $rules = [
            'phone' => [inputValidation::$define['phone'], \Prj\Lang\Broker::getMsg('passport.phone_number_is_not_valid')],
            'inviteCode' => [inputValidation::$define['invitationCode'], \Prj\Lang\Broker::getMsg('oauth.inviteCode_is_not_valid')],
            'clientId' => [
                inputValidation::$define['clientId'],
                $this->oauthResponse->invalid_request_client_id['error'],
                $this->oauthResponse->invalid_request_client_id['code'],
            ],
            'clientSecret' => [
                inputValidation::$define['clientSecret'],
                $this->oauthResponse->invalid_request_client_secret['error'],
                $this->oauthResponse->invalid_request_client_secret['code'],
            ],
        ];
        if (inputValidation::validateParams($params, $rules) === false) {
            return $this->returnError(inputValidation::$errorMsg, inputValidation::$errorCode);
        }

        try {
            $oauth = $this->getOauth();
            $oauth->authrize('common', [
                'clientId' => $params['clientId'],
                'clientSecret' => $params['clientSecret'],
                'redirectUri' => $params['redirectUri'],
            ]);

            //check phone
            $dbLogin = \Sooh\DB\Cases\AccountAlias::getCopy([$params['phone'], $params['cameFrom']]);
            $dbLogin->load();
            if ($dbLogin->exists()) {
                return $this->returnError(\Prj\Lang\Broker::getMsg('account.phone_number_is_incorrect'));
            }

            //check inviteCode
            if (\Prj\Data\InviteCode::getUser($params['inviteCode']) == null) {
                return $this->returnError(\Prj\Lang\Broker::getMsg('account.inviteCode_is_incorrect'));
            }

            return $this->returnOK(\Prj\Lang\Broker::getMsg('passport.success'));
        } catch (Exception $e) {
            return $this->returnError($e->getMessage(), $e->getCode());
        }
    }

    /**
     * 可信任客户端注册
     * @input integer phone 手机号
     * @input string invalidCode 短信验证码
     * @input string password 密码
     * @input string invitationCode 邀请码
     * @input string contractId 渠道ID
     * @input string clientType 客户端类型
     * @input string clientId 客户端ID
     * @input string clientSecret 客户端KEY
     * @input string redirectUri 回调地址
     * @input string protocol 协议版本号
     * @input string tongDunStr 同盾黑盒参数
     * //返回客户端旧标识信息
     * @output {"code":200,"info":{"code":"*****","redirectUri":"*****"},"msg":"成功"}
     * //返回客户端新标识信息
     * @output {"code":200,"infoappreg":{"code":"*****","redirectUri":"*****"},"msg":"成功"}
     * @errors {"code":400,"msg":"错误"}
     */
    public function appregAction() {
//		$_start_time = microtime(true);
        $params = [
            'phone' => $this->_request->get('phone'),
            'invalidCode' => $this->_request->get('invalidCode'),
            'password' => $this->_request->get('password'),
            'invitationCode' => strtoupper($this->_request->get('invitationCode')),
            'contractId' => $this->_request->get('contractId'),
            'clientType' => $this->_request->get('clientType', \Prj\Consts\ClientType::www),
            'clientId' => $this->_request->get('clientId'),
            'clientSecret' => $this->_request->get('clientSecret'),
            'redirectUri' => $this->_request->get('redirectUri'),
            'protocol' => $this->_request->get('protocol'),
            'contractData' => $this->_request->get('contractData', ''),
            'scope' => 'admin',
            'tongDunStr' => $this->_request->get('tongDunStr'),
        ];

        if (empty($params['contractId'])) {
            $params['contractId'] = 0;
        }

        //单独验证密码
        $pwd_len = mb_strlen($params['password']);
        if ($pwd_len < 6 || $pwd_len > 20) {
            return $this->returnError(\Prj\Lang\Broker::getMsg('passport.password_is_not_valid'));
        }

        $rules = [
            'phone' => [inputValidation::$define['phone'], \Prj\Lang\Broker::getMsg('passport.phone_number_is_not_valid')],
            'clientId' => [inputValidation::$define['clientId'], \Prj\Lang\Broker::getMsg('passport.clientId_is_not_valid')],
            'clientSecret' => [inputValidation::$define['clientSecret'], \Prj\Lang\Broker::getMsg('passport.clientSecret_is_not_valid')],
            'clientType' => [inputValidation::$define['clientType'], \Prj\Lang\Broker::getMsg('passport.clientType_is_not_valid')],
            'contractId' => [inputValidation::$define['contractId'], \Prj\Lang\Broker::getMsg('passport.contractId_is_not_valid')],
        ];
        if (inputValidation::validateParams($params, $rules) === false) {
            return $this->returnError(inputValidation::$errorMsg);
        }

        $customArgs = array(
            'contractId' => $params['contractId'],
            'contractData' => $params['contractData'],
            'regIP' => \Sooh\Base\Tools::remoteIP(),
            'clientType' => $params['clientType'],
            'nickname' => substr($params['phone'], 0, 3) . '****' . substr($params['phone'], -4),
            'invitationCode' => $params['invitationCode'],
            'protocol' => $params['protocol'],
        );
        if (empty($params['invitationCode'])) {
            unset($params['invitationCode']);
        }
        if (empty($customArgs['invitationCode'])) {
            unset($customArgs['invitationCode']);
        }
        if (empty($customArgs['protocol'])) {
            unset($customArgs['protocol']);
        }

        if (\Sooh\DB\Cases\SMSCode::getCopy($params['phone'])->chkCode($params['invalidCode']) === false) {
            $this->loger->mainType = 'appreg';
            $this->loger->target = ['phone' => $params['phone']];
            $this->loger->sarg1 = ['invalidCode' => $params['invalidCode']];
            return $this->returnError(\Prj\Lang\Broker::getMsg('passport.smscode_not_correct_or_timed_out'));
        }

        try {
            $oauth = $this->getOauth();
            $oauth->authrize('appreg', $params);

            if (\Sooh\Base\Tests\Bomb::blowup('Oauth_Appreg_returnAuthrizeFlag', false)) {
                return $this->returnOK('bomb');
            }

            $account = \Lib\Services\Account::getInstance(\Prj\BaseCtrl::getRpcDefault('Account'));
            //调用同盾接口
            if ($account->checkSecureForTongDun($params) == false) {
                return $this->returnError(\Prj\Lang\Broker::getMsg('oauth.unable_to_register_please_contact_customer_service'));
            }
            $accountInfo = $account->register($params['password'], [[$params['phone'], 'phone']], $customArgs);
            $accountInfo = $account->login($params['phone'], 'phone', $params['password']);
            if (\Sooh\Base\Tests\Bomb::blowup('Oauth_Appreg_returnAccountFlag')) {
                return $this->returnOK('bomb');
            }

            $ret = $oauth->getCode($accountInfo['accountId'], 'admin');

            try {
                \Prj\ReadConf::run(
                        [
                    'event' => 'suc_ok',
                    'brand' => \Prj\Message\Message::MSG_BRAND,
                    'tel_serve' => \Prj\Message\Message::MSG_TEL_SERVE,
                    'signin_redbag' => sprintf('%.2f', \Prj\Data\Config::get('REGISTER_RED_AMOUNT') / 100),
                        ], ['phone' => $params['phone'], 'userId' => $accountInfo['accountId']]
                );
            } catch (Exception $e) {
                $this->loger->target = $params['phone'];
            }

			//勋章注册任务
            try {
                if (isset($params['invitationCode']) && !empty($params['invitationCode'])) {     //通过邀请码注册的用户
                    $medalFriendsReg = new \Lib\Medal\MedalFriendsReg();    //注册参加好友邀请勋章活动
                    $medalFriendsReg->setUserId($inviteByUser)->setInvitationCode($params['invitationCode'])->logicByInvCode();
                }
            } catch (Exception $e) {
                $this->loger->ret = '勋章好友注册活动失败';
            }
            $this->_view->assign('info', $ret); //出现多个assign中的info字段重复，修改如下突出唯一识别
            $this->_view->assign('infoappreg', $ret);
//			$_end_time = microtime(true);
//			var_log($_end_time - $_start_time, '============Oauth run time from appreg:');
            $this->returnOK(\Prj\Lang\Broker::getMsg('passport.success'));
        } catch (Exception $e) {
            $this->loger->mainType = 'appreg';
            $this->loger->target = ['phone' => $params['phone']];
            $this->loger->sarg1 = ['invalidCode' => $params['invalidCode']];
            $this->loger->sarg2 = ['clientId' => $params['clientId']];
            return $this->returnError($e->getMessage(), $e->getCode());
        }
    }

    /**
     * M端注册
     * @input string phone 手机号
     * @input string smsCode 短信验证码
     * @input string password 密码
     * @input string inviteCode 邀请码
     * @input string contractId 渠道ID
     * @input string clientType 客户端类型
     * @input string clientId 客户端ID(base64_encode(*** . 'clientId'))
     * @input string clientSecret 客户端KEY(base64_encode(*** . 'clientSecret'))
     * @input string protocol 协议版本号
     * @input string contractData contractData
     * @input string tongDunStr 同盾参数
     * //返回客户端旧标识信息
     * @output {"code":200,"info":{"code":"*****","redirectUri":"*****"},"msg":"成功"}
     * //返回客户端新的标识信息
     * @output {"code":200,"infowebreg":{"code":"*****","redirectUri":"*****"},"msg":"成功"}
     * @errors {"code":400,"msg":"错误"}
     */
    public function webRegAction() {
        $params = [
            'phone' => $this->_request->get('phone'),
            'smsCode' => $this->_request->get('smsCode'),
            'password' => $this->_request->get('password'),
            'invitationCode' => strtoupper($this->_request->get('inviteCode')),
            'contractId' => $this->_request->get('contractId'),
            'clientType' => $this->_request->get('clientType', \Prj\Consts\ClientType::www),
            'clientId' => $this->_request->get('clientId'),
            'clientSecret' => $this->_request->get('clientSecret'),
            'protocol' => $this->_request->get('protocol'),
            'contractData' => $this->_request->get('contractData', ''),
            'redirectUri' => 'https://www.baidu.com/', //这里如何做？
            'scope' => 'admin',
            'tongDunStr' => $this->_request->get('tongDunStr'),
        ];

        if (empty($params['contractId'])) {
            $params['contractId'] = 0;
        }

        $params['clientId'] && $params['clientId'] = substr(base64_decode($params['clientId']), 0, -8);
        $params['clientSecret'] && $params['clientSecret'] = substr(base64_decode($params['clientSecret']), 0, -12);

        //单独验证密码
        $pwd_len = mb_strlen($params['password']);
        if ($pwd_len < 6 || $pwd_len > 20) {
            return $this->returnError(\Prj\Lang\Broker::getMsg('passport.password_is_not_valid'));
        }

        $rules = [
            'phone' => [inputValidation::$define['phone'], \Prj\Lang\Broker::getMsg('passport.phone_number_is_not_valid')],
            'clientId' => [inputValidation::$define['clientId'], \Prj\Lang\Broker::getMsg('passport.clientId_is_not_valid')],
            'clientSecret' => [inputValidation::$define['clientSecret'], \Prj\Lang\Broker::getMsg('passport.clientSecret_is_not_valid')],
            'clientType' => [inputValidation::$define['clientType'], \Prj\Lang\Broker::getMsg('passport.clientType_is_not_valid')],
            'contractId' => [inputValidation::$define['contractId'], \Prj\Lang\Broker::getMsg('passport.contractId_is_not_valid')],
            'smsCode' => [inputValidation::$define['smscode'], \Prj\Lang\Broker::getMsg('passport.contractId_is_not_valid')],
        ];
        if (inputValidation::validateParams($params, $rules) === false) {
            return $this->returnError(inputValidation::$errorMsg);
        }

        $customArgs = array(
            'contractId' => $params['contractId'],
            'contractData' => $params['contractData'],
            'regIP' => \Sooh\Base\Tools::remoteIP(),
            'clientType' => $params['clientType'],
            'nickname' => substr($params['phone'], 0, 3) . '****' . substr($params['phone'], -4),
            'invitationCode' => $params['invitationCode'],
            'protocol' => $params['protocol'],
        );
        if (empty($params['invitationCode'])) {
            unset($params['invitationCode']);
        }
        if (empty($customArgs['invitationCode'])) {
            unset($customArgs['invitationCode']);
        }
        if (empty($customArgs['protocol'])) {
            unset($customArgs['protocol']);
        }

        if (\Sooh\DB\Cases\SMSCode::getCopy($params['phone'])->chkCode($params['smsCode']) === false) {
            $this->loger->mainType = 'appreg';
            $this->loger->target = ['phone' => $params['phone']];
            $this->loger->sarg1 = ['smsCode' => $params['smsCode']];
            return $this->returnError(\Prj\Lang\Broker::getMsg('passport.smscode_not_correct_or_timed_out'));
        }

        try {
            $oauth = $this->getOauth();
            $oauth->authrize('appreg', $params);

            if (\Sooh\Base\Tests\Bomb::blowup('Oauth_Appreg_returnAuthrizeFlag', false)) {
                return $this->returnOK('bomb');
            }

            $account = \Lib\Services\Account::getInstance(\Prj\BaseCtrl::getRpcDefault('Account'));
            if (isset($params['tongDunStr'])) {
                //调用同盾接口
                if ($account->checkSecureForTongDun($params) == false) {
                    return $this->returnError(\Prj\Lang\Broker::getMsg('oauth.unable_to_register_please_contact_customer_service'));
                }
            }
            $accountInfo = $account->register($params['password'], [[$params['phone'], 'phone']], $customArgs);
            $accountInfo = $account->login($params['phone'], 'phone', $params['password']);


            if (\Sooh\Base\Tests\Bomb::blowup('Oauth_Appreg_returnAccountFlag')) {
                return $this->returnOK('bomb');
            }

            $ret = $oauth->getCode($accountInfo['accountId'], 'admin');

            try {
                \Prj\ReadConf::run(
                        [
                    'event' => 'suc_ok',
                    'brand' => \Prj\Message\Message::MSG_BRAND,
                    'tel_serve' => \Prj\Message\Message::MSG_TEL_SERVE,
                    'signin_redbag' => sprintf('%.2f', \Prj\Data\Config::get('REGISTER_RED_AMOUNT') / 100),
                        ], ['phone' => $params['phone'], 'userId' => $accountInfo['accountId']]
                );
            } catch (Exception $e) {
                $this->loger->target = $params['phone'];
            }

            $accountId = $accountInfo['accountId'];
            $user = \Prj\Data\User::getCopy($accountId);
            $user->load();
            if (!($user->exists())) {
                //新注册用户
                error_log('蜘蛛网用，以后可能要修改');
                $invitationCode = 0;
                $protocol = 1;
                $clientType = $customArgs['clientType'];

                if (\Sooh\Base\Tests\Bomb::blowup('Passport_Login_Return_InvitationCode', false)) {
                    $this->_view->assign('data', ['invitationCode' => $invitationCode]); //出现多个assign中的data字段重复，修改如下突出唯一识别
                    $this->_view->assign('datawebreg', ['invitationCode' => $invitationCode]);
                    return $this->returnOK('bomb');
                }

                $user = \Prj\Data\User::createNew($accountId, $params['phone'] ? : 0, $customArgs['contractId'] ? : 0, $invitationCode, $protocol, $clientType);

                //发送注册红包
                try {
                    $itemGiverReg = new \Prj\Items\ItemGiver($accountId);
                    $_finalItemsReg = $itemGiverReg->add('NewRegisterRedPacket', 1)->give();
                    if (empty($_finalItemsReg)) {
                        $this->loger->ext = $itemGiverReg->getLastError();
                    } else {
                        try {
                            \Prj\ReadConf::run(
                                    [
                                'event' => 'red_loging_packet',
                                'brand' => \Prj\Message\Message::MSG_BRAND,
                                'num_packet' => 1,
                                'private_gift' => sprintf('%.2f', $_finalItemsReg[0][1] / 100),
                                'num_deadline' => 48,
                                    ], ['phone' => $user->getField('phone'), 'userId' => $accountId]
                            );
                        } catch (\Exception $e) {
                            var_log($e->getMessage(), 'Send NewRegisterRedPacket Message Error');
                        }
                    }
                } catch (\Exception $e) {
                    $this->loger->ext = 'send NewRegisterRedPacket faild:' . $e->getMessage();
                }


                //周常-邀请注册
                try {
                    $inviteByUser = $user->getField('inviteByUser');
                    if (!empty($inviteByUser)) {
                        $weekActiveBonus = \Prj\ActivePoints\Invited::getCopy($inviteByUser)->addNum(1)->updUser();
                        \Prj\Data\User::getCopy($inviteByUser)->update();
                        if ($weekActiveBonus) {
                            \Lib\Services\Push::getInstance()->push('all', $inviteByUser, null, json_encode($weekActiveBonus));
                        }
                    }
                } catch (Exception $e) {
                    $this->loger->ret = '周常-邀请领积分发送失败';
                }
                //勋章任务
                try {
                    if (!empty($inviteByUser)) {     //通过邀请码注册的用户
                        $medalFriendsReg = new \Lib\Medal\MedalFriendsReg();    //注册参加好友邀请勋章活动
                        $medalFriendsReg->setUserId($inviteByUser)->logic();
                    }
                } catch (Exception $e) {
                    $this->loger->ret = '勋章好友注册活动失败';
                }
            }

            $user->setField('ipLast', \Sooh\Base\Tools::remoteIP());
            $user->setField('dtLast', \Sooh\Base\Time::getInstance()->ymdhis());
            $user->update();

            $this->_view->assign('info', $ret); //出现多个assign中的info字段重复，修改如下突出唯一识别
            $this->_view->assign('infowebreg', $ret);
            $this->returnOK(\Prj\Lang\Broker::getMsg('passport.success'));
        } catch (Exception $e) {
            $this->loger->mainType = 'webReg';
            $this->loger->target = ['phone' => $params['phone']];
            $this->loger->sarg1 = ['smsCode' => $params['smsCode']];
            $this->loger->sarg2 = ['clientId' => $params['clientId']];
            return $this->returnError($e->getMessage(), $e->getCode());
        }
    }

    /**
     * 快速注册，不用填写密码
     * @input integer phone 手机号
     * @input string smscode 短信验证码
     * @input string invitationCode 邀请码
     * @input string contractId 渠道ID
     * @input string clientType 客户端类型
     * @input string clientId 客户端ID
     * @input string clientSecret 客户端KEY
     * @input string protocol 协议版本号
     * @input string contractData contractData
     * @input string tongDunStr 同盾字符串
     * //返回客户端旧标识信息
     * @output {"code":200,"info":{"code":"*****","redirectUri":"*****"},"msg":"成功"}
     * //返回客户端新的标识信息
     * @output {"code":200,"infoquickreg":{"code":"*****","redirectUri":"*****"},"msg":"成功"}
     * @errors {"code":400,"msg":"错误"}
     */
    public function quickRegAction() {
//		$_start_time = microtime(true);
        $params = [
            'phone' => $this->_request->get('phone'),
            'smscode' => $this->_request->get('smscode'),
            'invitationCode' => strtoupper($this->_request->get('invitationCode')),
            'contractId' => $this->_request->get('contractId'),
            'clientType' => $this->_request->get('clientType', \Prj\Consts\ClientType::www),
            'clientId' => $this->_request->get('clientId'),
            'clientSecret' => $this->_request->get('clientSecret'),
            'protocol' => $this->_request->get('protocol'),
            'contractData' => $this->_request->get('contractData', ''),
            'redirectUri' => 'https://www.baidu.com/', //这里如何做？
            'scope' => 'admin',
            'tongDunStr' => $this->_request->get('tongDunStr'),
        ];

        if (empty($params['contractId'])) {
            $params['contractId'] = 0;
        }

        $rules = [
            'phone' => [inputValidation::$define['phone'], \Prj\Lang\Broker::getMsg('passport.phone_number_is_not_valid')],
            'smscode' => [inputValidation::$define['smscode'], \Prj\Lang\Broker::getMsg('passport.smscode_is_not_valid')],
            'clientId' => [inputValidation::$define['clientId'], \Prj\Lang\Broker::getMsg('passport.clientId_is_not_valid')],
            'clientSecret' => [inputValidation::$define['clientSecret'], \Prj\Lang\Broker::getMsg('passport.clientSecret_is_not_valid')],
            'clientType' => [inputValidation::$define['clientType'], \Prj\Lang\Broker::getMsg('passport.clientType_is_not_valid')],
            'contractId' => [inputValidation::$define['contractId'], \Prj\Lang\Broker::getMsg('passport.contractId_is_not_valid')],
        ];
        if (inputValidation::validateParams($params, $rules) === false) {
            return $this->returnError(inputValidation::$errorMsg);
        }

        $customArgs = array(
            'contractId' => $params['contractId'],
            'contractData' => $params['contractData'],
            'regIP' => \Sooh\Base\Tools::remoteIP(),
            'clientType' => $params['clientType'],
            'nickname' => substr($params['phone'], 0, 3) . '****' . substr($params['phone'], -4),
            'invitationCode' => $params['invitationCode'],
            'protocol' => $params['protocol'],
        );
        if (empty($params['invitationCode'])) {
            unset($params['invitationCode']);
        }
        if (empty($customArgs['invitationCode'])) {
            unset($customArgs['invitationCode']);
        }
        if (empty($customArgs['protocol'])) {
            unset($customArgs['protocol']);
        }

        if (\Sooh\DB\Cases\SMSCode::getCopy($params['phone'])->chkCode($params['smscode']) === false) {
            $this->loger->mainType = 'appreg';
            $this->loger->target = ['phone' => $params['phone']];
            $this->loger->sarg1 = ['smscode' => $params['smscode']];
            return $this->returnError(\Prj\Lang\Broker::getMsg('passport.smscode_not_correct_or_timed_out'));
        }

        try {
            if (isset($params['tongDunStr'])) {
                //调用同盾接口
                $_account = \Lib\Services\Account::getInstance(\Prj\BaseCtrl::getRpcDefault('Account'));
                if ($_account->checkSecureForTongDun($params) == false) {
                    return $this->returnError(\Prj\Lang\Broker::getMsg('oauth.unable_to_register_please_contact_customer_service'));
                }
            }
            $oauth = $this->getOauth();
            $params['customArgs'] = $customArgs;
            $ret = $oauth->authrize('quickReg', $params);


            if (\Sooh\Base\Tests\Bomb::blowup('Oauth_Appreg_returnAuthrizeFlag', false)) {
                return $this->returnOK('bomb');
            }

            try {
                \Prj\ReadConf::run(
                        [
                    'event' => 'suc_ok',
                    'brand' => \Prj\Message\Message::MSG_BRAND,
                    'tel_serve' => \Prj\Message\Message::MSG_TEL_SERVE,
                    'signin_redbag' => sprintf('%.2f', \Prj\Data\Config::get('REGISTER_RED_AMOUNT') / 100),
                        ], ['phone' => $params['phone'], 'userId' => $ret['accountId']]
                );
            } catch (Exception $e) {
                $this->loger->target = $params['phone'];
            }

            $this->_view->assign('info', $ret); //出现多个assign中的info字段重复，修改如下突出唯一识别
            $this->_view->assign('infoquickreg', $ret);
//			$_end_time = microtime(true);
//			var_log($_end_time - $_start_time, '============Oauth run time from quickreg:');
            $this->returnOK(\Prj\Lang\Broker::getMsg('passport.success'));
        } catch (Exception $e) {
            $this->loger->mainType = 'appreg';
            $this->loger->target = ['phone' => $params['phone']];
            $this->loger->sarg1 = ['invalidCode' => $params['invalidCode']];
            $this->loger->sarg2 = ['clientId' => $params['clientId']];
            return $this->returnError($e->getMessage(), $e->getCode());
        }
    }

    /**
     * 发送注册验证码-手机号未注册
     * @input integer phone 手机号
     * @output {"code":200,"msg":"成功"}
     * @errors {"code":400,"msg":"error"}
     */
    public function sendInvalidcodeAction() {
        $params = [
            'phone' => $this->_request->get('phone'),
        ];
        if (inputValidation::validateParams($params, ['phone' => [inputValidation::$define['phone'], \Prj\Lang\Broker::getMsg('passport.phone_number_is_not_valid')]]) === false) {
            return $this->returnError(inputValidation::$errorMsg);
        }

        try {
            $dbLogin = \Sooh\DB\Cases\AccountAlias::getCopy([$params['phone'], 'phone']);
            $dbLogin->load();
            if ($dbLogin->exists()) {
                return $this->returnError(\Prj\Lang\Broker::getMsg('account.accounts_existing'));
            } else {
                try {
                    $smsCode = mt_rand(100000, 999999);
                    if (\Sooh\Base\Ini::getInstance()->get('deploymentCode') <= 30) {
                        $smsCode = $this->_request->get('universalMachineCode', $smsCode);
                    }

                    \Prj\ReadConf::run(
                            ['event' => 'reg_name', 'brand' => \Prj\Message\Message::MSG_BRAND, 'num_num' => $smsCode, 'num_time' => \Prj\Message\Message::MSG_NUM_TIME_15], ['phone' => $params['phone'], 'smsCode' => $smsCode]
                    );
                } catch (Exception $e) {
                    
                }
                return $this->returnOK(\Prj\Lang\Broker::getMsg('passport.smscode_send_success'));
            }
        } catch (Exception $e) {
            $this->loger->target = $params['phone'];
            return $this->returnError($e->getMessage(), $e->getCode());
        }
    }

    /**
     * 申请访问令牌
     * @input string code 临时码
     * @input string redirectUri 或调URI
     * //返回客户端旧的标识信息
     * @output {"code":200,"info":{"accessToken":"*****","refreshToken":"*****"},"msg":"成功"}
     * //返回客户端新的标识信息
     * @output {"code":200,"infotoken":{"accessToken":"*****","refreshToken":"*****"},"msg":"成功"}
     * @errors {"code":400,"msg":"error"}
     */
    public function tokenAction() {
        $code = $this->_request->get('code');
        $redirectUri = $this->_request->get('redirectUri');

        $this->oauthResponse = new \Lib\Oauth\OauthResponse();

        $rules = [
            'code' => [
                inputValidation::$define['code'],
                $this->oauthResponse->invalid_request_code['error'],
                $this->oauthResponse->invalid_request_code['code'],
            ],
        ];
        if (inputValidation::validateParams(['code' => $code], $rules) === false) {
            return $this->returnError(inputValidation::$errorMsg);
        }

        try {
            $oauth = $this->getOauth();
            $ret = $oauth->getToken($code, $redirectUri);
            $this->_view->assign('info', $ret); //出现多个assign中的info字段重复，修改如下突出唯一识别
            $this->_view->assign('infotoken', $ret);
            $this->returnOK(\Prj\Lang\Broker::getMsg('passport.success'));
        } catch (Exception $e) {
            $this->loger->target = $code;
            $this->loger->sarg1 = ['redirectUri' => $redirectUri];
            return $this->returnError($e->getMessage(), $e->getCode());
        }
    }

    /**
     * 获取用户资源
     * @input string accessToken 密钥
     * //返回客户端旧的标识信息
     * @output {"code":200,"info":{...},"msg":"成功"}
     * //返回客户端的新的标识信息
     * @output {"code":200,"infouserinfo":{...},"msg":"成功"}
     * @errors {"code":400,"msg":"error"}
     */
    public function userInfoAction() {
        $token = $this->_request->get('accessToken');

        $this->oauthResponse = new \Lib\Oauth\OauthResponse();

        $rules = [
            'token' => [
                inputValidation::$define['accessToken'],
                $this->oauthResponse->invalid_request_access_token['error'],
                $this->oauthResponse->invalid_request_access_token['code'],
            ],
        ];
        if (inputValidation::validateParams(['token' => $token], $rules) === false) {
            return $this->returnError(inputValidation::$errorMsg);
        }

        try {
            $oauth = $this->getOauth();
            $ret = $oauth->getResources($token);
            $this->_view->assign('info', $ret); //出现多个assign中的info字段重复，修改如下突出唯一识别
            $this->_view->assign('infouserinfo', $ret);
            $this->returnOK(\Prj\Lang\Broker::getMsg('passport.success'));
        } catch (Exception $e) {
            $this->loger->target = $token;
            return $this->returnError($e->getMessage(), $e->getCode());
        }
    }

    /**
     * 刷新token
     * @input string refreshToken 刷新密钥
     * //返回客户端的旧的标识信息
     * @output {"code":200,"info":{"accessToken":"****","refreshToken":"****"},"msg":"成功"}
     * //返回客户端新的标识信息
     * @output {"code":200,"inforefresh":{"accessToken":"****","refreshToken":"****"},"msg":"成功"}
     * @errors {"code":400,"msg":"error"}
     */
    public function refreshAction() {
        $refreshToken = $this->_request->get('refreshToken');

        $this->oauthResponse = new \Lib\Oauth\OauthResponse();

        $rules = [
            'refreshToken' => [
                inputValidation::$define['refreshToken'],
                $this->oauthResponse->invalid_request_refresh_token['error'],
                $this->oauthResponse->invalid_request_refresh_token['code'],
            ],
        ];
        if (inputValidation::validateParams(['refreshToken' => $refreshToken], $rules) === false) {
            return $this->returnError(inputValidation::$errorMsg);
        }

        try {
            $oauth = $this->getOauth();
            $ret = $oauth->refreshToken($refreshToken);
            $this->_view->assign('info', $ret); //出现多个assign中的info字段重复，修改如下突出唯一识别
            $this->_view->assign('inforefresh', $ret);
            $this->returnOK(\Prj\Lang\Broker::getMsg('passport.success'));
        } catch (Exception $e) {
            $this->loger->target = $refreshToken;
            return $this->returnError($e->getMessage(), $e->getCode());
        }
    }

    /**
     * 找回密码/重置密码
     * @input string phone 手机号
     * @input string invalidCode 验证码
     * @input string newPwd 密码
     * @input string cameFrom 登录类型
     * @input string deviceId 唯一设备ID
     * @input string clientId clientId
     * @input string clientSecret clientSecret
     * @input string redirectUri redirectUri
     * //返回客户端旧的标识信息
     * @output {"code":200,"info":{"..."}"msg":"success"}
     * //返回客户端新的标识信息
     * @output {"code":200,"inforesetpwd":{"..."}"msg":"success"}
     * @errors {"code":400,"msg":"error"}
     */
    public function resetPwdAction() {
        $params = [
            'phone' => $this->_request->get('phone'),
            'invalidCode' => $this->_request->get('invalidCode'),
            'newPwd' => $this->_request->get('newPwd'),
            'cameFrom' => $this->_request->get('cameFrom'),
            'deviceId' => $this->_request->get('deviceId'),
            'clientId' => $this->_request->get('clientId'),
            'clientSecret' => $this->_request->get('clientSecret'),
            'redirectUri' => $this->_request->get('redirectUri'),
        ];
        if ($params['cameFrom'] != 'phone') {
            return $this->returnError(\Prj\Lang\Broker::getMsg('account.params_error'));
        }

        //单独验证密码
        $pwd_len = mb_strlen($params['newPwd']);
        if ($pwd_len < 6 || $pwd_len > 20) {
            return $this->returnError(\Prj\Lang\Broker::getMsg('passport.password_is_not_valid'));
        }

        $rules = [
            'phone' => [inputValidation::$define['phone'], \Prj\Lang\Broker::getMsg('account.account_or_password_input_error')],
            'invalidCode' => [inputValidation::$define['smscode'], \Prj\Lang\Broker::getMsg('passport.smscode_is_not_valid')],
            'deviceId' => ['#^[0-9a-zA-Z_\-:]{10,50}$#', \Prj\Lang\Broker::getMsg('passport.deviceId_is_not_valid')],
            'clientId' => [inputValidation::$define['clientId'], \Prj\Lang\Broker::getMsg('passport.clientId_is_not_valid')],
            'clientSecret' => [inputValidation::$define['clientSecret'], \Prj\Lang\Broker::getMsg('passport.clientSecret_is_not_valid')],
        ];
        if (inputValidation::validateParams($params, $rules) === false) {
            return $this->returnError(inputValidation::$errorMsg);
        }

        if (\Sooh\DB\Cases\SMSCode::getCopy($params['phone'])->chkCode($params['invalidCode']) == false) {
            $this->loger->target = $params['phone'];
            $this->loger->sarg1 = ['invalidCode' => $params['invalidCode']];
            return $this->returnError(msgDefine::$define['invalidcode_incorrect']);
        }

        try {
            $oauth = $this->getOauth();
            $oauth->authrize('resetPwd', [
                'clientId' => $params['clientId'],
                'clientSecret' => $params['clientSecret'],
                'redirectUri' => $params['redirectUri'],
            ]);

            $account = \Lib\Services\Account::getInstance(\Prj\BaseCtrl::getRpcDefault('Account'));
            $user = $account->resetPwd($params['phone'], $params['cameFrom'], $params['newPwd'], $params['deviceId']);
            $ret = $account->getResource($user['accountId']);

            $this->_view->assign('info', $ret); //出现多个assign中的info字段重复，修改如下突出唯一识别
            $this->_view->assign('inforesetpwd', $ret);
            return $this->returnOK(\Prj\Lang\Broker::getMsg('passport.success'));
        } catch (Exception $e) {
            $this->loger->target = $params['phone'];
            $this->loger->sarg1 = ['cameFrom' => $params['cameFrom']];
            return $this->returnError($e->getMessage(), $e->getCode());
        }
    }

    /**
     * 检查是否注册
     * @input string phone 手机号
     * @input string cameFrom 登录来源
      //	 * @output {"code":200,"msg":"exists"}
     * @errors {"code":400,"msg":"no exists"}
     */
    public function checkRegAction() {
        $params = [
            'phone' => $this->_request->get('phone'),
            'cameFrom' => $this->_request->get('cameFrom'),
        ];
        if ($params['cameFrom'] != 'phone') {
            return $this->returnError(\Prj\Lang\Broker::getMsg('account.account_or_password_input_error'));
        }
        $rules = [
            'phone' => [inputValidation::$define['phone'], \Prj\Lang\Broker::getMsg('account.account_or_password_input_error')],
        ];
        if (inputValidation::validateParams($params, $rules) === false) {
            return $this->returnError(inputValidation::$errorMsg);
        }

        $dbLogin = \Sooh\DB\Cases\AccountAlias::getCopy([$params['phone'], $params['cameFrom']]);
        $dbLogin->load();
        if ($dbLogin->exists()) {
            $this->_view->assign('accountId', $dbLogin->getField('accountId'));
            return $this->returnOK(\Prj\Lang\Broker::getMsg('passport.phone_is_already_registered'));
        } else {
            return $this->returnError(\Prj\Lang\Broker::getMsg('passport.phone_is_not_registered'));
        }
    }

    /**
     * 更新用户昵称
     * @input string accessToken accessToken
     * @input string nickname 昵称
     * @output {"code":200,"msg":"success"}
     * @errors {"code":200,"msg":"*****"}
     */
    public function updNicknameAction() {

        $this->oauthResponse = new \Lib\Oauth\OauthResponse();

        $params = [
            'token' => $this->_request->get('accessToken'),
            'nickname' => $this->_request->get('nickname'),
        ];
        $rules = [
            'token' => [
                inputValidation::$define['accessToken'],
                $this->oauthResponse->invalid_request_access_token['error'],
                $this->oauthResponse->invalid_request_access_token['code'],
            ],
            'nickname' => [
                inputValidation::$define['nickname'],
                \Prj\Lang\Broker::getMsg('passport.nickname_is_not_valid'),
            ],
        ];
        if (inputValidation::validateParams($params, $rules) === false) {
            return $this->returnError(inputValidation::$errorMsg, inputValidation::$errorCode);
        }

        try {
            $oauth = $this->getOauth();
            $oauth->updNickname($params['token'], $params['nickname']);
            return $this->returnOK(\Prj\Lang\Broker::getMsg('passport.success'));
        } catch (Exception $e) {
            return $this->returnError($e->getMessage(), $e->getCode());
        }
    }

    /**
     * 获取Oauth实例
     * @return \Lib\Oauth\Oauth
     */
    protected function getOauth() {
        return \Lib\Oauth\Oauth::getInstance();
    }

}
