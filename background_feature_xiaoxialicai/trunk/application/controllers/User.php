<?php

/**
 * 订单接口，诸如：下单，我的订单等等
 * @author simon.wang
 */
class UserController extends \Prj\UserCtrl
{
	const paypwd_format = '#^\d{6}$#';
    /**
     * 绑卡
     * By Hand
     * 绑定第一步：
     * @input string bankId 银行代码 icbc 【第一步】
     * @input string bankCard 银行卡号码 【第一步】
     * @input string realName 姓名 【第一步】
     * @input int phone 手机号 【第一步】
     * @input int idCardType 证件类型 1:身份证 【第一步】
     * @input string idCardSn 证件号码 【第一步】
     * @input string returnUrl 回转地址 【第一步】+ 【第二步】
     * @input string cmd 指令   binding:绑定第一步 bindingcode:绑定第二步 发送验证码 【第一步】+ 【第二步】
     * @input string smsCode 短信验证码 【第二步】
     * @input string ticket 第一步得到的神秘代码 【第二步】
     * //返回客户端旧的标识信息
     * @output 【第一步】{returnUrl: "",retAll: {ret: "ok",got: {code: "200",msg: "成功",ticket: "ceb1218c2a1d45ac8d1a87b077d6ea1a"}},code: 400,msg: "null_return"}
     * / 【第二步】{returnUrl: "",retAll: {ret: "ok",got: {cardId: "47495",code: "200",orderId: "1447724571610102103"}},CardBound: {orderId: "1447724571610102103",status: 16},code: 200}
     * //返回客户端新的标识信息
     * @output 【第一步】{returnUrl: "",bindCardretAll: {ret: "ok",got: {code: "200",msg: "成功",ticket: "ceb1218c2a1d45ac8d1a87b077d6ea1a"}},code: 400,msg: "null_return"}
     * / 【第二步】{returnUrl: "",bindCardCoderetAll: {ret: "ok",got: {cardId: "47495",code: "200",orderId: "1447724571610102103"}},CardBound: {orderId: "1447724571610102103",status: 16},code: 200}
     * @errors {"code":505,"msg":"db_error"} 数据库操作异常，比如插入新纪录失败，磁盘满了
     * @errors {"code":400,"msg":"error_name"} 姓名不符
     * @errors {"code":400,"msg":"error_idCardSn"} 身份证不符
     * @errors {"code":400,"msg":"error_cmd"} 错误的指令
     * @errors {"code":400,"msg":"card_exist"} 银行卡已经存在
     * @errors {"code":400,"msg":"addCard_failed"} 添加银行卡失败，系统错误
     * @errors {"code":400,"msg":"gw_error"} 支付网关错误
     * @errors {"code":400,"msg":"null_payCorp"} 支付网关未返回支付渠道
     * @errors {"code":400,"msg":"db_error"} 数据库错误
     * @errors {"code":400,"msg":"no_smsCode"} 为输入短信验证码
     * @errors {"code":400,"msg":"no_ticket"} 为输入神秘代码
     * @errors {"code":400,"msg":"null_cardId"} 支付网关未返回cardId
     * @errors {"code":400,"msg":"null_orderId"} 支付网关未返回orderId
     * @errors {"code":400,"msg":"null_card"} 不存在的银行卡
     * @errors {"code":400,"msg":"already_binding"} 该卡已经绑定过了
     * @errors {"code":400,"msg":"update_card_failed"} 卡信息更新失败，数据库错误
     * @errors {"code":400,"msg":"update_user_failed"} 用户信息更新失败，数据库错误
     * @errors {"code":400,"msg":"rpc_failed:binding"} 网关未响应
     * @errors {"code":400,"msg":"name_out"} 实名长度超过20
     * @errors {"code":400,"msg":"phone_number_is_incorrect"} 手机号码不合法
     */
    public function bindcardAction()
    {
        return $this->returnError('您的客户端版本过低，您需要更新至最新客户端才能登录小虾理财。目前更新服务器升级维护中，请您耐心等待。');
        $forbid_bind = \Prj\Data\Config::get('forbid_bind');
        if($forbid_bind['forbid'])return $this->returnError($forbid_bind['notice']);
        //error_reporting(E_ALL);
        //var_log($this->_checkNickname('mj'), 'mj>>>>>>>>>>');
        //TODO: 加验证码 防止频繁提交
        $this->user->load();
        var_log($this->user->userId, 'userId>>>>>>>>>>');
        $cmd = $this->_request->get('cmd', 'binding');
        $this->_view->assign('returnUrl', $this->_request->get('returnUrl'));
        //$this->_view->assign('returnUrl', $this->_request->get('returnUrl'));
        $frm = \Sooh\Base\Form\Broker::getCopy('default')
            ->init(\Sooh\Base\Tools::uri(), 'get', \Sooh\Base\Form\Broker::type_c);
        $frm->addItem('bankId', \Sooh\Base\Form\Item::factory('银行', 'icbc', \Sooh\Base\Form\Item::text)->initMore(new \Sooh\Base\Form\Options(\Prj\Consts\Banks::$enums, '不限')))
            ->addItem('bankCard', \Sooh\Base\Form\Item::factory('卡号', '6222021304002871932', \Sooh\Base\Form\Item::text))
            ->addItem('realName', \Sooh\Base\Form\Item::factory('姓名', '汤高航', \Sooh\Base\Form\Item::text))
            ->addItem('phone', \Sooh\Base\Form\Item::factory('手机号', '13262798028', \Sooh\Base\Form\Item::text))
            ->addItem('idCardType', \Sooh\Base\Form\Item::factory('证件类型', '1', \Sooh\Base\Form\Item::select)->initMore(new \Sooh\Base\Form\Options(\Prj\Consts\IdCardType::$enums, '不限')))
            ->addItem('idCardSn', \Sooh\Base\Form\Item::factory('证件号码', '340823199311284918', \Sooh\Base\Form\Item::text))
            ->addItem('ticket', \Sooh\Base\Form\Item::factory('标识', '123', \Sooh\Base\Form\Item::text))
            ->addItem('orderId', \Sooh\Base\Form\Item::factory('订单号', '', \Sooh\Base\Form\Item::text))
            ->addItem('smsCode', \Sooh\Base\Form\Item::factory('验证码', '123', \Sooh\Base\Form\Item::text))
            ->addItem('returnUrl', $this->_request->get('returnUrl'))
            ->addItem('cmd', \Sooh\Base\Form\Item::factory('指令', $cmd, \Sooh\Base\Form\Item::text));

        //TODO:oauth 安全校验
        //。。。

        $frm->fillValues();

        $this->_view->assign('returnUrl', $this->_request->get('returnUrl'));
      // $this->_view->assign('returnUrlbindcard', $this->_request->get('returnUrl'));
        $inputs = $frm->getFields();
        if (mb_strwidth($inputs['realName']) > 33) {
            return $this->returnError(\Prj\Lang\Broker::getMsg('user.realname_long_wrong'));
        }

        //提交表单
        if (!empty($inputs['cmd'])) {
            //如果已经有姓名了再绑第二张卡就不用姓名身份证和支付密码了
            $nickname = $this->user->getField('nickname');
            $idCardSn = $this->user->getField('idCard');
            if ($this->_checkNickname($nickname)) {
                var_log($this->_checkNickname($nickname), '$this->_checkNickname($nickname)>>>>>>>>>>>>');
                if (!empty($inputs['realName'])) {
                    if ($inputs['realName'] != $nickname) {
                        var_log($inputs['realName'] . '/' . $nickname, 'error>>>');
                        return $this->returnError(\Prj\Lang\Broker::getMsg('user.error_realname')); //error_name
                    }
                } else {
                    $inputs['realName'] = $nickname;
                }
            }

            if(!empty($idCardSn)){
                if (!empty($inputs['idCardSn'])) {
                    if ($inputs['idCardSn'] != $idCardSn) {
                        return $this->returnError(\Prj\Lang\Broker::getMsg('user.error_idCardSn')); //error_idCardSn
                    }
                } else {
                    $inputs['idCardSn'] = $idCardSn;
                }
            }

            //调用绑卡网关
            if (\Sooh\Base\Ini::getInstance()->get('noGW')) {
                $rpc = self::getRpcDefault('PayGW'); //debug
            } else {
                $rpc = \Sooh\Base\Rpc\Broker::factory('PayGW');
            }
            $sys = \Lib\Services\PayGW::getInstance($rpc);
            //绑卡第一步
            if ($inputs['cmd'] == 'binding') {
                //身份证唯一性验证
                if($inputs['idCardSn']){
                    $idCard = \Prj\Data\IdCard::check($inputs['idCardSn'],$this->user->userId);
                    if(empty($idCard) && \Prj\Data\Config::get('idcardUnique')){
                        return $this->returnError('该身份证已经被使用');
                    }
                }
                $result = $this->_bindCard($inputs, $sys);
                if (!empty($result)) return $this->returnError($result);
            } //绑卡第二步
            elseif ($inputs['cmd'] == 'bindingcode') {
                $result = $this->_bindCardCode($inputs, $sys);
                if (!empty($result)) return $this->returnError($result);
            } else {
                return $this->returnError(\Prj\Lang\Broker::getMsg('system.arg_error'));
            }
        }
        return $this->returnOK();
    }

    //实名认证过：true    没有认证：false
    protected function _checkNickname($nickname)
    {
        $preg = "/^[\x{4e00}-\x{9fa5}]+$/u"; //中文汉字匹配 utf-8
        return (preg_match($preg, $nickname)) ? true : false;
    }

    /**
     * 绑卡第一步 提交资料
     * By Hand
     */
    protected function _bindCard($inputs, $sys)
    {
        $age = \Prj\IdCard::getAge($inputs['idCardSn']);
        if(!\Prj\IdCard::verify($inputs['idCardSn']) || $age <= 0 || $age>=100){
            return '证件号不匹配';
        }
        if($age < 18){
            return '您的年龄未满18岁，无法接受小虾的服务';
        }

        $userId = $this->user->userId;

        //手机号验证
        $rules = [
            'phone' => [\Lib\Misc\InputValidation::$define['phone'], \Prj\Lang\Broker::getMsg('account.phone_number_is_incorrect')],
        ];
        if (\Lib\Misc\InputValidation::validateParams(['phone'=>$inputs['phone']], $rules) === false) {
            return $this->returnError(\Lib\Misc\InputValidation::$errorMsg);
        }


        //卡是否已经存在
        $rs = \Prj\Data\BankCard::repeatCheck($userId, $inputs['bankId'], $inputs['bankCard']);
        if (!empty($rs)) {
            var_log($rs, 'repeatCard>>>>>>>>>>');
            return \Prj\Lang\Broker::getMsg('user.card_exist');//'card_exist';
        }
        //添加流水
        $payCorp = 0;
        $o = \Prj\Data\BankCard::addCard($this->user->userId, $inputs['bankId'], $inputs['bankCard'], $payCorp, $inputs['realName'], $inputs['phone'], $inputs['idCardType'], $inputs['idCardSn']);
        if (empty($o)) {
            return \Prj\Lang\Broker::getMsg('user.addCard_failed');//'addCard_failed';
        }

        $sn = $o->getPKey()['orderId'];
        \Sooh\Base\Session\Data::getInstance()->set('snMark',['bindCard'=>$sn]);

        //添加注册手机号
        $regPhone = $this->user->getField('phone');
        try {
            $ret = $sys->binding($sn, $userId, $inputs['realName'], $inputs['idCardType'], $inputs['idCardSn'], $inputs['bankId'], $inputs['bankCard'], $inputs['phone'], \Sooh\Base\Tools::remoteIP() , $regPhone);
            $this->_view->assign('retAll', ['ret' => 'ok', 'got' => $ret]);//出现多个assign中的retAll字段重复，修改如下突出唯一识别
            $this->_view->assign('bindCardretAll', ['ret' => 'ok', 'got' => $ret]);
            
        } catch (\Sooh\Base\ErrException $e) {
            $this->loger->error('visit gateway failed when bind-card:' . $e->getMessage());
            $code = $e->getCode();
            if ($code == 400) {
                $this->_view->assign('retAll', ['ret' => $e->getMessage(), 'got' => $e->customData]);
                try{
                    $o->setField('resultMsg','rpc:'.$e->getMessage());
                    $o->update();
                }catch (\ErrorException $e){
                    //todo nothing
                }
                return $e->getMessage();
            } elseif ($code == 500) {
                $this->_view->assign('retAll', ['ret' => $e->getMessage(), 'got' => $e->customData]);
                error_log('error:' . '500');
                return $e->getMessage();
            }
            return \Prj\Lang\Broker::getMsg('system.gw_error');//'gw_error';
        }

        if($ret['code']!=200){
            return $ret['msg'];
        }

        if (empty($ret['payCorp'])) {
            $this->loger->error('get payCorp failed when bind-card:');
            return 'null_payCorp';
        }
        try {
            $o->setField('payCorp', $ret['payCorp']);
            $o->update();
        } catch (\ErrException $e) {
            $this->loger->error('update payCorp failed when bind-card:' . $e->getMessage());
            return \Prj\Lang\Broker::getMsg('system.db_error');
        }
        return;
    }

    /**
     * 绑卡第二步 短信验证码提交
     * By Hand
     */
    protected function _bindCardCode($inputs, $sys)
    {
        $this->user->load();

        if (empty($inputs['smsCode'])) {
            return \Prj\Lang\Broker::getMsg('user.no_smsCode');
        }
        /*
        if (empty($inputs['ticket'])) {
            return 'no_ticket';
        }
        */
        $sn = \Sooh\Base\Session\Data::getInstance()->get('snMark')['bindCard'];
        //var_log($sn,'绑卡获取的sn>>>');
        error_log('warning#'.$sn.'#从session里面获取卡号成功...');
        if(empty($sn)){
            return '系统错误:未获取到订单号';
        }

        //身份证检查
        $bindCard = \Prj\Data\BankCard::getCopy($sn);
        $bindCard->load();
        if(!$bindCard->exists())return $this->returnError('系统错误,请稍后重试');
        $inputs['idCardSn'] = $bindCard->getField('idCardSN');

        $idCard = \Prj\Data\IdCard::check($inputs['idCardSn'],$this->user->userId);
        if(empty($idCard) && \Prj\Data\Config::get('idcardUnique')){
            return '该身份证已经被使用';
        }
        if($idCard)$idCard->setField('statusCode',0);
        try {
            $ret = $sys->bindingCode($inputs['smsCode'], $sn, $this->user->userId, $sn );
            $this->_view->assign('retAll', ['ret' => 'ok', 'got' => $ret]);//出现多个assign中的retAll字段重复，修改如下突出唯一识别
            $this->_view->assign('bindCardCoderetAll', ['ret' => 'ok', 'got' => $ret]);
        } catch (\Sooh\Base\ErrException $e) {
            $this->loger->error('visit gateway failed when bind-card-smscode:' . $e->getMessage());
            $code = $e->getCode();
            if ($code == 400) {
                $this->_view->assign('retAll', ['ret' => $e->getMessage(), 'got' => $e->customData]);
                return $e->getMessage();
            } elseif ($code == 500) {
                $this->_view->assign('retAll', ['ret' => $e->getMessage(), 'got' => $e->customData]);
                return $e->getMessage();
            }
            return \Prj\Lang\Broker::getMsg('system.gw_error');
        }

        if($ret['code']!=200){
            return $ret['msg']?$ret['msg']:'rpc_failed:bindingCode';
        }

        //验证以后
        if (empty($ret['cardId'])) {
            $this->loger->error('get cardId failed when bindcard');
            return \Prj\Lang\Broker::getMsg('system.gw_error');
        }
        if (empty($ret['orderId'])) {
            $this->loger->error('get orderId failed when bindcard');
            return \Prj\Lang\Broker::getMsg('system.gw_error');
        }

        try {
            $card = \Prj\Data\BankCard::getCopy($ret['orderId']);
            $card->load();
            $card->setField('statusCode', \Prj\Consts\BankCard::abandon);
            $card->update();
        } catch (\ErrorException $e) {
            $this->loger->error('update card statusCode failed when bindcard');
            error_log('update card statusCode failed when bindcard');
            return \Prj\Lang\Broker::getMsg('system.db_error');
        }


        $card = \Prj\Data\BankCard::getCopy($ret['orderId']);
        $card->load();
        if (!$card->exists()) return 'null_card';
        $cardId = $card->getField('cardId');
        if ($card->getField('statusCode') == \Prj\Consts\BankCard::enabled && !empty($cardId)) {
            return \Prj\Lang\Broker::getMsg('user.already_binding');//return 'already_binding';
        }
        $card->setField('statusCode', \Prj\Consts\BankCard::enabled);
        $card->setField('cardId', $ret['cardId']);
        $card->setField('resultTime', \Sooh\Base\Time::getInstance()->ymdhis());
        try {
            $card->update();
            error_log('warning#'.$ret['orderId'].'#银行卡更新成功...');
        } catch (\ErrException $e) {
            $this->loger->error('update bindcard failed when bind-card:' . $e->getMessage());
            return \Prj\Lang\Broker::getMsg('system.update_card_failed');//return 'update_card_failed';
        }
        //存身份证
        if(\Prj\Data\Config::get('idcardUnique') && isset($idCard)){
            try{
                $idCard->update();
            }catch (\ErrorException $e){
                var_log("[error]身份证表更新失败 id:".$inputs['idCardSn']);
                $this->loger->error("[error]身份证表更新失败 id:".$inputs['idCardSn']);
            }
        }

        $error = $this->_updateUser($card); //更新用户信心
        if (!empty($error)) {
            $card->updStatus(\Prj\Consts\BankCard::abandon)->update();
            $this->loger->error('update user failed when bindcard');
            if(\Prj\Data\Config::get('idcardUnique') && isset($idCard)){
                $idCard->setField('statusCode',-1);
                $idCard->update();
            }
            return $error;
        }
        error_log('warning#'.$ret['orderId'].'#绑卡结束 The End ...');
        return;
    }

