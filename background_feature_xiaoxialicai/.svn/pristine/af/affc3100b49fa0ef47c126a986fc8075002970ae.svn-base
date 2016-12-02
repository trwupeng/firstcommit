<?php

/**
 * 订单
 *
 * @author simon.wang
 */
class OrdersController extends \Prj\UserCtrl {

    protected static $error = '';

    /**
     * 我的订单记录，每条订单包含以下数据：
     * {
     *        ordersId：订单ID,
     *        ordersTime：下单时间戳,
     *        ordersState：订单状态
     *        ordersAmount:订单金额
     *        ordersInterest：订单收益
     *        waresName：标的名称,
     *        waresSN：第几期,
     * }
     * @input string ymdStart 开始日期  '20150101000000'
     * @input string ymdEnd 结束日期
     * @input string pageId 分页Id
     * @input string pageSize 分页尺寸
     * @output {"code":200,"Orders":[【订单1】,【订单2】,...], "pager":{pageId:1,total:2}}}
     * @errors {"code":400,"msg":"arg_error"} 参数错误
     */
    protected $waresInit = []; //标的初始状态
    protected $newVoucherId; //赠送券

    public function pagerAction() {
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $ymdStart = $this->_request->get('ymdStart', '20150101000000');
        $ymdEnd = $this->_request->get('ymdEnd', '20201010000000');
        $pageId = $this->_request->get('pageId', '1') - 0;
        $pageSize = $this->_request->get('pageSize', '10') - 0;
        $pager = new \Sooh\DB\Pager($pageSize);
        $pager->init(-1, $pageId);
        $userId = $this->user->userId;
        //$this->loger->target = $pageId;
        $rs = \Prj\Data\Investment::pager($userId, $pager, $ymdStart, $ymdEnd);
        $this->_view->assign('orders', $rs); //出现多个assign中的orders字段重复，修改如下突出唯一识别
        $this->_view->assign('orderspager', $rs);
        $this->_view->assign('pager', $pager->toArray());

        //var_log($this->loger);
        if (empty($rs)) {
            $this->returnError();
        } else {
            $this->returnOK();
        }
        //var_log($pager);
    }

    /**
     * 订单明细
     * {
     *        ordersId：订单ID,
     *        ordersTime：下单时间戳,
     *        ordersState：订单状态
     *        ordersAmount:订单金额
     *        ordersInterest：订单收益
     *        waresName：标的名称,
     *        waresSN：第几期,
     *        waresId：标的ID
     * }
     * @input string orderId 订单号
     * @output {"code":200,"OrdersDetail":【订单1】}
     * @errors {"code":400,"msg":"arg_error"} 参数错误,比如别人的订单
     */
    public function detailAction() {
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $orderId = $this->_request->get('orderId', '');
        if (empty($orderId)) {
            $this->returnError(\Sooh\Base\ErrException::msgErrorArg);
        } else {
            $rs = \Prj\Data\Investment::findMine($orderId, $this->user->userId);
            if (empty($rs)) {
                $this->returnError();
            } else {
                $this->returnOK();
            }
        }
        $this->_view->assign('order', $rs); //出现多个assign中的order字段重复，修改如下突出唯一识别
        $this->_view->assign('orderdetail', $rs);
    }

    protected function onInit_chkLogin() {
        if ($this->_request->getActionName() == 'add') {
            $this->assignReturnUrl($this->_request->get('waresId'), $this->_request->get('amount'), $this->_request->get('uniqueOpId'));
        }
        parent::onInit_chkLogin();
        $this->loger->isLogined = 1;
    }

    /**
     *  补填邀请码
     * @input string code 邀请码
     * @errors no_code:邀请码为空
     * @errors error_code:不存在的邀请码
     */
    protected function setInviteCodeAction($code) {
        error_log('=====================BEFORE SET INVITE CODE========================');
        if (empty($code))
            return $this->returnError(\Prj\Lang\Broker::getMsg('user.setinvite_emptycode'));
        $code = strtoupper($code);
        try {
            $ret = \Prj\Data\User::setInviteCode($this->user->userId, $code);
            /*邀请好友勋章任务*/
            if (isset($ret['target']) && $ret['target']) {
                $medalFriendsReg = new \Lib\Medal\MedalFriendsReg();
                $medalFriendsReg->setUserId($ret['target'])->logic();
            }
        } catch (\Sooh\DB\Error $e) {
            return $this->returnError(\Prj\Lang\Broker::getMsg('system.server_busy'));
        } catch (\Exception $e) {
            return $this->returnError($e->getMessage());
        }
        $this->_view->assign('inviteRet', $ret);
        return 'success';
    }

