<?php

use Lib\Misc\InputValidation as inputValidation;

/**
 * 公开可用接口
 */
class PublicController extends \Prj\BaseCtrl
{
    protected $appId     = 'wxeb292725a9b34381';
    protected $appSecret = '87c8e5ce89d7cdbc76bc2accc3034259';

    protected $clientId     = '1104878344';
    protected $clientSecret = 's20vH9emKJ6BmT1Q';

    /**
     * 支持的银行卡
     * By Hand
     */
    public function banksAction()
    {
        $rs = [];
        foreach (\Prj\Consts\Banks::$enums as $k => $v) {
            \Sooh\Base\Ini::getInstance()->viewRenderType('json');
            $tmp['bankId']   = $k;
            $tmp['bankName'] = $v[0];
            $tmp['exp']      = '单笔限额' . $v[1] . '元/当日限额' . $v[2] . '元';
            $tmp['icon']     = 'http://epaper.ts.cn/ftp/site1/xjdsb/res/1/1/2014-09/26/C04/res01_attpic_brief.jpg';
            $rs[]            = $tmp;
        }
        $this->_view->assign('list', $rs);//出现多个assign中的list字段重复，修改如下突出唯一识别
        $this->_view->assign('listbanks', $rs);
        $this->returnOK();
    }

    /**
     * 获取图片
     * By Hand
     */
    public function getImageAction()
    {
        $this->ini->viewRenderType('echo');
        $fileId = $this->_request->get('fileId');
        $data   = \Prj\Data\Files::getDataById($fileId);
        header('Content-type: image/jpg');
        $cdnSever = \Prj\Data\Config::get('IMG_CDN_SERVER');
        if ($data['urlCdn'] && $cdnSever) {
            error_log('###img go cdn...');
            $cdnUrl = $cdnSever . '/uploadfile/app/wares/' . $data['urlCdn'];
            error_log('###cdnUrl:'.$cdnUrl);
            echo file_get_contents($cdnUrl);
        } elseif ($data['url']) {
            echo file_get_contents(APP_PATH . '/public' . $data['url']);
        } else {
            echo $data['data'];
        }
    }

    /**
     * 意见反馈
     * @input string deviceId 唯一设备ID
     * @input string content 反馈内容
     * @output {"code":200,"msg":"感谢您的反馈，我们会尽快处理的！"}
     * @error {"code":400,"msg":"****"}
     */
    public function feedbackAction()
    {
        $params = [
            'deviceId' => $this->_request->get('deviceId'),
            'content'  => $this->_request->get('content'),
        ];
        if (inputValidation::validateParams($params, ['deviceId' => ['#^[0-9a-zA-Z_\-:]+$#', '服务器忙']]) === false) {
            $this->returnError(inputValidation::$errorMsg);
            exit();
        }

        $userId = \Sooh\Base\Session\Data::getInstance()->get('accountId');

        try {
            $feedbackId = \Prj\Data\Feedback::createNew($params['deviceId'], $params['content'], $userId ? : null);
            return $this->returnOK('感谢您的反馈，我们会尽快处理的！');
        } catch (\Exception $e) {
            return $this->returnError($e->getMessage(), $e->getCode());
        }
    }