    /**
     * 绑卡成功后更新用户信息
     */
    protected function _updateUser(\Prj\Data\BankCard $card)
    {
        $this->user->load();
        $nickname = $this->user->getField('nickname');
        $userChanged = false;
        //var_log($this->_checkNickname($nickname), '$this->_checkNickname($nickname)>>>>>>>>>');
        if (!$this->_checkNickname($nickname)) {
            $this->user->setField('nickname', $card->getField('realName'));
            $userChanged = true;
        }
        if ($card->getField('idCardType') == \Prj\Consts\IdCardType::shenFenZheng && $this->user->getField('idCard') == '') {
            $this->user->setField('idCard', $card->getField('idCardSN'));
            var_log('[warning]idCard:'.$card->getField('idCardSN'));
            $userChanged = true;
        }
        //var_log($this->user->getField('ymdBindcard'), 'ymdBindcard>>>>>>');
        //var_log($card->getField('statusCode'), 'statusCode>>>>>>');


        if (($this->user->getField('ymdBindcard') == 0 && \Prj\Consts\BankCard::enabled == $card->getField('statusCode'))||$this->user->getField('isSuperUser')) {
            $userChanged = true;
            $this->user->setField('ymdBindcard', \Sooh\Base\Time::getInstance()->YmdFull);
            var_log('[warning]ymdBindcard:'.\Sooh\Base\Time::getInstance()->YmdFull);
            //首绑送红包
            $ret = \Lib\Services\EvtBinding::getInstance(self::getRpcDefault('EvtBinding'))->giveVouchersWherFirstBind($this->user->userId);
            if(!empty($ret)){
                $this->loger->sarg3 = json_encode(array_merge(json_decode($this->loger->sarg3, true) ? : [], [
                    '_updateUser' => [
                        ['event' => 'red_invate_packet', 'num_packet' => 1, 'private_gift' => $ret['amount']/100, 'num_deadline' => 48, 'brand' => \Prj\Message\Message::MSG_BRAND],
                        ['userId' => $this->user->userId, 'phone' => $this->user->getField('phone')]
                    ],
                ]));

                $this->_view->assign('award',$ret);
                $this->_view->assign('redPacket',$ret);
            }
        }
	    //此写法不对(也不用更新！)-可废弃
//        try {
//            $oauthRet = (new \Prj\Oauth\Oauth())->invokeOauth('updNickname', ['nickname' => $card->getField('realName')]);
//            if ($oauthRet['code'] != 200) {
//                $this->loger->ext = 'update oauth nickname error';
//            }
//        } catch (\ErrorException $e) {
//			error_log("update user failed when ".__FUNCTION__.':'.$e->getMessage()."\n".$e->getTraceAsString());
//            $this->loger->error('update oauth nickname error');
//        }

        try {
            if ($userChanged) {
                $this->user->update();
                //var_log($this->user,'this->user>>>>');
            }
        } catch (Exception $ex) {
            $this->loger->error('update user failed when bind-card:' . $ex->getMessage());
            var_log($ex->getMessage(), 'warn update user failed');
            return \Prj\Lang\Broker::getMsg('system.update_user_failed');//return 'update_user_failed';
        }

        $this->_view->assign('CardBound', ["orderId" => current($card->getPkey()), "status" => \Prj\Consts\BankCard::enabled]);
        return;
    }
	public function donothingAction(){$this->returnOK();}
    /**
     * 领取红包
     * By Hand
     * showType:  1:百分比         2:文字           0：金额
     * @input string voucherId 券ID
     */
    public function openVoucherAction()
    {
        //$voucherNum = (new \Prj\Items\RedPacketForShare())->getNumByInvestment($inputs['amount']+$redAmount);
        $voucherId = $this->_request->get('voucherId');
       // if(empty($voucherId))return $this->returnError('no_voucherId');】
        if(empty($voucherId))return $this->returnError(\Prj\Lang\Broker::getMsg('voucher.voucherId_error'));
        $voucher = \Prj\Data\Vouchers::getCopy($voucherId);
        $voucher->load();
        //$type = $voucher->getField('statusCode');
        if (!$voucher->exists()) return $this->returnError(\Prj\Lang\Broker::getMsg('user.getvoucher_notfound'));
        if ($voucher->getField('userId') != $this->user->userId) return $this->returnError(\Prj\Lang\Broker::getMsg('user.getvoucher_belong_other'));
        $doAddAmount = false;
        if($voucher->getField('statusCode')==\Prj\Consts\Voucher::status_wait && $voucher->getField('voucherType') == \Prj\Consts\Voucher::type_real)$doAddAmount = true;
        if ($voucher->getField('statusCode') != \Prj\Consts\Voucher::status_wait && ($voucher->getField('statusCode') != \Prj\Consts\Voucher::status_unuse)) return $this->returnError(\Prj\Lang\Broker::getMsg('user.getvoucher_closed'));
        $voucher->setField('statusCode', \Prj\Consts\Voucher::status_unuse);
        try {
            $voucher->update();
        } catch (\Exception $e) {

        }
        $this->user->load();
        if ($voucher->getField('voucherType') == \Prj\Consts\Voucher::type_real && $doAddAmount) {
            $this->user->setField('redPacket', $this->user->getField('redPacket') + $voucher->getField('amount'));
            try {
                $this->user->update();
            } catch (\ErrorException $e) {
				error_log("update user failed when ".__CLASS__.'::'.__FUNCTION__.'() '.$e->getMessage()."\n".$e->getTraceAsString());
                return $this->returnError(\Prj\Lang\Broker::getMsg('system.server_busy'));
            }
        }

        $voucherDump = $voucher->dump();
        switch ($voucherDump['voucherType']) {
            case \Prj\Consts\Voucher::type_yield :
                $voucherDump['showType'] = 1;
                break;
            case \Prj\Consts\Voucher::type_share :
                $voucherDump['showType'] = 2;
                break;
            default :
                $voucherDump['showType'] = 0;
        }

        if($voucherDump['voucherType']==\Prj\Consts\Voucher::type_share){
            $voucherDump['voucherNum'] =  (new \Prj\Items\RedPacketForShare())->getNumByInvestment($voucherDump['amount']);
        }

        $voucherDump['appAmount'] = [
            'amount' => $voucherDump['amount'],
            'exp1' => $voucherDump['exp1'],
            'exp2' => $voucherDump['exp2'],
            'showType' => $voucherDump['showType'],
        ];
        $this->_view->assign('detail', $voucherDump);
        $this->returnOK();
    }

    /**
     *  补填邀请码
     * @input string code 邀请码
     * @errors no_code:邀请码为空
     * @errors error_code:不存在的邀请码
     */
	public function setInviteCodeAction() {
		$code = $this->_request->get('code');
		if (empty($code))
			return $this->returnError(\Prj\Lang\Broker::getMsg('user.setinvite_emptycode'));
		$code = strtoupper($code);
		try {
			$ret = \Prj\Data\User::setInviteCode($this->user->userId, $code);
		} catch (\Sooh\DB\Error $e) {
			return $this->returnError(\Prj\Lang\Broker::getMsg('system.server_busy'));
		} catch (Exception $e) {
			return $this->returnError(\Prj\Lang\Broker::getMsg($e->getMessage()));
		}
		$this->_view->assign('ret', $ret);
		return $this->returnOK();
	}

    /**
     * 实名认证
     * By Hand
     */
    public function setRealNameAction()
    {
        $user = $this->user;
        $user->load();
        $userId = $user->userId;
        $frm = \Sooh\Base\Form\Broker::getCopy('default')
            ->init(\Sooh\Base\Tools::uri(), 'get', \Sooh\Base\Form\Broker::type_c);
        $frm->addItem('nickname', \Sooh\Base\Form\Item::factory('真实姓名', '汤高航', \Sooh\Base\Form\Item::text))
            ->addItem('idCard', \Sooh\Base\Form\Item::factory('身份证号', '340823199311284918', \Sooh\Base\Form\Item::text))
            ->addItem('payPwd', \Sooh\Base\Form\Item::factory('支付密码', '123', \Sooh\Base\Form\Item::text));
        $frm->fillValues();
        $inputs = $frm->getFields();
        if (!empty($inputs['nickname']) && !empty($inputs['idCard']) && !empty($inputs['payPwd'])) {
            //调用支付网关
            //$rpc = self::getRpcDefault('PayGW'); //debug
            $rpc = \Sooh\Base\Rpc\Broker::factory('PayGW');
            $sys = \Lib\Services\PayGW::getInstance($rpc);
            try {
                $ret = $sys->setRealName($userId, $inputs['nickname'], $inputs['idCard']);
                $this->_view->assign('retAll', ['ret' => 'ok', 'got' => $ret]);//多个assign里面有多个retAll,修改如下作为唯一标识
                $this->_view->assign('setRealNameretAll', ['ret' => 'ok', 'got' => $ret]);
            } catch (\Sooh\Base\ErrException $e) {
                $this->loger->error('visit gateway failed when setRealName:' . $e->getMessage());
                $code = $e->getCode();
                if ($code == 400) {
                    $this->_view->assign('retAll', ['ret' => $e->getMessage(), 'got' => $e->customData]);
                    return $this->returnError(\Prj\Lang\Broker::getMsg('user.contact_paygw_failed'));
                } elseif ($code == 500) {
                    $this->_view->assign('retAll', ['ret' => $e->getMessage(), 'got' => $e->customData]);
                    return $this->returnError(\Prj\Lang\Broker::getMsg('user.contact_paygw_failed'));
                }else{
					error_log("unknown code when contract paygw:".$e->getCode().":".$e->getMessage());
					return $this->returnError(\Prj\Lang\Broker::getMsg('user.contact_paygw_failed'));
                }
            }
            $user->setField('nickname', $inputs['nickname']);
            $user->setField('idCard', $inputs['idCard']);
            try {
                $user->update();
            } catch (\ErrException $e) {
                $this->loger->error('update user failed when setRealName:' . $e->getMessage());
                return $this->returnError(\Prj\Lang\Broker::getMsg('system.db_error'));
            }
        }
        $this->returnOK();
    }

    /**
     * TODO:我的状态
     */
    public function myselfAction()
    {

    }

    /**
     * 充值
     * By Hand
     * @input string $cardAutoId 使用的银行卡ID ① (不是卡号 是卡的orderId)
     * @input int $amount 充值额 ①
     * @input int $smsCode 短信验证码 ②
     * @input ticket $ticket 完成第一步后产生的神秘代码 ②
     * @input string $cmd 充值指令 recharge：第一步  rechargecode：第二步
     * @input string $paypwd 支付密码 ②
     * //返回客户端旧的标识信息
     * @output ①{returnUrl: "",retAll: {ret: "ok",got: {code: "200",msg: "成功","payPwdIsLocked":1,payCorp: "101",ticket: "8f8afb4c06b2497baf42a4211ac70a2c"}},code: 200}
     * ②{returnUrl: "",retAll: {ret: "ok",got: {code: "200",msg: "成功",payCorp: "101",ticket: "923c190d764b4b4bbed5c00cd9196079"}},code: 200}
     * //返回客户端新的标识信息
     * @output ①{returnUrlrecharge: "",rechargeretAll: {ret: "ok",got: {code: "200",msg: "成功","payPwdIsLocked":1,payCorp: "101",ticket: "8f8afb4c06b2497baf42a4211ac70a2c"}},code: 200}
     * ②{returnUrl: "",rechargeCoderetAll: {ret: "ok",got: {code: "200",msg: "成功",payCorp: "101",ticket: "923c190d764b4b4bbed5c00cd9196079"}},code: 200}
     * @errors {"code":400,"msg":"rpc_failed:recharge"} 服务器未响应 [网关错误]
     * @errors {"code":400,"msg":"rpc_failed:rechargeCode"} 服务器未响应
     * @errors {"code":400,"msg":"bank_error"} 银行卡错误
     * @errors {"code":400,"msg":"gw_error"} 网关错误
     * @errors {"code":400,"msg":"no_smsCode"} 为输入短信验证码
     * @errors {"code":400,"msg":"error_return"} 网关未返回orderId
     * @errors {"code":400,"msg":"ordersOfCharge_failed"} 获取充值订单失败
     * @errors {"code":400,"msg":"db_error"} 数据库错误
     * @errors {"code":400,"msg":"null_return"} 网关未返回支付渠道号
     * @errors {"code":400,"msg":"addOrder_failed"} 创建充值订单失败
     * @errors {"code":400,"msg":""}
     * @errors {"code":400,"msg":""}
     * @errors {"code":400,"msg":""}
     */
    public function rechargeAction()
    {
        return $this->returnError('您的客户端版本过低，您需要更新至最新客户端才能登录小虾理财。目前更新服务器升级维护中，请您耐心等待。');
        $forbid_recharge = \Prj\Data\Config::get('forbid_recharge');
        if($forbid_recharge['forbid'])return $this->returnError($forbid_recharge['notice']);
        //TODO: 如果已经有姓名了再绑第二张卡就不用姓名身份证和支付密码了
        //$cards = \Prj\Data\BankCard::getCopy('');
        $this->loger->target = $amount = $this->_request->get('amount') - 0;
        $cmd = $this->_request->get('cmd', 'recharge');
        //$paypwd = $this->_request->get('paypwd') - 0;//改用短信验证码
        $returnUrl = $this->_request->get('returnUrl');
        $this->_view->assign('returnUrl', $returnUrl);//出现多个assign中的returnUrl字段重复，修改如下突出唯一识别
        $this->_view->assign('returnUrlrecharge', $returnUrl);
        //$myCards      = $cards->db()->getAssoc($cards->tbname(), 'orderId', 'isDefault,bankId,bankCard,cardId', ['userId' => $this->user->userId, 'statusCode' => \Prj\Consts\BankCard::enabled]);
        $myCards = $this->_myCards();

        $this->loger->ext = $cardAutoId = $this->_request->get('cardAutoId', current($myCards)['orderId']);
        var_log(current($myCards), 'key($myCards)>>>>>>>>>>>>>>>>>>');
        //var_log($myCards,'recharge>>>>>>>>myCards');
        var_log($cardAutoId, 'recharge>>>>>>>>cardAutoId');
        $defaultCard = 0;
        $cardSelected = [];
        //TODO:oauth 安全校验
        //。。。
        foreach ($myCards as $k => $r) {
            if ($r['isDefault']) {
                $defaultCard = $k;
            }

            if ($k == $cardAutoId) {
                $cardSelected['bankId'] = $r['bankId'];
                $cardSelected['cardId'] = $r['cardId'];
                $this->loger->mainType = $cardSelected['bankId'];
                $cardSelected['bankCard'] = $r['bankCard'];
                $this->loger->subType = '**********' . substr($cardSelected['bankCard'], 10);
            }

            $myCards[$k] = $r['bankId'] . ')' . $r['bankCard'];
        }
        if (empty($defaultCard)) $defaultCard = key($myCards);  //没有默认就选第一张
        $frm = \Sooh\Base\Form\Broker::getCopy('default')
            ->init(\Sooh\Base\Tools::uri(), 'get', \Sooh\Base\Form\Broker::type_c);
        $frm->addItem('amount', \Sooh\Base\Form\Item::factory('充值额', $amount, \Sooh\Base\Form\Item::text))
            //->addItem('cardAutoId', \Sooh\Base\Form\Item::factory('使用卡', empty($cardAutoId) ? $defaultCard : $cardAutoId, \Sooh\Base\Form\Item::select, $myCards))
            ->addItem('paypwd', \Sooh\Base\Form\Item::factory('支付密码', '', \Sooh\Base\Form\Item::text))
            ->addItem('ticket', \Sooh\Base\Form\Item::factory('ticket', '', \Sooh\Base\Form\Item::text))
            ->addItem('smsCode', \Sooh\Base\Form\Item::factory('smsCode', '', \Sooh\Base\Form\Item::text))
            ->addItem('cmd', \Sooh\Base\Form\Item::factory('cmd', $cmd, \Sooh\Base\Form\Item::text))
            ->addItem('returnUrl', $returnUrl);
        $frm->fillValues();
        $inputs = $frm->getFields();
        if (!empty($inputs['cmd'])) {
            //调用绑卡网关
            if (\Sooh\Base\Ini::getInstance()->get('noGW')) {
                $rpc = null;
            } else {
                $rpc = \Sooh\Base\Rpc\Broker::factory('PayGW');
            }
            $sys = \Lib\Services\PayGW::getInstance($rpc);
            //充值第一步 提交资料
            $this->user->load();
            if ($inputs['cmd'] == 'recharge') {
                //检查支付密码是否被锁
                $failedForbidden = $this->user->getField('failedForbidden');
                $this->_view->assign('payPwdIsLocked', 0);
                if (!empty($failedForbidden)) {
                    if ($failedForbidden['forbidden'] == 1 && \Sooh\Base\Time::getInstance()->timestamp() <= $failedForbidden['forbiddenExpires']) {
                        $this->_view->assign('payPwdIsLocked', 1);
                    }
                }

                if (empty($cardSelected['bankId'])) {
                    $arr = explode(')', $myCards[$defaultCard]);
                    $cardSelected = [
                        'bankId' => $arr[0],
                        'bankCard' => $arr[1],
                    ];
                    //var_log($cardAutoId,'bank_error>>>>>>>>>');
                    //return $this->returnError('bank_error');
                }

                if (empty($cardSelected['bankId'])) {
                    var_log($cardSelected, 'bankcard_error on '.__CLASS__.':'.__FUNCTION__.'()>>>>>>>>>');
                    return $this->returnError(\Prj\Lang\Broker::getMsg('user.recharge_bankerror'));
                }

                $inputs = array_merge($inputs, $cardSelected);
                $error = $this->_recharge($inputs, $sys);
                if (!empty($error)) return $this->returnError($error);
                //充值第一步 提交资料
            } elseif ($inputs['cmd'] == 'rechargecode') {
                //支付密码验证 160229 取消支付密码验证
                /*
                if($this->_checkPaypwd($inputs['paypwd']) !== true)
                {
                    return $this->returnError(\Prj\Lang\Broker::getMsg('user.paypwd_error'));
                }
                */

                $error = $this->_rechargeCode($inputs, $sys);
                if (!empty($error)) return $this->returnError($error);
            } else {
                return $this->returnError(\Prj\Lang\Broker::getMsg('system.arg_error'));
            }
            return $this->returnOK();
        } else {
            $this->_view->assign('rechargesForm', $frm);
            $this->returnOK();
        }

    }

    protected function _myCards()
    {
        //$cards   = \Prj\Data\BankCard::getCopy('');
        //$myCards = $cards->db()->getAssoc($cards->tbname(), 'orderId', 'isDefault,bankId,bankCard,cardId', ['userId' => $this->user->userId, 'statusCode' => \Prj\Consts\BankCard::enabled]);
        $myCards = \Prj\Data\BankCard::loopAll(['userId' => $this->user->userId, 'statusCode' => \Prj\Consts\BankCard::enabled]);
        return $myCards;
    }

    /**
     * By Hand
     */
    protected function _recharge($inputs, \Lib\Services\PayGW $sys)
    {
        \Sooh\Base\Session\Data::getInstance()->set('snMark',[]);
        $inputs['amount'] = abs($inputs['amount']);
        $this->loger->target = $inputs['amount'];
        \Prj\Misc\OrdersVar::$introForUser = '伪充值';
        \Prj\Misc\OrdersVar::$introForCoder = 'fakecharge';
        $ordersOfCharge = \Prj\Data\Recharges::addOrders($this->user->userId, $inputs['amount'], $inputs['bankId'], $inputs['bankCard']);

        if($inputs['amount']<1){
            return '单笔充值最低0.01元';
        }

        if ($ordersOfCharge) {
            $orderId = current($ordersOfCharge->getPKey());
            try {
                \Sooh\Base\Session\Data::getInstance()->set('snMark',['recharge'=>$orderId]);
                $ret = $sys->recharge($orderId, $this->user->userId, $inputs['bankId'], $inputs['bankCard'], $inputs['amount'], \Sooh\Base\Tools::remoteIP());
                $this->_view->assign('retAll', ['ret' => 'ok', 'got' => $ret]);//出现多个assign中的retAll字段重复，修改如下突出唯一识别
                $this->_view->assign('rechargeretAll', ['ret' => 'ok', 'got' => $ret]);
                // throw new \Sooh\Base\ErrException('网关未响应>>>');
            } catch (\Sooh\Base\ErrException $e) {
                var_log($inputs, 'error:inputs>>>>>>>>');
                $this->loger->error('visit gateway failed when recharge:' . $e->getMessage());
                $code = $e->getCode();
                if ($code == 400) {
                    //todo 网关未响应
                    var_log('[error]网关未响应#_recharge >>>');
                    $this->_view->assign('retAll', ['ret' => $e->getMessage(), 'got' => $e->customData]);
                    //return $e->getMessage();
                } elseif ($code == 500) {
                    $this->_view->assign('retAll', ['ret' => $e->getMessage(), 'got' => $e->customData]);
                    return $e->getMessage();
                } else {
                    return 'gw_error';
                }
                if($e)$ret = $e->customData;
            }
            if($ret['code']==400){
                return $ret['msg']?$ret['msg']:'rpc_failed:rechargeCode';
            }
            if (empty($ret['payCorp'])) {
                $this->loger->error('get payCorp failed when recharge:');
                // return 'null_return';
            }
            try {
                $ret['payCorp'] = 101;
                $ordersOfCharge->updStatus(\Prj\Consts\OrderStatus::abandon);
                if(empty($ret['code']) && !empty($e))$ordersOfCharge->setField('exp',$e->getMessage());   //记录网关状态
                $ordersOfCharge->setField('payCorp', $ret['payCorp']);
                $ordersOfCharge->update();
            } catch (\ErrException $e) {
                $this->loger->error('update payCorp failed when recharge:' . $e->getMessage());
                return \Prj\Lang\Broker::getMsg('system.db_error');
            }
        } else {
            $this->loger->ret = 'create orders of recharges failed';
            return 'addOrder_failed';
        }
        return;
    }

