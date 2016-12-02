<?php
namespace Prj;
/**
 * 需要用户登入的ctrl
 *     提供 this->user,但只检查了是否登入，没执行load()
 */
class UserCtrl  extends \Prj\BaseCtrl {

//	protected function getFromRaw()
//	{
//		$s = file_get_contents('php://input');
//		if(!empty($s)){
//			parse_str($s,$inputs);
//			return $inputs;
//		}else{
//			return $inputs=array();
//		}
//	}
	public function init()
	{
		parent::init();
		$this->onInit_chkLogin();
	}

	protected function onInit_chkLogin()
	{
		$sessData = \Sooh\Base\Session\Data::getInstance();
		$userIdentifier = $sessData->get('accountId');
		if ($userIdentifier){
			$loginInfo = $sessData->get('loginInfo');
			if(!empty($loginInfo)){
				$dtNow=\Sooh\Base\Time::getInstance()->timestamp();
				$passed = $dtNow-$loginInfo[0];
				$minute = $loginInfo[1];

				if($passed>=($minute/4*60)){//过有效期的1/4了，，刷新过期时间
					$sessData->set('accountId', $userIdentifier, 60*$minute);
					$sessData->set('loginInfo', [$dtNow,$minute]);

                    //更新oauth的access_token和refresh_token
                    (new \Prj\Oauth\Oauth('', '', 'nonStandardMode'))->refreshToken();
				}

			}
			
			\Sooh\Base\Log\Data::getInstance()->userId = $userIdentifier;
			$this->user = \Prj\Data\User::getCopy($userIdentifier);
		}else{
			throw new \ErrorException(\Prj\ErrCode::errNotLogin,401);
		}
	}

	/**
	 * 检查密码版本号
	 * @return bool true表示同步，false表示不同步需要重新登录
	 * @author LTM <605415184@qq.com>
	 */
	public function checkPwdVer() {
		$pwdVer = \Sooh\Base\Session\Data::getInstance()->get('pwdVer');
		error_log('=============================START CHECK PWD VERSIOIN=============================');
		error_log('local pwdVer from session:' . $pwdVer);
		try {
			error_log('start get resource from Oauth');
			$resource = (new \Prj\Oauth\Oauth('', '', ''))->getResource();
			var_log($resource, 'resource from Oauth:');
			error_log('=============================CHECK PWD RET:' . ($pwdVer == $resource['pwdVer'] ? 'true' : 'false') . '=============================');
			if ($pwdVer == $resource['pwdVer']) {
				return true;
			} else {
				return false;
			}
		} catch (\Exception $e) {
			$this->loger->ret = $e->getMessage();
			$this->loger->ext = $e->getCode();
			return false;
		}
	}


    /**
     * 返回用户的可用券
     * By Hand  $amount 输入金额 非 实投金额
     */
    protected function _myVouchers($waresId='',$voucherType = [],$orderBy = '' , $amount = 0)
    {
        if(empty($orderBy))$orderBy = 'rsort timeCreate';
        $redPacket = [];
        $voucher = [];
        $this->user->load();
        $userId = $this->user->userId;
        $user = $this->user;
        $o = \Prj\Data\Vouchers::getCopy($userId);
        $db = $o->db();
        $tb = $o->tbname();

        if(!empty($voucherType)) {
	        $where = [
		        'userId'        => $userId,
		        'voucherType'   => $voucherType,
		        'statusCode'    => \Prj\Consts\Voucher::status_unuse,
		        'limitsAmount[' => $amount,
		        'dtExpired>'    => \Sooh\Base\Time::getInstance()->ymdhis(),
	        ];
        } else {
	        $where = [
		        'userId'        => $userId,
		        'statusCode'    => \Prj\Consts\Voucher::status_unuse,
		        'limitsAmount[' => $amount,
		        'dtExpired>'    => \Sooh\Base\Time::getInstance()->ymdhis(),
	        ];
        }

        $rs = $db->getRecords($tb,'*',$where,$orderBy);
        foreach($rs as $k=>$v)
        {
            $limit = [
                'limitsShelf'=>$v['limitsShelf'],
                'limitsType'=>$v['limitsType'],
                'limitsTag'=>$v['limitsTag'],
                'limitsAmount'=>$amount,
                'limitsDeadline'=>$v['limitsDeadline'],
            ];
            try{
                if(!empty($waresId) && !\Prj\Data\Vouchers::checkLimit($waresId,$limit))
                {
                    unset($rs[$k]);
                    continue;
                }
            }catch (\ErrorException $e){
                unset($rs[$k]);
                continue;
            }

            if($v['voucherType']==\Prj\Consts\Voucher::type_real)
            {
                $redPacket[] = $v;
            }
            else
            {
                $voucher[] = $v;
            }
        }
        $new['redPacketList'] = $redPacket;
        $new['voucherList'] = $voucher;
        return $new;
    }