    /**
     * 分享的红包-废弃（不再使用）
     * 400001 此券不存在
     * 301001 已经领取过
     * @input  integer type 1,2,3,4,5,6
     * @input  string id 红包ID
     * @error {"code":400,"msg":"sorry_there_is_not_exist"}
     * @author LTM <605415184@qq.com>
     */
    public function shareAction()
    {
        $openid = $this->authorizeForWechat();

        $dbWechatBindPhone = \Prj\Data\WechatBindPhone::getCopy($openid);
        $dbWechatBindPhone->load();
        if ($dbWechatBindPhone->exists()) {
            $phone  = $dbWechatBindPhone->getField('phone');
            $userId = $dbWechatBindPhone->getField('userId');
        }

        $params = [
            'id' => $this->_request->get('id'),
        ];

        if (!isset($params['id'])) {
            //return $this->returnError('此券不存在', 400001);
            return $this->returnError(\Prj\Lang\Broker::getMsg('public.voucher_missing'));
        }

        try {
            $voucher = \Prj\Data\Vouchers::getCopy($params['id']);
            $voucher->load();
            $oldReceive = 0;
            if ($voucher->exists()) {
                if (!empty($userId)) {
                    //是否领取过
                    $_map = [
                        'uniqueId'    => $openid,
                        'pid'         => $params['id'],
                        'voucherType' => \Prj\Consts\Voucher::type_real
                    ];
                    $_ret = \Prj\Data\Vouchers::findOne($_map);

                    if ($_ret) {
                        $this->_view->assign('amount', $_ret['amount'] / 100);
                        $this->_view->assign('dtExpired', date('Y年m月d日 H:i:s', strtotime($_ret['dtExpired'])));
                        $this->_view->assign('phone', $phone);
                        $oldReceive = 1;
                    } else {
                        //领取
                        $childVoucher = (new \Prj\Items\RedPacketForShare())->giveChild($openid, $params['id']);
                        if (is_array($childVoucher)) {
                            $this->_view->assign('amount', $childVoucher['amount'] / 100);
                            $this->_view->assign('dtExpired', date('Y年m月d日 H:i:s', $childVoucher['dtExpired']));
                            $this->_view->assign('phone', $phone);
                        }
                        $oldReceive = 1;
                    }
                }

                //领取列表
                $pager = new \Sooh\DB\Pager(1000);
                $list  = \Prj\Data\Vouchers::getReceiveList($params['id'], $pager, 1);

                $totalCount = \Prj\Data\VouchersInterim::loopGetRecordsCount(['pid' => $params['id']]);
                $listCount  = count($list);

                if ($list) {
                    foreach ($list as $v) {
                        $arrAmount[] = $v['amount'];
                    }
                    $maxAmount  = max($arrAmount);
                    $wechatInfo = null;
                    foreach ($list as &$v) {
                        if ($listCount == $totalCount) {
                            if ($v['amount'] == $maxAmount) {
                                $v['isMax'] = 1;
                            }
                        }

                        $v['datetime'] = date('Y-m-d  H:i:s', strtotime($v['timeCreate']));
                        $wechatInfo    = \Prj\Data\WechatUserinfo::getInfoByUserid($v['userId']);
                        if ($wechatInfo) {
                            $v['nickname'] = base64_decode($wechatInfo->getField('nickname'));
                            $v['pic']      = $wechatInfo->getField('headimgurl');
                        } else {
                            $v['nickname'] = $this->randName();
                            $v['pic']      = \Prj\Data\Config::get('WECHAT_EMPTY_PIC');
                        }
                    }
                    $this->_view->assign('list', $list);//出现多个assign中的list字段重复，修改如下突出唯一识别
                    $this->_view->assign('listshare', $list);
                }

                return $this->returnOK('success', $oldReceive ? 301001 : 200);
            } else {
                return $this->returnError(\Prj\Lang\Broker::getMsg('public.voucher_missing'));
            }
        } catch (\Exception $e) {
            return $this->returnError($e->getMessage(), $e->getCode());
        }
    }