    protected function _rechargeCode($inputs, \Lib\Services\PayGW $sys)
    {
        //if (empty($inputs['ticket'])) return 'no_ticket';
        if (empty($inputs['smsCode'])) return 'no_smsCode';
        $sn = \Sooh\Base\Session\Data::getInstance()->get('snMark')['recharge'];
        if(empty($sn)){
            return '系统错误:未获取充值单号';
        }
        //var_log($sn,'获取的充值sn>>>');
        //支付网关
        try {

            $ret = $sys->rechargeCode($inputs['smsCode'], $sn, $this->user->userId);
            $ret['orderId'] = $ret['orderId']?$ret['orderId']:$sn;
            $this->_view->assign('retAll', ['ret' => 'ok', 'got' => $ret]);//出现多个assign中的retAll字段重复，修改如下突出唯一识别
            $this->_view->assign('rechargeCoderetAll', ['ret' => 'ok', 'got' => $ret]);
            //$ret = [];throw new \Sooh\Base\ErrException('网关未响应>>>');
        } catch (\Sooh\Base\ErrException $e) {
            $this->loger->error('visit gateway failed when recharge:' . $e->getMessage());
            $code = $e->getCode();
            if ($code == 400) {
                //todo 网关超时 标记订单为异常
                $this->_view->assign('retAll', ['ret' => $e->getMessage(), 'got' => $e->customData]);
            } elseif ($code == 500) {
                $this->_view->assign('retAll', ['ret' => $e->getMessage(), 'got' => $e->customData]);
                return $e->getMessage();
            } else {
                return 'gw_error';
            }
            if($e)$ret = $e->customData;
           // return 'gw_error';   //说明:超时的情况下,置为异常,可接收回调
        }
        var_log($ret['code'],'ret>>>');
        if($ret['code']==400){
            $this->loger->sarg3 = json_encode(array_merge(json_decode($this->loger->sarg3, true) ? : [], [
                '_rechargeCode' => [
                    [
                        'event'          => 'recharge_wrong',
                        'money_recharge' => $inputs['amount'],
                    ],
                    ['phone' => $this->user->getField('phone'), 'userId' => $this->user->userId]
                ]
            ]));

            return $ret['msg']?$ret['msg']:'rpc_failed:rechargeCode';
        }else{
            $ret['orderId'] = $ret['code']==200 ? $ret['orderId'] : $sn;
            $ordersOfCharge = \Prj\Data\Recharges::getCopy($ret['orderId']);
            $ordersOfCharge->load();
            if (!$ordersOfCharge->exists()) {
                $this->loger->error('get ordersOfCharge failed when recharge');
                return 'ordersOfCharge_failed';
            }
            if($ret['code']==200){
                if (empty($ret['orderId'])) {
                    $this->loger->error('get orderId failed when recharge');
                    return 'error_return';
                    error_log('error_return_1');
                }
                //todo 验证成功 改订单
                $ordersOfCharge->updStatus(\Prj\Consts\OrderStatus::waiting);

            }else{
                //todo 超时的情况
                $ordersOfCharge->updStatus(\Prj\Consts\OrderStatus::unusual);
                if(!empty($e))$ordersOfCharge->setField('exp','rpc#'.$e->getMessage());
                $ret['orderId'] = $sn;
            }
        }

        //更新订单为 已受理 等待结果
        try {
            $ordersOfCharge->update();
        } catch (\ErrException $e) {
            $this->loger->error('update orderOfCharge status failed whern recharge :' . $e->getMessage());
            return 'db_error';
        }

        $this->_view->assign('OrdersDone',
            ['ordersId' => $ret['orderId'], 'orderStatus' => \Prj\Consts\OrderStatus::waiting, 'ordersTime' => \Sooh\Base\Time::getInstance()->ymdhis(), "extra" => []]);


        if(\Sooh\Base\Ini::getInstance()->get('noGW'))
        {
            var_log($ret['orderId'],'充值成功:自我回调>>>>>>>>>>>>>>');
            // $this->_testRechargeResult($ret['orderId']);
        }


        return;
    }

    /**
     * 提现
     * @input int amount 金额
     * @errors {code:400,msg:'void_card'} 无效的银行卡
     * //返回客户端旧的标志信息
     * @errors {code:400,msg:'error_paypwd','errorMsg':'***','errorCount':'***'} 错误的支付密码
     * //返回客户端新的标志信息
     * @errors {code:400,msg:'error_paypwd','errorMsgwithdraw':'***','errorCountwithdraw':'***'} 错误的支付密码
     * @errors {code:400,msg:'out_amount'} 提现金额大于账户余额
     * @errors {code:400,msg:'lock_user_failed'} 系统错误:锁定用户失败
     * @errors {code:400,msg:'perTime_out'} 单次限额5万
     * @errors {code:400,msg:'calendar_out'} 系统错误：日历表无法去到合适的到账日期
     * @errors {code:400,msg:'lost_date'} 系统错误：   日历表无法去到合适的到账日期
     * @errors {code:400,msg:'dayAmount_out'} 超过当日限额
     * By Hand
     */
    protected static $error = 'failed';
    protected $userInit;
    public function withdrawAction()
    {
        return $this->returnError('您的客户端版本过低，您需要更新至最新客户端才能登录小虾理财。目前更新服务器升级维护中，请您耐心等待。');
        $gotoMax = 3;
        retry:
        $userId = $this->user->userId;
        $this->user->load();
        $user = $this->user;
        $lockInfo = "lock by $userId when withdraw"; //锁定信息
        $lockSec = 10;
        $myCards = $this->_myCards();
        $cardOrderId = $this->_request->get('cardOrderId');
        if (empty($cardOrderId)) $cardOrderId = current($myCards)['orderId'];
        var_log($cardOrderId, '>>>>>>>>>>>>>>选择的卡ID');
        $cardError = false;
        foreach ($myCards as $k => $v) {
            //$cardOptions[$k] = $v['bankId'] . '>' . $v['bankCard'];//写log用的
            if ($v['isDefault'] == 1) $selectCard = $k;
            if (!empty($cardOrderId)) {
                if ($cardOrderId == $v['orderId']) {
                    var_log($cardOrderId . '/' . $k, '>>>>>>>>>>>>>');
                    $cardError = true;
                    $bankId = $v['bankId'];
                    $bankCard = $v['bankCard'];
                }
            }
        }
        reset($myCards);
        if (empty($selectCard)) $selectCard = key($myCards);
        $frm = \Sooh\Base\Form\Broker::getCopy('default')
            ->init(\Sooh\Base\Tools::uri(), 'get', \Sooh\Base\Form\Broker::type_c);
        $frm->addItem('userId', \Sooh\Base\Form\Item::factory('用户', $userId, \Sooh\Base\Form\Item::constval))
            ->addItem('amount', \Sooh\Base\Form\Item::factory('金额', 1, \Sooh\Base\Form\Item::text))
            ->addItem('paypwd', \Sooh\Base\Form\Item::factory('支付密码', 123456, \Sooh\Base\Form\Item::text));
        // ->addItem('cardOrderId', \Sooh\Base\Form\Item::factory('银行卡', $selectCard, \Sooh\Base\Form\Item::select, $cardOptions));

        $frm->fillValues();
        $inputs = $frm->getFields();

        if($inputs['amount'] !== null && $inputs['amount'] < 1){
            return $this->returnError('void_amount');
        }
        //$withNum = new \Prj\Items\Withdraw($user->getField('withdrawLeft'));
        //$awardTimes = $withNum->getNum(date('Ym'));//获取赠送次数

        $perTimeAmount = \Prj\Data\Config::get('WITHDRAW_PER_TIME_LIMIT_AMOUNT');
        $wallet = $this->user->getField('wallet');
        $monthTimes = \Prj\Data\Config::get('WITHDRAW_MONTH_TIMES');
        $wTimes = \Prj\Data\Recharges::getTimesWithdrawingByUserId($userId); //当月已经提现次数
        var_log($wTimes,'wTimes >>>');
        //$alreadyTimes = $wTimes<$monthTimes ? $wTimes : $monthTimes;
        $dayAmount = \Prj\Data\Config::get('WITHDRAW_DAY_AMOUNT'); //每日最多

        //todo 借款人金额不受限制
        $borrowerConfig = \Prj\Data\Config::get('borrower');
        if(!is_array($borrowerConfig)){
            error_log('[error]错误的借款人配置');
        }else{
            if(array_key_exists($userId,$borrowerConfig)){
                error_log(__METHOD__.'>>>userId:'.$userId.'#借款人提现');
                $perTimeAmount = 1000000000;
                $dayAmount = 1000000000;
            }
        }

        $dayAmountRemain = $dayAmount - \Prj\Data\Recharges::getAmountWithdrawingByOrderTime($userId, date('Ymd'));
        $dayAmountRemain = $dayAmountRemain > 0 ? $dayAmountRemain : 0; //限额剩余
        $getPayHours = 2;  //预计几小时到账  是配置好 还是程序计算
        $item = new \Prj\Items\Withdraw();
        $left = $item->numLeft($user);
        $alreadyTimes = $item->getUsed($user);
        var_log($left,'left >>> ');
        $awardTimes = $left;
        if (!empty($inputs['amount']) && !empty($cardOrderId)) {
            $forbid_withdraw = \Prj\Data\Config::get('forbid_withdraw');
            if($forbid_withdraw['forbid'])return $this->returnError($forbid_withdraw['notice']);
            if ($inputs['amount'] > $perTimeAmount) return $this->returnError('perTime_out');
            if ($inputs['amount'] > $dayAmountRemain) return $this->returnError('dayAmount_out');
            $_lyq_ret = $this->_checkPaypwd($inputs['paypwd']);
            if( $_lyq_ret !== true){
                $this->_view->assign('errorMsg', $_lyq_ret['msg']);//出现多个assign中的errorMsg字段重复，修改如下突出唯一识别
                $this->_view->assign('errorMsgwithdraw', $_lyq_ret['msg']);
                $this->_view->assign('errorCount', $_lyq_ret['errorCount']);//出现多个assign中的errorCount字段重复，修改如下突出唯一识别
                $this->_view->assign('errorCountwithdraw', $_lyq_ret['errorCount']);
                return $this->returnError(\Prj\Lang\Broker::getMsg('user.error_paypwd'));
            }
            $this->userInit = $user->dump(); //用户初始化
            unset($this->userInit['sLockData']);
            unset($this->userInit['iRecordVerID']);
            //计算提现手续费
            if ($left>0) {
                $wn = \Prj\Data\WithdrawNum::add($userId,-1,date('Ym'),'',$userId);
                $item->useit($user,1);
                $poundage = 0;
            }else{
                $poundage = \Prj\Data\Config::get('WITHDRAW_POUNDAGE')-0;
            }

            $inputs['amount'] = abs($inputs['amount']);
            if (!$cardError) return $this->returnError(\Prj\Lang\Broker::getMsg('user.withdraw_bankcard_error'));
            if ($inputs['amount'] + $poundage > $wallet) {
                $this->_view->assign('amount', $inputs['amount']);
                $this->_view->assign('poundage', $poundage);
                return $this->returnError(\Prj\Lang\Broker::getMsg('user.withdraw_over_remain'));
            }
            $withdraw = \Prj\Data\Recharges::addOrders($userId, -$inputs['amount'], $bankId, $bankCard, \Prj\Consts\OrderType::withdraw);
            $batchId = $this->_createBatchId(\Prj\Consts\OrderType::withdraw);
            //锁定用户
            if (!$user->lock($lockInfo, $lockSec)) {
                if($gotoMax){
                    sleep(1);
                    $this->user->reload();
                    $gotoMax--;
                    goto retry;
                }

                if (!$user->lock($lockInfo, $lockSec)) {
                    $this->loger->error('lock user failed when withdraw');
                    return $this->returnError(\Prj\Lang\Broker::getMsg('user.withdraw_user_lockfailed'));
                }
            }

            $num = 0;
            start:
            $num++;
            $withdraw->setField('poundage', $poundage);
            //获取到账日期 T+1 OR T+2
            $ymd = date('Ymd', strtotime('+1 days'));

            $newYmd = $ymd;
//            try {
//                $newYmd = \Prj\Data\Calendar::getWithdrawDate($userId, $ymd, $inputs['amount']);
//            } catch (\ErrorException $e) {
//                $this->loger->error('get WithdrawDate failed when withdraw');
//                return $this->returnError(\Prj\Lang\Broker::getMsg('user.withdraw_calendar_error'));
//            }
            $withdraw->setField('withdrawYmd', $newYmd);
            //尝试锁定日历
//            $cal = \Prj\Data\Calendar::getCopy($newYmd);
//            $cal->load();
//            error_log("TODO:锁日历失败后没解锁user");
//            if (!$cal->lock($lockInfo, $lockSec)) {
//                sleep(1);
//                $cal->reload();
//                if (!$cal->lock($lockInfo, $lockSec)) {
//                    $this->loger->error('lock tb_calendar failed when withdraw');
//                    return $this->returnError(\Prj\Lang\Broker::getMsg('user.withdraw_calendar_error'));
//                }
//            }
            //判断日期是否仍然可用
//            $realPerWithdraw = \Prj\Data\Recharges::getAmountWithdrawingByYmd($userId, $newYmd);
//            if (!\Prj\Data\Calendar::checkWithdrawDate($cal, $inputs['amount'], $realPerWithdraw)) {
//                $cal->unlock();
//                if ($num > 10) return $this->returnError(\Prj\Lang\Broker::getMsg('user.withdraw_calendar_error'));
//                goto start;
//            }
            //生成钱包流水
            $tally = \Prj\Data\WalletTally::addTally($userId, $user->getField('wallet'), -$inputs['amount'] - $poundage, 0, 0, \Prj\Consts\OrderType::withdraw);
            $tally->setField('poundage', $poundage);
            $tally->setField('freeze', 1); //冻结金额
            //修改日历额度
//            $cal->setField('realTotalWithdraw', $cal->getField('realTotalWithdraw') + $inputs['amount']);
            //修改订单
            $withdraw->setField('withdrawYmd', $newYmd);
            $withdraw->updStatus(\Prj\Consts\OrderStatus::waitingGW);
            $withdraw->setField('batchId',$batchId);
            //修改用户钱包
            $oldWallet = $user->getField('wallet');
            $user->setField('wallet', $oldWallet - $inputs['amount'] - $poundage);
            //提交订单
            try {
                $withdraw->update();
            } catch (\ErrorException $e) {
                $user->unlock();
                //$cal->unlock();
                $this->loger->error('add withdraw recharge failed when withdraw');
               // return $this->returnError('db_error2');
                return $this->returnError(\Prj\Lang\Broker::getMsg('system.db_error'));
            }
            $withdrawOrderId = $withdraw->getPKey()['ordersId'];
            $tally->setField('orderId', $withdrawOrderId);
            $tally->updStatus(\Prj\Consts\Tally::status_new);
            $tally->setField('codeCreate', "withdraw_$withdrawOrderId");
            $tally->setField('descCreate', "提现申请");
            //提交流水
            try {
                $tally->update();
            } catch (\ErrorException $e) {
                $user->unlock();
                //$cal->unlock();
                $withdraw->setField('orderStatus', \Prj\Consts\OrderStatus::abandon);
                $withdraw->update();
                $this->loger->error('add walletTally failed when withdraw(step3)');
				error_log('[error on lock]add walletTally failed when withdraw(step3)');
                return $this->returnError(\Prj\Lang\Broker::getMsg('system.db_error'));
            }
            //提交提现次数流水
            try{
                if(isset($wn)){
                    $wn->setField('exp',$withdraw->getPKey()['ordersId']);
                    $wn->update();
                }
            }catch (\ErrorException $e){
                $tally->updStatus(\Prj\Consts\Tally::status_abandon);
                $tally->update();
                $user->unlock();
                //$cal->unlock();
                $withdraw->setField('orderStatus', \Prj\Consts\OrderStatus::abandon);
                $withdraw->update();
                $this->loger->error('add walletTally failed when withdraw(step3)');
                error_log('[error on lock]add walletTally failed when withdraw(step3)');
                return $this->returnError(\Prj\Lang\Broker::getMsg('system.db_error'));
            }

            //提交用户钱包
            try {
                $user->setField('lastWithdraw',date('YmdHis'));
                $user->update();
                $user->lock(date('His').'#wait for payGW when withdraw ');
            } catch (\ErrorException $e) {
                if(isset($wn)){
                    $wn->setField('statusCode',\Prj\Consts\Tally::status_abandon);
                    $wn->update();
                }

                $user->unlock();
                //$cal->unlock();
                $withdraw->setField('orderStatus', \Prj\Consts\OrderStatus::abandon);
                $withdraw->update();
                $tally->updStatus(\Prj\Consts\Tally::status_abandon);
                $tally->update();
                $this->loger->error('update wallet failed when withdraw');
				error_log('[error on lock]update wallet failed when withdraw');
                return $this->returnError(\Prj\Lang\Broker::getMsg('system.db_error'));
            }
            $new[] = [
                'ordersId'=>$withdraw->getField('ordersId'),
                'userId'=>$withdraw->getField('userId'),
                'amount'=>$withdraw->getField('amountAbs'),
                'poundage'=>$poundage,
            ];
            $list = json_encode(['list'=>$new]);

            //todo 通知网关
            if (\Sooh\Base\Ini::getInstance()->get('noGW')) {
                $rpc = null; //debug
            } else {
                $rpc = \Sooh\Base\Rpc\Broker::factory('PayGW');
            }
            $sys = \Lib\Services\PayGW::getInstance($rpc);
            try{
                $ret = $sys->sendWithdraw($batchId,$list);
                $this->_view->assign('retAll',['ret'=>'ok','got'=>$ret]);//多个assign里面有retAll,修改如下作为唯一标识的根节点
                $this->_view->assign('withdrawretAll',['ret'=>'ok','got'=>$ret]);
            }catch(\Exception $e){
                self::$error = is_array($ret)?json_encode($ret):($e->getMessage().'#'.$ret);
                $this->withdraw_rollback($wn,$user,$withdraw,$tally);

                $this->loger->error('send order to gw failed where addorder '.$e->getMessage());
                $code = $e->getCode();
                if($code==400){
                    $this->_view->assign('retAll',['ret'=>$e->getMessage(),'got'=>$e->customData]);
                    return $this->returnError($e->getMessage());
                }elseif($code==500){
                    $this->_view->assign('retAll',['ret'=>$e->getMessage(),'got'=>$e->customData]);
                    return $this->returnError($e->getMessage());
                }
                //  return $this->returnError('gw_error');
                return $this->returnError(\Prj\Lang\Broker::getMsg('system.gw_error'));
            }

            if(empty($ret['payCorp'])){
                $this->withdraw_rollback($wn,$user,$withdraw,$tally);
                return $this->returnError('no_payCorp');
            }

            if($ret['code']==400){
                $this->withdraw_rollback($wn,$user,$withdraw,$tally);
                return $this->returnError($ret['msg']);
            }elseif($ret['code']==200){
                $withdraw->setField('payCorp',$ret['payCorp']);
                try{
                    $withdraw->update();
                }catch (\ErrorException $e){

                }

                $data['bankId'] = $bankId;
                $data['bankCard'] = $bankCard;
                $data['amount'] = $withdraw->getField('amountAbs');
                $data['withdrawYmd'] = $newYmd;
                $data['times'] = $wTimes;
                $data['poundage'] = $poundage;
                $phone = $user->getField('phone');
                //推送
                $this->loger->sarg3 = json_encode(array_merge(json_decode($this->loger->sarg3, true) ? : [], [
                    'action_ask_money' => [
                        ['event' => 'ask_money', 'time_full' => date('Y-m-d H:i'), 'money_now' => $data['amount'] / 100,],
                        ['phone' => $phone, 'userId' => $user->getField('userId')]
                    ],
                ]));

                $user->unlock();
                $this->_view->assign('data', $data);//出现多个assign中的data字段重复，修改如下突出唯一识别
                $this->_view->assign('datawithdraw', $data);
                $this->returnOK('success');

            }else{
                $this->withdraw_rollback($wn,$user,$withdraw,$tally);
                return $this->returnError('支付服务未响应');
            }


            //todo 金额冻结
            //$rpc = \Sooh\Base\Rpc\Broker::factory('PayGW');
            //$sys = \Lib\Services\PayGW::getInstance($rpc);

            //提交日历
//            try {
//                $cal->update();
//            } catch (\ErrorException $e) {
//                $user->unlock();
//                $cal->unlock();
//                $withdraw->setField('orderStatus', \Prj\Consts\OrderStatus::abandon);
//                $withdraw->update();
//                $tally->updStatus(\Prj\Consts\Tally::status_abandon);
//                $tally->update();
//                $user->setField('wallet', $user->getField('wallet') + $inputs['amount']);
//                $this->loger->error('update calendar failed when withdraw');
//                error_log('[error on lock]update calendar failed when withdraw');
//                return $this->returnError(\Prj\Lang\Broker::getMsg('system.db_error'));
//            }

        } else {
            $this->_view->assign('wallet', $wallet); //钱包余额
            $this->_view->assign('dayAmountRemain', $dayAmountRemain); //每日限额剩余
            $this->_view->assign('perTimeAmount', $perTimeAmount); //每次限额
            $this->_view->assign('dayAmount', $dayAmount); //每天限额
            $this->_view->assign('getPayHours', $getPayHours); //几小时到账
            $this->_view->assign('monthTimes', $monthTimes); //每月免费几次
            $this->_view->assign('alreadyTimes', $alreadyTimes); //已经免费几次
            $this->_view->assign('awardTimes',$awardTimes); //赠送的提现次数
            $this->_view->assign('poundageInit',\Prj\Data\Config::get('WITHDRAW_POUNDAGE')-0); //提现手续费
            $this->returnOK('');
        }
    }

    protected function withdraw_rollback($wn,$user,$withdraw,$tally){
        if(isset($wn)){
            $wn->setField('statusCode',\Prj\Consts\Tally::status_abandon);
            $wn->update();
        }
        if($user && $this->userInit){
            foreach($this->userInit as $k=>$v){
                $user->setField($k,$v);
            }
            $user->update();
        }
        if($withdraw){
            $withdraw->setField('exp',self::$error);
            $withdraw->setField('orderStatus', \Prj\Consts\OrderStatus::abandon);
            $withdraw->update();
        }
        if($tally){
            $tally->updStatus(\Prj\Consts\Tally::status_abandon);
            $tally->update();
        }
    }

