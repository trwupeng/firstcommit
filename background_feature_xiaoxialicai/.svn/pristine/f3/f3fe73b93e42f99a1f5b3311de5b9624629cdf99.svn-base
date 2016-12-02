<?php

use \Prj\Consts\MsgDefine as msgDefine;
use \Lib\Misc\InputValidation as inputValidation;

/**
 * Passport
 * > session会尝试设置cookie：SoohSessId
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class PassportController extends \Prj\UserCtrl {
	protected $clientId = '1104878344';
	protected $clientSecret = 's20vH9emKJ6BmT1Q';

	/**
	 * 这里不需要检查登入
	 */
	protected function onInit_chkLogin(){}

	/**
	 * 登出 ，成功后会清除session
	 * 登出 ，成功后会清除session
	 * @throws \Sooh\Base\ErrException
	 */
	public function logoutAction()
	{
		\Sooh\Base\Session\Data::getInstance()->set('accountId', null);
		\Sooh\Base\Session\Data::getInstance()->set('nickname', null);
		\Sooh\Base\Session\Data::getInstance()->set('pwdVer', null);
		setcookie('nickname', '', 0, '/', \Sooh\Base\Ini::getInstance()->cookieDomain());
		$this->returnOK(\Prj\Lang\Broker::getMsg('passport.logout_success'));
	}

	/**
	 * 修改登录密码时发送短信验证码-已经存在账号
	 * @input integer phone 手机号
	 * @output {"code":200,"msg":"发送成功"}
	 * @errors {"code":400,"msg":"error"}
	 */
	public function sendInvalidcodeAction() {
		$phone = $this->_request->get('phone');
		if (!empty($phone) && substr($phone, 3, 4) != '****') {
			$rules = [
				'phone' => [
					inputValidation::$define['phone'],
					\Prj\Lang\Broker::getMsg('passport.phone_number_is_not_valid'),
				],
			];
			if (inputValidation::validateParams(['phone' => $phone], $rules) === false) {
				return $this->returnError(inputValidation::$errorMsg);
			}
		} elseif (substr($phone, 3, 4) == '****') {
			//check login
			unset($phone);
			$userIdentifier = \Sooh\Base\Session\Data::getInstance()->get('accountId');
			if ($userIdentifier){
				\Sooh\Base\Log\Data::getInstance()->userId = $userIdentifier;
				$_user = \Prj\Data\User::getCopy($userIdentifier);
				$_user->load();
				if ($_user->exists()) {
					$phone = $_user->getField('phone');
				}
			}
			if (!isset($phone)) {
				return $this->returnError(\Prj\Lang\Broker::getMsg('passport.phone_number_is_not_valid'));
			}
		} else {
			return $this->returnError(\Prj\Lang\Broker::getMsg('passport.phone_number_is_not_valid'));
		}

		//TODO 其他验证

		try {
			if (\Prj\Data\User::getCount(['phone' => $phone]) == 0) {
				return $this->returnError(\Prj\Lang\Broker::getMsg('passport.phone_number_is_not_registered'));
			}

			$smsCode = mt_rand(100000, 999999);
			if (\Sooh\Base\Ini::getInstance()->get('deploymentCode') <= 30) {
				$smsCode = $this->_request->get('universalMachineCode', $smsCode);
			}

			$ret = \Prj\ReadConf::run(
				['event' => 'login_num', 'brand' => \Prj\Message\Message::MSG_BRAND, 'num_num' => $smsCode, 'num_time' => \Prj\Message\Message::MSG_NUM_TIME_15],
				['phone' => $phone, 'smsCode' => $smsCode]
			);
			if ($ret['sms'] === true) {
				return $this->returnOK(\Prj\Lang\Broker::getMsg('passport.smscode_send_success'));
			} else {
				return $this->returnError(\Prj\Lang\Broker::getMsg('passport.smscode_send_failed'));
			}
		} catch (Exception $e) {
			$this->loger->target = $phone;
			return $this->returnError($e->getMessage(), $e->getCode());
		}
	}

	/**
	 * 为快捷登录发送短信验证码
	 * @input integer phone 手机号
	 * @output {"code":200,"msg":"发送成功"}
	 * @errors {"code":400,"msg":"error"}
	 * @throws ErrorException
	 * @throws \Sooh\Base\ErrException
	 */
	public function sendSmscodeForQuickLoginAction()
	{
		$phone = $this->_request->get('phone');
		$rules = [
			'phone' => [
				inputValidation::$define['phone'],
				\Prj\Lang\Broker::getMsg('passport.phone_number_is_not_valid'),
			],
		];
		if (inputValidation::validateParams(['phone' => $phone], $rules) === false) {
			return $this->returnError(inputValidation::$errorMsg);
		}

		//TODO 其他验证

		try {
//			if (\Prj\Data\User::getCount(['phone' => $phone]) == 0) {
//				return $this->returnError(\Prj\Lang\Broker::getMsg('passport.phone_number_is_not_registered'));
//			}
			$tmp_oauthMap = [
				'func' => 'checkReg',
				'phone' => $phone,
				'cameFrom' => 'phone',
			];
			try {
				$oauthRet = (new \Prj\Oauth\Oauth('', '', 'nonStandardMode'))->invokeMode($tmp_oauthMap);
			} catch (\Exception $e) {
				return $this->returnError(\Prj\Lang\Broker::getMsg('passport.phone_number_is_not_registered'));
			}


			$smsCode = mt_rand(100000, 999999);
			if (\Sooh\Base\Ini::getInstance()->get('deploymentCode') <= 30) {
				$smsCode = $this->_request->get('universalMachineCode', $smsCode);
			}

			try {
				$ret = \Prj\ReadConf::run(
					['event' => 'fast_name', 'brand' => \Prj\Message\Message::MSG_BRAND, 'num_num' => $smsCode, 'num_time' => \Prj\Message\Message::MSG_NUM_TIME_15],
					['phone' => $phone, 'smsCode' => $smsCode]
				);

				if ($ret['sms'] === true) {
					return $this->returnOK(\Prj\Lang\Broker::getMsg('passport.smscode_send_success'));
				} else {
					return $this->returnError(\Prj\Lang\Broker::getMsg('passport.smscode_send_failed'));
				}
			} catch(Exception $e) {
				$this->loger->target = $phone;
			}

		} catch (Exception $e) {
			$this->loger->target = $phone;
			return $this->returnError($e->getMessage(), $e->getCode());
		}
	}

	/**
	 * 校验短信验证码
	 * @input string phone 手机号
	 * @input string invalidCode 短信验证码
	 * @output {"code":200,"msg":"成功"}
	 * @errors {"code":400,"msg":"error"}
	 */
	public function checkInvalidcodeAction() {
		$phone = $this->_request->get('phone');
		$invalidCode = $this->_request->get('invalidCode');

		$rules = [
			'phone' => [
				inputValidation::$define['phone'],
				\Prj\Lang\Broker::getMsg('passport.phone_number_is_not_valid'),
			],
		];
		if (!empty($phone) && substr($phone, 3, 4) != '****') {
			if (inputValidation::validateParams(['phone' => $phone], $rules) === false) {
				return $this->returnError(inputValidation::$errorMsg);
			}
		} elseif (substr($phone, 3, 4) == '****') {
			//check login
			unset($phone);
			$userIdentifier = \Sooh\Base\Session\Data::getInstance()->get('accountId');
			if ($userIdentifier){
				\Sooh\Base\Log\Data::getInstance()->userId = $userIdentifier;
				$_user = \Prj\Data\User::getCopy($userIdentifier);
				$_user->load();
				if ($_user->exists()) {
					$phone = $_user->getField('phone');
				}
			}
			if (!isset($phone)) {
				return $this->returnError(\Prj\Lang\Broker::getMsg('passport.phone_number_is_not_valid'));
			}
		} else {
			return $this->returnError(\Prj\Lang\Broker::getMsg('passport.phone_number_is_not_valid'));
		}

		$oauthMap = [
			'func' => 'checkReg',
			'phone' => $phone,
			'cameFrom' => 'phone',
		];
		try {
			$oauthRet = (new \Prj\Oauth\Oauth('', '', 'nonStandardMode'))->invokeMode($oauthMap);
		} catch (\Exception $e) {
			//未注册
			return $this->returnError(\Prj\Lang\Broker::getMsg('passport.phone_number_is_not_valid'));
		}

		if (\Sooh\DB\Cases\SMSCode::getCopy($phone)->chkCode($invalidCode) == false) {
			$this->loger->target = $phone;
			return $this->returnError(\Prj\Lang\Broker::getMsg('passport.smscode_not_correct_or_timed_out'));
		} else {
			return $this->returnOK(\Prj\Lang\Broker::getMsg('passport.success'));
		}
	}

	/**
	 * 登录
	 * hasBeenInvited:1已被邀请，0未被邀请
	 * @input string code 临时码
	 * @input string redirectUri 回调地址
	 * @input string clientType 客户端类型
	 * @output {"code":200,"info":{"accountId":"****","nickname":"*****"},"msg":"成功"}
	 * @errors {"code":400,"msg":"帐号不存在"}
	 */
	public function loginAction() {
		var_log(func_get_args(), 'func_get_args, Passport->login');
		$_oauthResponse = new \Lib\Oauth\OauthResponse();
		$curClientType = $this->_request->get('clientType');

		$params = [
			'code' => $this->_request->get('code'),
			'redirectUri' => $this->_request->get('redirectUri'),
		];
		var_log($params, 'params');
		$rules  = [
			'code' => [
				inputValidation::$define['code'],
			    $_oauthResponse->invalid_request_code['error'],
			    $_oauthResponse->invalid_request_code['code'],
			],
		];
		if (inputValidation::validateParams($params, $rules) === false) {
			return $this->returnError(inputValidation::$errorMsg, inputValidation::$errorCode);
		}

		try {
			$resources = (new \Prj\Oauth\Oauth($params['code'], $params['redirectUri']))->getResource();
			$accountId = $resources['accountId'];

			if (\Sooh\Base\Tests\Bomb::blowup('Passport_Login_Return_AccountId', false)) {
				$this->_view->assign('data', ['accountId' => $accountId]);
				return $this->returnOK('bomb');
			}


            $this->loger->mainType=0;//1:new
			$user = \Prj\Data\User::getCopy($accountId);
			$user->load();
			if (!($user->exists())) {
				//新注册用户
				$invitationCode = $resources['invitationCode'];
				$protocol       = $resources['protocol'];
				$clientType     = $resources['clientType'];

				if (\Sooh\Base\Tests\Bomb::blowup('Passport_Login_Return_InvitationCode', false)) {
					$this->_view->assign('data', ['invitationCode' => $invitationCode]);
					return $this->returnOK('bomb');
				}

				$user = \Prj\Data\User::createNew($accountId, $resources['phone'] ? : 0, $resources['contractId'] ? : 0, $invitationCode, $protocol, $clientType);

				//发送注册红包
//				try {
//					$giveRedPacket = (new \Prj\Items\RedPacketForRegister())->give($accountId);
//				} catch (Exception $e) {
//					$this->loger->ext = 'send redPacketForRegister error';
//					$this->loger->sarg2 = json_encode(['code' => $e->getCode(), 'msg' => $e->getMessage()]);
//				}
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
								],
								['phone' => $user->getField('phone'), 'userId' => $accountId]
							);
						} catch(\Exception $e) {
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
						if ($weekActiveBonus){
							\Lib\Services\Push::getInstance()->push( 'all', $inviteByUser, null, json_encode($weekActiveBonus));
						}
					}
				} catch (Exception $e) {
					$this->loger->ret = '周常-邀请领积分发送失败';
				}

                $this->loger->mainType=1;//1:new
			}

			if (!empty($curClientType)) {
				if (\Lib\Misc\InputValidation::validateParams(['clientType' => $curClientType], ['clientType' => [inputValidation::$define['clientType'], \Prj\Lang\Broker::getMsg('passport.clientType_is_not_valid')]]) == false) {
					return $this->returnError(\Lib\Misc\InputValidation::$errorMsg, \Lib\Misc\InputValidation::$errorCode);
				}

				$dbFirstLoginApp = $user->getField('firstLoginApp');
				if (empty($dbFirstLoginApp) && ($curClientType == \Prj\Consts\ClientType::android || $curClientType == \Prj\Consts\ClientType::appstore)) {
					$user->setField('firstLoginApp', $curClientType);
					//首次激活app的红包奖励
					try {
						$itemGiver = new \Prj\Items\ItemGiver($accountId);
						$finalItems = $itemGiver->add('NewFirstLoginAppRedPacket', 1)->give();
						if (empty($finalItems)) {
							$this->loger->ext = $itemGiver->getLastError();
						} else {
							\Prj\ReadConf::run(
								[
									'event' => 'first_login_packet',
									'brand' => \Prj\Message\Message::MSG_BRAND,
									'num_packet' => 1,//数量
									'private_gift' => sprintf('%.02f', $finalItems[0][1] / 100),//金额
									'num_deadline' => 48,//有效期
								],
								['userId' => $accountId]
							);
						}
					} catch (\Exception $e) {
						$this->loger->ext = 'send NewFirstLoginAppRedPacket faild:' . $e->getMessage();
					}
				}
			}

			$user->setField('ipLast', \Sooh\Base\Tools::remoteIP());
			$user->setField('dtLast', \Sooh\Base\Time::getInstance()->ymdhis());
			$user->update();
			$this->onLogin([
				'accountId' => $accountId,
				'nickname'  => $user->getField('nickname'),
				'pwdVer'    => $resources['pwdVer'],
				'phone'     => substr($resources['phone'], 0, 3) . '****' . substr($resources['phone'], -4),
//				'isBind'    => $user->getField('ymdBindcard'),
			    'isTradePwd' => $user->getField('tradePwd'),
			]);
			if (isset($_finalItemsReg)) {
				$this->_view->assign('redPacket', ['type' => 'RedPacketForRegister', 'amount' => $_finalItemsReg[0][1], 'dtExpired' => $_finalItemsReg[0][2]]);
			}

			$mineInviteTree = \Prj\Data\User::getMineInvitedTree($accountId);
			if (empty($mineInviteTree['parent'])) { //hasBeenInvited:1已被邀请，0未被邀请
				$this->_view->assign('hasBeenInvited', 0);
			} else {
				$this->_view->assign('hasBeenInvited', 1);
			}

			return $this->returnOK(\Prj\Lang\Broker::getMsg('passport.success'));
		} catch (Exception $e) {
			$this->loger->target = $params['code'];
			$this->loger->sarg1  = ['redirectUri' => $params['redirectUri']];
			return $this->returnError($e->getMessage(), $e->getCode());
		}
	}

	/**
	 * 快捷注册
	 * @input string phone 手机号
	 * @input integer smscode 短信验证码
	 * @input string inviteCode 邀请码
	 * @input integer contractId 渠道ID
	 * @input integer protocol 协议号
	 * @input integer clientType clientType
	 * @output {"code":200,'dtExpired':'','amount':52.36}
	 * @errors {'code':400,'msg':'error'}
	 * @throws ErrorException
	 * @throws Exception
	 * @throws \Sooh\Base\ErrException
	 */
	public function quickRegAction()
	{
		$params = [
			'phone'          => $this->_request->get('phone'),
			'smscode'        => $this->_request->get('smscode'),
			'invitationCode' => $this->_request->get('inviteCode'),
			'contractId'     => $this->_request->get('contractId'),
			'protocol'       => $this->_request->get('protocol'),
			'clientType'     => $this->_request->get('clientType'),
			'tongDunStr'     => $this->_request->get('tongDunStr'),
		];

		$rules = [
			'phone'        => [inputValidation::$define['phone'], \Prj\Lang\Broker::getMsg('passport.phone_number_is_not_valid')],
			'smscode'      => [inputValidation::$define['smscode'], \Prj\Lang\Broker::getMsg('passport.smscode_is_not_valid')],
			'clientType'   => [inputValidation::$define['clientType'], \Prj\Lang\Broker::getMsg('passport.clientType_is_not_valid')],
			'contractId'   => [inputValidation::$define['contractId'], \Prj\Lang\Broker::getMsg('passport.contractId_is_not_valid')],
		];

		if (inputValidation::validateParams($params, $rules) === false) {
			return $this->returnError(inputValidation::$errorMsg);
		}

		$_map = array_merge($params, ['clientId' => $this->clientId, 'clientSecret' => $this->clientSecret, 'func' => 'quickReg']);

		try {
			$oauthRet = (new \Prj\Oauth\Oauth('', '', 'nonStandardMode'))->invokeMode($_map);
			$resources = $oauthRet['resource'];
			$accountId = $oauthRet['accountId'];

			$user = \Prj\Data\User::getCopy($accountId);
			$user->load();
			if (!($user->exists())) {
				//新注册用户
				$invitationCode = $resources['invitationCode'];
				$protocol       = $resources['protocol'];
				$clientType     = $resources['clientType'];

				if (\Sooh\Base\Tests\Bomb::blowup('Public_Login_Return_InvitationCode', false)) {
					$this->_view->assign('data', ['invitationCode' => $invitationCode]);
					return $this->returnOK('bomb');
				}

				//本地用户注册
				$user = \Prj\Data\User::createNew($accountId, $resources['phone'] ? : 0, $resources['contractId'] ? : 0, $invitationCode, $protocol, $clientType);

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
								],
								['phone' => $user->getField('phone'), 'userId' => $accountId]
							);
						} catch(\Exception $e) {
							var_log($e->getMessage(), 'Send NewRegisterRedPacket Message Error');
						}
					}
				} catch (\Exception $e) {
					$this->loger->ext = 'send NewRegisterRedPacket faild:' . $e->getMessage();
				}

				//周常-邀请注册
				try {
					$inviteByUser = $user->getField('inviteByUser');
					$weekActiveBonus = \Prj\ActivePoints\Invited::getCopy($inviteByUser)->addNum(1)->updUser();
					\Prj\Data\User::getCopy($inviteByUser)->update();
					if ($weekActiveBonus){
						\Lib\Services\Push::getInstance()->push( 'all', $inviteByUser, null, 
							json_encode($weekActiveBonus));
					}
				} catch (Exception $e) {
					$this->loger->ret = '周常-邀请领积分发送失败';
				}
				//勋章注册任务
                try {
                    if (!empty($invitationCode) && !empty($inviteByUser)) {     //通过邀请码注册的用户
                        $medalFriendsReg = new \Lib\Medal\MedalFriendsReg();    //注册参加好友邀请勋章活动
                        $medalFriendsReg->setUserId($inviteByUser)->setInvitationCode($invitationCode)->logicByInvCode();
                    }
                } catch (Exception $e) {
                    $this->loger->ret = '勋章好友注册活动失败';
                }
                $this->loger->mainType=1;//1:new

				$this->_view->assign('amount', sprintf('%.2f', $_finalItemsReg[0][1] / 100));
				$this->_view->assign('dtExpired', date('Y-m-d H:i:s', $_finalItemsReg[0][2]));
				return $this->returnOK(\Prj\Lang\Broker::getMsg('passport.success'));
			}
		} catch (Exception $e) {
			return $this->returnError($e->getMessage(), $e->getCode());
		}
	}

	/**
	 * 快捷登录
	 *
	 * hasBeenInvited:1已被邀请，0未被邀请
	 * @input integer phone 手机号
	 * @input integer smscode 短信验证码
	 * @input integer clientId clientId
	 * @input string clientSecret clientSecret
	 * @input int clientType clientType
	 * @throws ErrorException
	 * @throws Exception
	 * @throws \Sooh\Base\ErrException
	 */
	public function quickLoginAction()
	{
		$params = [
			'phone' => $this->_request->get('phone'),
		    'smscode' => $this->_request->get('smscode'),
		    'clientId' => $this->_request->get('clientId'),
		    'clientSecret' => $this->_request->get('clientSecret'),
		    'redirectUri' => 'https://www.baidu.com/',//how do?
		];

		/**
		 * 当前登录的客户端类型
		 */
		$curClientType = $this->_request->get('clientType');

		$rules = [
			'phone' => [\Lib\Misc\InputValidation::$define['phone'], \Prj\Lang\Broker::getMsg('passport.phone_number_is_not_valid')],
		    'smscode' => [\Lib\Misc\InputValidation::$define['smscode'], \Prj\Lang\Broker::getMsg('passport.smscode_is_not_valid')],
		    'clientId' => [\Lib\Misc\InputValidation::$define['clientId'], \Prj\Lang\Broker::getMsg('passport.clientId_is_not_valid')],
		    'clientSecret' => [\Lib\Misc\InputValidation::$define['clientSecret'], \Prj\Lang\Broker::getMsg('passport.clientSecret_is_not_valid')],
		];

		if (\Lib\Misc\InputValidation::validateParams($params, $rules) == false) {
			return $this->returnError(\Lib\Misc\InputValidation::$errorMsg, \Lib\Misc\InputValidation::$errorCode);
		}

		//check smscode
		if (\Sooh\DB\Cases\SMSCode::getCopy($params['phone'])->chkCode($params['smscode']) == false) {
			return $this->returnError(\Prj\Lang\Broker::getMsg('passport.smscode_not_correct_or_timed_out'));
		}

		//request oauth
		$oauthMap = array_merge(['func' => 'quickLogin'], $params);

		try {
			$oauthRet = (new \Prj\Oauth\Oauth('', '', 'nonStandardMode'))->invokeMode($oauthMap);
		} catch (Exception $e) {
			return $this->returnError($e->getMessage(), $e->getCode());
		}

		try {
			$accountId = $oauthRet['accountId'];
			$resources = $oauthRet['resource'];
			$user = \Prj\Data\User::getCopy($accountId);
			$user->load();
			if (!($user->exists())) {
				//新注册用户
				$invitationCode = $resources['invitationCode'];
				$protocol       = $resources['protocol'];
				$clientType     = $resources['clientType'];

				if (\Sooh\Base\Tests\Bomb::blowup('Passport_QuickLogin_Return_InvitationCode', false)) {
					$this->_view->assign('data', ['invitationCode' => $invitationCode]);
					return $this->returnOK('bomb');
				}

				$user = \Prj\Data\User::createNew($accountId, $resources['phone'] ? : 0, $resources['contractId'] ? : 0, $invitationCode, $protocol, $clientType);
				//发送注册红包
//				try {
//					$giveRedPacket = (new \Prj\Items\RedPacketForRegister())->give($accountId);
//				} catch (Exception $e) {
//					$this->loger->ext = 'send redPacketForRegister error';
//					$this->loger->sarg2 = json_encode(['code' => $e->getCode(), 'msg' => $e->getMessage()]);
//				}
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
								],
								['phone' => $user->getField('phone'), 'userId' => $accountId]
							);
						} catch(\Exception $e) {
							var_log($e->getMessage(), 'Send NewRegisterRedPacket Message Error');
						}
					}
				} catch (\Exception $e) {
					$this->loger->ext = 'send NewRegisterRedPacket faild:' . $e->getMessage();
				}

				//周常-邀请注册
				try {
					$inviteByUser = $user->getField('inviteByUser');
					$weekActiveBonus = \Prj\ActivePoints\Invited::getCopy($inviteByUser)->addNum(1)->updUser();
					\Prj\Data\User::getCopy($inviteByUser)->update();
					if ($weekActiveBonus){
						\Lib\Services\Push::getInstance()->push( 'all', $inviteByUser, null, 
							json_encode($weekActiveBonus));
					}
				} catch (Exception $e) {
					$this->loger->ret = '周常-邀请领积分发送失败';
				}
                $this->loger->mainType=1;//1:new
			}

			if (!empty($curClientType)) {
				if (\Lib\Misc\InputValidation::validateParams(['clientType' => $curClientType], ['clientType' => [inputValidation::$define['clientType'], \Prj\Lang\Broker::getMsg('passport.clientType_is_not_valid')]]) == false) {
					return $this->returnError(\Lib\Misc\InputValidation::$errorMsg, \Lib\Misc\InputValidation::$errorCode);
				}

				$dbFirstLoginApp = $user->getField('firstLoginApp');
				if (empty($dbFirstLoginApp) && ($curClientType == \Prj\Consts\ClientType::android || $curClientType == \Prj\Consts\ClientType::appstore)) {
					$user->setField('firstLoginApp', $curClientType);
					//首次激活app的红包奖励
					try {
						$itemGiver = new \Prj\Items\ItemGiver($accountId);
						$finalItems = $itemGiver->add('NewFirstLoginAppRedPacket', 1)->give();
						if (empty($finalItems)) {
							$this->loger->ext = $itemGiver->getLastError();
						} else {
							\Prj\ReadConf::run(
								[
									'event' => 'first_login_packet',
									'brand' => \Prj\Message\Message::MSG_BRAND,
									'num_packet' => 1,//数量
									'private_gift' => sprintf('%.02f', $finalItems[0][1] / 100),//金额
									'num_deadline' => 48,//有效期
								],
								['userId' => $accountId]
							);
						}
					} catch (\Exception $e) {
						$this->loger->ext = 'send NewFirstLoginAppRedPacket faild:' . $e->getMessage();
					}
				}
			}

			$user->setField('ipLast', \Sooh\Base\Tools::remoteIP());
			$user->setField('dtLast', \Sooh\Base\Time::getInstance()->ymdhis());
			$user->update();
			$this->onLogin([
				'accountId' => $accountId,
				'nickname'  => $user->getField('nickname'),
				'pwdVer'    => $resources['pwdVer'],
				'phone'     => substr($resources['phone'], 0, 3) . '****' . substr($resources['phone'], -4),
//				'isBind'    => $user->getField('ymdBindcard'),
				'isTradePwd' => $user->getField('tradePwd'),
			]);
			//			$this->_view->assign('info', $resources);
			if (isset($_finalItemsReg)) {
				$this->_view->assign('redPacket', ['type' => 'RedPacketForRegister', 'amount' => $_finalItemsReg[0][1], 'dtExpired' => $_finalItemsReg[0][2]]);
			}

			$mineInviteTree = \Prj\Data\User::getMineInvitedTree($accountId);
			if (empty($mineInviteTree['parent'])) { //hasBeenInvited:1已被邀请，0未被邀请
				$this->_view->assign('hasBeenInvited', 0);
			} else {
				$this->_view->assign('hasBeenInvited', 1);
			}

			return $this->returnOK(\Prj\Lang\Broker::getMsg('passport.success'));
		} catch (Exception $e) {
			$this->loger->target = $params['code'];
			$this->loger->sarg1  = ['redirectUri' => $params['redirectUri']];
			return $this->returnError($e->getMessage(), $e->getCode());
		}
	}

	/**
	 * 重置密码-warning
	 *
	 * hasBeenInvited:1已被邀请，0未被邀请
	 * @input integer phone 手机号
	 * @input string invalidCode 验证码
	 * @input string newPwd 密码
	 * @input string cameFrom 帐号类型
	 * @input string deviceId 唯一设备ID
	 * @input string clientId clientId
	 * @input string clientSecret clientSecret
	 * @input string redirectUri redirectUri
	 * @output {"code":200,"msg":"success"}
	 * @errors {"code":400,"smg":"****"}
	 */
	public function resetPwdAction() {
		$params = [
			'phone'        => $this->_request->get('phone'),
			'invalidCode'  => $this->_request->get('invalidCode'),
			'newPwd'       => $this->_request->get('newPwd'),
			'cameFrom'     => $this->_request->get('cameFrom'),
			'deviceId'     => $this->_request->get('deviceId'),
			'clientId'     => $this->_request->get('clientId'),
			'clientSecret' => $this->_request->get('clientSecret'),
			'redirectUri'  => $this->_request->get('redirectUri'),
		];
		if ($params['cameFrom'] != 'phone') {
			return $this->returnError(msgDefine::$define['request_parameter_incorrect']);
		}

		$_rules = [
			'phone' => [inputValidation::$define['phone'], \Prj\Lang\Broker::getMsg('passport.phone_number_is_not_valid')]
		];

		if (!empty($params['phone']) && substr($params['phone'], 3, 4) != '****') {
			if (inputValidation::validateParams(['phone' => $params['phone']], $_rules) === false) {
				return $this->returnError(inputValidation::$errorMsg);
			}
		} elseif (substr($params['phone'], 3, 4) == '****') {
			//check login
			$userIdentifier = \Sooh\Base\Session\Data::getInstance()->get('accountId');
			if ($userIdentifier){
				\Sooh\Base\Log\Data::getInstance()->userId = $userIdentifier;
				$_user = \Prj\Data\User::getCopy($userIdentifier);
				$_user->load();
				if ($_user->exists()) {
					$_phone = $_user->getField('phone');
					$params['phone'] = $_phone;
				}
			}
			if (!isset($_phone)) {
				return $this->returnError(\Prj\Lang\Broker::getMsg('passport.phone_number_is_not_valid'));
			}
		} else {
			return $this->returnError(\Prj\Lang\Broker::getMsg('passport.phone_number_is_not_valid'));
		}

		//单独验证密码
		$pwd_len = mb_strlen($params['newPwd']);
		if ($pwd_len < 6 || $pwd_len > 20) {
			return $this->returnError(\Prj\Lang\Broker::getMsg('passport.password_is_not_valid'));
		}

		$rules = [
			'invalidCode'  => [inputValidation::$define['smscode'], \Prj\Lang\Broker::getMsg('passport.smscode_is_not_valid')],
			'deviceId'     => ['#^[0-9a-zA-Z_\-:]{10,50}$#', \Prj\Lang\Broker::getMsg('passport.deviceId_is_not_valid')],
			'clientId'     => [inputValidation::$define['clientId'], \Prj\Lang\Broker::getMsg('passport.clientId_is_not_valid')],
			'clientSecret' => [inputValidation::$define['clientSecret'], \Prj\Lang\Broker::getMsg('passport.clientSecret_is_not_valid')],
		];

		if (inputValidation::validateParams($params, $rules) == false) {
			return $this->returnError(inputValidation::$errorMsg, inputValidation::$errorCode);
		}

		try {
			$ret = (new \Prj\Oauth\Oauth('', '', 'nonStandardMode'))->invokeMode(array_merge(['func' => 'resetPwd'], $params));
			//发送自定义push，透传app
			$pushContent = json_encode(['type' => 'kickout', 'msg' => '您的密码已在其他设备上修改，请重新登录！']);
			try {
				\Lib\Services\Push::getInstance()->push('all', $ret['accountId'], null, $pushContent);//旧版本
//				\Lib\Services\Push::getInstance()->push('all', $ret['accountId'], null, ['type' => 'kickout', 'msg' => '您的密码已在其他设备上修改，请重新登录！']);//标准版本
			} catch (\Exception $e) {
				error_log('push kickout error:', $e->getMessage());
			}

			//login
			$accountId = $ret['accountId'];
			$user      = \Prj\Data\User::getCopy($accountId);
			$user->load();
			if (!($user->exists())) {
				//新注册用户
				$invitationCode = $ret['invitationCode'];
				$protocol       = $ret['protocol'];
				$clientType     = $ret['clientType'];

				if (\Sooh\Base\Tests\Bomb::blowup('Passport_Login_Return_InvitationCode', false)) {
					$this->_view->assign('data', ['invitationCode' => $invitationCode]);
					return $this->returnOK('bomb');
				}

				$user = \Prj\Data\User::createNew($accountId, $ret['phone'] ? : 0, $ret['contractId'] ? : 0, $invitationCode, $protocol, $clientType);

				//发送注册红包
//				try {
//					$giveRedPacket = (new \Prj\Items\RedPacketForRegister())->give($accountId);
//				} catch (Exception $e) {
//					$this->loger->ext   = 'send redPacketForRegister error';
//					$this->loger->sarg2 = json_decode(['code' => $e->getCode(), 'msg' => $e->getMessage()]);
//				}
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
								],
								['phone' => $user->getField('phone'), 'userId' => $accountId]
							);
						} catch(\Exception $e) {
							var_log($e->getMessage(), 'Send NewRegisterRedPacket Message Error');
						}
					}
				} catch (\Exception $e) {
					$this->loger->ext = 'send NewRegisterRedPacket faild:' . $e->getMessage();
				}

				//周常-邀请注册
				try {
					$inviteByUser = $user->getField('inviteByUser');
					$weekActiveBonus = \Prj\ActivePoints\Invited::getCopy($inviteByUser)->addNum(1)->updUser();
					\Prj\Data\User::getCopy($inviteByUser)->update();
					if ($weekActiveBonus){
						\Lib\Services\Push::getInstance()->push( 'all', $inviteByUser, null, 
							json_encode($weekActiveBonus));
					}
				} catch (Exception $e) {
					$this->loger->ret = '周常-邀请领积分发送失败';
				}
			}
			$user->setField('ipLast', \Sooh\Base\Tools::remoteIP());
			$user->setField('dtLast', \Sooh\Base\Time::getInstance()->ymdhis());
			$user->update();
			$this->onLogin([
				'accountId' => $accountId,
				'nickname'  => $user->getField('nickname'),
				'pwdVer'    => $ret['pwdVer'],
				'phone'     => substr($ret['phone'], 0, 3) . '****' . substr($ret['phone'], -4),
//			    'isBind'    => $user->getField('ymdBindcard'),
				'isTradePwd' => $user->getField('tradePwd'),
			]);
			if (isset($giveRedPacket)) {
				$this->_view->assign('redPacket', $giveRedPacket);
			}

			$mineInviteTree = \Prj\Data\User::getMineInvitedTree($accountId);
			if (empty($mineInviteTree['parent'])) { //hasBeenInvited:1已被邀请，0未被邀请
				$this->_view->assign('hasBeenInvited', 0);
			} else {
				$this->_view->assign('hasBeenInvited', 1);
			}

			\Sooh\Base\Session\Data::getInstance()->set('pwdVer', $ret['pwdVer']);
			return $this->returnOK(\Prj\Lang\Broker::getMsg('passport.success'));
		} catch (Exception $e) {
			$this->returnError($e->getMessage(), $e->getCode());
		}
	}

	/**
	 * 根据cookie用户用户信息
	 * @throws ErrorException
	 * @throws \Sooh\Base\ErrException
	 */
	public function getInfoByCookieAction()
	{
		$userIdentifier = \Sooh\Base\Session\Data::getInstance()->get('accountId');
		if ($userIdentifier) {
			\Sooh\Base\Log\Data::getInstance()->userId = $userIdentifier;
			$user = \Prj\Data\User::getCopy($userIdentifier);
			$user->load();
			if ($user->exists()) {
				$info = [
					'nickname' => $user->getField('nickname'),
				    'phone' => $user->getField('phone'),
				    'accountId' => $user->getField('userId'),
				];
				$this->_view->assign('info', $info);
				return $this->returnOK('success');
			}
		}
		return $this->returnError(\Prj\Lang\Broker::getMsg('passport.no_login_or_has_timed_out'));
	}

	/**
	 * 当登入后，设置session,设置返回客户端的值
	 * @param array $accountInfo [accountId,nickname]
	 */
	protected function onLogin($accountInfo)
	{
		$sess = \Sooh\Base\Session\Data::getInstance();

		//清空当前会话的所有session
//		$sess->destroy();

		//app登入有效期7天，其他登入有效期15分钟
		$clientType = $this->_request->get('clientType') - 0;
		$dtNow = \Sooh\Base\Time::getInstance()->timestamp();
		if ($clientType === 901 || $clientType === 902 || $clientType < 900 || $clientType > 999) {
			$sess->set('accountId', $accountInfo['accountId'], 86400 * 7);
			$sess->set('loginInfo', [$dtNow,10080]);
		} else {
			$sess->set('accountId', $accountInfo['accountId'], 15 * 60);
			$sess->set('loginInfo', [$dtNow,15]);
		}
		$this->loger->deviceId = $this->_request->get('deviceId',$this->loger->deviceId);
		$sess->set('nickname', $accountInfo['nickname']);
		$sess->set('pwdVer', $accountInfo['pwdVer']);
        $isBind = \Prj\Data\BankCard::getList($accountInfo['accountId'], ['statusCode' => \Prj\Consts\BankCard::enabled]);
		$this->_view->assign('account', array(
			'accountId'  => $accountInfo['accountId'],
			'nickname'   => $accountInfo['nickname'],
			'phone'      => $accountInfo['phone'],
            'isBind'     => empty($isBind) ? 0 : 1,
			'isTradePwd' => $accountInfo['isTradePwd'] ? 1 : 0,
		));

        
// 		$this->_view->assign('accountonlogin', array(
// 		    'accountId'  => $accountInfo['accountId'],
// 		    'nickname'   => $accountInfo['nickname'],
// 		    'phone'      => $accountInfo['phone'],
// 		    'isBind'     => $accountInfo['isBind'] ? 1 : 0,
// 		    'isTradePwd' => $accountInfo['isTradePwd'] ? 1 : 0,
// 		));
		if (\Sooh\Base\Tests\Bomb::blowup('Passport_Onlogin_returnAccountInfo', false)) {
			$this->_view->assign('data', [
				'accountId' => $accountInfo['accountId'],
				'nickname'  => $accountInfo['nickname']
			]);
			return $this->returnOK('bomb');
		}

		$this->loger->isLogined = 1;
		$this->loger->userId = $accountInfo['accountId'];

		$this->user = \Prj\Data\User::getCopy($accountInfo['accountId']);
		$this->user->load();

		$showCheckinBookBonus = 1;
		$checkinBook          = \Lib\Services\CheckinBook::getInstance(\Prj\BaseCtrl::getRpcDefault('CheckinBook'));
		$this->_view->assign('checkinBook', $checkinBook->doGetTodayStatus($showCheckinBookBonus, $accountInfo['accountId'])['data']);

//		$sess->shutdown();
		$this->returnOK();
	}

	/**
	 * @return \Lib\Services\Account
	 */
	protected function getAccount()
	{
		$acc = \Lib\Services\Account::getInstance(\Prj\BaseCtrl::getRpcDefault('Account'));
		return $acc;
	}
}