    /**
     * 检查券状态
     * @input  string id voucherId
     * @throws ErrorException
     * @output 状态码对应的含义如下<br />
     *  200     未绑定帐号<br />
     *  201001  红包已经领完<br />
     *  201002  红包已经过期<br />
     *  201003  您已经领取过了<br />
     *  201004  领过太多红包了<br />
     *  301001  您已经领取过了<br />
     *  400001  券不存在<br />
     *  400     其他错误
     */
    public function checkVoucherAction()
    {
        error_log('>>>>>>>>>>>>>>>>>>>>BEFORE checkVoucher');
        //		$this->checkWechatBrowser(true);
        //
        //		if (($openid = $this->getOpenid()) == false) {
        //			if (($openid = $this->authorizeCode()) == false) {
        //				return $this->returnError(\Prj\Lang\Broker::getMsg('public.cant_found_openid'));
        //			}
        //		}
        //
        $params = ['id' => $this->_request->get('id')];
        if (!isset($params['id'])) {
            return $this->returnError(\Prj\Lang\Broker::getMsg('public.voucher_missing'));
        }
        //
        //		if (($authorizeOpenid = $this->authorizeOpenid($openid))) {
        //			list($phone, $userId) = $authorizeOpenid;
        //		} else {
        //			$userId = '';
        //			$phone = '';
        //		}

        $sessPhone = $_COOKIE['phone'];
        if (!empty($sessPhone)) {
            $oauthMap = [
                'func'     => 'checkReg',
                'phone'    => $sessPhone,
                'cameFrom' => 'phone',
            ];
            try {
                $oauthRet = (new \Prj\Oauth\Oauth('', '', 'nonStandardMode'))->invokeMode($oauthMap);
                setcookie('phone', $sessPhone, strtotime('+30 days'), '/', \Sooh\Base\Ini::getInstance()->cookieDomain());//保存一个月
                $userId = $oauthRet['accountId'];
                $phone  = $sessPhone;
            } catch (\Exception $e) {
                //未注册
                return $this->returnError(\Prj\Lang\Broker::getMsg('passport.phone_number_is_not_valid'));
                //				error_log('checkVoucher not found phone');
            }
        } else {
            $userId = '';
            $phone  = '';
        }

        $phone  = $lastPhone;
        $userId = $lastUserId;

        try {
            $voucher = \Prj\Data\Vouchers::getCopy($params['id']);
            $voucher->load();
            $oldReceive = 0;
            if ($voucher->exists()) {
                error_log('voucher exists');
                if (!empty($userId)) {
                    error_log('user exists');
                    //是否领取过
                    $_map = [
                        //						'uniqueId' => $openid,
                        'userId'      => $userId,
                        'pid'         => $params['id'],
                        'voucherType' => \Prj\Consts\Voucher::type_real
                    ];
                    $_ret = \Prj\Data\Vouchers::findOne($_map);

                    if ($_ret) {
                        $this->_view->assign('amount', $_ret['amount'] / 100);
                        $this->_view->assign('dtExpired', date('Y年m月d日 H:i:s', strtotime($_ret['dtExpired'])));
                        $this->_view->assign('phone', $phone);
                        $oldReceive = 1;
                    } else {
                        //领取
                        //						$childVoucher = (new \Prj\Items\RedPacketForShare())->giveChild($openid, $params['id']);
                        $childVoucher = (new \Prj\Items\RedPacketForShare())->giveChild($userId, $params['id']);
                        if (is_array($childVoucher)) {
                            $this->_view->assign('amount', $childVoucher['amount'] / 100);
                            $this->_view->assign('dtExpired', date('Y年m月d日 H:i:s', $childVoucher['dtExpired']));
                            $this->_view->assign('phone', $phone);
                        }
                        $oldReceive = 1;
                    }
                }

                //领取列表
                $pager = new \Sooh\DB\Pager(1000);
                $list  = \Prj\Data\Vouchers::getReceiveList($params['id'], $pager, 1);

                $totalCount = \Prj\Data\VouchersInterim::loopGetRecordsCount(['pid' => $params['id']]);
                $listCount  = count($list);

                if ($list) {
                    foreach ($list as $v) {
                        $arrAmount[] = $v['amount'];
                    }
                    $maxAmount  = max($arrAmount);
                    $wechatInfo = null;
                    foreach ($list as &$v) {
                        if ($listCount == $totalCount) {
                            if ($v['amount'] == $maxAmount) {
                                $v['isMax'] = 1;
                            }
                        }

                        $v['amount'] = sprintf('%.2f', $v['amount'] / 100);

                        $v['datetime'] = date('Y-m-d  H:i:s', strtotime($v['timeCreate']));
                        //						$wechatInfo = \Prj\Data\WechatUserinfo::getInfoByUserid($v['userId']);
                        //						if ($wechatInfo) {
                        //							$v['nickname'] = base64_decode($wechatInfo->getField('nickname'));
                        //							$v['pic'] = $wechatInfo->getField('headimgurl');
                        //						} else {
                        //							$v['nickname'] = $this->randName();
                        //							$v['pic'] = \Prj\Data\Config::get('WECHAT_EMPTY_PIC');
                        //						}

                        $_user = \Prj\Data\User::getCopy($v['userId']);
                        $_user->load();
                        if ($_user->exists()) {
                            $v['nickname'] = substr_replace($_user->getField('phone'), '****', 3, 4);
                        } else {
                            $v['nickname'] = '170****' . mt_rand(1000, 9999);
                        }
                    }
                    $this->_view->assign('list', $list);
                }

                return $this->returnOK('success', $oldReceive ? 301001 : 200);
            } else {
                return $this->returnError(\Prj\Lang\Broker::getMsg('public.voucher_missing'));
            }
        } catch (\Exception $e) {
            return $this->returnError($e->getMessage(), $e->getCode());
        }
    }