    /**
     * 批次号生成
     * @param $type
     * @return string
     */
    protected function _createBatchId($type)
    {
        return time().rand(1000,9999).$type;
    }

    /**
     * 显示登录用户的钱包余额
     * By Hand
     * @input none
     * @output {"code":200,"userId":【用户ID】,"wallet":【钱包余额】}
     * @error {"code":400,"msg":“user_notfound”}用户不存在
     */
    /*
    public function walletAction()
    {
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $user = $this->user;
        $user->load();
        if ($user->exists()) {
            $this->returnOK();
        } else {
            $this->returnError(\Prj\Lang\Broker::getMsg('user.user_notfound'));
        }
        $rs['userId']     = $user->getField('userId');
        $rs['wallet']     = $user->getField('wallet');
        $rs['redPacket']  = $user->getField('redPacket');
        $rs['wallAndRed'] = (string)($rs['wallet'] + $rs['redPacket']);
        $this->_view->assign('result', $rs);

    }
    */


    /**
     * 我的银行卡
     * By Hand
     * @output perTimeWithdrawLimit 单次提现限额
     * @output isSetPaypwd 是否设置了支付密码
     * @output isFirstRecharge 是否首充    1代表还没有充过值 是首充
     * //返回客户端旧的标志信息
     * @output {"list":{"****"}}
     * //返回客户端新的标志信息
     * @output {"listmybindcard":{"***"}}
     */
    public function myBindCardAction()
    {
        //$rs = \Prj\Data\BankCard::getList($this->user->userId, ['statusCode' => \Prj\Consts\BankCard::enabled]);
        $this->_view->assign('userId', $this->user->userId);
        $this->_view->assign('perTimeWithdrawLimit', 5000000);
        $this->_view->assign('isSetPaypwd', $this->_isSetPaypwd($this->user));
        $rs = \Prj\Data\BankCard::loopAll(['userId' => $this->user->userId, 'statusCode' => \Prj\Consts\BankCard::enabled]);

        $this->_view->assign('list', $rs);//出现多个assign中的list字段重复(financing/investList与user/myBindcard)，修改如下突出唯一识别
        $this->_view->assign('listmybindcard', $rs);
        if(empty($rs))$rs = '';
        $this->_view->assign('listmybindcardOT', $rs);
        //是否首充
        $this->user->load();
        $ymdFirstCharge = $this->user->getField('ymdFirstCharge');
        var_log($ymdFirstCharge, 'ymdFirstCharge>>>');
        $this->_view->assign('isFirstRecharge', $ymdFirstCharge ? 0 : 1);
        $this->returnOK();
    }

    /**
     * 是否设置了支付密码
     */
    protected function _isSetPaypwd($user)
    {
        $user->load();
        $tradePwd = $this->user->getField('tradePwd', true);
        return empty($tradePwd) ? 0 : 1;
    }

    /**
     * 我的充值记录
     * By Hand
     * //返回客户端旧的标志信息
     * @output {"list":{"***"}}
     * //返回客户端新的标志信息
     * @output {"listmyrecharge":{"***"}}
     */
    public function myRechargeAction()
    {
        $ymdStart = $this->_request->get('ymdStart', '20150101000000');
        $ymdEnd = $this->_request->get('ymdEnd', '20201010000000');
        $pageId = $this->_request->get('pageId', '1') - 0;
        $pageSize = $this->_request->get('pageSize', '10') - 0;
        $pager = new \Sooh\DB\Pager($pageSize);
        $pager->init(-1, $pageId);
        $userId = $this->user->userId;
        $rs = \Prj\Data\Recharges::paged($userId, $pager, $ymdStart, $ymdEnd);
        $this->returnOK();
        $this->_view->assign('list', $rs);//出现多个assign中的list字段重复，修改如下突出唯一识别
        $this->_view->assign('listmyrecharge', $rs);
        $this->_view->assign('pager', $pager->toArray());
    }

    /**
     * 我的订单查看
     * 订单状态: 2:投资中 7:满标  8/10/20/21/38:还款中   39:已还清
     * By Hand
     * info: {
     *  redPacket: "1000",
     *  wallet: "191605983",
     *  interestTotal: "0",      【累计获利】
     *  totalAssets: 192307983, 【资产总额】
     *  holdingAssets: 701000,
     *  interestWait: 71979,        【待收利息】
     *  amountWait: 601000        【待收本金】
     *  getTotal:{
     *          interestStatic  [房贷累计收益]
     *          interestFloat  [车贷累计收益]
     *      }
     *  },
     * list
     * {
     *  ordersId: "1014467141319509610",                                       【】
     *  waresId: "1446519809479610",                                           【】
     *  userId: "90003837339748",                                              【】
     *  amount: "100000",                                                      【实际投资额 单位分】
     *  amountExt: "0",                                                        【活动赠送投资额（可取现） 单位分】
     *  amountFake: "2000",                                                    【活动赠送投资额（不可取现） 单位分】
     *  yieldStaticAdd: "0.01",                                                【定固年化收益率上浮】
     *  yieldStatic: "0.12",                                                   【定固年化收益率】
     *  interest: "12369",                                                     【本金收益 单位分】
     *  interestExt: "247",                                                    【券金收益 单位分】
     *  brief: "0.00",                                                         【投资摘要（显示列表时的数据）】
     *  extDesc: "",                                                           【动活赠送说明】
     *  orderTime: "20151105170211",                                           【下单时间】
     *  orderStatus: "-1",                                                     【单订状态】
     *  codeCreate: "buy_wares_1446519809479610",                              【创建流水的代码标示】
     *  descCreate: "购买：某知名面粉企业升级改造设备直租项目2498[12]",           【用途的用户说明】
     *  vouchers: "9014466868640349748",                                       【使用券】
     *  iRecordVerID: "1",                                                     【iRecordVerID】
     *  returnType: "2",                                                       【还款方式：0未定，1：一次定还本付息，2:按月付息，到期还本】
     *  returnNext: "0",                                                       【下次还款日】
     *  returnPlan: null,                                                      【回款计划】
     *  firstTime: "0"                                                         【是否该用户的首次购买】
     *
     * dtStart:''                          【起息日】
     * interestAlready: 0                   【已收利息】
     * interestTotal: 0                      【预期收益】
     * }
     *
     * 
     * @input string ymdStart 起始日期'20150101000000'
     * @input string ymdEnd 结束日期'20201010000000'
     * @input int pageId 当前页
     * @input int pageSize 页容量
     * @input int orderStatus 订单状态
     * @input string cmd 指令 service:我的服务
     * @input string shelfId 类型 固定：2000   浮动：3000,4000
     * //返回客户端旧的标志信息
     * @output {"code":200,"list"【订单列表】,"pager":【分页信息】}
     * //返回客户端新的标志信息
     * @output {"code":200,"listmyinvestto"【订单列表】,"myInvestPager":【分页信息】}
     * @errors {"code":400,"msg":"no_record"} 空记录
     */
    public function myInvestAction()
    {
        $where = [];
        $orderBy = null;
        $ymdStart = $this->_request->get('ymdStart', '20150101000000');
        $ordersId = $this->_request->get('ordersId');
        $ymdEnd = $this->_request->get('ymdEnd', '20201010000000');
        $pageId = $this->_request->get('pageId', '1') - 0;
        $pageSize = $this->_request->get('pageSize', '10') - 0;
        $orderStatus = $this->_request->get('orderStatus');
        $shelfId = $this->_request->get('shelfId');
        $cmd = $this->_request->get('cmd');
        //我的服务扩展
        $serviceChk = strpos($cmd, 'service') === false ? false : true;
        if ($serviceChk) {
            $running = \Prj\Consts\OrderStatus::$running;
            $running[] = \Prj\Consts\OrderStatus::done;
            if (empty($orderStatus)) {
                $where['orderStatus'] = $running;
            }
            $info = $this->_accountInfo($this->user);
            $info['getTotal'] = $this->_amountByReturnPlan(1);
            $this->_view->assign('info', $info);//出现多个assign中的info字段重复，修改如下突出唯一识别
            $this->_view->assign('infomyinvest', $info);
        }
        if (is_array($shelfId)) {
            $where['shelfId'] = $shelfId;
        } elseif (!empty($shelfId)) {
            $where['shelfId'] = explode(',', $shelfId);
        }
        if (!empty($orderStatus)) {
            $orderStatusArr = explode(',', $orderStatus);
            $where['orderStatus'] = array_intersect($running, $orderStatusArr);
        }

        $pager = new \Sooh\DB\Pager($pageSize);
        $pager->init(-1, $pageId);
        $userId = $this->user->userId;
        if($ordersId)$where['ordersId'] = $ordersId;
        $rs = \Prj\Data\Investment::pager($userId, $pager, $ymdStart, $ymdEnd, $where, $orderBy);


        if (empty($rs)) {
            $this->returnOK();
        } else {
            foreach ($rs as $k => $v) {
                if ($serviceChk) {
                    $wares = \Prj\Data\Wares::getCopy($v['waresId']);
                    $wares->load();
                    $interestAlready = 0; //已获收益
                    $interestTotal = 0; //预期收益
                    if(!empty($v['returnPlan'])){

                    }
                    $returnPlan = \Prj\ReturnPlan\Std01\ReturnPlan::createPlan($v['returnPlan']);
                    $rs[$k]['dtStart'] = $returnPlan->dtStart ? date('Ymd',$returnPlan->dtStart) : '';
                    if (empty($rs[$k]['dtStart'])) {
                        if ($wares->getField('interestStartType') == \Prj\Consts\InterestStart::whenBuy) {
                            $rs[$k]['dtStart'] = substr($rs[$k]['orderTime'], 0, 8);
                        }
                    }
                    if ($returnPlan->calendar != null) {
                        foreach ($returnPlan->calendar as $vv) {
                            $interestTotal += ($vv['interestStatic'] + $vv['interestAdd'] + $vv['interestExt'] + $vv['interestFloat'] );  //+ $vv['interestSub'] todo 不算贴息
                            if ($vv['isPay']) {
                                $interestAlready += ( $vv['realPayInterest']);
                            }
                        }
                    }
                    $rs[$k]['interestAlready'] = $interestAlready;
                    $rs[$k]['interestTotal'] = $interestTotal;
                    $rs[$k]['interestStartType'] = $wares->getField('interestStartType');
                    $rs[$k]['waresStatusCode'] = $wares->getField('statusCode');
                    if($rs[$k]['waresStatusCode']==\Prj\Consts\Wares::status_go && $rs[$k]['orderStatus']==\Prj\Consts\OrderStatus::waiting){
                        $rs[$k]['orderStatus'] = '7';
                    }
                    $rs[$k]['returnType'] = $wares->getField('returnType');
                    $rs[$k]['returnPlan'] = $returnPlan->calendar ? $returnPlan->calendar : [];
                    $rs[$k]['item'] = '中华基金';
                    $rs[$k]['deadLine'] = $wares->getField('dlUnit') == '天' ? $wares->getField('deadLine') : $wares->getField('deadLine') * 30;

                    $rs[$k]['deadLineNum'] = $wares->getField('deadLine');
                    $rs[$k]['deadLineUnit'] = $wares->getField('dlUnit');

                    $wares = \Prj\Data\Wares::getCopy($v['waresId']);
                    $wares->load();
                    $arr = $wares->dump();
                    $tplClass = "\\Prj\\WaresTpl\\" . $arr['viewTPL'] . "\\Viewer";
                    if (class_exists($tplClass)) {
                        $introDisplay = $arr['introDisplay'];
                    }
                    $rs[$k]['borrowerName'] =  \Prj\IdCard::getCall($arr['introDisplay']['b']['idCard'],$arr['introDisplay']['b']['name']);
                    //$rs[$k]['introDisplay'] = $introDisplay;
                    $rs[$k]['webUrl']=\Sooh\Base\Tools::uri(['waresId'=>$v['waresId']],'newDec','financing');
                    $rs[$k]['images']=\Prj\WaresTpl\Std02\Viewer::getImgList($introDisplay);

                } else {
                    unset($rs[$k]['returnPlan']);
                }
                $rs[$k]['licence'] && $rs[$k]['licence'] = json_decode($rs[$k]['licence'], true);
            }

            $this->returnOK();
        }
        $this->_view->assign('list', $rs);//出现多个assign中的list字段重复，修改如下突出唯一识别
        $this->_view->assign('listmyinvestto', $rs);
        $this->_view->assign('pager', $pager->toArray());
        
        //新版分页
        $this->_view->assign('myInvestPager',$pager->toArray());
        
    }

    /**
     * By Hand
     */
    protected function _accountInfo($user)
    {
        $user->load();
	    $userId               = $user->getField('userId');
	    $ret                  = [];
	    $ret['redPacket']     = $this->user->getField('redPacket');
	    $ret['wallet']        = $this->user->getField('wallet');
	    $ret['interestTotal'] = $this->user->getField('interestTotal'); //累计收益
	    $ret['nickname']      = $this->user->getField('nickname');
	    $ret['ymdFirstBuy']   = $this->user->getField('ymdFirstBuy');
	    $_checkinBook         = $this->user->getField('checkinBook');
	    if (isset($_checkinBook['ymd']) && !empty($_checkinBook['ymd'])) {
		    $ret['isTodayCheckin'] = $_checkinBook['ymd'] == \Sooh\Base\Time::getInstance()->YmdFull ? 1 : 0;
	    } else {
		    $ret['isTodayCheckin'] = 0;
	    }

	    $redPacketDtLast = $this->user->getField('redPacketDtLast');
        $voucherSys = \Prj\Data\Vouchers::getCopy($userId);
        $lastRedPacket = $voucherSys->db()->getRecord($voucherSys->tbname(), 'timeCreate', ['userId' => $userId, 'voucherType' => \Prj\Consts\Voucher::type_real, 'statusCode]' => 0], 'rsort timeCreate');

        $ret['hasNewRedPacket'] = $lastRedPacket['timeCreate'] > $redPacketDtLast ? 1 : 0;//是否有未领取的红包
        $waresId = $this->_request->get('waresId');
        $cmd = $this->_request->get('cmd');
        if (!empty($cmd)) {
            if (strpos($cmd, 'voucher') !== false) {
                foreach ($this->_myVouchers($waresId, [\Prj\Consts\Voucher::type_fake, \Prj\Consts\Voucher::type_yield]) as $k => $v) {
                    $ret[$k] = !empty($v) ? $v : [];
                }
            }
            if (strpos($cmd, 'service') !== false) {
                $isPay = $this->_request->get('isPay', 0);
                $rpAmount = $this->_amountByReturnPlan($isPay);  //从还款计划里搜刮信息
                $ret = array_merge($ret, $rpAmount);
            }
        }
        //是否有待拆的红包
        $where = [
            'userId' => $userId,
            'voucherType' => \Prj\Consts\Voucher::type_real,
            'statusCode' => \Prj\Consts\Voucher::status_wait,
            'dtExpired]' => \Sooh\Base\Time::getInstance()->ymdhis(),
        ];
//        $ret['CountRedPacketWait'] = \Prj\Data\Vouchers::loopGetRecordsCount($where);
	    $_dbVoucher = \Prj\Data\Vouchers::getCopy($userId);
        $ret['CountRedPacketWait'] = $_dbVoucher->db()->getRecordCount($_dbVoucher->tbname(), $where);

        if (!empty($waresId)) {
            $ret['uniqueOp'] = \Lib\Misc\UniqueOp::createFor($waresId, 'orders/add');
        }
        $holdingAssets = \Prj\Data\Investment::getHoldingAssetsByUserId($userId);
        //$withdrawing = \Prj\Data\Recharges::getAmountWithdrawingByUserId($userId);
        $ret['freezeAmount'] = $this->freezeAmount();
        $ret['totalAssets'] =  $ret['wallet'] + $holdingAssets + $ret['freezeAmount']; //资产总额 = 钱包+回款中的订单+冻结资产  //$ret['redPacket']
        $ret['holdingAssets'] = $holdingAssets; //持有资产 = 回款中的订单

        //未读消息
        $ret['msgCounts'] = \Lib\Services\Message::getInstance()->getCount(['receiverId' => $userId, 'status' => \Prj\Consts\Message::status_unread]);

        return $ret;
    }

    /**
     * 流水详情
     * By Hand
     */
    /*
    public function tallyDetailAction()
    {
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $tallyId = $this->_request->get('tallyId');

    }
    */

    /**
     * 从还款计划里提取利息 本金
     * 性能很差
     */
    protected function _amountByReturnPlan($isPay = 0)
    {
        $amountWait = 0; //待收本金
        $interestWait = 0; //待收利息
        $running = \Prj\Consts\OrderStatus::$running;
        $statusAll = $running;
        $where['userId'] = $this->user->userId;
        $where['orderStatus'] = $statusAll;
        $all = \Prj\Data\Investment::loopAll($where); //获取所有订单
        foreach ($all as $v) {
            if ($v['shelfId'] == \Prj\Consts\Wares::shelf_static) {
                $allStatic[] = $v;
            } elseif ($v['shelfId'] == \Prj\Consts\Wares::shelf_static_float) {
                $allFloat[] = $v;
            }
        }
        $returnPlan = $this->_getReturnPlan($allStatic, $isPay);
        if (!empty($returnPlan)) {
            foreach ($returnPlan as $v) {
                if($isPay){
                    $interestWait += ($v['realPayInterest']);  //todo $v['realPayinterestSub']+    不计算贴息
                    $amountWait += ($v['realPayAmount']);
                }else{
                    $interestWait += ($v['interestStatic'] + $v['interestAdd'] + $v['interestExt'] + $v['interestFloat'] );  //todo + $v['interestSub'];  不计算贴息
                    $amountWait += ($v['amount'] + $v['amountExt']);
                }

            }
            $interestStatic = $interestWait - 0; //房贷待收收益
            $amountStatic = $amountWait - 0;  //房贷待收本金
        }
        $interestWait = 0;
        $amountWait = 0;
        $returnPlan = $this->_getReturnPlan($allFloat, $isPay);
        if (!empty($returnPlan)) {
            foreach ($returnPlan as $v) {
                if($isPay){
                    $interestWait += ($v['realPayInterest']);  //todo $v['realPayinterestSub']+ 不计算贴息
                    $amountWait += ($v['realPayAmount']);
                }else{
                    $interestWait += ($v['interestStatic'] + $v['interestAdd'] + $v['interestExt'] + $v['interestFloat'] );  //todo $v['interestSub'] 不计算贴息
                    $amountWait += ($v['amount'] + $v['amountExt']);
                }

            }
            $interestFloat = $interestWait - 0; //车贷待收收益
            $amountFloat = $amountWait - 0; //车贷待收本金
        }

        return [
            'interestStatic' => $interestStatic - 0,
            'amountStatic' => $amountStatic - 0,
            'interestFloat' => $interestFloat - 0,
            'amountFloat' => $amountFloat - 0,
            'interestRP' => $interestStatic + $interestFloat - 0,
            'amountRP' => $amountStatic + $amountFloat - 0
        ];
    }

    /**
     * 从订单列表里抽出还款计划
     * By Hand
     */
    protected function _getReturnPlan($rs, $isPay = 0)
    {
        self::$totalAmount = 0;
        self::$totalInterest = 0;
        $tinyReturnPlan = [];
        if (!empty($rs)) {
            foreach ($rs as $k => $v) {
                $returnPlan = json_decode($v['returnPlan'], true);
                if (!empty($returnPlan)) {
                    foreach ($returnPlan['calendar'] as  $vv) {
                        $vv['waresId'] = $v['waresId'];
                        $vv['waresName'] = $v['waresName'];
                        //$vv['month']      = (int)substr($kk, 0, 6);
                        //$vv['date']       = $kk;
                        $tinyReturnPlan[] = $vv;
                    }
                }
            }
        }
        if($isPay){
            usort($tinyReturnPlan, function ($a, $b) { //排序
                if ($a['realDateYmd'] == $b['realDateYmd']) return 0;
                return $a['realDateYmd'] > $b['realDateYmd'] ? 1 : -1;
            });
        }else{
            usort($tinyReturnPlan, function ($a, $b) { //排序
                if ($a['planDateYmd'] == $b['planDateYmd']) return 0;
                return $a['planDateYmd'] > $b['planDateYmd'] ? 1 : -1;
            });
        }

        foreach ($tinyReturnPlan as $k => $v) {
            if ($isPay !== 'all') {
                if ($v['isPay'] != $isPay) continue;
            }

            if($isPay){
                self::$totalAmount+=$v['realPayAmount'];
                self::$totalInterest+=($v['realPayInterest']);  //todo +$v['realPayinterestSub'] 不计算贴息
                //self::$totalAmountExt += $v['amountExt'];
            }else{
                self::$totalAmount += ($v['amount']+$v['amountExt']);
                self::$totalInterest += ($v['interestStatic']+$v['interestAdd']+$v['interestExt']+$v['interestFloat']); //todo 不计算贴息 +$v['interestSub']
                self::$totalAmountExt += $v['amountExt'];
            }
            $newList[] = $v;
        }
        return $newList;
    }