	/**
	 * 验证支付密码
	 * @param string $paypwd 支付密码
	 * @return bool true
	 * @throws \ErrorException
	 * @throws \Exception
	 * @throws \Sooh\Base\Exception
	 * @author LTM <605415184@qq.com>
	 */
    protected function _checkPaypwd($paypwd)
    {
        $user = $this->user;
        $user->load();
        $tradePwd = $user->getField('tradePwd');
        $salt = $user->getField('salt');
	    $dt = \Sooh\Base\Time::getInstance();
	    $nowTimestamp = $dt->timestamp();
		$failedForbidden = $user->getField('failedForbidden');

	    if (!empty($failedForbidden)) {
		    if ($failedForbidden['forbidden'] == 1 && $nowTimestamp <= $failedForbidden['forbiddenExpires']) {
			    return ['msg' => '您的密码已被冻结，请重置支付密码', 'errorCount' => 5];
		    }

		    if ($tradePwd !== md5($paypwd . $salt)) {
			    if (!empty($failedForbidden['errorCount'])) {
				    if ($nowTimestamp <= $failedForbidden['errorExpires']) {
					    $errorCount = $failedForbidden['errorCount'] + 1;
				    } else {
					    $errorCount = 1;
				    }
			    } else {
				    $errorCount = 1;
			    }

			    if ($errorCount >= 5) {
				    $dbFailed = [
					    'forbidden'        => 1,
					    'forbiddenExpires' => $nowTimestamp + 2 * 3600,
					    'errorExpires'     => 0,
					    'errorCount'       => 0,
				    ];
				    $user->setField('failedForbidden', $dbFailed);
				    $user->update();
				    return ['msg' => '您的密码已被冻结，请重置支付密码', 'errorCount' => $errorCount];
			    }

			    $dbFailed = [
				    'forbidden'        => 0,//是否锁定
				    'forbiddenExpires' => 0,//锁定结束时间
				    'errorExpires'     => $nowTimestamp + 1800,//错误次数时间
				    'errorCount'       => $errorCount,//错误次数
			    ];
			    $user->setField('failedForbidden', $dbFailed);
			    $user->update();

			    switch ($errorCount) {
				    case 1:
				    case 2:
					    $msg = '密码输入错误';
					    break;
				    case 3:
					    $msg = '密码输入错误，请重置密码或重新输入';
					    break;
				    case 4:
					    $msg = '密码输入错误，还可以输入1次';
					    break;
				    default:
					    $msg = '您的密码已被冻结，请重置支付密码';
					    break;
			    }
			    return ['msg' => $msg, 'errorCount' => $errorCount];
		    }
	    } else {
		    if ($tradePwd !== md5($paypwd . $salt)) {
			    $dbFailed = [
				    'forbidden'        => 0,//是否锁定
				    'forbiddenExpires' => 0,//锁定结束时间
				    'errorExpires'     => $nowTimestamp + 1800,//错误次数时间
				    'errorCount'       => 1,//错误次数
			    ];
			    $user->setField('failedForbidden', $dbFailed);
			    $user->update();

			    return ['msg' => '密码输入错误', 'errorCount' => 1];
		    }
	    }

	    $dbFailed = [
		    'forbidden'        => 0,//是否锁定
		    'forbiddenExpires' => 0,//锁定结束时间
		    'errorExpires'     => 0,//错误次数时间
		    'errorCount'       => 0,//错误次数
	    ];
	    $user->setField('failedForbidden', $dbFailed);
	    $user->update();

	    return true;
    }