    /**
     * 领取红包
     * 通过手机号+券ID 或者手机号+验证码+券ID领取红包
     * 返回值：code状态码含义：
     *      code:200:success
     *      code:201001:红包已经领完
     *      code:201002:红包已经过期
     *      code:201003:您已经领取过了
     *      code:201004:领过太多红包了
     *      code:202001:发送短信验证码成功了
     *      code:202002:发送短信验证码失败了
     * 当需要返回红包信息时：
     * amount：红包金额
     * dtExpired：过期时间
     * @input integer id 券ID
     * @input integer phone 手机号
     * @input string cameFrom cameFrom，可不传，默认为phone
     * @input integer smscode 短信验证码，没有时不传
     * @output {'code':200,'amount':23.32,'dtExpired':'2016年3月3日 12:12:12'}
     * @errors {'code':400,'msg':'error'}
     * @return mixed code:200:success
     *               code:201001:红包已经领完
     *               code:201002:红包已经过期
     *               code:201003:您已经领取过了
     *               code:201004:领过太多红包了
     *               code:202001:发送短信验证码成功了
     *               code:202002:发送短信验证码失败了
     */
    public function receiveVoucherAction()
    {
//        $openId = $this->authorizeForWechat();
//        if (empty($openId)) {
//            return $this->returnError(\Prj\Lang\Broker::getMsg('user.voucher_missing'));
//        }

        $params = [
            'id'       => $this->_request->get('id'),
            'phone'    => $this->_request->get('phone'),
            'cameFrom' => $this->_request->get('cameFrom', 'phone'),
            'smscode'  => $this->_request->get('smscode'),
        ];

        $rules = [
            'phone' => [\Lib\Misc\InputValidation::$define['phone'], '手机号不合法'],
            'id'    => ['#^\d{18,20}$#', '对不起，此券不存在'],
        ];

        if (\Lib\Misc\InputValidation::validateParams($params, $rules) === false) {
            return $this->returnError(\Lib\Misc\InputValidation::$errorMsg);
        }

        try {
            $voucher = \Prj\Data\Vouchers::getCopy($params['id']);
            $voucher->load();
            if ($voucher->exists()) {
                try {
                    $_map = [
                        'func'     => 'checkReg',
                        'phone'    => $params['phone'],
                        'cameFrom' => $params['cameFrom']
                    ];
                    $ret  = (new \Prj\Oauth\Oauth('', '', 'nonStandardMode'))->invokeMode($_map);
                } catch (\Exception $e) {
                    //未注册
                    if (!empty($params['smscode'])) {
                        if (\Sooh\DB\Cases\SMSCode::getCopy($params['phone'])->chkCode($params['smscode']) == true) {
                            //注册
                            $oauthMap = [
                                'func'         => 'quickReg',
                                'phone'        => $params['phone'],
                                'smscode'      => $params['smscode'],
                                'clientId'     => $this->clientId,
                                'clientSecret' => $this->clientSecret,
                                'clientType'   => \Prj\Consts\ClientType::weixin,
                            ];
                            $quickReg = (new \Prj\Oauth\Oauth('', '', 'nonStandardMode'))->invokeMode($oauthMap);

                            $_oauthMap = [
                                'func'  => 'userInfo',
                                '_cmd_' => ['accessToken' => ''],
                            ];
                            $resources = (new \Prj\Oauth\Oauth('', '', 'nonStandardMode'))->invokeMode($_oauthMap);
//                            $resources = (new \Prj\Oauth\Oauth($quickReg['code'], $quickReg['redirectUri']))->getResource();
                            $accountId = $resources['accountId'];

                            $user = \Prj\Data\User::getCopy($accountId);
                            $user->load();
                            if (!($user->exists())) {
                                //本地新注册用户
                                $invitationCode = $resources['invitationCode'];
                                $protocol       = $resources['protocol'];
                                $clientType     = $resources['clientType'];

                                //本地用户注册
                                $user = \Prj\Data\User::createNew($accountId, $resources['phone'] ? : 0, $resources['contractId'] ? : 0, $invitationCode, $protocol, $clientType);

                                setcookie('phone', $params['phone'], strtotime('+30 days'), '/', \Sooh\Base\Ini::getInstance()->cookieDomain());//保存一个月
                                //发送注册红包
//                                try {
//                                    $giveRedPacket = (new \Prj\Items\RedPacketForRegister())->give($accountId);
//                                } catch (\Exception $e) {
//                                    $this->loger->ext = 'send redPacketForRegister error';
//                                    $this->loger->sarg2 = json_encode(['code' => $e->getCode(), 'msg' => $e->getMessage()]);
//                                }
                                try {
                                    $itemGiverReg   = new \Prj\Items\ItemGiver($accountId);
                                    $_finalItemsReg = $itemGiverReg->add('NewRegisterRedPacket', 1)->give();
                                    if (empty($_finalItemsReg)) {
//                                        $this->loger->ext = $itemGiverReg->getLastError();
                                        error_log('send new register redpacket fail');
                                    } else {
                                        //发送首次登陆红包
                                        $this->loger->sarg1 = json_encode([
                                            [
                                                'event'        => 'red_loging_packet',
                                                'brand'        => \Prj\Message\Message::MSG_BRAND,
                                                'num_packet'   => 1,
                                                'private_gift' => sprintf('%.2f', $_finalItemsReg[0][1] / 100),
                                                'num_deadline' => 48,
                                            ],
                                            ['phone' => $user->getField('phone'), 'userId' => $accountId]
                                        ]);
//                                        try {
//                                            \Prj\Message\Message::run(
//                                                [
//                                                    'event'        => 'red_loging_packet',
//                                                    'brand'        => \Prj\Message\Message::MSG_BRAND,
//                                                    'num_packet'   => 1,
//                                                    'private_gift' => sprintf('%.2f', $_finalItemsReg[0][1] / 100),
//                                                    'num_deadline' => 48,
//                                                ],
//                                                ['phone' => $user->getField('phone'), 'userId' => $accountId]
//                                            );
//                                        } catch (\Exception $e) {
//                                            var_log($e->getMessage(), 'Send NewRegisterRedPacket Message Error');
//                                        }
                                    }
                                } catch (\Exception $e) {
//                                    $this->loger->ext = 'send NewRegisterRedPacket faild:' . $e->getMessage();
                                    error_log('send NewRegisterRedPacket faild:' . $e->getMessage());
                                }

                                //周常-邀请注册
                                try {
                                    $inviteByUser = $user->getField('inviteByUser');
                                    if (!empty($inviteByUser)) {
                                        $weekActiveBonus = \Prj\ActivePoints\Invited::getCopy($inviteByUser)->addNum(1)->updUser();
                                        \Prj\Data\User::getCopy($inviteByUser)->update();
                                        if ($weekActiveBonus) {
//                                            \Lib\Services\Push::getInstance()->push('all', $inviteByUser, null,
//                                                json_encode($weekActiveBonus));
                                            $this->loger->sarg2 = json_encode(['all', $inviteByUser, null, json_encode($weekActiveBonus)]);
                                        }
                                    }
                                } catch (Exception $e) {
//                                    $this->loger->ret = '周常-邀请领积分发送失败';
                                    error_log('周常-邀请领积分发送失败');
                                }
                            }

                            //wx 绑定
//                            \Prj\Data\WechatBindPhone::bandUser($openId, $params['phone'], $accountId);

                            //领取
//                            $childVoucher = (new \Prj\Items\RedPacketForShare())->giveChild($openId, $params['id']);
                            $childVoucher = (new \Prj\Items\RedPacketForShare())->giveChild($accountId, $params['id']);
                            $childId      = $childVoucher['id'];
                            $_tmp         = \Prj\Data\Vouchers::getCopy($childId);
                            $_tmp->load();
                            if ($_tmp->exists()) {
                                $this->_view->assign('amount', $_tmp->getField('amount') / 100);
                                $this->_view->assign('dtExpired', date('Y年m月d日  H:i:s', strtotime($_tmp->getField('dtExpired'))));
                                $this->_view->assign('isReg', 1);
                                return $this->returnOK('success');
                            }
                        } else {
                            return $this->returnError(\Prj\Lang\Broker::getMsg('public.smscode_not_correct'));
                        }
                    } else {
                        //发送注册短信
                        $this->loger->sarg3 = $params['phone'];
                        return $this->returnOK(\Prj\Consts\MsgDefine::$define['send_success'], 202001);
//                        $smsCode = mt_rand(100000, 999999);
//                        $ret     = \Prj\Message\Message::run(
//                            ['event' => 'reg_name', 'brand' => '小虾理财', 'num_num' => $smsCode, 'num_time' => 15],
//                            ['phone' => $params['phone'], 'smsCode' => $smsCode]
//                        );
//                        if ($ret['sms'] === true) {
//                            return $this->returnOK(\Prj\Consts\MsgDefine::$define['send_success'], 202001);
//                        } else {
//                            return $this->returnError(\Prj\Consts\MsgDefine::$define['send_error'], 202002);
//                        }
                    }
                }

                setcookie('phone', $params['phone'], strtotime('+30 days'), '/', \Sooh\Base\Ini::getInstance()->cookieDomain());//保存一个月

                $userId = $ret['accountId'];
//                \Prj\Data\WechatBindPhone::bandUser($openId, $params['phone'], $userId);

                //是否领取过
                $__map = [
                    //					'uniqueId'    => $openId,
                    'userId'      => $userId,
                    'pid'         => $params['id'],
                    'voucherType' => \Prj\Consts\Voucher::type_real
                ];
                $__ret = \Prj\Data\Vouchers::findOne($__map);

                if ($__ret) {
                    $this->_view->assign('amount', $__ret['amount'] / 100);
                    $this->_view->assign('dtExpired', date('Y年m月d日 H:i:s', strtotime($__ret['dtExpired'])));
                    return $this->returnOK('success', 201003);
                } else {
                    //already reg
                    //					$childVoucher = (new \Prj\Items\RedPacketForShare())->giveChild($openId, $params['id']);
                    $childVoucher = (new \Prj\Items\RedPacketForShare())->giveChild($userId, $params['id']);
                    //					$childId      = $childVoucher['id'];
                    //					$_tmp         = \Prj\Data\Vouchers::getCopy($childId);
                    //					$_tmp->load();
                    //					if ($_tmp->exists()) {
                    $this->_view->assign('amount', $childVoucher['amount'] / 100);
                    $this->_view->assign('dtExpired', date('Y年m月d日 H:i:s', $childVoucher['dtExpired']));
                    return $this->returnOK('success');
                    //					}
                }
            }
        } catch (\Exception $e) {
            return $this->returnError($e->getMessage(), $e->getCode());
        }
        return $this->returnError(\Prj\Lang\Broker::getMsg('public.voucher_missing'));
    }

