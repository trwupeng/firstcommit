<?php

/**
 * Description of User2
 *
 * @author wu.chen
 */
class User2Controller extends UserController {

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
    public function myInvestAction() {
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
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
            $this->_view->assign('info', $info); //出现多个assign中的info字段重复，修改如下突出唯一识别
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
        foreach ($where['shelfId'] as $key => $v) {
            if ($v == 2000) {
                $where['shelfId'][$key] = 4000;
            } elseif ($v == 3000) {
                $where['shelfId'][$key] = 5000;
            }
        }

        $pager = new \Sooh\DB\Pager($pageSize);
        $pager->init(-1, $pageId);
        $userId = $this->user->userId;
        if ($ordersId)
            $where['ordersId'] = $ordersId;
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
                    if (!empty($v['returnPlan'])) {
                        
                    }
                    $returnPlan = \Prj\ReturnPlan\Std01\ReturnPlan::createPlan($v['returnPlan']);
                    $rs[$k]['dtStart'] = $returnPlan->dtStart ? date('Ymd', $returnPlan->dtStart) : '';
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
                    if ($rs[$k]['waresStatusCode'] == \Prj\Consts\Wares::status_go && $rs[$k]['orderStatus'] == \Prj\Consts\OrderStatus::waiting) {
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
                    $rs[$k]['borrowerName'] = $arr['introDisplay']['b']['name'];
                    //$rs[$k]['introDisplay'] = $introDisplay;
                    $rs[$k]['webUrl'] = \Sooh\Base\Tools::uri(['waresId' => $v['waresId']], 'newDec', 'financing');
                    $rs[$k]['images'] = \Prj\WaresTpl\Std02\Viewer::getImgList($introDisplay);
                } else {
                    unset($rs[$k]['returnPlan']);
                }
                $rs[$k]['licence'] && $rs[$k]['licence'] = json_decode($rs[$k]['licence'], true);
            }

            $this->returnOK();
        }
        $this->_view->assign('list', $rs); //出现多个assign中的list字段重复，修改如下突出唯一识别
        $this->_view->assign('listmyinvestto', $rs);
        $this->_view->assign('pager', $pager->toArray());

        //新版分页
        $this->_view->assign('myInvestPager', $pager->toArray());
    }

    /**
     * By Hand
     */
    protected function _accountInfo($user) {
        $user->load();
        $userId = $user->getField('userId');
        $ret = [];
        $ret['redPacket'] = $this->user->getField('redPacket');
        $ret['wallet'] = $this->user->getField('wallet');
        $ret['interestTotal'] = $this->user->getField('interestTotal'); //累计收益
        $ret['nickname'] = $this->user->getField('nickname');
        $ret['ymdFirstBuy'] = $this->user->getField('ymdFirstBuy');
        $_checkinBook = $this->user->getField('checkinBook');
        if (isset($_checkinBook['ymd']) && !empty($_checkinBook['ymd'])) {
            $ret['isTodayCheckin'] = $_checkinBook['ymd'] == \Sooh\Base\Time::getInstance()->YmdFull ? 1 : 0;
        } else {
            $ret['isTodayCheckin'] = 0;
        }

        $redPacketDtLast = $this->user->getField('redPacketDtLast');
        $voucherSys = \Prj\Data\Vouchers::getCopy($userId);
        $lastRedPacket = $voucherSys->db()->getRecord($voucherSys->tbname(), 'timeCreate', ['userId' => $userId, 'voucherType' => \Prj\Consts\Voucher::type_real, 'statusCode]' => 0], 'rsort timeCreate');

        $ret['hasNewRedPacket'] = $lastRedPacket['timeCreate'] > $redPacketDtLast ? 1 : 0; //是否有未领取的红包
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
        $ret['totalAssets'] = $ret['wallet'] + $holdingAssets + $ret['freezeAmount']; //资产总额 = 钱包+回款中的订单+冻结资产  //$ret['redPacket']
        $ret['holdingAssets'] = $holdingAssets; //持有资产 = 回款中的订单
        //未读消息
        $ret['msgCounts'] = \Lib\Services\Message::getInstance()->getCount(['receiverId' => $userId, 'status' => \Prj\Consts\Message::status_unread]);

        return $ret;
    }

    /**
     * 从还款计划里提取利息 本金
     * 性能很差
     */
    protected function _amountByReturnPlan($isPay = 0) {
        $amountWait = 0; //待收本金
        $interestWait = 0; //待收利息
        $running = \Prj\Consts\OrderStatus::$running;
        $statusAll = $running;
        $where['userId'] = $this->user->userId;
        $where['orderStatus'] = $statusAll;
        $all = \Prj\Data\Investment::loopAll($where); //获取所有订单
        foreach ($all as $v) {
            if ($v['shelfId'] == \Prj\Consts\Wares::shelf_house_review) {
                $allStatic[] = $v;
            } elseif ($v['shelfId'] == \Prj\Consts\Wares::shelf_car_review) {
                $allFloat[] = $v;
            }
        }
        $returnPlan = $this->_getReturnPlan($allStatic, $isPay);
        if (!empty($returnPlan)) {
            foreach ($returnPlan as $v) {
                if ($isPay) {
                    $interestWait += ($v['realPayInterest']);  //todo $v['realPayinterestSub']+    不计算贴息
                    $amountWait += ($v['realPayAmount']);
                } else {
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
                if ($isPay) {
                    $interestWait += ($v['realPayInterest']);  //todo $v['realPayinterestSub']+ 不计算贴息
                    $amountWait += ($v['realPayAmount']);
                } else {
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

}