    /**
     * 钱包流水查询
     * By Hand
     * {
     *      tallyId: "144670825975169748",                                   【tallyId】
     *      userId: "90003837339748",                                         【userId】
     *      orderId: "1014467082597459610",                                   【orderId】
     *      tallyType: "1",                                                   【类型 0：默认 10：订单 20:充值 30:提现 40:回款 70:绑卡 90:券】
     *      statusCode: "0",                                                  【状态 -1：作废 0：正常】
     *      codeCreate: "buy_wares_1446519809479610",                         【创建流水的代码标示】
     *      descCreate: "购买：某知名面粉企业升级改造设备直租项目2498[12]",      【用途的用户说明】
     *      timeCreate: "20151105152419",                                     【创建时间】
     *      nOld: "198476000",                                                【原余额 单位分】
     *      nAdd: "-100000",                                                  【增加额（可负） 单位分】
     *      nNew: "198376000",                                                【新余额 单位分】
     *      iRecordVerID: "1",                                                【iRecordVerID】
     *      poundage: null                                                    【手续费 单位分】
     *      typeName : 【类别名称 】
     *      detail : 【交易备注】
     *}
     * @input string  ymdStart 开始日期 20150101000000
     * @input string  ymdEnd 结束日期
     * @input string  type  [
     * 3011:提现申请
     * 3000:提现成功
     * 10:投标冻结
     * 20:充值
     * 55:还本
     * 50:支付收益
     * 60:提现还款
     * 80:平台贴息
     * 100:邀请返利
     * 240:存钱罐收益
     * ]
     * @input int  pageId 当前页
     * @input int  pageSize 每页数量
     * //返回客户端旧的标识信息
     * @output {code:200,"list":{'***'},"pager":{"分页"}}
     * //返回客户端新的标识信息
     * @output {code:200,"listwallettallylist":{'***'},"wallettallyListPager":{"分页"}}
     */
    public function wallettallyListAction()
    {
        $ymdStart = $this->_request->get('ymdStart', '20150101000000');
        $ymdEnd = $this->_request->get('ymdEnd', '20201010000000');
        $pageId = $this->_request->get('pageId', '1') - 0;
        $pageSize = $this->_request->get('pageSize', '10') - 0;
        $pager = new \Sooh\DB\Pager($pageSize);
        $pager->init(-1, $pageId);
        $userId = $this->user->userId;
        //$this->loger->target = $pageId;
        $where = ['statusCode' => \Prj\Consts\Tally::status_new];
        $tallyType = $this->_request->get('type');
        if (!empty($tallyType)) {
            switch($tallyType){
                case \Prj\Consts\OrderType::withdraw.'11': //提现申请
                    $where['tallyType'] = \Prj\Consts\OrderType::withdraw;
                    //$where['freeze'] = 1;
                    break;
                case \Prj\Consts\OrderType::withdraw.'00': //提现成功
                    $where['tallyType'] = \Prj\Consts\OrderType::withdraw;
                    $where['freeze'] = 0;
                    break;
                case \Prj\Consts\OrderType::investment.'11':
                    $where['tallyType'] = \Prj\Consts\OrderType::investment;
                    $where['freeze'] = 1;
                    break;
                case \Prj\Consts\OrderType::investment.'00':
                    $where['tallyType'] = \Prj\Consts\OrderType::investment;
                    $where['freeze'] = 0;
                    break;
                //只显示冻结的订单
                case \Prj\Consts\OrderType::investment:
                    $where['tallyType'] = \Prj\Consts\OrderType::investment;
                    //$where['freeze'] = 1;
                    break;
                case \Prj\Consts\OrderType::dayInterest:
                    $where['tallyType'] = \Prj\Consts\OrderType::dayInterest;
                    break;
                default :
                    $where['tallyType'] = $tallyType;
            }
        }
        $rs = \Prj\Data\WalletTally::pager($userId, $pager, $ymdStart, $ymdEnd, $where);
        if (!empty($rs)) {
            foreach ($rs as $k => $v) {
                switch (true) {
                    case ($v['tallyType'] == \Prj\Consts\OrderType::recharges):
                        $typeName = '充值';
                        $detail = "订单{orderId}充值成功";
                        break;
                    case ($v['tallyType'] == \Prj\Consts\OrderType::withdraw && $v['freeze'] == 0):
                        $typeName = '提现成功';
                        $detail = "订单{orderId}提现成功";
                        break;
                    case ($v['tallyType'] == \Prj\Consts\OrderType::withdraw && $v['freeze'] == 1):
                        $typeName = '提现申请';
                        $detail = "订单{orderId}已申请";
                        break;
                    case ($v['tallyType'] == \Prj\Consts\OrderType::investment && $v['freeze'] == 1):
                        $typeName = '投标冻结';
                        $detail = "订单{orderId}已冻结,{waresName}";
                        break;
                    case ($v['tallyType'] == \Prj\Consts\OrderType::investment && $v['freeze'] == 0):
                        $typeName = '投标冻结';
                        $detail = "订单{orderId}投标成功,{waresName}";
                        break;
                    case ($v['tallyType'] == \Prj\Consts\OrderType::payAmount):
                        $typeName = '还本';
                        $detail = "订单{orderId}已还本,{waresName}";
                        break;
                    case ($v['tallyType'] == \Prj\Consts\OrderType::paysplit):
                        $typeName = '支付收益';
                        $detail = "订单{orderId}已收取收益,{waresName}";
                        break;
                    case ($v['tallyType'] == \Prj\Consts\OrderType::advPaysplit):
                        $typeName = '提前还款';
                        $detail = "订单{orderId}已提前还款,{waresName}";
                        break;
                    case ($v['tallyType'] == \Prj\Consts\OrderType::invite):
                        $typeName = '邀请返利';
                        $detail = "{nickname}({phone})使用您的邀请码投资\"{waresName}\"返利";
                        break;
                    case ($v['tallyType'] == \Prj\Consts\OrderType::giftInterest):
                        $typeName = '平台贴息';
                        $detail = "投资\"{waresName}\"贴息";
                        break;
                    case ($v['tallyType'] == \Prj\Consts\OrderType::dayInterest):
                        $typeName = '存钱罐收益';
                        $detail = "此次存钱罐收益的结算";
                        $detail = $v['descCreate']?(mb_substr($v['descCreate'],0,11,'utf-8').'存钱罐的收益结算'):$detail;
                        break;
                    case ($v['tallyType'] == \Prj\Consts\OrderType::flow):
                        $typeName = '流标退款';
                        $detail = "订单{orderId}流标退款,{waresName}";
                        break;
                    case ($v['tallyType'] == \Prj\Consts\OrderType::manualReturn):
                        $typeName = '提现失败';
                        $detail = "交易备注：订单{orderId}提现失败{returnPoundage}";
                        break;
                    default :
                        $typeName = '资金记录';
                        $detail = "资金记录";
                }
                if(false!==strpos($detail,"{orderId}")){
                    $detail = str_replace("{orderId}",$v['orderId'],$detail);
                }
                if(false!==strpos($detail,"{returnPoundage}")){
                    $detail = str_replace("{returnPoundage}",$v['poundage']?'。（同时退回手续费'.($v['poundage']/100).'元）':'',$detail);
                }

                //todo 返利邀请
                $invest = \Prj\Data\Investment::getCopy($v['orderId']);
                $invest->load();
                if($invest->exists()){
                    $ware = \Prj\Data\Wares::getCopy($invest->getField('waresId'));
                    $ware->load();
                    if($ware->exists()){
                        $arr['start'] = $ware->getField('priceStep')/100;
                        $arr['waresName'] = $ware->getField('waresName');
                    }
                    $user = \Prj\Data\User::getCopy($invest->getField('userId'));
                    $user->load();
                    if($user->exists()){
                        $arr['nickname'] = $user->getField('nickname');
                        $arr['phone'] = $user->getField('phone');
                        $arr['idCard'] = $user->getField('idCard');
                    }
                }
                $detail = str_replace("{start}",$arr['start'],$detail);
                $detail = str_replace("{waresName}",$arr['waresName'],$detail);
                $detail = str_replace("{nickname}",\Prj\IdCard::getCall($arr['idCard'],$arr['nickname']),$detail);
                $detail = str_replace("{phone}",substr($arr['phone'], 0, 3) . '****' . substr($arr['phone'], -4),$detail);

                if(false!==strpos($detail,"{start}")){
                    $invest = \Prj\Data\Investment::getCopy($v['orderId']);
                    $invest->load();
                    if($invest->exists()){
                        $waresId = $invest->getField('waresId');
                        $ware = \Prj\Data\Wares::getCopy($waresId);
                        $ware->load();
                        if($ware->exists()){
                            $start = $ware->getField('priceStart')/100;
                            $waresName = $ware->getField('waresName');
                            $detail = str_replace("{start}",$start,$detail);
                            $detail = str_replace("{waresName}",$waresName,$detail);
                        }
                    }
                }

                if($v['poundage']>0){
                    $poundageTag = '';
                    if($v['tallyType'] == \Prj\Consts\OrderType::manualReturn){
                        $rs[$k]['nAdd']-=$v['poundage'];
                    }else{
                        $poundageTag = "另收手续费".($v['poundage']/100)."元";
                        $rs[$k]['nAdd']+=$v['poundage'];
                    }
                    $rs[$k]['poundageDetail'] = $poundageTag;
                }


                $rs[$k]['detail'] = $detail;
                $rs[$k]['typeName'] = $typeName;

            }
        }
        $this->returnOK();
        $this->_view->assign('userId', $userId);
        $this->_view->assign('list', $rs);//出现多个assign中的list字段重复，修改如下突出唯一识别
        $this->_view->assign('listwallettallylist', $rs);
        $this->_view->assign('pager', $pager->toArray());
        
        //新版分页
        $this->_view->assign('wallettallyListPager',$pager->toArray());
        

    }

    /**
     * 检查支付密码是否正确
     * @input string paypwd 旧的支付密码
     * @output {code:200,msg:'success'}
     * //返回客户端旧的标志信息
     * @error {"code":400,"msg":"error","message":"***","errorCount":3}
     * //返回客户端新的标志信息
     * @error {"code":400,"msg":"error","message":"***","errorCountcheckpaypwd":3}
     * @author LTM
     */
    public function checkPaypwdAction()
    {
        $pwd = $this->_request->get('paypwd');
        if (empty($pwd)) return $this->returnError(\Prj\Lang\Broker::getMsg('system.arg_error'));
        $checkPayPwd = $this->_checkPaypwd($pwd);
        if (is_array($checkPayPwd)) {
            $this->_view->assign('errorCount', $checkPayPwd['errorCount']);//出现多个assign中的errorCount字段重复，修改如下突出唯一识别
            $this->_view->assign('errorCountcheckpaypwd', $checkPayPwd['errorCount']);
            $this->_view->assign('message', $checkPayPwd['msg']);
            return $this->returnError(\Prj\Lang\Broker::getMsg('user.error_paypwd'));
        } else {
            return $this->returnOK('success');
        }
    }

    /**
     * 我持有的券
     * By Hand
     * list:
     * {
     *      voucherId: "9014466868563899748",     【券ID】
     *      userId: "90003837339748",             【给哪个用户的】
     *      voucherType: "4",                     【券类型 8:红包 4：代金券 2：加息券】
     *      amount: "2000",                       【券金额 单位分/加息 单位%】
     *      codeCreate: "",                       【创建流水的代码标示】
     *      descCreate: "",                       【创建流水的用户说明】
     *      timeCreate: "20151105092736",         【创建时间】
     *      dtExpired: "20151205235959",          【失效时间】
     *      voucherTPL: "Std01",                  【使用规则模板】
     *      dtUsed: "20151105135617",             【使用后：实际使用时间】
     *      orderId: "1014467029775479610",       【使用后：订单】
     *      statusCode: "1",                      【状态 -1：废弃 0：未使用 1：已使用】
     *      iRecordVerID: "2",                    【iRecordVerID】
     *      limitsShelf: "",                      【类型限制】
     *      limitsType: "",                       【类型限制】
     *      limitsTag: ""                         【标签限制】
     *  }
     * @input int pageId 页码
     * @input int pageSize 每页容量
     * @input int type 券类型  空：全部  4：代金券 2：加息券
     * @input int status 券状态  空：全部 -1：废弃 0：未使用 1：已使用
     * @input int isDate 是否过期  空：全部 1：未过期
     * //返回客户端旧的标志信息
     * @output {'code':200,'list':【券列表】,'pager':【分页信息】}
     * //返回客户端新的标志信息
     * @output {'code':200,'listmyvouchers':【券列表】,'myVoucherspager':【分页信息】}
     * @errors {"code":400,"msg":"loginout"} 未登录
     * @errors {"code":400,"msg":"no_records"} 空记录
     */
    public function myVouchersAction()
    {
        $pageId = $this->_request->get('pageId', '1') - 0;
        $pageSize = $this->_request->get('pageSize', '10') - 0;
        $voucherType = $this->_request->get('voucherType');
        $statusCode = $this->_request->get('status');
        $isDate = $this->_request->get('isDate');
        $pager = new \Sooh\DB\Pager($pageSize);
        $pager->init(-1, $pageId);
        $userId = $this->user->userId;
        if (empty($userId)) {
            return $this->returnError('loginout');
        }
        $where = array(
            'userId' => $userId,
            'statusCode]' => 0
        );
        if ($isDate) {
            $where['dtExpired]'] = \Sooh\Base\Time::getInstance()->ymdhis();
        }
        if (empty($voucherType)) {
            $where['voucherType'] = [
                \Prj\Consts\Voucher::type_fake,
                \Prj\Consts\Voucher::type_yield,
            ];
        } else {
            $where['voucherType'] = $voucherType;
        }

        if (!empty($statusCode) || $statusCode === 0 || $statusCode === '0') {
            if (strpos($statusCode, ',')) {
                $where['statusCode'] = explode(',', $statusCode);
            } else {
                $where['statusCode'] = $statusCode;
            }
        }
        $rs = \Prj\Data\Vouchers::paged($pager, $where);
        if (empty($rs)) {
            $this->returnOK('no_records');
        } else {
            $this->_view->assign('list', $rs);//出现多个assign中的list字段重复，修改如下突出唯一识别
            $this->_view->assign('listmyvouchers', $rs);
            $this->_view->assign('pager', $pager->toArray());
            $this->_view->assign('myVoucherspager', $pager->toArray());
            $this->returnOK();
        }
    }

    /**
     * 创建支付密码
     * By Hand
     * @input int paypwd paypwd
     * @errors {"code":400,"msg":"arg_error"} 参数错误
     */
    public function creatPwdAction()
    {
        $paypwd = $this->_request->get('paypwd');
        if (empty($paypwd)) return $this->returnError(\Prj\Lang\Broker::getMsg('system.arg_error'));

        //验证为6位纯数字
        if (!preg_match(self::paypwd_format, $paypwd)) {
            return $this->returnError(\Prj\Lang\Broker::getMsg('user.paypwd_invalid'));
        }

        $this->user->load();
        $userId = $this->user->userId;
        $tradePwd = $this->user->getField('tradePwd', true);
        if (!empty($tradePwd)) return $this->returnError('paypwd_exist');
        $salt = $this->user->getField('salt', true) ? $this->user->getField('salt', true) : substr(uniqid(), -4);
        $this->user->setField('tradePwd', md5($paypwd . $salt));
        $this->user->setField('salt', $salt);
        try {
            $this->user->update();
        } catch (\ErrorException $e) {
			error_log("update user failed when ".__FUNCTION__.':'.$e->getMessage()."\n".$e->getTraceAsString());
            return $this->returnError(\Prj\Lang\Broker::getMsg('system.db_error'));
        }
        $this->_view->assign('paypwd', $paypwd);
        $this->_view->assign('userId', $userId);
        $this->returnOK();
    }

    /**
     * 是否设置了支付密码
     * By Hand
     * @output {userId: "90003837339748",status: 1,code: 200}
     */
    public function isSetPaypwdAction()
    {
        if (!$this->_isSetPaypwd($this->user)) {
            $this->_view->assign('status', 0);
        } else {
            $this->_view->assign('status', 1);
        }
        $this->returnOK();
    }

    /**
     * 我的红包列表
     * list:
     * {
     *     "voucherId":             券ID
     *     "userId":                用户ID
     *     "voucherType":           券类型 8：红包；4：利息券；2：加息券
     *     "amount":                金额，分为单位
     *     "codeCreate":            codeCreate
     *     "descCreate":            签到奖励
     *     "timeCreate":            创建时间
     *     "dtExpired":             过期时间
     *     "voucherTPL":            "Std01",
     *     "dtUsed":                使用时间
     *     "orderId":               使用时的订单ID
     *     "statusCode":            状态 -1：废弃；0：未使用；1：已使用
     *     "iRecordVerID":          "2",
     *     "limitsShelf":           "类型限制",
     *     "limitsType":            "类型限制",
     *     "limitsTag":             "标签限制"
     *     "explain":               "使用说明"
     *     "isExpired"              "是否过期"
     *  }
     * @input integer pageId 第几页 默认第一页
     * @input string order 排序条件 默认以获得时间从大到小
     * @input string pageSize 每页条数 默认5条
     * @input string lastPage 获取下一页时需要传递的参数
     * //返回客户端旧的标志信息
     * @output {"code":200,"msg":"success","usedAmount":"已使用金额","unuseAmount":"未使用金额","list":"****","lastPage":"**分页使用的参数**","countPages":"总页数"}
     * //返回客户端新的标志信息
     * @output {"code":200,"msg":"success","usedAmount":"已使用金额","unuseAmount":"未使用金额","redpacketlist":"****","redPacketPager":【分页信息】}
     * @errors {"code":400,"msg":"error"}
     */
    public function redPacketAction()
    {
        $pageId = $this->_request->get('pageId', 1) - 0;
        $pageSize = $this->_request->get('pageSize', 10) - 0;
	    $lastPage = $this->_request->get('lastPage');
        $timeMin = date('YmdHis', strtotime('-30 days'));//30天之内

	    if (empty($pageId)) {
		    $pageId = $lastPage - 0;
	    }

	    if (empty($pageId) || !is_numeric($pageId) || $pageId < 1) {
		    $pageId = 1;
	    }

        $this->loger->target = $pageId;
        try {
            $accountId = $this->user->userId;
			$this->user->load();
			$this->fixUserRedPack();
	        $map = [
		        'userId'      => $accountId,
		        'voucherType' => \Prj\Consts\Voucher::type_real,
		        'statusCode]' => 0,
		        'timeCreate]' => $timeMin
	        ];


//	        if (empty($lastPage)) {
//		        $where = ['where' => $map];
//	        } else {
//		        $where = json_decode(urldecode($lastPage), true);
//		        if (!$where) {
//			        $where = ['where' => $map];
//		        }
//	        }

	        $_dbVoucher = \Prj\Data\Vouchers::getCopy($this->user->userId);
	        $_dbObj = $_dbVoucher->db();
	        $_tbName = $_dbVoucher->tbname();
	        $counts = $_dbObj->getRecordCount($_tbName, $map);

            $pager = new \Sooh\DB\Pager($pageSize);
	        $pager->init($counts, $pageId);

//	        $counts = \Prj\Data\Vouchers::loopGetRecordsCount($map);
	        $countPages = ceil($counts / $pageSize);
//            $pager->init($counts, $pageId);

//            $list = \Prj\Data\Vouchers::loopGetRecordsPage(array('timeCreate' => 'rsort', 'voucherId' => 'sort'), $where, $pager);
	        $list = $_dbObj->getRecords($_tbName, '*', $map, 'rsort timeCreate sort voucherId', $pager->page_size, $pager->rsFrom());

	        $mineRedList = [];
	        $nowTime = \Sooh\Base\Time::getInstance()->ymdhis();
	        if ($list) {
		        foreach ($list as $k => $v) {
					$mineRedList[$k] = $v;
			        $mineRedList[$k]['isExpired'] = $v['dtExpired'] <= $nowTime ? 1 : 0;
		        }
	        }

            $this->_view->assign('usedAmount', $this->user->getField('redPacketUsed'));
            $this->_view->assign('unuseAmount', $this->user->getField('redPacket'));
            $this->_view->assign('list', $mineRedList);//出现多个assign中的list字段重复，修改如下突出唯一识别
            $this->_view->assign('redpacketlist', $mineRedList);
           
//	        $this->_view->assign('lastPage', urlencode(json_encode($list['lastPage'])));
	        $this->_view->assign('lastPage', $pageId + 1);
	        $this->_view->assign('countPages', $countPages);
	        //新版分页
	        $this->_view->assign('redPacketPager',['lastPage'=> $pageId + 1,'countPage'=>$countPages]);
	        
            return $this->returnOK();
        } catch (Exception $e) {
            return $this->returnError($e->getMessage(), $e->getCode());
        }
    }
	protected function fixUserRedPack()
	{
		\Prj\Data\User::refreshExpiredRedpacketAmount($this->user->userId);

		//		$accountId=$this->user->userId;
//		$expireChk = $this->user->getField('redPacketRecentlyExpired');
//		$now = date("YmdHis",\Sooh\Base\Time::getInstance()->timestamp());
//		error_log('用户表里红包过期的算法有问题--------------'.$accountId);
//		//if($expireChk<$now){
//			$tmp = \Prj\Data\Vouchers::getCopy($accountId);
//			$remain = $tmp->db()->getOne($tmp->tbname(),'sum(amount)',['userId' => $accountId, 'voucherType' => \Prj\Consts\Voucher::type_real, 'statusCode' => 0,'dtExpired>'=>$now]);
//			$remain-=0;
//			var_log($remain,  \Sooh\DB\Broker::lastCmd());
//			if($remain){
//				$nextmin = $tmp->db()->getOne($tmp->tbname(),'sum(amount)',['userId' => $accountId, 'voucherType' => \Prj\Consts\Voucher::type_real, 'statusCode' => 0,'dtExpired>'=>$now]);
//				$this->user->setField('redPacketRecentlyExpired', $nextmin);
//			}else{
//				$this->user->setField('redPacketRecentlyExpired', 0);
//			}
//			$this->user->setField('redPacket', $remain);
//
////				try{
////					$this->user->update();
////				} catch (Exception $ex) {
////
////				}
//		//}
	}
    /**
     * 我的发出的红包列表
     * list:
     * {
     *     "voucherId":             券ID
     *     "userId":                用户ID
     *     "voucherType":           券类型 8：红包；4：利息券；2：加息券
     *     "amount":                金额，分为单位
     *     "codeCreate":            codeCreate
     *     "descCreate":            签到奖励
     *     "timeCreate":            创建时间
     *     "dtExpired":             过期时间
     *     "voucherTPL":            "Std01",
     *     "dtUsed":                使用时间
     *     "orderId":               使用时的订单ID
     *     "statusCode":            状态 -1：废弃；0：未使用；1：已使用
     *     "iRecordVerID":          "2",
     *     "limitsShelf":           "类型限制",
     *     "limitsType":            "类型限制",
     *     "limitsTag":             "标签限制"
     *     "explain":               "使用说明"
     *  }
     * @input integer pageId 第几页 默认第一页
     * @input string order 排序条件 默认以获得时间从大到小
     * @input string pageSize 每页条数 默认5条
     * //返回客户端旧的标志信息
     * @output {"code":200,"msg":"success","usedAmount":"已使用金额","unuseAmount":"未使用金额","list":"****"}
     * //返回客户端新的标志信息
     * @output {"code":200,"msg":"success","usedAmount":"已使用金额","unuseAmount":"未使用金额","listsendpacket":"****"}
     * @errors {"code":400,"msg":"error"}
     */
    public function sendPacketAction()
    {
        $pageId = $this->_request->get('pageId', 1) - 0;
        $order = $this->_request->get('order');
        $this->loger->target = $pageId;
        try {
            $accountId = $this->user->userId;

            $pager = new \Sooh\DB\Pager($this->_request->get('pageSize', 100) - 0);
            $pager->init(-1, $pageId);
            $list = \Prj\Data\Vouchers::paged($pager, ['userId' => $accountId, 'voucherType' => \Prj\Consts\Voucher::type_share, 'statusCode]' => 0], $order);
            $this->user->load();
            $this->_view->assign('usedAmount', $this->user->getField('redPacketUsed'));
            $this->_view->assign('unuseAmount', $this->user->getField('redPacket'));
            $this->_view->assign('list', $list);//出现多个assign中的list字段重复，修改如下突出唯一识别
            $this->_view->assign('listsendpacket', $list);
            return $this->returnOK();
        } catch (Exception $e) {
            return $this->returnError($e->getMessage(), $e->getCode());
        }
    }