    /**
     * 红包领取列表
     * @input string id 券ID
     */
    public function getVoucherListAction()
    {
        $params = ['id' => $this->_request->get('id')];
        if (!empty($params['id'])) {
            try {
                $voucher = \Prj\Data\Vouchers::getCopy($params['id']);
                $voucher->load();
                $oldReceive = 0;
                if ($voucher->exists()) {
                    //领取列表
                    $pager = new \Sooh\DB\Pager(1000);
                    $list  = \Prj\Data\Vouchers::getReceiveList($params['id'], $pager, 1);

                    $totalCount = \Prj\Data\VouchersInterim::loopGetRecordsCount(['pid' => $params['id']]);
                    $listCount  = count($list);

                    if ($list) {
                        foreach ($list as $v) {
                            $arrAmount[] = $v['amount'];
                        }
                        $maxAmount  = max($arrAmount);
                        $wechatInfo = null;
                        $_list      = [];
                        foreach ($list as $k => $v) {
                            if ($listCount == $totalCount) {
                                if ($v['amount'] == $maxAmount) {
                                    $_list[$k]['isMax'] = 1;
                                }
                            }

                            $_list[$k]['amount'] = sprintf('%.2f', $v['amount'] / 100);

                            $_list[$k]['datetime'] = date('Y-m-d  H:i:s', strtotime($v['timeCreate']));
//                            $wechatInfo = \Prj\Data\WechatUserinfo::getInfoByUserid($v['userId']);
//                            if ($wechatInfo) {
//                                $_list[$k]['nickname'] = base64_decode($wechatInfo->getField('nickname'));
//                                $_list[$k]['pic'] = $wechatInfo->getField('headimgurl');
//                            } else {
//                                $_list[$k]['nickname'] = $this->randName();
//                                $_list[$k]['pic'] = \Prj\Data\Config::get('WECHAT_EMPTY_PIC');
//                            }

                            $_user = \Prj\Data\User::getCopy($v['userId']);
                            $_user->load();
                            if ($_user->exists()) {
                                $_list[$k]['nickname'] = substr_replace($_user->getField('phone'), '****', 3, 4);
                            } else {
                                $_list[$k]['nickname'] = '170****' . mt_rand(1000, 9999);
                            }
                        }
                        $this->_view->assign('list', $_list);//出现多个assign中的list字段重复，修改如下突出唯一识别
                        $this->_view->assign('listgetvoucherlist', $_list);
                    }

                    return $this->returnOK('success', $oldReceive ? 301001 : 200);
                }
            } catch (\Exception $e) {
                return $this->returnError($e->getMessage(), $e->getCode());
            }
        }
        return $this->returnError(\Prj\Lang\Broker::getMsg('public.voucher_missing'));
    }