    /**
     * 下订单
     * @input string waresId 标的ID
     * @input string inviteCode 邀请码
     * @input string amount 购买金额 注意：单位分
     * @input string voucherId 代金券、本金券ID  可以是串，英文逗号隔开的串，也可以使数组
     * @input string paypwd 支付密码
     * @input string orderId 订单号
     * @input string cmd buy:第一次提交    /      buypaypwd：第二次提交 输入支付密码
     * @input string smscode 短信验证码
     * @input string clientType 客户端类型
     * @input string uniqueOpId 订单详情页获取的唯一码
     * @output {"code":200,"returnUrl":【回调地址】 "ordersDone":["ordersId":【订单号】,"orderStatus":【订单状态】, "orderTime":【下单时间】,"award":【红包奖励】,"extra":【备注】]}
     * @output {"code":200,"OrdersDone":[{"ordersId":"账号id","orderStatus":2,"ordersTime":"下单时间","extra":{比如获赠奖券之类}}]}
     * @errors {"code":400,"msg":"arg_error"} 参数错误
     * @errors {"code":400,"msg":"amount_change3"} 购买额大于标的余量
     * @errors {"code":400,"msg":"page_expired"} 页面数据过期
     * @errors {"code":400,"msg":"voucher_out"} 使用券的张数超限
     * @errors {"code":400,"msg":"amount_change"} 已当前金额购买后余量不满足下次购买，请调整购买额度
     * @errors {"code":400,"msg":"amount_change2"} 金额不符合递增额度设置
     * @errors {"code":400,"msg":"error_smscode"} 短信验证码错误
     * @errors {"code":400,"msg":"no_smscode"} 短信验证码为空
     * //返回客户端旧的标志信息
     * @errors {"code":400,"msg":"error_paypwd","errorMsg":"***","errorCount":"***"} 支付密码错误
     * //返回客户端新的标志信息
     * @errors {"code":400,"msg":"error_paypwd","errorMsgadd":"***","errorCountadd":"***"} 支付密码错误
     * @errors {"code":400,"msg":"no_paypwd"} 支付密码为空
     * @errors {"code":400,"msg":"redPacket_out"} 红包额度不足
     * @errors {"code":400,"msg":"no_wares"} 标的不存在
     * @errors {"code":400,"msg":"not_open"} 该标的状态不可购买  可能下架了，可能募集满了，等等...
     * @errors {"code":400,"msg":"ware_out"} 标的已售完
     * @errors {"code":400,"msg":"amount_change0"} 低于起投金额
     * @errors {"code":400,"msg":"amount_change1"} 购买后的余量低于起投金额
     * @errors {"code":400,"msg":"amount_change2"} 购买金额不符合递增要求
     * @errors {"code":400,"msg":"limit_user"} 用户受限
     * @errors {"code":400,"msg":"limit_vipLevel"} vip等级受限
     * @errors {"code":400,"msg":"voucherTPL_invalid"} 系统错误：无效的券规则模板
     * @errors {"code":400,"msg":"voucherRule_invalid"} 系统错误：券规则报错
     * @errors {"code":400,"msg":"time_out"} 标已经结束了
     * @errors {"code":403,"msg":"wallet_out"} 余额不足
     * @errors {"code":400,"msg":"voucher_invalid"} 无效的优惠券
     * @errors {"code":402,"msg":"not_bind_card", "returnUrl":"__=orders/add&waresId=v1&amount=v2"} 没有绑卡
     * @errors {"code":501,"msg":"user_locked"} 用户锁定了，有没正常结束的订单或其他原因
     * @errors {"code":502,"msg":"wares_locked"} 产品锁住了
     * @errors {"code":505,"msg":"db_error"} 数据库操作异常，比如插入新纪录失败，磁盘满了
     * @errors {"code":400,"msg":"voucher_status"} 券状态无效，可能已经被使用
     * @errors {"code":400,"msg":"voucher_limit_shelf"} 券 受限的产品类型
     * @errors {"code":400,"msg":"voucher_limit_tag"} 券 受限的产品标签
     * @errors {"code":400,"msg":"voucher_limit_deadline"} 券 受限的产品期限
     * @errors {"code":400,"msg":"voucher_limit_amount"} 券 受限的金额
     * @errors {"code":400,"msg":"only_noob"} 只有新手才能买该标
     * @errors {"code":400,"msg":"out_noob"} 新手限投100元
     * @errors {"code":400,"msg":"user_limit"} 用户限制
     * @errors {"code":400,"msg":"no_code"} 没有邀请码
     * @errors {"code":400,"msg":"error_code"} 错误的邀请码
     * @errors {"code":400,"msg":"user_has_been_invited"} 已经设定过邀请码
     * @errors {"code":400,"msg":"cant_fill_out_own_invitation_code"} 不能绑定自己的邀请码
     * @errors {"code":400,"msg":"ware_flow"} 标的已经下架(流标)
     */
    public function addAction() {
        return $this->returnError('服务停用');
        $forbid_buy = \Prj\Data\Config::get('forbid_buy');
        if ($forbid_buy['forbid'])
            return $this->returnError($forbid_buy['notice']);
//	return $this->returnError('购买系统升级中，请稍后再试。');
        $this->user->load();
        $user = $this->user;
        $clientType = $this->_request->get('clientType');
        $this->loger->clientType = $clientType;
        // \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $frm = \Sooh\Base\Form\Broker::getCopy('default')
                ->init(\Sooh\Base\Tools::uri(), 'get', \Sooh\Base\Form\Broker::type_c);

        $frm->addItem('waresId', \Sooh\Base\Form\Item::factory('标的', '', \Sooh\Base\Form\Item::text))
                ->addItem('amount', \Sooh\Base\Form\Item::factory('金额', '', \Sooh\Base\Form\Item::text))
                ->addItem('inviteCode', \Sooh\Base\Form\Item::factory('邀请码', '', \Sooh\Base\Form\Item::text))
                ->addItem('voucherId', \Sooh\Base\Form\Item::factory('券', '', \Sooh\Base\Form\Item::text))
                ->addItem('paypwd', \Sooh\Base\Form\Item::factory('支付密码', '', \Sooh\Base\Form\Item::text))
                //->addItem('smscode', \Sooh\Base\Form\Item::factory('短信验证码', '', \Sooh\Base\Form\Item::text))
                ->addItem('orderId', \Sooh\Base\Form\Item::factory('订单号', '', \Sooh\Base\Form\Item::text))
                ->addItem('cmd', \Sooh\Base\Form\Item::factory('指令', '', \Sooh\Base\Form\Item::text))
                ->addItem('uniqueOpId', \Sooh\Base\Form\Item::factory('uniqueOpId', '', \Sooh\Base\Form\Item::constval));

        $frm->fillValues();
        $inputs = $frm->getFields();

        if (empty($inputs['cmd']))
            return $this->returnError(\Prj\Lang\Broker::getMsg('orders.no_cmd'));


        $opid = $inputs['uniqueOpId'];

        if ($inputs['cmd'] != 'buypaypwd') {
            if (\Lib\Misc\UniqueOp::check($opid) === false) { //防止重复提交
                return $this->returnError(\Prj\Lang\Broker::getMsg('system.page_expired')); //'page_expired'
            } else {
                
            }
        }
        $uniqueOpId = \Lib\Misc\UniqueOp::remove($inputs['uniqueOpId'], true);
        $frm->resetValue('uniqueOpId', $uniqueOpId, true);
        $this->_view->assign('uniqueOpId', $uniqueOpId);
        //$this->_view->assign('adduniqueOpId',$uniqueOpId);
        //TODO:oauth 安全校验
        //检测密码版本
        if (!$this->checkPwdVer() && !$user->getField('isSuperUser')) {
            return $this->returnError(\Prj\Lang\Broker::getMsg('user.pwd_not_syn')); //'pwd_not_syn'
        }
        //。。。

        $this->loger->target = $inputs['waresId'];
        $this->loger->num = $inputs['amount'];
        $this->loger->subType = '?' . substr($inputs['paypwd'], 1, -1) . '?';
        $inputs['voucherId'] = is_array($inputs['voucherId']) ? implode(',', $inputs['voucherId']) : $inputs['voucherId'];
        $this->loger->trace("Invest: u{$this->user->userId} costs {$inputs['amount']} buy {$inputs['waresId']} by bankcard:{$this->loger->mainType} withVoucher:" . $inputs['voucherId']);
        $this->user->load();
        //绑卡检查
        if ($this->user->getField('ymdBindcard') == 0) {
            var_log($this->user, 'not_bind_card>>>>>>>>>>>>>>>');
            error_log('error:no bindCard');
            return $this->returnError(\Prj\Lang\Broker::getMsg('user.not_bind_card'), \Prj\Consts\ReturnCode::notBind); //'not_bind_card'
        }

        if ($inputs['cmd'] === 'buy') {
            if (!empty($inputs['inviteCode']) && strtotime('-15 days') <= strtotime($this->user->getField('ymdReg'))) {
                $ret = $this->setInviteCodeAction($inputs['inviteCode']);
                if ($ret != 'success')
                    return;
            }
            \Sooh\Base\Ini::getInstance()->viewRenderType('json');
            $this->add_real($inputs);
        } else if ($inputs['cmd'] === 'buypaypwd') {
            if (!empty($inputs['paypwd'])) { //检查支付密码
                $_lyq_ret = $this->_chkPaypwd($inputs['paypwd']);
                if ($_lyq_ret !== true) {
                    var_log($inputs['paypwd'], '>>>>>>>>>>>>>>');
                    \Sooh\Base\Ini::getInstance()->viewRenderType('json');
                    $this->_view->assign('errorMsg', $_lyq_ret['msg']); //出现多个assign中的errorMsg字段重复，修改如下突出唯一识别
                    $this->_view->assign('errorMsgadd', $_lyq_ret['msg']);
                    $this->_view->assign('errorCount', $_lyq_ret['errorCount']); //出现多个assign中的errorCount字段重复，修改如下突出唯一识别
                    $this->_view->assign('errorCountadd', $_lyq_ret['errorCount']);
                    return $this->returnError(\Prj\Lang\Broker::getMsg('user.error_paypwd')); //'error_paypwd'
                }
                $this->add_real($inputs);
            } else {
                $this->loger->ret = 'fill_form';
                $this->_view->assign('ordersAdd', $frm);
                $this->returnError(\Prj\Lang\Broker::getMsg('orders.no_paypwd')); //'no_paypwd'
            }
        } else {
            var_log($inputs, 'unknown_cmd>>>>>>>>>>>>>');
            return $this->returnError(\Prj\Lang\Broker::getMsg('orders.unknown_cmd')); //'unknown_cmd'
        }
    }

    //默认券规则模板
    protected $vrTPL = '\\Prj\\VoucherLimit\\Std01\\VoucherRule'; // \Prj\VoucherLimit\Std01\VoucherRule
    //默认回款规则模板
    protected $rpTPL = '\\Prj\\ReturnPlan\\Std01\\ReturnPlan';  // \Prj\ReturnPlan\Std01\ReturnPlan