    /**
     * 我的回款计划表
     * By Hand
     * id: 1,
     * days: 323,【天数】
     * realDateYmd: 0,【实际支付日】
     * isPay: 0,【是否支付】
     * interestStatic: 10766,【固定收益】
     * interestAdd: 897,【活动附加收益】
     * interestExt: 0,【加息券券附加收益】
     * interestFloat: 0,【浮动收益】
     * amount: "100000",【本金】
     * amountExt: "0",【红包】
     * waresId: "1447911192363756",【标的ID】
     * waresName: "某知名面粉企业升级改造设备直租项目5319",【标的名】
     * month: 201610,【月份】
     * data:20161019,【计划支付日期】
     * //返回客户端旧的标志信息
     * @output {code:200,data:"...",msg:""}
     * //返回客户端新的标志信息
     * @output {code:200,datamyreturnplan:"...",msg:""}
     * @input int $isPay
     */
    protected static $totalInterest = 0;
    protected static $totalAmount = 0;
    protected static $totalAmountExt = 0;
    public function myReturnPlanAction()
    {
        $isPay = $this->_request->get('isPay', 0);
        $userId = $this->user->userId;
        $this->user->load();
        $invest = \Prj\Data\Investment::getCopy($userId);
        $db = $invest->db();
        $tb = $invest->tbname();
        $where = [
            'userId' => $userId,
            'orderStatus' => \Prj\Consts\OrderStatus::$running,
            'returnPlan!' => '',
        ];
        $rs = $db->getRecords($tb, ['waresId', 'waresName', 'returnPlan'], $where, ' rsort orderTime ');
        $newList = $this->_getReturnPlan($rs, $isPay);
        $this->_view->assign('totalAmount',self::$totalAmount);
        $this->_view->assign('totalAmountExt',self::$totalAmountExt);
        $this->_view->assign('totalInterest',self::$totalInterest);
        $data = $newList;
        $this->_view->assign('userId', $userId);
        $this->_view->assign('data', $data ? $data : []);//出现多个assign中的data字段重复，修改如下突出唯一识别
        $this->_view->assign('datamyreturnplan', $data ? $data : []);
        $this->returnOK();
    }

    /**
     * 获取积分列表
     * @input integer pageId 第几页 默认第一页
     * @input string order 排序条件 默认以获得时间从大到小
     * @input string pageSize 每页条数 默认5条
     * //返回客户端旧的标志信息
     * @output {"code":200,"msg":"success","list":"****"}
     * //返回客户端新的标志信息
     * @output {"code":200,"msg":"success","listpoints":"****"}
     * @errors {"code":400,"msg":"error"}
     */
    public function pointsAction()
    {
        $pageId = $this->_request->get('pageId', 1) - 0;
        $order = $this->_request->get('order');
        $this->loger->target = $pageId;
        try {
            $accountId = $this->user->userId;

            $pager = new \Sooh\DB\Pager($this->_request->get('pageSize', 5) - 0);
            $pager->init(-1, $pageId);

            $ret = \Prj\Data\ShopPoints::paged($pager, ['userId' => $accountId, 'statusCode]' => 0], $order);
            $this->_view->assign('list', $ret);//出现多个assign中的list字段重复，修改如下突出唯一识别
            $this->_view->assign('listpoints', $ret);
            return $this->returnOK('success');
        } catch (\Exception $e) {
            return $this->returnError($e->getMessage(), $e->getCode());
        }
    }

    /**
     * 券详情
     * list:
     * {
     *     "voucherId":             券ID
     *     "userId":                用户ID
     *     "voucherType":           券类型 8：红包；4：利息券；2：加息券
     *     "amount":                金额，分为单位
     *     "codeCreate":            codeCreate
     *     "descCreate":            签到奖励
     *     "timeCreate":            创建时间
     *     "dtExpired":             过期时间
     *     "voucherTPL":            "Std01",
     *     "dtUsed":                使用时间
     *     "orderId":               使用时的订单ID
     *     "statusCode":            状态 -1：废弃；0：未使用；1：已使用
     *     "limitsShelf":           "",
     *     "limitsType":            "",
     *     "limitsTag":             ""
     *  }
     * @input string voucherId 券ID
     * @input integer voucherType 券类型 8：红包；4：利息券；2：加息券
     * //返回客户端旧的标志信息
     * @output {"code":200,"msg":"success","data":"****"}
     * //返回客户端新的标志信息
     * @output {"code":200,"msg":"success","datavoucherdetail":"****"}
     * @errors {"code":400,"msg":"error"}
     */
    public function voucherDetailAction()
    {
        $voucherId = $this->_request->get('voucherId');
        //$voucherType = $this->_request->get('voucherType');

        $dbVoucher = \Prj\Data\Vouchers::getCopy($voucherId);
        $dbVoucher->load();
        if ($dbVoucher->exists()) {
            $ret = [
                'voucherId' => $dbVoucher->getField('voucherId'),
                'userId' => $dbVoucher->getField('userId'),
                'voucherType' => $dbVoucher->getField('voucherType'),
                'amount' => $dbVoucher->getField('amount'),
                'codeCreate' => $dbVoucher->getField('codeCreate'),
                'descCreate' => $dbVoucher->getField('descCreate'),
                'timeCreate' => $dbVoucher->getField('timeCreate'),
                'dtExpired' => $dbVoucher->getField('dtExpired'),
                'voucherTPL' => $dbVoucher->getField('voucherTPL'),
                'dtUsed' => $dbVoucher->getField('dtUsed'),
                'orderId' => $dbVoucher->getField('orderId'),
                'statusCode' => $dbVoucher->getField('statusCode'),
                'limitsShelf' => $dbVoucher->getField('limitsShelf'),
                'limitsType' => $dbVoucher->getField('limitsType'),
                'limitsTag' => $dbVoucher->getField('limitsTag'),
            ];
            $this->_view->assign('data', $ret);//出现多个assign中的data字段重复，修改如下突出唯一识别
            $this->_view->assign('datavoucherdetail', $ret);
            return $this->returnOK('success');
        } else {
            return $this->returnError(\Prj\Lang\Broker::getMsg('user.voucher_missing'));
        }
    }

    /**
     * 用户账户一览
     * data: {
     *      "redPacket":        红包金额
     *      "wallet":           现金金额
     *      "interestTotal":    累计获利
     *      "totalAssets":      资产总额
     *      "holdingAssets":    持有资产
     *      interestRP: 71979,  总收益
     *      amountRP: 601000,   总本金
     *      interestStatic: 5898,固定产品收益
     *      amountStatic: 1000000,固定产品本金
     *      interestFloat：0，浮动产品收益
     *      amountFloat：0，浮动产品本金
     *      CountRedPacketWait：3,未领取的红包数量
     *      hasNewRedPacket 1|0 1有未读红包，0没有未读红包
     *      msgCounts:未读消息数
     *      nickname:账户名
     *      isTodayCheckin：今天是否签到：1已经迁到；0未签到
     *      unsentVoucherNum: 未发出红包数
     *      freezeAmount:冻结金额
     *      hasBeenInvited:1已被邀请，0未被邀请
     *      canSetInviteCode:1可以设置邀请码，0不可以设置邀请码
     * }
     * @input string cmd cmd=voucher 获取券+红包   service 获取待收或已收本金和利息 isPay=All
     * @input string waresId 限制当前标的可用的券
     * @input string isPay 配合cmd=service 获取 待收或者已收或者全部
     * //返回客户端旧的标志信息
     * @output {"code":200,"msg":"success","data":"****"}
     * //返回客户端新的标志信息
     * @output {"code":200,"msg":"success","datainfo":"****"}
     * @errors {"code":400,"msg":"error"}
     */
    public function infoAction()
    {
		$this->user->load();
		$this->fixUserRedPack();
        $data = $this->_accountInfo($this->user);

	    $unsentVoucherNum = 0;
	    $map = [
		    'userId' => $this->user->userId,
		    'voucherType' => \Prj\Consts\Voucher::type_share,
		    'statusCode]' => 0,
		    'timeCreate]' => date('YmdHis', strtotime('-30 days')),//30天之内,
	    ];
//	    $pager = new \Sooh\DB\Pager(1000);

	    $_dbVoucher = \Prj\Data\Vouchers::getCopy($this->user->userId);
//	    $_dbVoucherCount = $_dbVoucher->db()->getRecordCount($_dbVoucher->tbname(), $map);

//	    $pager->init($_dbVoucherCount, 1);
//	    $ret = \Prj\Data\Vouchers::loopGetRecordsPage(array('timeCreate' => 'rsort', 'voucherId' => 'sort'), ['where' => $map], $pager);
	    $ret = $_dbVoucher->db()->getRecords($_dbVoucher->tbname(), '*', $map, 'rsort timeCreate sort voucherId');

	    //$list = [];
	    $keys = [];
	    if (!empty($ret)) {
		    foreach ($ret as $v) {
			    $pid      = $v['voucherId'];
			    $keys[]   = $pid;
			    $totalNum = \Prj\Data\VouchersInterim::loopGetRecordsCount(['pid' => $pid]);
			    $usedNum = \Prj\Data\VouchersInterim::loopGetRecordsCount(['pid' => $pid, 'isUsed' => 2]);
			    if (strtotime($v['dtExpired']) > \Sooh\Base\Time::getInstance()->timestamp() && $totalNum > $usedNum) {
				    $unsentVoucherNum++;
			    }
		    }
	    }
	    $data['unsentVoucherNum'] = $unsentVoucherNum;  //待使用的红包个数

	    $mineInviteTree = \Prj\Data\User::getMineInvitedTree($this->user->userId);
	    if (empty($mineInviteTree['parent'])) { //是否设置过邀请码
		    $data['hasBeenInvited'] = 0;
	    } else {
		    $data['hasBeenInvited'] = 1;
	    }

	    $data['canSetInviteCode'] = $this->_checkInviteStatus() ? 1 : 0;  //是否可以设置邀请码

        $this->_view->assign('data', $data);//出现多个assign中的data字段重复(user/msgcounts，userext/getsafelevel，user/info)，修改如下突出唯一识别
        $this->_view->assign('datainfo', $data);
	    return $this->returnOK('success');
    }

    /**
     * 冻结金额
     */
    protected function freezeAmount(){
        $userId = $this->user->userId;
        $tally = \Prj\Data\WalletTally::getCopy($userId);
        $db = $tally->db();
        $tbname = $tally->tbname();
        $rs = $db->getRecord($tbname,'sum(nAdd) as amount,sum(ext) as ext,sum(poundage) as poundage',['freeze'=>1,'statusCode'=>\Prj\Consts\Tally::status_new,'userId'=>$userId]);
        return abs($rs['amount']-0)+abs($rs['ext']) ;
    }

    /**
     * 更新最后读取红包的时间
     * @output {"code":200,"msg":"success"}
     * @errors {"code":400,"msg":"server_busy"}
     * @author LTM <605415184@qq.com>
     */
    public function readRedPacketAction()
    {
        try {
            $this->user->load();
            if ($this->user->exists()) {
                $this->user->setField('redPacketDtLast', \Sooh\Base\Time::getInstance()->ymdhis());
                $this->user->update();
                return $this->returnOK('success');
            } else {
                return $this->returnError(\Prj\Lang\Broker::getMsg('system.server_busy'));
            }
        } catch (\Exception $e) {
            return $this->returnErroe($e->getMessage(), $e->getCode());
        }
    }

    /**
     * 发送短信验证码-只能给当前登录用户发送
     * @throws ErrorException
     */
    public function sendSmsCodeAction()
    {
        $this->user->load();
        $phone = $this->user->getField('phone');

        $this->loger->sarg1 = $phone;
        if (\Sooh\Base\Ini::getInstance()->get('deploymentCode') <= 30) {
            $smsCode = $this->_request->get('universalMachineCode', '');
            if (!empty($smsCode)) {
                $this->loger->sarg2 = $smsCode;
            }
        }
        return $this->returnOK(\Prj\Consts\MsgDefine::$define['send_success']);
    }

    /*
    /**
     * 测试-添加一个用户并且登录
     * By Hand

    public function testAddUserAction()
    {
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        if (!$this->debug()) return;
        $user = \Prj\Data\User::createNew(rand(100000, 999999), '13262798028', '', '', '', '', '');
        $this->_view->assign('userId', $user->getField('userId'));
        $sess = \Sooh\Base\Session\Data::getInstance();
        $sess->set('accountId', $user->getField('userId'));
        $sess->set('nickname', $user->getField('nickname'));
    }
    */

    /**
     * 修改支付密码
     * @input string oldPwd 旧的交易密码
     * @input string newPwd 新的交易密码
     * @output {"code":200,"msg":"success"}
     * //返回客户端旧的标志信息
     * @error {"code":400,"msg":"error","message":"***","errorCount":3}
     * //返回客户端新的标志信息
     * @error {"code":400,"msg":"error","message":"***","errorCountresettradepwd":3}
     * @throws ErrorException
     * @author LTM <605415184@qq.com>
     */
    public function resetTradePwdAction()
    {
        $params = [
            'oldPwd' => $this->_request->get('oldPwd'),
            'newPwd' => $this->_request->get('newPwd'),
        ];

        $rules = [
            'oldPwd' => [self::paypwd_format, \Prj\Lang\Broker::getMsg('user.resetpaypwd_oldinvalid')],
            'newPwd' => [self::paypwd_format, \Prj\Lang\Broker::getMsg('user.paypwd_invalid')],
        ];
        if (\Lib\Misc\InputValidation::validateParams($params, $rules) === false) {
            return $this->returnError(\Lib\Misc\InputValidation::$errorMsg);
        }

        $ret = $this->_resetPaypwd($params['oldPwd'], $params['newPwd']);
        if ($ret !== true) {
            $this->_view->assign('message', $ret['msg']);
            $this->_view->assign('errorCount', $ret['errorCount']);//出现多个assign中的errorCount字段重复，修改如下突出唯一识别
            $this->_view->assign('errorCountresettradepwd', $ret['errorCount']);
            $this->returnError(\Prj\Lang\Broker::getMsg('system.error_custom'));
        } else {
            $this->returnOK('success');
        }
    }

    /**
     * 校验身份证号
     * @input string idCardSn 身份证号
     * @output {"code":200,"msg":"success"}
     * @error {"code":400,"msg":"error"}
     * @throws ErrorException
     */
    public function checkIdCardSnAction()
    {
        $idCardSn = $this->_request->get('idCardSn');

        $this->user->load();
        $dbSn = $this->user->getField('idCard');
        if (is_string($idCardSn) && $idCardSn === $dbSn) {
            return $this->returnOK('success');
        } else {
            return $this->returnError(\Prj\Lang\Broker::getMsg('user.idcard_mismatch'));
        }
    }

    /**
     * 找回支付密码
     * @input string idCardSn 身份证号码
     * @input string pwd 新的支付密码
     * @output {"code":200,"msg":"success"}
     * @error {"code":400,"msg":"***"}
     * @throws ErrorException
     * @throws Exception
     */
    public function findPayPwdAction()
    {
        $idCardSn = $this->_request->get('idCardSn');
        $pwd = $this->_request->get('pwd');

        //单独验证密码
        $pwd_len = mb_strlen($pwd);
        if (!preg_match('#^\d{6}$#', $pwd)) {
            //return $this->returnError('密码不合法');
            return $this->returnError(\Prj\Lang\Broker::getMsg('passport.password_is_not_valid'));
        }

        $this->user->load();
        $dbSn = $this->user->getField('idCard');
        if (is_string($idCardSn) && $idCardSn === $dbSn) {
            $salt = substr(uniqid(), -4);
            $dbFailed = [
                'forbidden' => 0,//是否锁定
                'forbiddenExpires' => 0,//锁定结束时间
                'errorExpires' => 0,//错误次数时间
                'errorCount' => 0,//错误次数
            ];

            $this->user->setField('tradePwd', md5($pwd . $salt));
            $this->user->setField('salt', $salt);
            $this->user->setField('failedForbidden', $dbFailed);
            $this->user->update();
            return $this->returnOK('success');
        } else {
            return $this->returnError(\Prj\Lang\Broker::getMsg('user.idcard_mismatch'));
        }
    }

    public function testAction()
    {

    }

    /**
     * 测试-充值以后回调
     * By Hand
     */
    public function testRechargeResultAction()
    {
        if (!$this->debug()) return;
        //测试-请求自己的接口
        $orderId = $this->_request->get('orderId');
        $test = $this->_testRechargeResult($orderId);
        $this->_view->assign('test', $test);
    }