    /**
     * wx端-修改手机号
     * @input string phone phone
     * @output {'code':200,'msg':'success'}
     * @errors {'code':400,'msg':'error'}
     */
    public function updPhoneAction()
    {
        //		$openid = $this->authorizeForWechat();
        $phone = $this->_request->get('phone');

        if (\Lib\Misc\InputValidation::validateParams(['phone' => $phone], [
                'phone' => [
                    \Lib\Misc\InputValidation::$define['phone'],
                    '手机号不合法'
                ]
            ]) === false
        ) {
            return $this->returnError(\Lib\Misc\InputValidation::$errorMsg, \Lib\Misc\InputValidation::$errorCode);
        }

        try {
            $_map = [
                'func'     => 'checkReg',
                'phone'    => $phone,
                'cameFrom' => 'phone',
            ];
            $ret  = (new \Prj\Oauth\Oauth('', '', 'nonStandardMode'))->invokeMode($_map);

//            \Prj\Data\WechatBindPhone::bandUser($openid, $phone, $ret['accountId']);

            setcookie('phone', $phone, strtotime('+30 days'), '/', \Sooh\Base\Ini::getInstance()->cookieDomain());//保存一个月

            return $this->returnOK('success');
        } catch (\Exception $e) {
            return $this->returnError($e->getMessage(), 401001);
        }
    }