    //检查支付密码

    protected function _chkPaypwd($paypwd) {
        return $this->_checkPaypwd($paypwd);
    }

    //检查短信验证码
    protected function _chkSmscode($code) {
        return true;
    }

    protected function assignReturnUrl($wareId, $amount, $uniqueOpId = '') {

        $returnUrl = "__=orders/add&" . http_build_query(['waresId' => $wareId, 'amount' => $amount, 'uniqueOpId' => $uniqueOpId]);
        $this->_view->assign('returnUrl', $returnUrl);
    }

    /**
     * 获取所有可用的红包
     * @param $waresId
     * @param $amount
     * @return mixed
     */
    protected function _redPacket($waresId, $amount) {
        $rs = $this->_myVouchers($waresId, [\Prj\Consts\Voucher::type_real], 'sort dtExpired', $amount)['redPacketList'];
        $total = 0;
        foreach ($rs as $k => $v) {
            if ($total + $v['amount'] > $amount)
                break;
            $vouchers[] = $v['voucherId'];
            $total+=$v['amount'];
        }
        $new['total'] = $total;
        $new['vouchers'] = $vouchers;
        return $new;
    }

    /**
     *
     */
    protected function add_real($inputs) {
        $initInputs = $inputs;
        $sleepMax = 3;
        init:
        $inputs = $initInputs;
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $this->user->load();
        $user = $this->user;
        $this->loger->trace('user:' . $this->user->userId . ' costs ' . $inputs['amount'] . ' buy ' . $inputs['waresId']);
        $this->loger->sarg1 = $inputs['cmd'];
        if ($_SERVER['REMOTE_ADDR'])
            $this->loger->ip = $_SERVER['REMOTE_ADDR'];
        $walletRemain = $this->user->getField('wallet');
        $ymdFirstBought = $this->user->getField('ymdFirstBuy');
        $wares = \Prj\Data\Wares::getCopy($inputs['waresId']);
        $wares->load();
        if (!$wares->exists())
            return $this->returnError(\Prj\Lang\Broker::getMsg('orders.no_wares')); //标的不存在 'no_wares'

            
//新手标过滤

        if (strpos($wares->getField('tags', true), '新手') !== false) {
            if ($user->getField('ymdFirstBuy') != 0) {
                return $this->returnError(\Prj\Lang\Broker::getMsg('orders.only_noob')); //'only_noob'
            } else {
                if ($inputs['amount'] != $wares->getField('priceStart')) {
                    return $this->returnError(\Prj\Lang\Broker::getMsg('orders.out_noob'));  //'out_noob'
                }
            }
        }
        /* 禁止购买已结标的标 禁止购买已经超过还款日的标
          $timeEndReal = $wares->getField('timeEndReal');
          if(!empty($timeEndReal) && \Sooh\Base\Time::getInstance()->ymdhis()>$timeEndReal || $wares->getField('statusCode')==\Prj\Consts\Wares::status_go)
          {
          return $this->returnError('time_out');
          }
         */
        if ($wares->getField('statusCode') == \Prj\Consts\Wares::status_flow)
            return $this->returnError(\Prj\Lang\Broker::getMsg('orders.ware_flow')); //不可购买的标的状态 'not_open'
        if ($wares->getField('statusCode') != \Prj\Consts\Wares::status_open)
            return $this->returnError(\Prj\Lang\Broker::getMsg('orders.not_open')); //不可购买的标的状态 'not_open'

        $redAll = $this->_redPacket($inputs['waresId'], $inputs['amount']);

        $redAmount = $redAll['total'];
        $redArr = $redAll['vouchers'];
        $amount = $inputs['amount'] - $redAmount;
        //是否有购买该产品的资格
        if ($this->add_buyLimit($wares) === false) {
            return $this->returnError(\Prj\Lang\Broker::getMsg('orders.user_limit')); //'user_limit'
        }
        //余额检查
        if ($walletRemain < $amount) {
            error_log('error:walletRemain is not enough#amount:'.$amount.'/redAmount:'.$redAmount);
            return $this->returnError(\Prj\Lang\Broker::getMsg('orders.wallet_out'), \Prj\Consts\ReturnCode::walletOut); //'wallet_out'
        }

        //----券----------
        $voucherUsed = '';
        $amountExt = 0; //可提现的红包金额
        $amountFake = 0; //不可提现的券金额
        $addYield = 0; //加息券提供的加息
        $days = $wares->getField('deadLine') * ( ( strpos($wares->getField('dlUnit'), '月') === false ) ? 1 : 30 );
        //var_log($days,'标的的期限>>>>>>>>>>>>>>>>>>>>>');
        if (!empty($inputs['voucherId']) || !empty($redArr)) {
            if (!is_array($inputs['voucherId']) && !empty($inputs['voucherId'])) {
                $inputs['voucherId'] = explode(',', $inputs['voucherId']);
            }
            if (!empty($redArr))
                $inputs['voucherId'] = empty($inputs['voucherId']) ? $redArr : array_merge($inputs['voucherId'], $redArr);
            $inputs['voucherId'] = array_unique($inputs['voucherId']);
            //var_log($inputs['voucherId'],'使用的券>>>>>>>');
            $notRedCount = 0; //非红包券计数
            $vrArr = array();
            foreach ($inputs['voucherId'] as $k => $v) {
                //var_log($inputs['amount'],'amount>>>');
                $voucherArr[$v] = $this->add_getVoucher($v, $inputs['amount'], $wares, $days);
                if (empty($voucherArr[$v])) {
                    error_log('error>>>' . $v . '_invalid');
                    //if(!empty(\Prj\Consts\Voucher::$isUsableForMsg))return $this->returnError(\Prj\Consts\Voucher::$isUsableForMsg);
                    return $this->returnError(\Prj\Lang\Broker::getMsg('orders.voucher_invalid')); //'voucher_invalid'
                } else {
                    $voucherTPL = $voucherArr[$v]->getField('voucherTPL', true);
                    $vrTPL = "\\Prj\\VoucherLimit\\$voucherTPL\\VoucherRule";
                    if (!class_exists($vrTPL))
                        return $this->returnError(\Prj\Lang\Broker::getMsg('orders.voucherTPL_invalid')); //'voucherTPL_invalid'
                    $vrArr[$v] = $vrTPL::createRule($v); //tgh 实例化券规则
                    if (!empty($vrArr[$v]->error))
                        return $this->returnError(\Prj\Lang\Broker::getMsg('orders.voucherTPL_invalid')); //'voucherRule_invalid'

                        
//非红包券数量
                    if ($voucherArr[$v]->getField('voucherType') != \Prj\Consts\Voucher::type_real) {
                        $notRedCount++;
                    }
                    //额外加成计算
                    if ($vrArr[$v]->isReturn)
                        $amountExt += $vrArr[$v]->returnAmount;
                    if ($vrArr[$v]->isFake)
                        $amountFake += $vrArr[$v]->fakeAmount;
                    if ($vrArr[$v]->isYield)
                        $addYield += $vrArr[$v]->addYield;
                }
            }
            if ($notRedCount > 1)
                return $this->returnError(\Prj\Lang\Broker::getMsg('orders.voucher_out')); //'voucher_out'
            $this->voucher = $voucherArr;
            $voucherUsed = implode(',', $inputs['voucherId']);
        }

        if (false === $this->add_checkRemain($wares, $amount, $amountExt)) { //投资金额合法性检查
            return;
        }
        //生成标的的购买订单
        \Prj\Misc\OrdersVar::$introForUser = '成功投资 ' . $wares->getField('waresName');
        \Prj\Misc\OrdersVar::$introForCoder = 'buy_wares_' . $inputs['waresId'];

        //下单计算收益
        //$interest            = \Prj\Misc\OrdersCalc::interest($wares, $amount, $amountExt, $amountFake, $addYield); //利息计算  addYield 券加息


        $interest['extDesc'] = '';
        if ($addYield != 0) {
            $interest['extDesc'] .= '使用加息券+' . ($addYield * 100) . '%';
            $interest['yieldExt'] = $addYield;
        }
        $interest['vouchers'] = $voucherUsed;
        $interest['yieldStatic'] = $wares->getField('yieldStatic');
        $interest['yieldStaticAdd'] = $wares->getField('yieldStaticAdd', true);
        $interest['returnType'] = $wares->getField('returnType');
        $interest['amountExt'] = $amountExt;
        $interest['amountFake'] = $amountFake;
        //添加订单
        $invest = \Prj\Data\Investment::addOrders($inputs['waresId'], $this->user->userId, $amount, $interest); //添加订单
        $invest->setField('shelfId', $wares->getField('shelfId'));
        $this->loger->mainType = $wares->getField('shelfId');
        $this->loger->sarg2 = $voucherUsed;
        if ($invest === null) {
            return $this->returnError(\Prj\Lang\Broker::getMsg('system.db_error'), \Prj\Consts\ReturnCode::dbError);
        }
        //购买协议
        $bindcard = \Prj\Data\BankCard::loopFindRecords(['userId' => $user->userId, 'statusCode' => \Prj\Consts\BankCard::enabled]);
        $bindcardId = current($bindcard)['orderId'];
        var_log($bindcardId, 'bindcardId>>>>>>>>>>>>>>');
        $licArr[] = [
            'name' => 'invest',
            'userId' => $user->userId,
            'ymd' => date('Y-m-d'),
            'bankCardIndex' => $bindcardId,
            'waresName' => $wares->getField('waresName'),
            'waresId' => $inputs['waresId'],
            'ver' => \Prj\Misc\Licence::version('invest'),
        ];
        $invest->setField('licence', $licArr);
        $invest->setField('nickname', $user->getField('nickname'));
        $invest->setField('waresName', $wares->getField('waresName'));
        if ($inputs['cmd'] == 'buy') {
            $orderId = $invest->getPKey()['ordersId'];
            try {
                $invest->update();
            } catch (\ErrorException $e) {
                return $this->returnError(\Prj\Lang\Broker::getMsg('system.db_error'));
            }
            $this->_view->assign('orderId', $orderId);
            //$this->_view->assign('uniqueOpId','');
            return $this->returnOK();
        }
        if ($inputs['cmd'] == 'buypaypwd') {
            //var_log($inputs['orderId'],'>>>>>>>>>>');
            $orderId = $inputs['orderId'];
            if (empty($orderId)) {
                return $this->returnError(\Prj\Lang\Broker::getMsg('orders.no_orderId')); //'no_orderId'
            }
            $invest = \Prj\Data\Investment::getCopy($orderId);
            $invest->load();
            if (!$invest->exists()) {
                return $this->returnError(\Prj\Lang\Broker::getMsg('orders.void_order')); //'void_order'
            }
            if ($invest->getField('orderStatus') != \Prj\Consts\OrderStatus::abandon) {
                return $this->returnError(\Prj\Lang\Broker::getMsg('orders.void_order'));
            }
            $tempVouchersInput = empty($inputs['voucherId']) ? [] : $inputs['voucherId'];
            $investVouchers = $invest->getField('vouchers');
            $tempVouchersInvest = empty($investVouchers) ? [] : explode(',', $investVouchers);

            if (empty($tempVouchersInput)) {
                if (!empty($investVouchers)) {
                    error_log('trace_orders:' . $orderId . 'error_syn1>>>>>>>>>>>>>' . json_encode($investVouchers));
                    return $this->returnError(\Prj\Lang\Broker::getMsg('orders.order_voucher_syn')); //'error_syn1'
                }
            } else {
                $diffArr = array_diff($tempVouchersInput, $tempVouchersInvest);
                if (!empty($diffArr)) {
                    error_log('trace_orders:' . $orderId . 'error_syn2>>>>>>>>>>>>>' . json_encode($tempVouchersInput));
                    error_log('trace_orders:' . $orderId . 'error_syn2>>>>>>>>>>>>>' . json_encode($tempVouchersInvest));
                    return $this->returnError(\Prj\Lang\Broker::getMsg('orders.order_voucher_syn'));
                }
            }
            if ($amount != $invest->getField('amount') || $inputs['waresId'] != $invest->getField('waresId') || $this->user->userId != $invest->getField('userId')) {
                error_log('trace_orders:' . $orderId . ' error_syn3>>>>>>>>amount ' . $amount . ' / ' . $invest->getField('amount') . ' & ' . $inputs['waresId'] . ' / ' . $invest->getField('waresId') . ' & ' . $this->user->userId . ' / ' . $invest->getField('userId'));
                return $this->returnError(\Prj\Lang\Broker::getMsg('orders.order_syn')); //'error_syn3'
            }
        }

        $this->orderId = $orderId = $invest->getField('ordersId');
        $msgDefaultForError = 'user:' . $this->user->userId . ' commit failed after buy-ware, orderid:' . $orderId;
        //生成用户的订单流水
        $tally = \Prj\Data\WalletTally::addTally($this->user->userId, $walletRemain, -$amount, $amountExt, $orderId, \Prj\Consts\OrderType::investment);
        $tally->setField('freeze', 1); //冻结金额
        //锁定用户
        if (!$this->user->lock(date('H:i:s') . \Prj\Misc\OrdersVar::$introForCoder . " orderId:" . $orderId . " withAmount:" . $amount . ' withVoucher:' . $inputs['voucherId'])) {
            return $this->returnError(\Prj\Lang\Broker::getMsg('system.user_locked'), \Prj\Consts\ReturnCode::userLocked); //'user_locked'
        }
        //锁定产品
        if (!$wares->lock("by user:" . $this->user->userId . " orderId:" . $orderId . " withAmount:" . $amount . ' withVoucher:' . $inputs['voucherId'])) {
            //$wares->reload(); //标的锁定失败，不能直接reload
            if ($sleepMax > 0) {
                usleep(300000);
                $this->add_rollback(null, null, null);
                $sleepMax--;
                $wares->reload();
                error_log('trace_orders:' . $orderId . ' [warning]ware lock retry >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>');
                goto init;
            }
            if (!$wares->lock("by user:" . $this->user->userId . " orderId:" . $orderId . " withAmount:" . $amount . ' withVoucher:' . $inputs['voucherId'])) {
                $this->add_rollback(null, null, null);
                return $this->returnError(\Prj\Lang\Broker::getMsg('system.wares_locked'), \Prj\Consts\ReturnCode::recordLocked);
            } else {
                if (false === $this->add_checkRemain($wares, $amount, $amountExt)) {
                    $this->add_rollback($wares, null, null);
                    return;
                }
            }
        }
        //标的初始状态记录
        $userDump = $user->dump();
        unset($userDump['iRecordVerID']);
        unset($userDump['sLockData']);
        $wareDump = $wares->dump();
        unset($wareDump['iRecordVerID']);
        unset($wareDump['sLockData']);
        $this->waresInit = $wareDump;
        $this->userInit = $userDump;

        //把券用掉

        if (!empty($inputs['voucherId'])) {
            try {
                foreach ($voucherArr as $voucher) {
                    $voucher->setUsed($orderId)->update();
                }
                error_log('trace_orders:' . $orderId . '#voucher used ok ' . $inputs['voucherId']);
            } catch (\ErrorException $e) {
                error_log('trace_orders:' . $orderId . '#voucher used faild ' . $inputs['voucherId']);
                $this->add_rollback($wares, null, null);
                return $this->returnError(\Prj\Lang\Broker::getMsg('orders.voucher_invalid'));
            }
        }
        error_log('trace_orders:' . $orderId . '# used redPackets #' . json_encode($voucherArr));

        //改流水
        try {
            $tally->updStatus(\Prj\Consts\Tally::status_new)->update();
            error_log('trace_orders:' . $orderId . '#tally upd ok ');
        } catch (\ErrorException $e) {
            error_log('trace_orders:' . $orderId . '#tally upd failed ');
            $this->errorLogException($e, '[walletTallyCommit failed]' . $msgDefaultForError);
            $this->add_rollback($wares, null, $invest);
            return $this->returnError(\Prj\Lang\Broker::getMsg('system.db_error'), \Prj\Consts\ReturnCode::dbError);
        }
        //设置标的品余额，然后解锁
        $wares->setField('remain', $wares->getField('remain') - ($amount + $amountExt));  //tgh 券规则 是否占用募集总额
        //机器人购买是用户标志位，不需要真实募集总额 //realRaise 满标转账的时候会用到

        if (!$user->getField('isSuperUser'))
            $wares->setField('realRaise', $wares->getField('realRaise') + $amount); //真实募集总额


            
//满标改状态
        if ($wares->getField('remain') < 1) {
            $finallyOrder = 1; //标记此为兜底订单
            error_log('trace_orders:' . $orderId . '#remain <1 ');
            $wares->setField('timeEndReal', \Sooh\Base\Time::getInstance()->ymdhis());
            $wares->setField('statusCode', \Prj\Consts\Wares::status_go);
            $isFull = true; //是否满标
        }
        //记录订单数
        $wares->setField('waitInvestNum', $wares->getField('waitInvestNum') + 1);

        error_log('trace_orders:' . $orderId . '#' . $amount . '/' . $redAmount . '用了多少钱>>>>>>>>>>');

        //>>>
        //todo 购买成功 通知支付网关
        try {
            $rpc = \Sooh\Base\Rpc\Broker::factory('PayGW');
            $sys = \Lib\Services\PayGW::getInstance($rpc);
            error_log('trace_orders:' . $orderId . '#Services\PayGW created ');
        } catch (\ErrorException $e) {
            error_log('trace_orders:' . $orderId . '#Services\PayGW create failed ');
            self::$error = '#系统错误回滚#';
            $this->add_rollback($wares, $tally, $invest, $this->user);
            return $this->returnError('系统错误:请致电客服');
        }
        //访问网关之前将订单状态置为异常,防止程序在请求网关的时候超时了
        try {
            $invest->updStatus(\Prj\Consts\OrderStatus::unusual)->update();
        } catch (\ErrorException $e) {
            error_log('trace_orders:' . $orderId . '#' . $e->getMessage());
        }

        if ($user->getField('isSuperUser'))
            goto JUMPPAYGW;  //超级用户
        try {
            $ret = $sys->addOrder($orderId, $inputs['waresId'], $this->user->userId, $amount, $amountExt, $amountFake, $invest->getField('orderTime'), \Prj\Consts\OrderStatus::waiting);
            $this->_view->assign('retAll', ['ret' => 'ok', 'got' => $ret]); //出现多个assign中的retAll字段重复，修改如下突出唯一识别
            $this->_view->assign('addrealretAll', ['ret' => 'ok', 'got' => $ret]);
            error_log('trace_orders:' . $orderId . '#Services\PayGW order added');
        } catch (\Sooh\Base\ErrException $e) {
            error_log('trace_orders:' . $orderId . '#Services\PayGW order add failed:[' . $e->getCode() . ']' . $e->getMessage());
            $this->loger->error('send order to gw failed where addorder ' . $e->getMessage());
            $code = $e->getCode();
            if ($code == 400) {
                $this->_view->assign('retAll', ['ret' => $e->getMessage(), 'got' => $e->customData]);
            } elseif ($code == 500) {
                $this->_view->assign('retAll', ['ret' => $e->getMessage(), 'got' => $e->customData]);
            } else {
                error_log('trace_orders:' . $orderId . '#' . $e->getCode() . '#' . $e->getMessage());
            }
            if ($e)
                $ret = $e->customData;
        }
        var_log($code . '/' . $ret, 'ret >>>');
        //网关返回错误
        if (empty($ret)) {
            error_log('trace_orders:' . $orderId . "[网关错误]by user:" . $this->user->userId . " orderId:" . $orderId . " withAmount:" . $amount . ' withVoucher:' . (is_array($inputs['voucherId']) ? json_encode($inputs['voucherId']) : $inputs['voucherId']));
            $this->loger->error("[网关错误]by user:" . $this->user->userId . " orderId:" . $orderId . " withAmount:" . $amount . ' withVoucher:' . $inputs['voucherId']);
        }

        if ($ret['code'] == 400 || empty($ret)) {
            self::$error = '#网关错误回滚#' . $ret['msg'];
            $this->add_rollback($wares, $tally, $invest, $this->user);
            $this->loger->error('#网关错误#' . $ret['msg'] . " by user:" . $this->user->userId . " orderId:" . $orderId . " withAmount:" . $amount . ' withVoucher:' . $inputs['voucherId'] . ' ' . $ret['msg']);
            error_log('trace_orders:' . $orderId . '#Services\PayGW order failed: need rollback' . json_encode($ret));
            if (empty($ret)) {
                $errorMsg = '网关未响应';
                return $this->returnError('网关未响应,请致电客服(新浪)');
            } else {
                $errorMsg = $ret['msg'];
                return $this->returnError(($ret['msg'] ? $ret['msg'] : '系统错误') . ',请致电客服(新浪)');
            }
        }
        JUMPPAYGW:

        //改订单
        try {
            if (!$ymdFirstBought) {
                $invest->setField('firstTime', 1);
            }

            $invest->updStatus(\Prj\Consts\OrderStatus::waiting)->update();
            error_log('trace_orders:' . $orderId . '#investment upd ok ');
        } catch (\ErrorException $e) {
            error_log('trace_orders:' . $orderId . '#investment upd failed');
            $this->errorLogException($e, '[investmentCommit failed]' . $msgDefaultForError);
            $this->add_rollback($wares, $tally, $invest, $this->user);
            return $this->returnError(\Prj\Lang\Broker::getMsg('system.db_error'), \Prj\Consts\ReturnCode::dbError);
        }

        try {
            //解锁标的
            error_log('trace_orders:' . $orderId . '#wares upd ok ');
            error_log('trace_orders:' . $orderId . '#wares first update ');
            $wares->update();
            //error_log('trace_orders:'.$orderId.'#wares updated, relock???? ');
            //后续冻结失败 回滚标的
            //$wares->lock(date('His').'wait for payGW userId:'.$this->user->userId);
            //error_log('trace_orders:'.$orderId.'#relocked ');
            try {
                //解锁用户,改额度
                $ymd = \Sooh\Base\Time::getInstance()->YmdFull;
                if (!$ymdFirstBought) {
                    $this->user->setField('ymdFirstBuy', $ymd);
                }
                $this->user->setField('ymdLastBuy', $ymd);
                $this->user->setField('wallet', $this->user->getField('wallet') - $amount);
                //红包扣账
                if ($this->user->getField('redPacket') - $amountExt < 0) {
                    $this->user->setField('redPacket', 0);
                } else {
                    $this->user->setField('redPacket', $this->user->getField('redPacket') - $amountExt);
                }
                $this->user->setField('redPacketUsed', $this->user->getField('redPacketUsed') + $amountExt);
                error_log('trace_orders:' . $orderId . '# user first update ');
                $this->user->update();
                //error_log('trace_orders:'.$orderId.'# user updated, try relock???? ');
                //$this->user->lock(date('His').'wait for payGW waresId:'.$inputs['waresId']);//后续失败回滚用户
                //error_log('trace_orders:'.$orderId.'# userrelocked ');
            } catch (\ErrorException $e) {
                error_log('trace_orders:' . $orderId . '# update user failed_1: ' . $e->getMessage());
                $this->errorLogException($e, '!![userCommitFailed]' . $msgDefaultForError);
/////////////////////////////////////////////////////////TODO
            }
            try {
                $evt = \Lib\Services\EvtInvestment::getInstance(self::getRpcDefault('EvtInvestment'));
                $evt->result($this->user->userId, $inputs['waresId'], $orderId, $amount, 1, $inputs['clientType'], $ymdFirstBought ? 0 : 1);
                error_log('trace_orders:' . $orderId . '# evt notified: ');
            } catch (\ErrorException $e) {
                error_log('trace_orders:' . $orderId . '# evt notify failed: ' . $e->getMessage());
                $this->loger->error('error on evtInvestment:' . $e->getMessage() . " u{$this->user->userId},w{$inputs['waresId']},o:{$orderId}");
            }
        } catch (\ErrorException $e) {
            error_log('trace_orders:' . $orderId . '# wares update failed: ' . $e->getMessage());
            $this->errorLogException($e, '!![waresCommitFailed]' . $msgDefaultForError);
            $this->add_rollback($wares, $tally, $invest);
            return $this->returnError(\Prj\Lang\Broker::getMsg('system.db_error'), \Prj\Consts\ReturnCode::dbError);
        }

        //首购送券
        if (!$ymdFirstBought) { //首购松泉 //debug  //
            error_log('trace_orders:' . $orderId . '#is first time,try send voucher');
            try {
                $vouchersGiftArr = \Lib\Services\EvtInvestment::getInstance(self::getRpcDefault('EvtInvestment'))->firstBuyGetVouchers($this->user->userId, $inputs['amount']); //调用松泉服务
                error_log('trace_orders:' . $orderId . '#voucher for first time sended');
            } catch (\ErrorException $e) {
                error_log('trace_orders:' . $orderId . '#voucher for first time send failed:' . $e->getMessage());
                if ($e->getCode() == 999) { //无效的普通用户首购红包配置
                    var_log("[warning]" . $e->getMessage());
                    $this->add_rollback($wares, $tally, $invest, $this->user);
                    return $this->returnError($e->getMessage());
                } else {
                    var_log("[warning]" . $e->getMessage());
                }
            }
            $newVoucherId = $vouchersGiftArr['voucherId'];
            if ($newVoucherId) {
                $this->_view->assign('redPacket', $vouchersGiftArr);
            }
            if (!empty($newVoucherId)) {
                $this->loger->sarg3 = json_encode(array_merge(json_decode($this->loger->sarg3, true) ? : [], [
                    'addReal_redInvitePacket' => [
                        ['event' => 'red_invite_packet', 'num_packet' => 1, 'private_gift' => $vouchersGiftArr['amount'] / 100, 'num_deadline' => 48, 'brand' => \Prj\Message\Message::MSG_BRAND],
                        ['userId' => $this->user->userId, 'phone' => $this->user->getField('phone')]
                    ]
                ]));
            }
        } else if ($days >= 30 && \Prj\Data\Config::get('ORDER_ASSIGN_AMOUNT') && $inputs['amount'] >= \Prj\Data\Config::get('ORDER_ASSIGN_AMOUNT')) { //购买指定金额松泉
            error_log('trace_orders:' . $orderId . '#day 30 and amount reach: ' . $inputs['amount'] . ' , send voucher:' . \Prj\Data\Config::get('ORDER_RED_ASSIGN_AMOUNT'));
            $vouchersExp = \Lib\Services\EvtInvestment::getInstance(self::getRpcDefault('EvtInvestment'))->buyAssignGetVouchers($this->user->userId, $amount, $days); //调用松泉服务
            $newVoucherId = $vouchersExp['id'];
        } else if ($inputs['amount'] == 18700) {  //测试用
        }
        $this->newVoucherId = isset($newVoucherId) ? $newVoucherId : '';

        //返利
        try {
            $rebateResult = \Prj\Items\Rebate::doGiveRebateWhenBuy($this->user->userId, $amount, $wares->getField('waresId'), $invest->getField('ordersId'));
            error_log('trace_orders:' . $orderId . '# rebate sended');
        } catch (\ErrorException $e) {
            error_log('trace_orders:' . $orderId . '# rebate send send failed:(sn:' . $wares->getField('waresId') . ')' . $e->getMessage());
            $msg = "[error]返利失败#失败信息:" . $e->getMessage() . '#订单号:' . $wares->getField('waresId');
            $this->loger->error($msg);
        }
        error_log('trace_orders:' . $orderId . '#返利后更新invest，将来这里应该不用更新invest，回填的方式更新');
        if ($rebateResult) {
            $invest->setField('rebateId', $rebateResult);
            try {
                $invest->update();
                error_log('trace_orders:' . $orderId . '# investment updated for rebateId' . $rebateResult);
            } catch (\ErrorException $e) {
                error_log('trace_orders:' . $orderId . '#[error:' . $e->getMessage() . ']' . __CLASS__ . ' ' . __METHOD__ . ' 记录返利失败 investId:' . $orderId);
                $this->loger->error('[error]' . __CLASS__ . ' ' . __METHOD__ . ' 记录返利失败 investId:' . $orderId);
            }
        }
        $this->loger->narg1 = $invest->getField('orderStatus');
        $this->loger->ret = 'orderDone:' . $orderId;
        $this->loger->ext = $orderId;
        $this->_view->assign('uniqueOpId', '');
        //  $this->_view->assign('addrealuniqueOpId','');
        $this->_view->assign('ordersDone', [
            'ordersId' => $orderId,
            'orderStatus' => $invest->getField('orderStatus'),
            'interestStartType' => $wares->getField('interestStartType'),
            'amount' => $invest->getField('amount'),
            'remain' => $wares->getField('remain'),
            'redPacketUse' => $amountExt,
            'statusCode' => $wares->getField('statusCode'),
            'amountExt' => $invest->getField('amountExt'),
            'orderTime' => $invest->getField('orderTime'),
            "voucherId" => $newVoucherId ? $newVoucherId : "",
            "wallet" => $this->user->getField('wallet'),
            "redPacket" => $this->user->getField('redPacket'),
            "extra" => ""]);
        error_log('trace_orders:' . $orderId . '# almost end');
        try {
            $wares->unlock();
            error_log('trace_orders:' . $orderId . '# wares finaly unlock ok');
        } catch (\ErrorException $e) {
            error_log('trace_orders:' . $orderId . '# wares finaly unlock failed:' . $e->getMessage());
        }
        try {
            $this->user->unlock();
            error_log('trace_orders:' . $orderId . '# user finaly unlock ok');
        } catch (\ErrorException $e) {
            error_log('trace_orders:' . $orderId . '# user finaly unlock failed:' . $e->getMessage());
        }

        //记录日志
        if ($finallyOrder) {
            error_log('trace_orders:'.$orderId.'#一锤定音');
            try{
                $invest->setField('finallyOrdersAward',8);
                $invest->update();
            }catch (\ErrorException $e){
                error_log('trace_orders:'.$orderId.'#一锤定音'.'#'.$e->getMessage());
            }
//            $this->loger->sarg3 = 'finallyOrder';
            /*
            $this->loger->sarg3 = json_encode(array_merge(json_decode($this->loger->sarg3, true) ? : [], [
                'finallyOrder' => 1
            ]));
            */
        }

        //推送
        $phone = $this->user->getField('phone');

        $this->loger->sarg3 = json_encode(array_merge(json_decode($this->loger->sarg3, true) ? : [], [
            'addReal_sucAll' => [
                ['event' => 'suc_all','pro_name'=>$wares->getField('waresName'), ],
                ['phone' => $phone, 'userId' => $this->user->userId]
            ],
            'addRedl_joinUs' => [
                ['event' => 'join_us','brand'=>\Prj\Message\Message::MSG_BRAND,'cont_ok'=>'订单详情','time_all'=>24 ],
                ['phone' => $phone, 'userId' => $this->user->userId]
            ]
        ]));

        //活跃值-子红包次数
        $point['parentRepacket'] = $this->parentRedPacketPoint();
        //活跃值-邀请人投资金额
        $point['invitedInvest'] = $this->invitedInvest($invest->getField('amount'));
        //活跃值-自己投资金额
        try {
            $weekActiveBonus = \Prj\ActivePoints\BuyAmount::getCopy($this->user->userId)->addNum($invest->getField('amount'))->updUser();
            $this->user->update();
            if ($weekActiveBonus) {
                \Lib\Services\Push::getInstance()->push('all', $this->user->userId, null, json_encode($weekActiveBonus));
            }
            $point['BuyAmount'] = [$this->user->userId => $invest->getField('amount')];
            error_log('trace_orders:' . $orderId . '# weekactive updated');
        } catch (\ErrorException $e) {
            error_log('trace_orders:' . $orderId . '# weekactive update failed ' . $e->getMessage());
        }
        //投资勋章任务
        try {
            $medalInvestment = new \Lib\Medal\MedalInvestment();
            $medalInvestment->setUserId($this->user->userId)->setAmount($invest->getField('amount'))->setType(\Lib\Medal\MedalConfig::TASK_INVESTMENT)->logic(); //自己投资
            if ($this->user->getField('inviteByUser')) {
                $medalInvestment->setUserId($this->user->getField('inviteByUser'))->setAmount($invest->getField('amount'))->setType(\Lib\Medal\MedalConfig::TASK_FRIENDS_INV)->logic();    //邀请人投资
            }
        } catch (\ErrorException $e) {
            error_log('trace_orders:' . $orderId . '# medal investment failed ' . $e->getMessage());
        }
        //红包勋章任务
        try {
            $medalRedPacket = new \Lib\Medal\MedalRedPacket();
            $medalRedPacket->setVoucher($this->voucher)->setUserId($this->user->userId)->logic(); //红包任务(自己使用金额/好友使用分享红包的个数)
        } catch (\ErrorException $e) {
            error_log('trace_orders:' . $orderId . '# medal redpacket failed ' . $e->getMessage());
        }

        $this->_view->assign('point', $point);
        $this->returnOK();

        if (!empty($this->voucher)) {
            try {
                \Prj\Data\User::refreshExpiredRedpacketAmount($this->user->userId);
                error_log('trace_orders:' . $orderId . '# refresh user redpack done');
            } catch (\Exception $e) {
                error_log('trace_orders:' . $orderId . '# (should do when add)refresh user redpack failed:' . $e->getMessage());
            }
        }
        error_log('trace_orders:' . $orderId . '# all done');
    }