	/**
	 * 重置支付密码
	 * @param integer $oldPwd 旧密码
	 * @param integer $newPwd 新密码
	 * @return array|bool
	 * @throws \ErrorException
	 * @throws \Exception
	 */
	protected function _resetPaypwd($oldPwd, $newPwd)
	{
		$this->user->load();
		$oldSalt = $this->user->getField('salt');
		$_oldPwd = $this->user->getField('tradePwd');
		$nowTimestamp = \Sooh\Base\Time::getInstance()->timestamp();
		$failedForbidden = $this->user->getField('failedForbidden');

		if (!empty($failedForbidden)) {
			if ($failedForbidden['forbidden'] == 1 && $nowTimestamp <= $failedForbidden['forbiddenExpires']) {
				return ['msg' => '您的密码已被冻结，请稍后重试或联系客服', 'errorCount' => 5];
			}
		}

		if (md5($oldPwd . $oldSalt) != $_oldPwd) {
			if (!empty($failedForbidden)) {
				if ($failedForbidden['forbidden'] == 1) {
					$dbFailed = [
						'forbidden'        => 1,
						'forbiddenExpires' => $nowTimestamp + 2 * 3600,
						'errorExpires'     => 0,
						'errorCount'       => 0,
					];
					$this->user->setField('failedForbidden', $dbFailed);
					$this->user->update();
					$this->_view->assign('errorCount', 5);
					return ['msg' => '您的密码已被冻结，请重置支付密码', 'errorCount' => 5];
				}

				if (!empty($failedForbidden['errorCount'])) {
					if ($nowTimestamp <= $failedForbidden['errorExpires']) {
						$errorCount = $failedForbidden['errorCount'] + 1;
					} else {
						$errorCount = 1;
					}
				} else {
					$errorCount = 1;
				}

				if ($errorCount >= 5) {
					$dbFailed = [
						'forbidden'        => 1,
						'forbiddenExpires' => $nowTimestamp + 2 * 3600,
						'errorExpires'     => 0,
						'errorCount'       => 0,
					];
					$this->user->setField('failedForbidden', $dbFailed);
					$this->user->update();
					return ['msg' => '您的密码已被冻结，请重置支付密码', 'errorCount' => $errorCount];
				}

				$dbFailed = [
					'forbidden'        => 0,//是否锁定
					'forbiddenExpires' => 0,//锁定结束时间
					'errorExpires'     => $nowTimestamp + 1800,//错误次数时间
					'errorCount'       => $errorCount,//错误次数
				];
				$this->user->setField('failedForbidden', $dbFailed);
				$this->user->update();

				switch ($errorCount) {
					case 1:
					case 2:
						$msg = '密码输入错误';
						break;
					case 3:
						$msg = '密码输入错误，请重置密码或重新输入';
						break;
					case 4:
						$msg = '密码输入错误，还可以输入1次';
						break;
					default:
						$msg = '您的密码已被冻结，请重置支付密码';
						break;
				}
				return ['msg' => $msg, 'errorCount' => $errorCount];
			} else {
				$dbFailed = [
					'forbidden'        => 0,//是否锁定
					'forbiddenExpires' => 0,//锁定结束时间
					'errorExpires'     => $nowTimestamp + 1800,//错误次数时间
					'errorCount'       => 1,//错误次数
				];
				$this->user->setField('failedForbidden', $dbFailed);
				$this->user->update();

				return ['msg' => '密码输入错误', 'errorCount' => 1];
			}
		} else {
			$salt = substr(uniqid(), -4);
			$dbFailed = [
				'forbidden'        => 0,//是否锁定
				'forbiddenExpires' => 0,//锁定结束时间
				'errorExpires'     => 0,//错误次数时间
				'errorCount'       => 0,//错误次数
			];

			$this->user->setField('tradePwd', md5($newPwd . $salt));
			$this->user->setField('salt', $salt);
			$this->user->setField('failedForbidden', $dbFailed);
			$this->user->update();
			return true;
		}
	}

    /**
     * 检查支付密码是否冻结
     * @return bool
     * @throws \ErrorException
     */
    protected function _isPayPwdFreeze()
    {
        $user = $this->user;
        $user->load();
        $dt = \Sooh\Base\Time::getInstance();
        $nowTimestamp = $dt->timestamp();
        $failedForbidden = $user->getField('failedForbidden');

        if (!empty($failedForbidden)) {
            if ($failedForbidden['forbidden'] == 1 && $nowTimestamp <= $failedForbidden['forbiddenExpires']) {
                return true;
            }
        }
        return false;
    }

	/**
	 * 获取秒数的自然语言描述
	 * @param integer $seconds 秒数
	 * @return string
	 * @author LTM <605415184@qq.com>
	 */
	protected function secondsToStr($seconds) {
		$str = '';
		if ($seconds >= 86400) {
			$str = intval($seconds / 86400) . '天';
			$seconds = $seconds % 86400;
		}if ($seconds >= 3600) {
			$str .= intval($seconds / 3600) . '小时';
			$seconds = $seconds % 3600;
		}if ($seconds >= 60) {
			$str .= intval($seconds / 60) . '分钟';
			$seconds = $seconds % 60;
		}if ($seconds > 0) {
			$str .= $seconds . '秒';
		}
		return $str;
	}

    protected $logMark = ''; //日志标记

    protected $logRand = 0; //日志随机数

    protected function varLog($msg){
        if($this->logRand == 0)$this->logRand = mt_rand(1000,9999);
        error_log("#varlog#[$this->logRand]".$this->logMark.'#'.$msg);
    }

	/**
	 *
	 * @var \Prj\Data\User
	 */
	protected $user=null;
	
}