    public function heartbeatAction()
    {
        $this->ini->viewRenderType('echo');
        $ret = \Sooh\DB\Broker::getInstance()->getOne('db_p2p.tb_config', 'v', ['k' => 'dbsql.ver']);
        if (!empty($ret)) {
            echo 'ok';
        }
    }

    public function donothingAction()
    {
        $this->returnOK();
    }

    /**
     * 微信网页授权调度
     * @input string code code
     * @input jumpUrl 目标地址
     */
    public function wechatAuthorizeAction()
    {
        $this->checkWechatBrowser(true);

        $jumpUrl = urldecode($this->_request->get('jumpUrl'));

        //为连接拼上时间戳字符串
        $urlQuery = parse_url($jumpUrl, PHP_URL_QUERY);
        parse_str($urlQuery, $arrUrlQuery);
        if (!isset($arrUrlQuery['randstr'])) {
            $arrUrlQuery['randstr'] = \Sooh\Base\Time::getInstance()->timestamp();
            $jumpUrl                = str_replace($urlQuery, http_build_query($arrUrlQuery), $jumpUrl);
        }

        if (($openid = $this->getOpenid()) == false) {
            if (($openid = $this->authorizeCode()) == false) {
                $baseUrl = 'https://open.weixin.qq.com/connect/oauth2/authorize';
                $params  = [
                    'appid'         => \Prj\Data\Config::get('WECHAT_APP_ID'),
                    'redirect_uri'  => $jumpUrl,
                    'response_type' => 'code',
                    'scope'         => 'snsapi_userinfo',
                    'state'         => 888,
                ];

                $url = $baseUrl . '?' . http_build_query($params) . '#wechat_redirect';

                ob_clean();
                header('Location:' . $url);
                exit();
            }
        }

        ob_clean();
        header('Location:' . urldecode($jumpUrl));
        exit();
    }

    /**
     * 空白跳转
     * @input string jumpUrl 目标地址
     */
    public function emptyGotoAction()
    {
        $jumpUrl = $this->_request->get('jumpUrl');
        header('Location:' . urldecode($jumpUrl));
    }

    /**
     * 活动页数据跟踪
     * @input  string a 手机输入框-数字型
     * @input  string b 密码输入框-数字型
     * @input  string c 短信验证码输入框-数字型
     * @input  string reged md_lastRegSuc_，一般在点击注册后触发， 初始值为0-数字型
     * @input  string sendcode _md_lastSendCode_，一般在点击获取验证码后触发， 初始值为0-数字型
     * @input  string lt md_lastForm，最后获取焦点的输入框（a?b?c），初始值为0-字符串
     * @input  string source _source，渠道号-字符串
     * @input  string channel 前端类型(901?902?903)-数字型
     * @author lyq
     */
    public function recordRegEvtAction()
    {
        \Prj\Misc\LogOfLandingPage::standTrace($this->loger, $this->_request);
        $this->_view->assign('code', 200);
    }