    public function _testRechargeResult($orderId)
    {
        $recharge = \Prj\Data\Recharges::getCopy($orderId);
        $recharge->load();
        $amount = $recharge->getField('amount');
        $args = json_encode(['orderId' => $orderId, 'amount' => $amount, 'status' => 'success']);
        $time = time();
        $sign = md5($time . 'asgdfw4872hfhjksdhr8732trsj');
        //RpcConfig
        $url = \Sooh\Base\Ini::getInstance()->get('RpcConfig')['urls'][0] . "&service=PayGW&cmd=rechargeResult&args=" . $args . "&dt=" . $time . "&sign=" . $sign;
        var_log($url,'充值回调url>>>');
        var_log($url, __CLASS__ . '479>>>>>>>>>');
        $result = file_get_contents($url);
        $test = json_decode($result, true);
        $test['url'] = $url;
        return $test;
    }

    /**
     * 我的消息列表
     * list:
     * {
     *        "msgId": "10978456801117",       消息ID
     *        "title": "dev/testMsg",          标题
     *        "content": "zxcvzxcv",           内容
     *        "ext": "",                       扩展
     *        "sendId": "90003837339748",      发送者ID
     *        "receiverId": "81568478941117",  接受者ID
     *        "createTime": "20151120151327", 创建时间
     *        "status": "0",                   状态
     *        "type": "1",                     类型
     *        "iRecordVerID": "1"              iRecordVerID
     * }
     * @input  integer type 消息类型 1投标；2合同下发；3项目回款；4提现，5红包；6返利
     * @input  integer status 消息状态 0未读；-1已删除废弃；1已读
     * @input  integer pageId 页数
     * @input  integer pageSize 每页大小
     * //返回客户端旧的标志信息
     * @output {"code":200,"msg":"success","list": [{"msgId": "10978456801117","title": "dev/testMsg","content":
     *         "zxcvzxcv","ext": "","sendId": "90003837339748","receiverId": "81568478941117","createTime":
     *         "20151120151327","status": "0","type": "1","iRecordVerID": "1"},{"...":"..."},]}
     * //返回客户端新的标志信息       
     *@output {"code":200,"msg":"success","MyMsgList": [{"msgId": "10978456801117","title": "dev/testMsg","content":
     *         "zxcvzxcv","ext": "","sendId": "90003837339748","receiverId": "81568478941117","createTime":
     *         "20151120151327","status": "0","type": "1","iRecordVerID": "1"},{"...":"..."},"MyMsgPager":【分页】]}
     * @errors {"code":400,"msg":"****"}
     * @author LTM <605415184@qq.com>
     */
    public function myMsgAction()
    {
        $type = $this->_request->get('type', 0) - 0;
        $status = $this->_request->get('status') - 0;
        $pageId = $this->_request->get('pageId', 1) - 0;
        $pageSize = $this->_request->get('pageSize') - 0;
        $userId = $this->user->userId;

        try {
            $ret = \Lib\Services\Message::getInstance()->getList($userId, $type == 0 ? null : $type, $status, $pageId, $pageSize);
            $this->_view->assign('list', $ret['list']);//出现多个assign中的list字段重复，修改如下突出唯一识别
			
	        $this->_view->assign('pageSize', $pageSize);
	        $this->_view->assign('pageId', $pageId);//出现多个assign中的pageId字段重复，修改如下突出唯一识别
			$this->_view->assign('countPage', $ret['countPage']);
			//新版
			$this->_view->assign('MyMsgList', $ret['list']);
			$this->_view->assign('MyMsgPager',['pageSize'=>$pageSize,'pageId'=>$pageId,'countPage'=>$ret['countPage']]);

            return $this->returnOK('success');
        } catch (\Exception $e) {
            return $this->returnError($e->getMessage(), $e->getCode());
        }
    }

    /**
     * 更改消息为读取状态
     * @input string msgId 消息ID，多个用英文逗号隔开
     * @output {"code":200,"msg":"success"}
     * @errors {"code":400,"msg":"***"}
     */
    public function readMsgAction()
    {
        $msgId = $this->_request->get('msgId');
        $userId = $this->user->userId;
        try {
            \Lib\Services\Message::getInstance()->read($msgId, $userId);
            $this->returnOK('success');
        } catch (\Exception $e) {
            return $this->returnError($e->getMessage(), $e->getCode());
        }
    }

	/**
	 * 全部设为已读
	 * @output {'code':200,'msg':'success'}
	 */
	public function readAllMsgAction()
	{
		$userId = $this->user->userId;
		$ret = \Lib\Services\Message::getInstance()->readAll($userId);
		return $this->returnOK('success');
	}

    /**
     * 删除消息
     * @input string msgId 消息ID，多个用英文逗号隔开
     * @output {"code":200,"msg":"success"}
     * @errors {"code":400,"msg":"***"}
     */
    public function delMsgAction()
    {
        $msgId = $this->_request->get('msgId');
        $userId = $this->user->userId;
        try {
            \Lib\Services\Message::getInstance()->del($msgId, $userId);
            $this->returnOK('success');
        } catch (\Exception $e) {
            return $this->returnError($e->getMessage(), $e->getCode());
        }
    }

    /**
     * 未读消息总数
     * //返回客户端旧的标志信息
     * @output {"code":200,"msg":"success","data":{"counts":80}}
     * //返回客户端新的标志信息
     * @output {"code":200,"msg":"success","datamsgcounts":{"counts":80}}
     * @errors {"code":200,"msg":"error"}
     */
    public function msgCountsAction()
    {
        $userId = $this->user->userId;
        $where = ['receiverId' => $userId, 'status' => \Prj\Consts\Message::status_unread];
        try {
            $ret = \Lib\Services\Message::getInstance()->getCount($where);
            $this->_view->assign('data', ['counts' => $ret]);//出现多个assign中的data字段重复(user/msgcounts，userext/getsafelevel，user/info)，修改如下突出唯一识别
            $this->_view->assign('datamsgcounts', ['counts' => $ret]);
            return $this->returnOK('success');
        } catch (\Exception $e) {
            return $this->returnError($e->getMessage(), $e->getCode());
        }
    }

    /**
     * 申请成为借款人
     * @errors {"code":200,"msg":"is_borrower"}  已经是借款人了
     * @errors {"code":200,"msg":"wait_borrower"}  已经发送过借款申请了
     * @errors {"code":200,"msg":"forbid"} 禁止申请
     * @errors {"code":200,"msg":"db_error"}  数据库错误
     */
    public function toBeBorrowerAction()
    {
        $frm = \Sooh\Base\Form\Broker::getCopy('default')
            ->init(\Sooh\Base\Tools::uri(), 'get', \Sooh\Base\Form\Broker::type_c);
        $frm->addItem('userId', \Sooh\Base\Form\Item::factory('用户ID', $this->user->userId, \Sooh\Base\Form\Item::constval, []));

        $frm->fillValues();
        if ($frm->isThisFormSubmited()) {
            $user = $this->user;
            $user->load();
            if ($user->getField('isBorrower') == \Prj\Consts\Borrower::forbid) {
                return $this->returnError(\Prj\Lang\Broker::getMsg('user.beBorrower_forbidden'));
            }
            if ($user->getField('isBorrower') == \Prj\Consts\Borrower::is) {
                return $this->returnError(\Prj\Lang\Broker::getMsg('user.beBorrower_already'));
            }
            if ($user->getField('isBorrower') == \Prj\Consts\Borrower::wait) {
                return $this->returnError(\Prj\Lang\Broker::getMsg('user.beBorrower_wait'));
            }
            $user->setField('isBorrower', \Prj\Consts\Borrower::wait);
            try {
                $user->update();
            } catch (\ErrorException $e) {
                var_log($e->getMessage(), 'error>>>>>>>>>>>>>>');
                return $this->returnError(\Prj\Lang\Broker::getMsg('system.db_error'));
            }
            return $this->returnOK();
        }
    }

    /**
     * 推送设置
     * @input string key 名称 all：总开关；bid：投标；contractIssued：合同下发；repayment：项目回款；withdrawal：提现；redPacket：红包；rebate：返利
     * @input integer value 值，1表示开启，0表示关闭
     * @output {"code":200,"msg":"success"}
     * @errors {"code":400,"msg":"error"}
     */
    public function setToPushAction()
    {
        $key = $this->_request->get('key');
        $value = $this->_request->get('value');

	    $setsArr = array_values(\Prj\Message\Message::$pushType);
	    array_unshift($setsArr, 'all');

//        $setsArr = ['all', 'bid', 'contractIssued', 'repayment', 'withdrawal', 'redPacket', 'rebate'];
        if (in_array($key, $setsArr, true) && in_array($value, ['0', '1'], true)) {
            try {
                $userId = $this->user->userId;
                \Prj\Data\User::setToPush($userId, $key, $value);
                return $this->returnOK('success');
            } catch (\Exception $e) {
                return $this->returnError($e->getMessage(), $e->getCode());
            }
        } else {
            return $this->returnError(\Prj\Lang\Broker::getMsg('account.params_error'));
        }
    }

    /**
     * 投资输入金额的页面
     *      canSetInviteCode    1可以设置邀请码，0不可以设置邀请码
     * @input string waresId 标的Id
     * @output {'code':200,'canSetInviteCode':1,...}
     * @errors {"code":400,"msg":"no_waresId"} 没有传入标的ID
     * @errors {"code":400,"msg":"void_waresId"} 无效的标的ID
     */
    public function addOrderAction()
    {
		$this->user->load();
		$this->fixUserRedPack();
        $waresId = $this->_request->get("waresId");
        if (empty($waresId)) return $this->returnError(\Prj\Lang\Broker::getMsg('user.wares_invalid'));
        $ware = \Prj\Data\Wares::getCopy($waresId);
        $ware->load();
        if (!$ware->exists()) return $this->returnError(\Prj\Lang\Broker::getMsg('user.wares_invalid'));
        $wareInfo = $ware->dump();
        $wareHide = ['returnPlan', 'iRecordVerID', 'sLockData'];
        if (!empty($wareHide)) {
            foreach ($wareHide as $v) {
                unset($wareInfo[$v]);
            }
        }
        $tplClass = "\\Prj\\WaresTpl\\" . $wareInfo['viewTPL'] . "\\Viewer";
        var_log($tplClass, '$tplClass>>>>>>>>>>');
        if (empty($wareInfo['introDisplay'])) {
            var_log('[error]tpl_class_not_exists>>>>>>>>>>>>>>');
            $temp = [
                'a' => '',
                'b' => [],
            ];
        } else {
            $temp = $wareInfo['introDisplay'];
        }
        //传假数组
        $temp = [
            'a' => '',
            'b' => [],
        ];

        $wareInfo['introDisplay'] = $temp;
        $wareInfo['show'] = $this->_buyTime($waresId, $this->user->userId) ? 1 : 0;

        $userInfo = $this->_accountInfo($this->user);
        $userInfo['isSetPaypwd'] = $this->_isSetPaypwd($this->user);
        $userInfo['isBindCard'] = $this->user->getField('ymdBindcard') ? 1 : 0;
        $cardsList = $this->_myCards();
        $vouchersList = $this->_myVouchers($waresId, [\Prj\Consts\Voucher::type_fake, \Prj\Consts\Voucher::type_yield])['voucherList'];
        $userInfo['canSetInviteCode'] = $this->_checkInviteStatus() ? 1 : 0;  //是否可以设置邀请码
        $this->_view->assign('userInfo', $userInfo);//出现多个assign中的userInfo字段重复，修改如下突出唯一识别
        $this->_view->assign('userInfoaddorder', $userInfo);
        $wareInfo['webUrl']=\Sooh\Base\Tools::uri(['waresId'=>$waresId],'newDec','Financing');
        $wareInfo['images']=\Prj\WaresTpl\Std02\Viewer::getImgList($ware->getField('introDisplay'));
        $wareInfo['uniqueOpId'] = $userInfo['uniqueOp'];
        $this->_view->assign('wareInfo', $wareInfo);
        $this->_view->assign('cardsList', $cardsList);
        $this->_view->assign('canSetInviteCode', $userInfo['canSetInviteCode']);
        $this->_view->assign('vouchersList', $vouchersList ? $vouchersList : []);
        $this->returnOK();
    }
    /**
     * 投资输入金额的页面
     * @input string waresId 标的Id
     * @errors {"code":400,"msg":"void_waresId"} 无效的标的ID
     */
    public function addOrderV1Action()
    {
        $this->addOrderAction();
    }

    protected function _buyTime($waresId = null, $userId)
    {
        $orderStatus = \Prj\Consts\OrderStatus::$running;
        $orderStatus[] = \Prj\Consts\OrderStatus::flow;
        $where = [
            'userId' => $userId,
            'orderStatus' => \Prj\Consts\OrderStatus::$running,
        ];
        if(!empty($waresId))$where['waresId'] = $waresId;
        return \Prj\Data\Investment::loopGetRecordsCount($where);
    }

    /**
     * 获取推送设置
     * all：总开关；bid：投标；contractIssued：合同下发；repayment：项目回款；withdrawal：提现；redPacket：红包；rebate：返利...
     * 1表示开启，0表示关闭
     * //返回客户端旧的标志信息
     * @output {"code":200,"msg":"***","data":{"all":1,"data":{['type':'bid','name':'投标','value':1],['type':'contractIssued','name':'合同下发','value':0]}}}
     * //返回客户端新的标志信息
     * @output {"code":200,"msg":"***","datagetpushsetting":{"all":1,"data":{['type':'bid','name':'投标','value':1],['type':'contractIssued','name':'合同下发','value':0]}}}
     * @errors {"code":400,"msg":"***"}
     */
    public function getPushSettingAction()
    {
        $userId = $this->user->userId;
        try {
            $dbUser = \Prj\Data\User::getCopy($userId);
            $dbUser->load();
            if ($dbUser->exists()) {
                $ret = $dbUser->getField('pushSetting');
	            $typeMapName = \Prj\Message\Message::getPushMap();
	            $list = [];
	            foreach ($typeMapName as $k => $v) {
		            if (empty($ret)) {
			            $list[] = [
				            'type' => $k,
				            'name' => $v,
			                'value' => 1,
			            ];
		            } else {
			            $list[] = [
				            'type' => $k,
				            'name' => $v,
				            'value' => isset($ret[$k]) ? $ret[$k] : 1,
			            ];
		            }
	            }
	            //增加开标提醒
	            $list[] = [
		            'type' => 'startBid',
		            'name' => '标的开售提醒',
		            'value' => isset($ret['startBid']) ? $ret['startBid'] : 1,
	            ];

	            if (empty($ret)) {
		            $_all = 1;
	            } else {
		            $_all = $ret['all'];
	            }

                $this->_view->assign('all', $_all);
                $this->_view->assign('data', $list);//出现多个assign中的data字段重复，修改如下突出唯一识别
                $this->_view->assign('datagetpushsetting', $list);
                return $this->returnOK(\Prj\Lang\Broker::getMsg('passport.success'));
            } else {
	            return $this->returnError(\Prj\Lang\Broker::getMsg('account.account_is_not_existing'));
            }
        } catch (Exception $e) {
            return $this->returnError(\Prj\Lang\Broker::getMsg('system.server_busy'));
        }
    }

    /**
     * 充值订单详情查询
     * @input string ordersId 充值订单号
     * orderStatus   2=>网关受理中  39=>成功      4=>失败
     * @output {code:200,recharge:[]}
     * @errors {code:400,msg:'no_orders_id'} 单号为空
     */
    public function rechargeDetailAction()
    {
        $ordersId = $this->_request->get('ordersId');
        if (empty($ordersId)) return $this->returnError(\Prj\Lang\Broker::getMsg('user.recharegeOrders_missing'));
        $recharge = \Prj\Data\Recharges::getCopy($ordersId);
        $recharge->load();
        if (!$recharge->exists()) {
            $this->_view->assign('recharge', []);
            return $this->returnOK();
        }
        $data = $recharge->dump();
        if ($data['userId'] != $this->user->userId) {
            var_log('别人的充值订单', 'rechargeDetail>>>>>>>>');
            $this->_view->assign('recharge', []);
            return $this->returnOK();
        }
        $delete = [ 'poundage', 'batchId', 'iRecordVerID'];
        if (!empty($delete)) {
            foreach ($delete as $v) {
                unset($data[$v]);
            }
        }
        $this->_view->assign('recharge', $data);
        $this->_view->assign('withdrawDetail', $data);
        return $this->returnOK();
    }

    /**
     * 获取自己的优惠码
     * //返回客户端旧的标志信息
     * @output {"code":200,"info":{"invitationCode":"****"},"msg":"成功"}
     * //返回客户端新的标志信息
     * @output {"code":200,"infogetinvitationcode":{"invitationCode":"****"},"msg":"成功"}
     * @errors {"code":400,"msg":"用户不存在"}
     */
    public function getInvitationCodeAction()
    {
        try {
            $this->user->load(myInviteCode);
            if ($this->user->exists()) {
                $invitationCode = $this->user->getField('myInviteCode');
                $this->_view->assign('info', ['invitationCode' => $invitationCode]);//出现多个assign中的info字段重复，修改如下突出唯一识别
                $this->_view->assign('infogetinvitationcode', ['invitationCode' => $invitationCode]);
                return $this->returnOK(\Prj\Consts\MsgDefine::$define['success']);
            } else {
                $this->loger->target = $this->loger->userId = $this->user->userId;
                return $this->returnError(\Prj\Lang\Broker::getMsg('user.user_notfound'));
            }
        } catch (Exception $e) {
            return $this->returnError($e->getMessage(), $e->getCode());
        }
    }

    /**
     * 更新最后登录时间
     * @output {"code":200,"msg":"success"}
     * @errors {"code":400,"msg":"failed"}
     * @author LTM <605415184@qq.com>
     */
    public function updDtLastAction()
    {
        try {
            $this->user->load();
            if ($this->user->exists()) {
                $this->user->setField('dtLast', \Sooh\Base\Time::getInstance()->ymdhis());
                $this->user->update();
                return $this->returnOK('success');
            } else {
                return $this->returnError(\Prj\Lang\Broker::getMsg('system.server_busy'));
            }
        } catch (Exception $e) {
            return $this->returnError($e->getMessage());
        }
    }

    /**
     * 邀请人页面
     *  isInvite        是否有邀请资格
     *  inviteCode      我的邀请码
     *  inviteUserNum   已邀请人数
     *  rebatingAmount  待返金额
     *  rebateAmount    已返金额
     *  list
     *      ymd         年月日
     *      his         时分秒
     *      phoneName   手机号
     *      nickName    姓名
     *      amount      本次返利
     *      sumAmount   累计返利(元)
     *      sumAmountReal 累计返利（分）
     *      isRebating  是否待返,1待返，0返利完成
     * //返回客户端旧的标志信息
     * @output {code:200,data:"***",msg:success}
     * //返回客户端新的标志信息
     * @output {code:200,datainvite:"***",msg:success}
     * @author LTM <605415184@qq.com>
     */
    public function inviteAction()
    {
        $userId = $this->user->userId;
	    $pageSize = 10;
	    $pageId = 1;
        $this->user->load();
        if (!($this->user->exists())) {
	       // return $this->returnError('server busy');
	        return $this->returnError(\Prj\Lang\Broker::getMsg('system.server_busy'));
        }

        //检查是否获得邀请资格
        if ($this->_buyTime(null, $this->user->userId) < 2) {
            //未获得邀请资格
            $data = [
                'isInvite' => '0',
                'inviteCode' => '投资满2次后即可成为邀请人',
                'invitedUserNum' => '0',
                'stayedRebate' => '0',
                'rebatingAmount' => '0',
                'rebateAmount' => '0',
                'list' => [],
                'url' => '',
            ];
        } else {
            $data['isInvite'] = '1';
            $data['inviteCode'] = $this->user->getField('myInviteCode');//我的邀请码
            //$data['invitedTree'] = \Prj\Data\InviteCode::find($data['inviteCode']);//我的邀请串-向上
	        $pager = new \Sooh\DB\Pager($pageSize);
	        $counts = \Prj\Data\Rebate::getCount(['userId' => $userId, 'statusCode]' => 3, 'statusCode!' => 4]);
	        $data['countPages'] = ceil($counts / $pageSize);
	        $data['pageId'] = $pageId;
	        $pager->init($counts, 1);

	        $list = \Prj\Data\Rebate::paged($pager, ['userId' => $userId, 'statusCode]' => 3, 'statusCode!' => 4], 'rsort updateYmd', $userId);

            $invitedUserList = $this->user->getInvitedUser($userId);//我邀请的用户列表
            $data['invitedUserNum'] = count($invitedUserList);//我邀请的用户数
            $data['rebatingAmount'] = $this->user->getField('rebating');//待返金额
            $data['rebateAmount'] = $this->user->getField('rebate');//已获返利

            if (!empty($list)) {
				$tmp=[];
                foreach ($list as $k => $v) {
	                $tmp[$k]['ymd']           = date('Y-m-d', strtotime($v['updateYmd']));
	                $tmp[$k]['his']           = date('H:i:s', strtotime($v['updateYmd']));
	                $tmp[$k]['phoneName']     = substr($v['childPhone'], 0, 3) . '****' . substr($v['childPhone'], -4);
	                $tmp[$k]['nickName']      = $v['childNickname'];
	                $tmp[$k]['amount']        = $v['amount'];
	                $tmp[$k]['sumAmount']     = sprintf('%.2f', $v['sumAmount'] / 100);
	                $tmp[$k]['sumAmountReal'] = $v['sumAmount'];
	                $tmp[$k]['isRebating']    = $v['statusCode'] == 39 ? 0 : 1;
                }
                $data['list'] = $tmp;//返利列表
            } else {
	            $data['list'] = [];//返利列表
            }
	        $data['url'] = 'http://' . $_SERVER['HTTP_HOST'] . '/spread/register/inviteReg.html?inviteCode=' . $data['inviteCode'];
        }
        $this->_view->assign('data', $data);//出现多个assign中的data字段重复，修改如下突出唯一识别
        $this->_view->assign('datainvite', $data);
//		$this->_view->assign('uri', \Sooh\Base\Ini::getInstance()->get('uriBase')['www'] . '/index.php?__=user/getInviteList&__VIEW__=json');

        $this->returnOK('success');
    }