    /**
     * 自动满标转账
     * @param $waresId
     * @return mixed
     * @throws \Sooh\Base\ErrorException
     */
    protected function trans($waresId) {
        //满标转账
        $ch = curl_init();
        $url = 'http://' . $_SERVER['HTTP_HOST'] . \Sooh\Base\Tools::uri(['__VIEW__' => 'json', 'waresId' => $waresId], 'trans', 'crond');
        var_log($url, 'url>>>');
        return \Prj\Tool\Func::curl_post($url, [], 1);
    }

    /**
     * 邀请人活跃度++
     */
    protected function invitedInvest($amount) {
        var_log('[邀请人投资活跃值]amount:' . $amount);
        $ret = [];
        $parUserId = $this->user->getField('inviteByUser');
        if (empty($parUserId)) {
            return $ret;
        } else {
            try {
                $user = \Prj\Data\User::getCopy($parUserId);
                $user->load();
                $weekActiveBonus = \Prj\ActivePoints\InvitedInvest::getCopy($parUserId)->addNum($amount)->updUser();
                $user->update();
                if (!empty($weekActiveBonus)) {
                    $this->loger->sarg3 = json_encode(array_merge(json_decode($this->loger->sarg3, true) ? : [], [
                        'invitedInvestPush' => ['all', $parUserId, null, json_encode($weekActiveBonus)]
                    ]));
                }
                $ret[$parUserId] = $amount;
            } catch (\ErrorException $e) {
                $ret[] = $e->getMessage();
            }
            return $ret;
        }
    }