    /**
     * 检查是否设置过openid
     * @param string $openid openid
     * @return bool|string $openid
     */
    private function getOpenid($openid = '')
    {
        $openid = $openid ? : $_COOKIE['openid'];
        if (!empty($openid)) {
            setcookie('openid', $openid, strtotime('+30 days'), '/', \Sooh\Base\Ini::getInstance()->cookieDomain());//保存一个月
            return $openid;
        }

        return false;
    }

    /**
     * 检验openid
     * @param string $openid openid
     * @return array|bool ['phone' => $phone, ['userId' => $userId]
     * @throws ErrorException
     */
    private function authorizeOpenid($openid)
    {
        $dbWechatBindPhone = \Prj\Data\WechatBindPhone::getCopy($openid);
        $dbWechatBindPhone->load();
        if ($dbWechatBindPhone->exists()) {
            $phone  = $dbWechatBindPhone->getField('phone');
            $userId = $dbWechatBindPhone->getField('userId');
            return [$phone, $userId];
        }

        return false;
    }

    /**
     * 校验临时码code
     * @param string $code code
     * @return bool|string $openid
     */
    private function authorizeCode($code = '')
    {
        $code = $code ? : $this->_request->get('code');

        if (!empty($code)) {
            $wechatAuth = new \Lib\WX\WechatAuth(\Prj\Data\Config::get('WECHAT_APP_ID'), \Prj\Data\Config::get('WECHAT_APP_SECRET'));
            $openid     = $wechatAuth->saveUserinfoFromCode($code);
            if ($openid) {
                setcookie('openid', $openid, strtotime('+30 days'), '/', \Sooh\Base\Ini::getInstance()->cookieDomain());//保存一个月
                return $openid;
            }
        }

        return false;
    }

    /**
     * wechat网页Oauth授权与回调`
     */
    private function authorizeForWechat()
    {
        //回调
        $code = $this->_request->get('code');
        if (!empty($code)) {
            $wechatAuth = new \Lib\WX\WechatAuth(\Prj\Data\Config::get('WECHAT_APP_ID'), \Prj\Data\Config::get('WECHAT_APP_SECRET'));

            $openid = $wechatAuth->saveUserinfoFromCode($code);
            if ($openid) {
                setcookie('openid', $openid, strtotime('+30 days'), '/', \Sooh\Base\Ini::getInstance()->cookieDomain());//保存一个月
                return $openid;
            }
        }

        if ($this->checkWechatBrowser()) {
            $openid = $_COOKIE['openid'];
        }

        //如果没有openid 则跳转到授权页面
        if (!isset($openid) || empty($openid)) {
            $baseUrl    = 'https://open.weixin.qq.com/connect/oauth2/authorize';
            $currentUrl = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING'];
            $params     = [
                'appid'         => \Prj\Data\Config::get('WECHAT_APP_ID'),
                'redirect_uri'  => $currentUrl,
                'response_type' => 'code',
                'scope'         => 'snsapi_userinfo',
                'state'         => 888,
            ];

            $url = $baseUrl . '?' . http_build_query($params) . '#wechat_redirect';

            ob_clean();
            header('Location:' . $url);
            exit();
        }
        return $openid;
    }

    /**
     * 判断是否为微信浏览器
     * @param bool|false $enforcement 是否强制为微信浏览器
     * @return bool
     */
    private function checkWechatBrowser($enforcement = false)
    {
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger')) {
            return true;
        }

        if ($enforcement) {
            exit('请在微信中打开');
        }
        return false;
    }

    /**
     * 随机一个用户名
     * @param string $strLib 字符库(券中文字符)
     * @param int    $length 结果字符长度
     * @return string
     */
    private function randName($strLib = '', $length = 0)
    {
        return '匿名用户';
        $strLib = empty($strLib) ? \Prj\Data\Config::get('RANDNAME_STR_LIB') : $strLib;
        $length = empty($length) ? mt_rand(2, 5) : $length;
        $ret    = '';
        for ($i = 0; $i < $length; $i++) {
            $ret .= substr($strLib, mt_rand(0, 8) * 3, 3);//要以三字节为单位处理
        }
        return $ret;
    }

    //todo======================================2016/7/11 新接口=================================
    public function sinaReturnUrlAction(){
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
    }
}