    /**
     * 获取被邀请人的返利列表
     * @input integer pageId 第几页，默认第一页
     * @input integer pageSize 默认每页10条
     * //返回客户端旧的标识信息
     * @output {"code":200,"msg":"success","list":[{"nickname":"nickname","counts":"counts","zhName":"zhName","amount":"amount","ymd":"ymd","his":"his"},{"nickname":"..."}]}
     * //返回客户端新的标志信息
     * @output {"code":200,"msg":"success","listgetinvitelist":[{"nickname":"nickname","counts":"counts","zhName":"zhName","amount":"amount","ymd":"ymd","his":"his"},{"nickname":"..."},{"getInviteListPager":【分页】}]}
     * @errors {"code":401,"msg":"error"}
     * @author LTM <605415184@qq.com>
     */
    public function getInviteListAction()
    {
        $userId = $this->user->userId;
	    $pageId = $this->_request->get('pageId' ,1) - 0;
	    $pageSize = $this->_request->get('pageSize', 10) - 0;

	    $counts = \Prj\Data\Rebate::getCount(['userId' => $userId, 'statusCode!' => -1]);
	    $countPages = ceil($counts / $pageSize);
	    $pager = new \Sooh\DB\Pager($pageSize);
	    $pager->init($counts, $pageId);
	    $list = \Prj\Data\Rebate::paged($pager, ['userId' => $userId, 'statusCode!' => -1], 'rsort updateYmd', $userId);

	    if (!empty($list)) {
			$tmp=[];
		    foreach ($list as $k => $v) {
			    $tmp[$k]['ymd'] = date('Y-m-d', strtotime($v['createYmd']));
			    $tmp[$k]['his'] = date('H:i:s', strtotime($v['createYmd']));
			    $tmp[$k]['phoneName'] = substr($v['childPhone'], 0, 3) . '****' . substr($v['childPhone'], -4);
			    $tmp[$k]['nickName'] = $v['childNickname'];
			    $tmp[$k]['amount'] = $v['amount'];
			    $tmp[$k]['sumAmount'] = sprintf('%.2f', $v['sumAmount'] / 100);
			    $tmp[$k]['sumAmountReal'] = $v['sumAmount'];
			    $tmp[$k]['isRebating'] = $v['statusCode'] == 39 ? 0 : 1;
		    }
		    $data['list'] = $tmp;//返利列表
	    } else {
		    $data['list'] = [];//返利列表
	    }
	    $this->_view->assign('countPages', $countPages);
	    $this->_view->assign('pageId', $pageId);
        $this->_view->assign('list',  $data['list']);///出现多个assign中的list字段重复，修改如下突出唯一识别
        $this->_view->assign('listgetinvitelist',  $data['list']);
        
        //新版分页规则
        $this->_view->assign('getInviteListPager',['pageId'=>$pageId,'countPage'=>$countPages]);
        $this->returnOK('success');
    }

    /**
     * 发出红包列表
     * receiveNumForParent 被领取子红包个数
     * usedNumForParent 被使用子红包个数
     * shareVoucherTitle 分享用的标题
     * shareVoucherDesc 分享用的描述
     * shareVoucherPic 分享用的图片
     * shareVoucherUrl 分享用的链接
     * countPages 总页数
     * lastPage 获取下一页时需要传递的参数
     * list:usedNum 被领取数
     * list:unUsedNum 剩余数
     * list:expiredDesc 有效期
     * list:flag 状态：1发红包；2领完了；3已过期
     * @input integer pageId 分页ID
     * @input integer pageSize 可不传，分页大小，默认每页10条
     * @input string lastPage 获取下一页时需要传递的参数
     * //返回客户端旧的标志信息
     * @output {"code":"200","msg":"success","receiveNumForParent":"13","usedNumForParent":"2","list":[{"usedNum":"3","unUsedNum":"2","expiredDesc":"2016-12-12 12:12:12","flag":"1","voucherId":"23766127361616"},{...}],}
     * //返回客户端新的标志信息
     * @output {"code":"200","msg":"success","receiveNumForParent":"13","usedNumForParent":"2","listgetparentvoucher":[{"usedNum":"3","unUsedNum":"2","expiredDesc":"2016-12-12 12:12:12","flag":"1","voucherId":"23766127361616"},{...}],"getParentVoucherListPager":【分页】}
     * @errors {"code":"200","msg":"error"}
     * @author LTM <605415184@qq.com>
     */
    public function getParentVoucherListAction()
    {
        $pageId = $this->_request->get('pageId', 1) - 0;
        $pageSize = $this->_request->get('pageSize', 10) - 0;
	    $lastPage = $this->_request->get('lastPage');
	    $timeMin = date('YmdHis', strtotime('-30 days'));//30天之内

	    if (empty($pageId)) {
		    $pageId = $lastPage - 0;
	    }

	    if (empty($pageId) || !is_numeric($pageId) || $pageId < 1) {
		    $pageId = 1;
	    }

        try {
            $accountId = $this->user->userId;
            $map = [
                'userId' => $accountId,
                'voucherType' => \Prj\Consts\Voucher::type_share,
                'statusCode]' => 0,
                'timeCreate]' => $timeMin,
            ];

//	        if (empty($lastPage)) {
//		        $where = ['where' => $map];
//	        } else {
//		        $where = json_decode(urldecode($lastPage), true);
//		        if (!$where) {
//			        $where = ['where' => $map];
//		        }
//	        }

	        $_dbVoucher = \Prj\Data\Vouchers::getCopy($this->user->userId);
	        $_dbObj = $_dbVoucher->db();
	        $_tbName = $_dbVoucher->tbname();
	        $counts = $_dbObj->getRecordCount($_tbName, $map);

	        $pager = new \Sooh\DB\Pager($pageSize);
	        $pager->init($counts, $pageId);

//	        $counts = \Prj\Data\Vouchers::loopGetRecordsCount($map);
	        $countPages = ceil($counts / $pageSize);
//            $pager = new \Sooh\DB\Pager($pageSize);
//            $pager->init($counts, $pageId);
//            $ret = \Prj\Data\Vouchers::loopGetRecordsPage(['timeCreate' => 'rsort', 'voucherId' => 'sort'], $where, $pager);
	        $ret = $_dbObj->getRecords($_tbName, '*', $map, 'rsort timeCreate sort voucherId', $pager->page_size, $pager->rsFrom());


            $list = [];
            $keys = [];
            if (is_array($ret) && !empty($ret)) {
                foreach ($ret as $v) {
                    $pid = $v['voucherId'];
                    $keys[] = $pid;
                    $totalNum = \Prj\Data\VouchersInterim::loopGetRecordsCount(['pid' => $pid]);
					if ($totalNum) {
	                    $usedNum = \Prj\Data\VouchersInterim::loopGetRecordsCount(['pid' => $pid, 'isUsed' => 2]);
	                    if (strtotime($v['dtExpired']) <= \Sooh\Base\Time::getInstance()->timestamp()) {
	                        $flag = 3;//过期了
	                    } elseif ($totalNum > $usedNum) {
	                        $flag = 1;//发红包
	                    } else {
	                        $flag = 2;//领完了
	                    }
	                    $list[] = [
	                        'usedNum' => $usedNum,
	                        'unUsedNum' => ($totalNum - $usedNum),
	                        'expiredDesc' => date('Y-m-d H:i:s', strtotime($v['dtExpired'])),
	                        'flag' => $flag,
	                        'voucherId' => $pid,
	                    ];
					}
                }
            }

            //计算被使用的红包
            if ($pageId == 1) {
                $receiveNumForParent = empty($keys) ? 0 : \Prj\Data\VouchersInterim::loopGetRecordsCount(['pid' => $keys, 'status' => 2]);//被领取
                $usedNumForParent = empty($keys) ? 0 : \Prj\Data\Vouchers::loopGetRecordsCount(['pid' => $keys, 'statusCode' => \Prj\Consts\Voucher::status_used]);//被使用
                $this->_view->assign('receiveNumForParent', $receiveNumForParent);
                $this->_view->assign('usedNumForParent', $usedNumForParent);
            }

	        $this->_view->assign('countPages', $countPages);
	        $this->_view->assign('pageId', $pageId);
            $this->_view->assign('shareVoucherTitle', \Prj\Data\Config::get('SHARE_VOUCHER_TITLE'));
            $this->_view->assign('shareVoucherDesc', \Prj\Data\Config::get('SHARE_VOUCHER_DESC'));
            $this->_view->assign('shareVoucherPic', \Prj\Data\Config::get('SHARE_VOUCHER_PIC'));
            $this->_view->assign('shareVoucherUrl', \Prj\Data\Config::get('SHARE_VOUCHER_URL'));

            $this->_view->assign('list', $list);//出现多个assign中的list字段重复，修改如下突出唯一识别
            $this->_view->assign('listgetparentvoucher', $list);
	        $this->_view->assign('lastPage', $pageId + 1);
	        
	        //新版分页规则
	        $this->_view->assign('getParentVoucherListPager',['lastPage'=>$pageId + 1,'pageId'=>$pageId,'countPage'=>$countPages]);
            return $this->returnOK();
        } catch (Exception $e) {
            return $this->returnError($e->getMessage(), $e->getCode());
        }
    }

	/**
	 * 检查是否可以设置邀请码
	 * @return bool true:可以设置，false不可以设置
	 * @throws ErrorException
	 */
	protected function _checkInviteStatus()
	{
		$this->user->load();
		$inviteByUser = $this->user->getField('inviteByUser');
		if (!empty($inviteByUser)) {
			return false;
		}

		$ymdReg = $this->user->getField('ymdReg');

		if ($ymdReg && strtotime('-15 days') <= strtotime($ymdReg)) {
			return true;
		}
		return false;
	}

    /**
     * 设置标的的上架提醒
     * @input string waresId 标的ID
     * @input int remind 1:打开提醒  0:关闭提醒
     * @output {code:200,recharge:[]}
     * @errors {code:400,msg:'miss_waresId'} 没有传参数waresId
     * @errors {code:400,msg:'error_waresId'} 错误的标的ID
     * @errors {code:400,msg:'db_error'} 数据库错误
     * @errors {code:400,msg:'error_status'} 错误的标的状态
     * @errors {code:400,msg:'config_miss'} 配置缺失
     * @errors {code:400,msg:'error_time_close'} 无法取消,不合适的时间
     * @errors {code:400,msg:'error_time_open'} 无法开启,不合适的时间
     * @errors {code:400,msg:'have_push'} 已经推送过
     */
    public function setRemindWaresAction(){
        $waresId = $this->_request->get('waresId');
        $remind = $this->_request->get('remind')-0;
        $userId = $this->user->userId;
        $this->user->load();
        $user = $this->user;
        if(empty($waresId))return $this->returnError('miss_waresId');
        if(!in_array($remind,[0,1]))return $this->returnError('error_remind');
        $wares = \Prj\Data\Wares::getCopy($waresId);
        $wares->load();
        if(!$wares->exists())return $this->returnError('error_waresId');
        try{
            $error = \Prj\Wares\Wares::setReminWares($user,$waresId,$remind);
            if($error!==null){
                return $this->returnError($error);
            }
        }catch (\ErrorException $e){
            var_log('setRemindWares#'.$userId.'#'.$e->getMessage());
            return $this->returnError('db_error');
        }
        try{
            $user->update();
        }catch (\ErrorException $e){
            var_log('setRemindWares#'.$userId.'#'.$e->getMessage());
            return $this->returnError('db_error');
        }
        return $this->returnOK($remind?'已开启提醒':'已关闭提醒');
    }
    //error_time
    //no_card
    //db_error rpc_error
    //wallet_not_null orders_not_null
    //cmd = first/second
    /**
     * 用户解绑银行卡
     * @input string cmd first:解绑第一步获取验证码/second:解绑第二步
     * @input string smscode 第一步获取的验证码
     * @errors {code:400,msg:'error_time'} 非服务时间
     * @errors {code:400,msg:'wallet_not_null'} 钱包余额不为0
     * @errors {code:400,msg:'orders_not_null'} 在投资金不为0
     * @errors {code:400,msg:'freezeAmount_not_null'} 冻结金额不为0
     * @errors {code:400,msg:'bankcard_miss'} 未知的银行卡
     * @errors {code:400,msg:'idCard_miss'} 未知的身份证
     * @errors {code:400,msg:'void_smscode'} 无效的验证码
     * @errors {code:400,msg:'rpc_error'} 网关错误
     * @errors {code:400,msg:'db_error'} 数据库错误
     */
    public function unbindCardAction(){
        $cmd = $this->_request->get('cmd');
        $unbindTime = \Prj\Data\Config::get('unbind_time');
        $this->_view->assign('unbindTime',$unbindTime);
        if(empty($unbindTime) || !is_array($unbindTime))return $this->returnError('error_time');

        $beginhi = sprintf('%04d',$unbindTime[0]);
        $endhi = sprintf('%04d',$unbindTime[1]);
        if(date('Hi')<$beginhi || date('Hi')>$endhi){
            return $this->returnError('error_time');
        }
        error_log('unbindCard#time#userId:'.$this->user->userId.'#'.$beginhi.'<'.date('Hi').'<'.$endhi);

        $userId = $this->user->userId;
        $this->user->load();
        $user = $this->user;
        $cards = \Prj\Data\BankCard::getList($userId,['statusCode'=>\Prj\Consts\BankCard::enabled]);
        var_log($cards , 'cards >>> ');
        $cardArr = current($cards);
        if(empty($cardArr))return $this->returnError('no_card');
        if(in_array($cmd,['first','second'])){
            $wallet = $user->getField('wallet');
            $holdingAssets = \Prj\Data\Investment::getHoldingAssetsByUserId($userId);
            $freezeAmount = $this->freezeAmount();
            if($wallet>0){
                $this->_view->assign('error',['wallet'=>$wallet]);
                return $this->returnError('wallet_not_null');
            }
            if($holdingAssets > 0){
                $this->_view->assign('error',['holdingAssets'=>$holdingAssets,'freezeAmount'=>$freezeAmount]);
                return $this->returnError('orders_not_null');
            }
            if($freezeAmount > 0 ){
                $this->_view->assign('error',['holdingAssets'=>$holdingAssets,'freezeAmount'=>$freezeAmount]);
                return $this->returnError('freezeAmount_not_null');
            }
            error_log('unbindCard#'.$userId.'#'.$cmd.'#>>>');
            try{
                if($cmd == 'first'){
                    error_log('unbindCard# first ...');
                    $this->unbindCardFirst();
                }elseif($cmd == 'second'){
                    error_log('unbindCard# second ...');
                    $this->data['smscode'] = $this->_request->get('smscode');
                    $this->data['cardOrderId'] = $cardArr['orderId'];
                    $this->data['idCardId'] = $cardArr['idCardSN'];
                    $this->unbindCardSecond();
                    //todo 多设备踢人...
                    error_log('unbindCard# push begin ...');
                    $pushContent = json_encode(['type' => 'kickoutByUnbind', 'msg' => '您的银行卡绑定信息已更改，请重新登录！']);
                    $this->loger->sarg1 = json_encode(['all', $userId, null, $pushContent]);
                }else{
                    return $this->returnError('db_error');
                }
            }catch (\ErrorException $e){
                return $this->returnError($e->getMessage());
            }
            return $this->returnOK();
        }else{
            $cardArr['bankCard'] = substr_replace($cardArr['bankCard'],str_pad('',strlen($cardArr['bankCard'])-8,'*'),4,-4);
            $unsetArr = ['idCardSN','realName','userId','orderId','payCorp','isDefault','cardId'];
            foreach($unsetArr as $v){
                unset($cardArr[$v]);
            }
            $this->_view->assign('cardInfo',$cardArr);
        }
        return $this->returnOK();
    }

    /**
     * 检查支付密码是否冻结
     */
    public function checkPayPwdFreezeAction()
    {
        if ($this->_isPayPwdFreeze()) {
            return $this->returnError();
        } else {
            return $this->returnOK();
        }
    }

    protected $data = [];

    protected function unbindCardFirst(){
        $userId = $this->user->userId;
        $method = 'unbindCard';
        $data = [
            $userId
        ];
        $ret = \Lib\Services\PayGWCmd::sendToPayGWCmd($method,$data);
        if($ret['code']==200){
            $serialNo = $ret['data']['serialNo'];
            if(empty($serialNo))$this->throwError('rpc_error');
            \Sooh\Base\Session\Data::getInstance()->set('unbindCard_serialNo',$serialNo);
        }else{
            $this->throwError($ret['msg']);
        }
    }

    protected function unbindCardSecond(){
        $user = $this->user;
        $userId = $user->userId;
        $cardOrderId = $this->data['cardOrderId'];
        $log = '>>>#unbindCard#userId:'.$userId.'#cardOrderId:'.$cardOrderId.'#';
        error_log($log.'beging>>>');
        $idCardId = $this->data['idCardId'];

        $serialNo = \Sooh\Base\Session\Data::getInstance()->get('unbindCard_serialNo');
        if(empty($serialNo))$this->throwError('void_smscode');
        if(empty($this->data['smscode']))$this->throwError('void_smscode');
        $method = 'unbindCardAdvance';
        $data = [
            $serialNo , $this->data['smscode']
        ];
        $ret = \Lib\Services\PayGWCmd::sendToPayGWCmd($method,$data);
        if($ret['code']==200){
            $bankcard = \Prj\Data\BankCard::getCopy($cardOrderId);
            $bankcard->load();
            if(!$bankcard->exists())$this->throwError('bankcard_miss');
            $idCard = \Prj\Data\IdCard::getCopy($idCardId);
            $idCard->load();
            if(!$idCard->exists()){
                $noId = 1;  //没有身份证
            }

            if(!$noId)$idCard->setField('statusCode',-1);

            $bankcard->setField('statusCode',\Prj\Consts\BankCard::disabled);
            $bankcard->setField('resultMsg','serialNo_'.$serialNo);
            $bankcard->setField('unBindTime',date('YmdHis'));

            $userInit = ['tradePwd'=>$user->getField('tradePwd'),'salt'=>$user->getField('salt')];
            $user->setField('tradePwd','');
            $user->setField('salt','');

            try{
                if(!$noId)$idCard->update();
                error_log($log.'idCard update >>>');
                try{
                    $bankcard->update();
                    error_log($log.'bankcard update >>>');
                    try{
                        $user->update();
                        error_log($log.'user update >>>');
                    }catch (\ErrorException $e){
                        if(!$noId)$idCard->setField('statusCode',0);
                        if(!$noId)$idCard->update();
                        $bankcard->update('statusCode',\Prj\Consts\BankCard::enabled);
                        $bankcard->update();
                        error_log('unbindCard#'.$userId.'#'.$e->getMessage());
                        $this->throwError($e->getMessage());
                    }
                }catch (\ErrorException $e){
                    if(!$noId)$idCard->setField('statusCode',0);
                    if(!$noId)$idCard->update();
                    error_log('unbindCard#'.$userId.'#'.$e->getMessage());
                    $this->throwError($e->getMessage());
                }
            }catch (\ErrorException $e){
                error_log('unbindCard#'.$userId.'#'.$e->getMessage());
                $this->throwError($e->getMessage());
            }

            error_log($log.'success >>>');
        }else{
            $this->throwError($ret['msg']);
        }
    }

    protected function throwError($message,$code = 400){
        throw new \ErrorException($message,$code);
    }

}