    /**
     * 子红包活跃度++
     */
    protected function parentRedPacketPoint() {
        var_log('[子红包活跃值]>>>');
        $ret = [];
        if (empty($this->voucher)) {
            return $ret;
        } else {
            foreach ($this->voucher as $voucher) {
                $pid = $voucher->getField('pid');
                $voucherId = $voucher->getField('voucherId');
                if (empty($pid)) {
                    continue;
                } else {
                    $parVoucher = \Prj\Data\Vouchers::getCopy($pid);
                    $parVoucher->load();
                    if (!$parVoucher->exists()) {
                        continue;
                    } else {
                        $userId = $parVoucher->getField('userId');
                        try {
                            $user = \Prj\Data\User::getCopy($userId);
                            $user->load();
                            $weekActiveBonus = \Prj\ActivePoints\UsedShareVoucher::getCopy($userId)->addNum(1)->updUser();
                            $user->update();
                            if (!empty($weekActiveBonus)) {
                                $this->loger->sarg3 = json_encode(array_merge(json_decode($this->loger->sarg3, true) ? : [], [
                                    'parentRedPacketPointPush' => ['all', $userId, null, json_encode($weekActiveBonus)],
                                ]));
                            }
                            $ret[$userId] = 1;
                        } catch (\ErrorException $e) {
                            $ret[$voucherId] = $e->getMessage();
                        }
                    }
                }
            }
        }
        return $ret;
    }

    protected $orderId = 0;
    protected $userInit;

    /**
     * 添加失败的回滚
     * @param \Prj\Data\Wares $wares
     * @param \Prj\Data\WalletTally $tally
     * @param \Prj\Data\Investment $invest
     */
    protected function add_rollback($wares, $tally, $invest, $user = null) {
        $msgForError = "u:" . $this->user->userId . " buy:" . $this->_request->get('waresId')
                . 'order:' . $this->orderId . ' amount:' . $this->_request->get('amount');

        if ($wares) {
            try {
                if ($this->waresInit) {
                    foreach ($this->waresInit as $k => $v) {
                        $wares->setField($k, $v);
                    }
                    try {
                        $wares->update();
                    } catch (\ErrorException $e) {
                        $this->loger->error("[err#wares#" . $e->getMessage() . "]$msgForError");
                    }
                } else {
                    $wares->unlock();
                }
            } catch (\ErrorException $e) {
                $this->loger->error("[err#wares#" . $e->getMessage() . "]$msgForError");
            }
        }
        if ($tally) {
            try {
                $tally->setField('descCreate', self::$error);
                $tally->updStatus(\Prj\Consts\Tally::status_abandon)->update();
            } catch (\ErrorException $e) {
                $this->loger->error("[err#tally#" . $e->getMessage() . "]$msgForError");
            }
        }
        if ($invest) {
            try {
                $invest->setField('descCreate', self::$error);
                $invest->updStatus(\Prj\Consts\OrderStatus::unusual)->update();
            } catch (\ErrorException $e) {
                $this->loger->error("[err#invest#" . $e->getMessage() . "]$msgForError");
            }
        }
        if ($this->voucher) {
            try {
                if (!is_array($this->voucher)) {
                    $this->voucher->setUsed($this->orderId, \Prj\Consts\Voucher::status_unuse)->update();
                } else {
                    //购买，改订单状态失败后回滚中没回滚券的使用（add_rollback只回滚了一张） 批量回滚
                    foreach ($this->voucher as $v) {
                        $v->setUsed($this->orderId, \Prj\Consts\Voucher::status_unuse)->update();
                    }
                }
            } catch (\ErrorException $e) {
                $this->loger->error("[err#voucher#" . $e->getMessage() . "]$msgForError");
            }
        }
        if ($user) {
            if ($this->userInit) {
                // var_log($this->userInit);
                foreach ($this->userInit as $k => $v) {
                    $user->setField($k, $v);
                }
            }
            try {
                $this->user->update();
            } catch (\ErrorException $e) {
                var_log('[error]用户更新失败');
                var_log($e->getMessage());
                $this->loger->error("[err#user#" . $e->getMessage() . "]$msgForError");
            }
        }
        if ($this->newVoucherId) {
            $tmp = \Prj\Data\Vouchers::getCopy($this->newVoucherId);
            $tmp->setField('descCreate', self::$error);
            if ($tmp->exists()) {
                $tmp->setUsed(0, \Prj\Consts\Voucher::status_abandon)->update();
            }
        }
        try {
            $this->user->unlock();
        } catch (\ErrorException $e) {
            
        }
    }

    /**
     *
     * @var \Prj\Data\Vouchers
     */
    protected $voucher;

    /**
     * 检查是否有用券，没有返回null,尝试使用无效的券，返回false, 正常返回券实例，
     * @param string $voucherId
     * @param number $amount 购买金额
     * @param \Prj\Data\Wares $wares 购买什么
     * @return \Prj\Data\Vouchers 没用券返回null，不符合使用条件，返回false
     */
    protected function add_getVoucher($voucherId, $amount, $wares, $days = 0) {
        if (!empty($voucherId)) {
            $voucher = \Prj\Data\Vouchers::getCopy($voucherId);
            $voucher->load();
            if (!$voucher->exists()) { //查询为空
                \Prj\Consts\Voucher::$isUsableForMsg = 'voucher_invalid';
                return false;
            }
            if ($voucher->isExpire()) {
                \Prj\Consts\Voucher::$isUsableForMsg = 'voucher_expire';
                error_log('error:voucher expire');
                return false;
            }
            if ($voucher->getField('userId') != $this->user->userId) {
                \Prj\Consts\Voucher::$isUsableForMsg = 'voucher_not_mine';
                return false;
            }
            if ($voucher->isUsableFor($wares, $amount, $days)) {
                return $voucher;
            } else {
                error_log('error:voucher with error userId or error status');
                return false;
            }
            return $voucher;
        } else {
            return null;
        }
    }

    /**
     * 检查产品余额，如果不足设置returnError and return false
     * @param \Prj\Data\Wares $wares
     * @param int $amount
     * @return bool
     */
    protected function add_checkRemain($wares, $amount, $amountExt) {
        $totalLeft = $wares->getField('remain');
        if ($totalLeft < $wares->getField('priceStart')) {
            $this->returnError(\Prj\Lang\Broker::getMsg('orders.ware_out'));
            return false;
        }
        if ($totalLeft < ($amount + $amountExt)) {  //购买金额大于标的余额  //tgh 券规则 是否占用募集总额
            $this->returnError(\Prj\Lang\Broker::getMsg('orders.remain_out'));
            return false;
        }
        //购买金额不得少于起投金额
        if (($amountExt + $amount) < $wares->getField('priceStart')) { //tgh 券规则 是否参与起步价
            $this->returnError(\Prj\Lang\Broker::getMsg('orders.priceStart_out'));
            return false;
        }
        //已当前金额购买后余量不满足下次购买，请调整购买额度
        if (($totalLeft - $amount - $amountExt) < $wares->getField('priceStart') && $totalLeft - $amount - $amountExt != 0) { //tgh 券规则 是否占用募集总额
            $this->returnError(\Prj\Lang\Broker::getMsg('orders.remain_next_out'));
            return false;
        }
        //购买额不符合递增价的设置
        $chk = ($amountExt + $amount) - $wares->getField('priceStart'); //tgh 券规则 是否参与起步价
        if ($chk != floor($chk / $wares->getField('priceStep')) * $wares->getField('priceStep')) { //float类型影响结果 !==  改成  !=
            error_log('amount_change2>>>>> $amountExt=' . $amountExt . ' $amount=' . $amount . ' $priceStart=' . $wares->getField('priceStart'));
            $this->returnError(\Prj\Lang\Broker::getMsg('orders.priceStep_out'));
            return false;
        }
        return true;
    }

    //是否有购买该产品的资格
    protected function add_buyLimit(\Prj\Data\Wares $wares) {
        $userLimit = $wares->getField('userLimit', true);
        $vipLevel = $wares->getField('vipLevel', true);
        $userId = $this->user->userId;
        $myVipLevel = $this->user->getField('vipLevel');
        //var_log($userLimit,'error>>>');
        if ($userLimit !== '0') {
            $userArr = explode(',', $userLimit);
            if (!in_array($userId, $userArr)) {
                $this->returnError(\Prj\Lang\Broker::getMsg('orders.user_limit'));
                return false;
            }
        }
        if ($vipLevel !== '0') {
            if ($vipLevel > $myVipLevel) {
                $this->returnError(\Prj\Lang\Broker::getMsg('orders.limit_vipLevel'));
                return false;
            }
        }
        return true;
    }

    /**
     * 我的可用的红包/券
     * @input string waresId 标的ID
     * @input string voucherType 券类型 为空默认红包 8:红包    4：代金券    2：加息券
     * @output {'code':200,'count':【红包总额】,'list':【红包列表】}
     * @errors {'code':400,'msg':'args_error'} 参数错误
     * @errors {'code':400,'msg':'no_wares'} 标的不存在
     */
    public function myRedPacketAction() {
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $waresId = $this->_request->get('waresId');
        $voucherType = $this->_request->get('voucherType', \Prj\Consts\Voucher::type_real);
        if (empty($waresId))
            return $this->returnError(\Prj\Lang\Broker::getMsg('system.arg_error'));
        $wares = \Prj\Data\Wares::getCopy($waresId);
        $wares->load();
        if (!$wares->exists())
            return $this->returnError(\Prj\Lang\Broker::getMsg('orders.no_wares'));
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $pager = new \Sooh\DB\Pager(9999, $this->pageSizeEnum, false);
        $pager->init(-1, 1);
        $where = array(
            "voucherType" => $voucherType,
            "statusCode" => 0,
            "dtExpired]" => \Sooh\Base\Time::getInstance()->ymdhis(),
            "userId" => $this->user->userId,
            'statusCode]' => 0
        );
        $rs = \Prj\Data\Vouchers::paged($pager, $where);
        $count = 0;
        foreach ($rs as $k => $v) {
            if (!\Prj\Data\Vouchers::checkLimit($waresId, $v)) {
                unset($rs[$k]);
            } else {
                $count += $v['amount'];
            }
        }
        $this->returnOK();
        $this->_view->assign('count', $count);
        $this->_view->assign('list', $rs); //出现多个assign中的list字段重复，修改如下突出唯一识别
        $this->_view->assign('listmyredpacket', $rs);
    }

    /**
     * 跳转过渡页面
     * @input string notice 跳转页的提示信息
     * @input string url 跳转页的跳转地址
     * @input int time 跳转页的倒计时
     */
    public function redirectShowAction() {
        $notice = $this->_request->get('notice', '需要跳转');
        $time = $this->_request->get('time', '1');
        $url = $this->_request->get('url');
        $this->_view->assign('notice', $notice);
        $this->_view->assign('time', $time);
        $this->_view->assign('url', $url);
    }

}
