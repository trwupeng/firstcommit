<?php

use Sooh\Base\Form\Item as form_def;
use Prj\Data\User as User;
use Prj\Consts\OrderStatus as OrderStatus;
use Sooh\DB\Pager;

/**
 * 用户中心
 */
class UserController extends \Prj\ManagerCtrl {

    protected $borrower = [];

    /**
     * 用户一览
     */
    public function indexAction() {
        $header = ['用户ID' => 45, '用户名' => 35, '用户等级' => 20, '注册日期' => 25, '首次下单' => 25, '最后下单' => 25, '最后登入IP' => 40, '最后登入时间' => 40, '手机号' => 35, '钱包余额' => 30];
        $pageId = $this->_request->get('pageId', 1) - 0;
        $pageSize = $this->_request->get('pageSize', 10);
        //配置搜索项
        $frm = \Sooh\Base\Form\Broker::getCopy('default')->init(\Sooh\Base\Tools::uri(), 'get', \Sooh\Base\Form\Broker::type_s);
        $frm->addItem('_nickname_lk', form_def::factory('用户名关键字', '', form_def::text))
                ->addItem('_userId_eq', form_def::factory('用户名Id', '', form_def::text))
                ->addItem('_phone_eq', form_def::factory('手机号', '', form_def::text))
                ->addItem('pageId', $pageId)
                ->addItem('pageSize', $pageSize);
        $frm->fillValues();
        $where = [];
        if ($frm->flgIsThisForm) { //submit
            $where = $frm->getWhere();
        }
        //配置分页
        $pager = new \Sooh\DB\Pager($pageSize, $this->pageSizeEnum, false);
        $pager->init(-1, $pageId);
        $pager->total = \Prj\Data\User::loopGetRecordsCount($where);
        $lastPage = $where;
        if ($pager->pageid() > 1) {
            $lastPage = \Sooh\Base\Session\Data::getInstance()->get('wp_lastPage');
        }
        $ret = \Prj\Data\User::loopGetRecordsPage(['ymdReg' => 'rsort'], $lastPage, $pager);
        if (!empty($ret)) {
            \Sooh\Base\Session\Data::getInstance()->set('wp_lastPage', $ret['lastPage']);
            $ret = $ret['records'];
        }

        $this->_view->assign('where', $where);
        $this->_view->assign('pager', $pager);
        $this->_view->assign('header', $header);
        $this->_view->assign('rs', $ret);
    }

    /**
     * 充值一览
     * By Hand
     */
    public function rechargeAction() {
        $isDownloadEXCEL = $this->_request->get('__EXCEL__');
        //配置表格
        $fieldsMapArr = array(
            'ordersId' => ['订单号', '20'],
            'userId' => ['用户ID', '20'],
            'amountFlg' => ['类型', '20'],
            'amount' => ['充值金额', '20'],
            'orderTime' => ['下单时间', '20'],
            'payTime' => ['状态变更时间', '20'],
            'orderStatus' => ['订单状态', '20'],
            'payCorp' => ['支付渠道', '20'],
            'bankAbs' => ['开户行', '20'],
            'bankCard' => ['银行卡号', '20'],
            'exp' => ['处理结果', '20'],
        );


        //配置分页
        $pageid = $this->_request->get('pageId', 1) - 0;
        $pager = new \Sooh\DB\Pager($this->_request->get('pageSize', 50), $this->pageSizeEnum, false);
        //$pager->init();
        $pager->init(-1, $pageid);

        //配置搜索项
        $frm = \Sooh\Base\Form\Broker::getCopy('default')->init(\Sooh\Base\Tools::uri(), 'get', \Sooh\Base\Form\Broker::type_s);
        $frm->addItem('_ordersId_eq', form_def::factory('订单号', '', form_def::text))
                ->addItem('_userId_eq', form_def::factory('用户ID', '', form_def::text))
                ->addItem('_orderTime_g2', form_def::factory('下单时间', '', form_def::datepicker))
                ->addItem('_orderTime_l2', form_def::factory('', '', form_def::datepicker))
                ->addItem('pageId', $pageid)
                ->addItem('pageSize', $this->pager->page_size);
        $frm->fillValues();
        if ($frm->flgIsThisForm) {
            $where = $frm->getWhere();
            if (!empty($where['orderTime]']))
                $where['orderTime]'] = date('YmdHis', strtotime($where['orderTime]']));
            if (!empty($where['orderTime[']))
                $where['orderTime['] = date('Ymd' . '235959', strtotime($where['orderTime[']));
        } else {
            $where = array();
        }
        //拉取记录
        $where['amountFlg'] = \Prj\Consts\OrderType::recharges;
        $search = \Prj\Misc\View::decodePkey($this->_request->get('where'));
        $where = array_merge($search ? $search : [], $where);
        var_log($where, '查询条件>>>>>>>>>>>>>>>>>>');
        // $rs = \Prj\Data\Recharges::loopAll($where);

        $pager->total = \Prj\Data\Recharges::loopGetRecordsCount($where);

        if ($isDownloadEXCEL) {
            $rs = \Prj\Data\Recharges::loopAll($where);
        } else {
            if ($pager->pageid() == 1) {

                //  var_log(\Sooh\DB\Broker::lastCmd(false),'sql>>>');
                // var_log('this is page 1 >>>');
                $ret = \Prj\Data\Recharges::loopGetRecordsPage(['orderTime' => 'rsort'], ['where' => $where], $pager);
                //  var_log($ret,'ret>>>>>>>>>>>>>');
            } else {
                //  var_log('this is not page '.$pager->pageid().' >>>');
                $lastPage = \Sooh\Base\Session\Data::getInstance()->get('wp_lastPage');
                // var_log($lastPage);
                $ret = \Prj\Data\Recharges::loopGetRecordsPage(['orderTime' => 'rsort'], $lastPage, $pager);
            }
            \Sooh\Base\Session\Data::getInstance()->set('wp_lastPage', $ret['lastPage']);
            $rs = $ret['records'];
        }

        //格式配置
        $tempArr = array();
        $newArr = array();
        foreach ($fieldsMapArr as $kk => $vv) {
            $header[$vv[0]] = '';
        }
        foreach ($rs as $k => $v) {
//            if ($ids == $this->_request->get('ids')) {
//                $tmp = [];
//                foreach ($ids as $vv) {
//                    $tmp[] = \Prj\Misc\View::decodePkey($vv)['ordersId'];
//                }
//                if (!in_array($v['ordersId'], $tmp)) {
//                    continue;
//                }
//            }
            foreach ($fieldsMapArr as $kk => $vv) {
                $tempArr[$kk] = $v[$kk];
                if (empty($tempArr[$kk]))
                    $tempArr[$kk] = '';
            }
            $tempArr['orderTime'] = date('Y-m-d H:i:s', strtotime($tempArr['orderTime']));
            $tempArr['payTime'] && $tempArr['payTime'] = date('Y-m-d H:i:s', strtotime($tempArr['payTime']));
            $tempArr['orderStatus'] = OrderStatus::$enum[$tempArr['orderStatus'] - 0];
            $tempArr['amountFlg'] = \Prj\Consts\OrderType::$enum[$tempArr['amountFlg']];
            $tempArr['amount'] /= 100;
            $tempArr['_pkey_val_'] = \Prj\Misc\View::encodePkey(['ordersId' => $tempArr['ordersId']]);
            $tempArr['payCorp'] = \Prj\Consts\PayGW::$payCorp[$tempArr['payCorp']] ? \Prj\Consts\PayGW::$payCorp[$tempArr['payCorp']] : $tempArr['payCorp'];
            $tempArr['bankAbs'] = \Prj\Consts\Banks::$enums[$tempArr['bankAbs']][0];
            $tempArr['bankCard'] = substr_replace($tempArr['bankCard'], '***********', 4, 11);
            $newArr[] = $tempArr;
        }
        $rs = $newArr;
        if ($isDownloadEXCEL) {
            foreach ($rs as $k => $v) {
                unset($rs[$k]['_pkey_val_']);
            }
            return $this->downEXCEL($rs, array_keys($header), null, true);
        }

        //输出
        $this->_view->assign('rs', $rs);
        $this->_view->assign('header', $header);
        $this->_view->assign('pager', $pager);
        $this->_view->assign('where', \Prj\Misc\View::encodePkey($where));
    }

    /**
     * todo 提现一览
     * By Hand
     */
    public function withdrawAction() {
        $isDownloadEXCEL = $this->_request->get('__EXCEL__');
        //配置表格
        $fieldsMapArr = array(
            'ordersId' => ['订单号', '155'],
            'userId' => ['用户ID', '120'],
            'amountFlg' => ['类型', '60'],
            'amountAbs' => ['提现金额', '60'],
            'poundage' => ['手续费', '55'],
            'withdrawYmd' => ['到账日期', '90'],
            'orderTime' => ['下单时间', '155'],
            'payTime' => ['状态变更时间', '155'],
            'orderStatus' => ['订单状态', '85'],
            'payCorp' => ['支付渠道', '60'],
            'bankAbs' => ['开户行', '60'],
            'bankCard' => ['银行卡号', '155'],
        );

        //配置分页
        $pageid = $this->_request->get('pageId', 1) - 0;
        $pager = new \Sooh\DB\Pager($this->_request->get('pageSize', 10), $this->pageSizeEnum, false);
        $pager->init(-1, $pageid);

        //配置搜索项
        $frm = \Sooh\Base\Form\Broker::getCopy('default')->init(\Sooh\Base\Tools::uri(), 'get', \Sooh\Base\Form\Broker::type_s);
        $frm->addItem('_ordersId_lk', form_def::factory('订单号', '', form_def::text))
                ->addItem('_userId_eq', form_def::factory('用户ID(精确)', '', form_def::text))
                ->addItem('_withdrawYmd_g2', form_def::factory('到账时间', '', form_def::datepicker))
                ->addItem('_withdrawYmd_l2', form_def::factory('', '', form_def::datepicker))
                ->addItem('_orderStatus_eq', form_def::factory('订单状态', '', form_def::select, [
                            '' => '全部',
                            OrderStatus::waiting => OrderStatus::$wEnum[OrderStatus::waiting],
                            OrderStatus::waitingGW => OrderStatus::$wEnum[OrderStatus::waitingGW],
                            OrderStatus::done => OrderStatus::$wEnum[OrderStatus::done],
                            OrderStatus::failed => OrderStatus::$wEnum[OrderStatus::failed],
                            OrderStatus::unusual => OrderStatus::$wEnum[OrderStatus::unusual],
                ]))
                ->addItem('pageId', $pageid)
                ->addItem('pageSize', $this->pager->page_size);
        $frm->fillValues();
        if ($frm->flgIsThisForm) {
            $where = $frm->getWhere();
        } else {
            $where = array();
        }

        //拉取记录
        $where['amountFlg'] = \Prj\Consts\OrderType::withdraw;
        if (!empty($where['withdrawYmd]']))
            $where['withdrawYmd]'] = date('Ymd', strtotime($where['withdrawYmd]']));
        if (!empty($where['withdrawYmd[']))
            $where['withdrawYmd['] = date('Ymd', strtotime($where['withdrawYmd[']));
        var_log($where, '查询条件>>>>>>>>>>>>>>>>');
        //$rs = \Prj\Data\Recharges::loopAll($where);
        $pager->total = \Prj\Data\Recharges::loopGetRecordsCount($where);
        $ids = $this->_request->get('ids');
        $idsArr = [];
        if (!empty($ids)) {
            foreach ($ids as $idk) {
                $idsArr = \Prj\Misc\View::decodePkey($idk)['id'];
            }
            if (!empty($idsArr)) {
                $where = array_merge($where, ['ordersId' => $idsArr]);
            }
        }
        var_log($where, 'where>>>>>>>>>>>>>>>>>>>>>>');
        if ($isDownloadEXCEL) {
            $rs = \Prj\Data\Recharges::loopAll($where);
        } else {
            if ($pager->pageid() == 1) {

                //  var_log(\Sooh\DB\Broker::lastCmd(false),'sql>>>');
                // var_log('this is page 1 >>>');
                $ret = \Prj\Data\Recharges::loopGetRecordsPage(['withdrawYmd' => 'rsort'], ['where' => $where], $pager);
            } else {
                //  var_log('this is not page '.$pager->pageid().' >>>');
                $lastPage = \Sooh\Base\Session\Data::getInstance()->get('tgh_lastPage');
                // var_log($lastPage);
                $ret = \Prj\Data\Recharges::loopGetRecordsPage(['withdrawYmd' => 'rsort'], $lastPage, $pager);
            }
            \Sooh\Base\Session\Data::getInstance()->set('tgh_lastPage', $ret['lastPage']);
            $rs = $ret['records'];
        }
        // var_log($rs);
        //格式配置
        $tempArr = array();
        $newArr = array();
        foreach ($fieldsMapArr as $kk => $vv) {
            $header[$vv[0]] = $vv[1];
        }
        foreach ($rs as $v) {
            //
            foreach ($fieldsMapArr as $kk => $vv) {
                $tempArr[$kk] = $v[$kk];
            }
            $tempArr['_orderStatus'] = $tempArr['orderStatus'];

            $tempArr['amountAbs'] /= 100;
            $tempArr['poundage'] /= 100;
            $tempArr['withdrawYmd'] = date('Y-m-d', strtotime($tempArr['withdrawYmd']));
            $tempArr['orderTime'] = date('Y-m-d H:i:s', strtotime($tempArr['orderTime']));
            $tempArr['payTime'] = $tempArr['payTime'] ? date('Y-m-d H:i:s', strtotime($tempArr['payTime'])) : '';
            $tempArr['payCorp'] = \Prj\Consts\PayGW::$payCorp[$tempArr['payCorp']] ? \Prj\Consts\PayGW::$payCorp[$tempArr['payCorp']] : '其它';
            $tempArr['amountFlg'] = \Prj\Consts\OrderType::$enum[$tempArr['amountFlg']];
            $tempArr['bankAbs'] = \Prj\Consts\Banks::$enums[$tempArr['bankAbs']][0];
            $tempArr['orderStatus'] = OrderStatus::$wEnum[$tempArr['orderStatus']];
            $tempArr['bankCard'] = substr_replace($tempArr['bankCard'], '***********', 4, 11);

            $newArr[] = $tempArr;
        }
        $rs = $newArr;


        if ($isDownloadEXCEL) {

            $this->downExcel($rs, array_keys($header));
        }
        // var_log(\Sooh\DB\Broker::lastCmd(),'sql>>>>>>>>>>>>>>>>>>>>');
        //  var_log($rs);
        //输出
        $this->_view->assign('rs', $rs);
        $this->_view->assign('header', $header);
        $this->_view->assign('pager', $pager);
    }

    public function withdrawReturnAction() {
        $_pkey_val_ = $this->_request->get('_pkey_val_');
        $where = \Prj\Misc\View::decodePkey($_pkey_val_);
        var_log($where, 'where >>> ');
        $withdraw = \Prj\Data\Recharges::getCopy($where['id']);
        $withdraw->load();
        if (!$withdraw->exists()) {
            return $this->returnError('不存在的提现');
        }
        if ($withdraw->getField('orderStatus') != \Prj\Consts\OrderStatus::failed) {
            return $this->returnError('非法的订单状态');
        }
        $amount = $withdraw->getField('amountAbs');
        $poundage = $withdraw->getField('poundage') - 0;
        $userId = $withdraw->getField('userId');
        $user = \Prj\Data\User::getCopy($userId);
        $user->load();
        if (!$user->exists()) {
            return $this->returnError('不存在的用户');
        }
        $retryNum = 3;
        while (!$user->lock(date('H:i:s') . '#withdrawReturn#odersId:' . $where['id']) && $retryNum >= 0) {
            if ($retryNum == 0) {
                return $this->returnError('系统正忙,请稍候重试');
            }
            sleep(2);
            $user->reload();
            $retryNum--;
        }

        $tally = \Prj\Data\WalletTally::addTally($userId, $user->getField('wallet'), $amount + $poundage, 0, $where['id'], \Prj\Consts\OrderType::manualReturn);
        $tally->setField('poundage', $poundage);
        $tally->setField('statusCode', \Prj\Consts\Tally::status_new);

        $withdraw->setField('orderStatus', \Prj\Consts\OrderStatus::unusual);
        if($poundage == 0){
            $item = new \Prj\Items\ItemGiver($userId);
            $item->add('Withdraw', 1)->give([]);
            $withNum = \Prj\Data\WithdrawNum::add($userId, 1, date('Ym'), $where['id'] . '#提现退还', $this->manager->getField('loginName'));
        }

        $user->setField('wallet', $user->getField('wallet') + $amount + $poundage);

        $oldTally = \Prj\Data\WalletTally::getCopy($userId);
        $oldTally->load();
        $oldTallyArr = $oldTally->db()->getRecord($oldTally->tbname(),'*',['orderId'=>$where['id'],'tallyType'=>\Prj\Consts\OrderType::withdraw]);
        var_log($oldTallyArr,'oldTallyArr >>> ');
        if($oldTallyArr){
            $oldTallyy = \Prj\Data\WalletTally::getCopy($oldTallyArr['tallyId']);
            $oldTallyy->load();
            $oldTallyy->setField('freeze',0);
            try{
                $oldTallyy->update();
            }catch (\ErrorException $e){
                var_log($where['id'].'#原流水解冻失败');
            }
        }

        try {
            $tally->update();
        } catch (\ErrorException $e) {
            $user->unlock();
            return $this->returnError('流水更新失败');
        }

        try {
            $withdraw->update();
        } catch (\ErrorException $e) {
            $user->unlock();
            $tally->setField('statusCode', \Prj\Consts\Tally::status_abandon);
            $tally->update();
            return $this->returnError('订单更新失败');
        }

        try {
            $withNum->update();
        } catch (\ErrorException $e) {
            $user->unlock();
            $tally->setField('statusCode', \Prj\Consts\Tally::status_abandon);
            $tally->update();
            $withdraw->setField('statusCode', \Prj\Consts\OrderStatus::failed);
            $withdraw->update();
            return $this->returnError('提现次数流水更新失败');
        }

        try {
            $user->update();
        } catch (\ErrorException $e) {
            $user->unlock();
            $tally->setField('statusCode', \Prj\Consts\Tally::status_abandon);
            $tally->update();
            $withdraw->setField('statusCode', \Prj\Consts\OrderStatus::failed);
            $withdraw->update();
            $withNum->setField('statusCode', \Prj\Consts\Tally::status_abandon);
            $withNum->update();
            return $this->returnError('用户更新失败');
        }

        return $this->returnOK('金额已经退还到用户账户');
    }

    /**
     * 提现申请
     * By Hand
     */
    public function withdrawingAction() {
        $isDownloadEXCEL = $this->_request->get('__EXCEL__');
        //配置表格
        $fieldsMapArr = array(
            'ordersId' => ['订单号', '20'],
            'userId' => ['用户ID', '20'],
            'amountFlg' => ['类型', '20'],
            'amountAbs' => ['提现金额(元)', '20'],
            'poundage' => ['手续费', '20'],
            'withdrawYmd' => ['到账日期', '20'],
            'orderTime' => ['下单时间', '20'],
            'payTime' => ['状态变更时间', '20'],
            'batchId' => ['批次号', '20'],
            'orderStatus' => ['订单状态', '20'],
            'payCorp' => ['支付渠道', '20'],
            'bankAbs' => ['开户行', '20'],
            'bankCard' => ['银行卡号', '20'],
            'exp' => ['处理结果', '20'],
        );


        //配置分页
        $pageid = $this->_request->get('pageId', 1) - 0;
        //$pager  = new \Sooh\DB\Pager($this->_request->get('pageSize', 10), $this->pageSizeEnum, false);
        $pager = new \Sooh\DB\Pager(10, $this->pageSizeEnum, false);
        $pager->page_size = $this->_request->get('pageSize', current($this->pageSizeEnum));
        $pager->init(-1, $pageid);

        //配置搜索项
        $frm = \Sooh\Base\Form\Broker::getCopy('default')->init(\Sooh\Base\Tools::uri(), 'get', \Sooh\Base\Form\Broker::type_s);
        $frm->addItem('_ordersId_lk', form_def::factory('订单号', '', form_def::text))
                ->addItem('_userId_eq', form_def::factory('用户ID(精确)', '', form_def::text))
                ->addItem('_withdrawYmd_g2', form_def::factory('到账时间', '', form_def::datepicker))
                ->addItem('_withdrawYmd_l2', form_def::factory('', '', form_def::datepicker))
                /* ->addItem('_orderStatus_eq', form_def::factory('订单状态', '', form_def::select,[
                  ''=>'全部',
                  OrderStatus::waiting=>OrderStatus::$enum[OrderStatus::waiting],
                  OrderStatus::waitingGW=>OrderStatus::$enum[OrderStatus::waitingGW],
                  ])) */
                ->addItem('pageId', $pageid)
                ->addItem('pageSize', $this->pager->page_size);
        $frm->fillValues();
        if ($frm->flgIsThisForm) {
            $where = $frm->getWhere();
            if (!empty($where['withdrawYmd]']))
                $where['withdrawYmd]'] = date('Ymd', strtotime($where['withdrawYmd]']));
            if (!empty($where['withdrawYmd[']))
                $where['withdrawYmd['] = date('Ymd', strtotime($where['withdrawYmd[']));
            if (!empty($where['withdrawYmd='])) {
                $where['withdrawYmd='] = date('Ymd', strtotime($where['withdrawYmd=']));
            }
            //  var_log($where,'>>>>>>>>>>>>>>');
        } else {
            $where = array();
        }

        //拉取记录
        $where['amountFlg'] = \Prj\Consts\OrderType::withdraw;
        $where['orderStatus'] = [OrderStatus::waiting, OrderStatus::waitingGW];

        $pager->total = \Prj\Data\Recharges::loopGetRecordsCount($where);


        $ids = $this->_request->get('ids');
        $idsArr = [];
        if (!empty($ids)) {
            foreach ($ids as $idk) {
                $idsArr = \Prj\Misc\View::decodePkey($idk)['id'];
            }
            if (!empty($idsArr)) {
                $where = array_merge($where, ['ordersId' => $idsArr]);
            }
        }

        //$ordersId=\Prj\Data\Recharges::loopFindRecordsByFields($where,null,'ordersId');

        if ($isDownloadEXCEL) {
            $rs = \Prj\Data\Recharges::loopAll($where);
            // $rs=$Recharges->db()->getRecords($Recharges->tbname(),implode(',', array_keys($fieldsMapArr)),$where,'rsort withdrawYmd');
        } else {
            if ($pager->pageid() == 1) {

                //  var_log(\Sooh\DB\Broker::lastCmd(false),'sql>>>');
                // var_log('this is page 1 >>>');
                $ret = \Prj\Data\Recharges::loopGetRecordsPage(['withdrawYmd' => 'rsort'], ['where' => $where], $pager);
            } else {
                //  var_log('this is not page '.$pager->pageid().' >>>');
                $lastPage = \Sooh\Base\Session\Data::getInstance()->get('wp_lastPage');
                // var_log($lastPage);
                $ret = \Prj\Data\Recharges::loopGetRecordsPage(['withdrawYmd' => 'rsort'], $lastPage, $pager);
            }
            \Sooh\Base\Session\Data::getInstance()->set('wp_lastPage', $ret['lastPage']);
            $rs = $ret['records'];
        }

        // $rs = \Prj\Data\Recharges::loopAll($where);
        //格式配置
        $tempArr = array();
        $newArr = array();
        foreach ($fieldsMapArr as $kk => $vv) {
            $header[$vv[0]] = '';
        }
        foreach ($rs as $v) {
            //
            foreach ($fieldsMapArr as $kk => $vv) {
                $tempArr[$kk] = $v[$kk];
            }
            $tempArr['amountAbs'] /= 100;
            $tempArr['poundage'] /= 100;
            $tempArr['withdrawYmd'] = date('Y-m-d', strtotime($tempArr['withdrawYmd']));
            $tempArr['orderTime'] = date('Y-m-d H:i:s', strtotime($tempArr['orderTime']));
            $tempArr['payTime'] = $tempArr['payTime'] ? date('Y-m-d H:i:s', strtotime($tempArr['payTime'])) : '';
            $tempArr['payCorp'] = \Prj\Consts\PayGW::$payCorp[$tempArr['payCorp']] ? \Prj\Consts\PayGW::$payCorp[$tempArr['payCorp']] : '其它';
            $tempArr['bankAbs'] = \Prj\Consts\Banks::$enums[$tempArr['bankAbs']][0];
            $tempArr['batchId'] = $tempArr['batchId'] ? $tempArr['batchId'] : '';
            $tempArr['bankCard'] = substr_replace($tempArr['bankCard'], '**********', 4, 10);

            $tempArr['amountFlg'] = \Prj\Consts\OrderType::$enum[$tempArr['amountFlg']];
            $tempArr['orderStatus'] = OrderStatus::$wEnum[$tempArr['orderStatus']];

            $newArr[] = $tempArr;
        }
        $rs = $newArr;

        if ($isDownloadEXCEL) {
//             foreach($rs as $k=>$v){
//                 unset($rs[$k]['_pkey_val_']);
//             }
            return $this->downEXCEL($rs, array_keys($header));
        }
        //输出

        $this->_view->assign('rs', $rs);
        $this->_view->assign('header', $header);
        $this->_view->assign('pager', $pager);
    }

    /**
     * 提现申请发送至支付网关
     */
    public function sendWithdrawingAction() {
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $batchId = $this->_createBatchId(\Prj\Consts\OrderType::withdraw);
        $loger = \Sooh\Base\Log\Data::getInstance();
        $this->manager->load();
        $loger->userId = $this->manager->getField('loginName');
        $new = [];
        $orderIds = $this->_request->get('ids');
        // if(empty($orderIds))return $this->returnError('ID参数不能为空!');
        if (empty($orderIds))
            return $this->returnError(\Prj\Lang\Broker::getMsg('user.no_orderId'));
        $loger->sarg3 = $orderIds;
        $orderIds = explode(',', $orderIds);
        if (!empty($orderIds)) {
            foreach ($orderIds as $k => $v) {
                $withdraw[$k] = \Prj\Data\Recharges::getCopy($v);
                $withdraw[$k]->load();
                if (!$withdraw[$k]->exists())
                    return $this->returnError($v . '不存在于资料库中');
                if ($withdraw[$k]->getField('orderStatus') != OrderStatus::waiting) {
                    return $this->returnError('错误的订单状态:' . $v);
                } else {
                    $withdraw[$k]->setField('batchId', $batchId);
                    $new[] = [
                        'ordersId' => $v,
                        'userId' => $withdraw[$k]->getField('userId'),
                        'amount' => $withdraw[$k]->getField('amountAbs'),
                        'poundage' => $withdraw[$k]->getField('poundage'),
                    ];
                }
            }
        }
        $list = json_encode(['list' => $new]);
        // var_log($list,'list>>>>>>>>>>>');
        //todo 通知支付网关
        if (\Sooh\Base\Ini::getInstance()->get('noGW')) {
            $rpc = self::getRpcDefault('PayGW'); //debug
        } else {
            $rpc = \Sooh\Base\Rpc\Broker::factory('PayGW');
        }
        $sys = \Lib\Services\PayGW::getInstance($rpc);

        try {
            $ret = $sys->sendWithdraw($batchId, $list);
            $this->_view->assign('retAll', ['ret' => 'ok', 'got' => $ret]);
        } catch (\Sooh\Base\ErrException $e) {
            $this->loger->error('send order to gw failed where addorder ' . $e->getMessage());
            $code = $e->getCode();
            if ($code == 400) {
                $this->_view->assign('retAll', ['ret' => $e->getMessage(), 'got' => $e->customData]);
                return $this->returnError($e->getMessage());
            } elseif ($code == 500) {
                $this->_view->assign('retAll', ['ret' => $e->getMessage(), 'got' => $e->customData]);
                return $this->returnError($e->getMessage());
            }
            //  return $this->returnError('gw_error');
            return $this->returnError(\Prj\Lang\Broker::getMsg('system.gw_error'));
        }
        if (empty($ret['payCorp']))
            return $this->returnError('no_payCorp');
        $error = '';
        foreach ($withdraw as $k => $v) {
            try {
                $v->setField('payCorp', $ret['payCorp']);
                $v->setField('orderStatus', OrderStatus::waitingGW);
                $v->setField('payTime', \Sooh\Base\Time::getInstance()->ymdhis());
                $v->update();
            } catch (\ErrorException $e) {
                $error .= ($v->getField('ordersId') . ':' . $e->getMessage() . ',');
            }
        }
        $loger->sarg2 = $error;
        if (!empty($error)) {
            return $this->returnError('致命错误：' . $error);
        } else {
            return $this->returnOK('提现申请已经提交！');
        }
    }

    /**
     * 批次号生成
     */
    protected function _createBatchId($type) {
        return time() . rand(1000, 9999) . $type;
    }

///////////////////////////////////////////////测试代码//////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * todo 绑卡记录
     * By Hand
     */
    public function bankcardAction() {
        $isDownloadEXCEL = $this->_request->get('__EXCEL__');
        $fieldsMapArr = array(
            'orderId' => ['订单号', '20'],
            'realName' => ['姓名', '20'],
            'userId' => ['用户ID', '20'],
            //'payCorp'    => ['支付通道', '20'],
            'bankId' => ['银行', '20'],
            'bankCard' => ['卡号', '20'],
            //'isDefault'  => ['是否默认', '20'],
            'statusCode' => ['状态', '20'],
            'timeCreate' => ['创建时间', '20'],
            //'resultMsg'  => ['验证结果', '20'],
            'resultTime' => ['验证时间', '20'],
            //'idCardType' => ['证件类型', '20'],
            'idCardSN' => ['身份证号码', '20'],
            'phone' => ['手机号', '20'],
        );

        //配置分页
        $pageid = $this->_request->get('pageId', 1) - 0;
        $pager = new \Sooh\DB\Pager($this->_request->get('pageSize', 50), $this->pageSizeEnum, false);
        $pager->init(-1, $pageid);

        //配置搜索项
        $frm = \Sooh\Base\Form\Broker::getCopy('default')->init(\Sooh\Base\Tools::uri(), 'get', \Sooh\Base\Form\Broker::type_s);
        $frm->addItem('_userId_eq', form_def::factory('用户ID(精确)', '', form_def::text))
                ->addItem('_phone_eq', form_def::factory('手机号(精确)', '', form_def::text))
                ->addItem('pageId', $pageid)
                ->addItem('pageSize', $this->pager->page_size);
        $frm->fillValues();
        if ($frm->flgIsThisForm) {
            $where = $frm->getWhere();
        } else {
            $where = array();
        }

        $search = \Prj\Misc\View::decodePkey($this->_request->get('where'));
        $where = array_merge($search ? $search : [], $where);
        // var_log($where,'查询条件>>>>>>>>>>>>>>>>>>');
        //拉取记录
        $pager->total = \Prj\Data\BankCard::loopGetRecordsCount($where);

        if ($isDownloadEXCEL) {
            $rs = \Prj\Data\BankCard::loopAll($where);
        } else {
            if ($pager->pageid() == 1) {

                //  var_log(\Sooh\DB\Broker::lastCmd(false),'sql>>>');
                // var_log('this is page 1 >>>');
                $ret = \Prj\Data\BankCard::loopGetRecordsPage(['timeCreate' => 'rsort'], ['where' => $where], $pager);
            } else {
                //  var_log('this is not page '.$pager->pageid().' >>>');
                $lastPage = \Sooh\Base\Session\Data::getInstance()->get('wp_lastPage');
                // var_log($lastPage);
                $ret = \Prj\Data\BankCard::loopGetRecordsPage(['timeCreate' => 'rsort'], $lastPage, $pager);
            }
            \Sooh\Base\Session\Data::getInstance()->set('wp_lastPage', $ret['lastPage']);
            $rs = $ret['records'];
        }

        //表格配置
        $tempArr = array();
        $newArr = array();
        foreach ($fieldsMapArr as $kk => $vv) {
            $header[$vv[0]] = '';
        }
        foreach ($rs as $k => $v) {
            //todo 数据处理
//            if ($ids == $this->_request->get('ids')) {
//                $tmp = [];
//                foreach ($ids as $vv) {
//                    $tmp[] = \Prj\Misc\View::decodePkey($vv)['ordersId'];
//                }
//                if (!in_array($v['orderId'], $tmp)) {
//                    continue;
//                }
//            }

            $v['bankCard'] = substr_replace($v['bankCard'], '**********', 6, 10);
            $v['idCardSN'] = substr_replace($v['idCardSN'], '********', 6, 8);
            $v['phone'] = substr_replace($v['phone'], '****', 3, 4);

            $v['bankId'] = \Prj\Consts\Banks::$enums[$v['bankId']][0];
            $v['isDefault'] = $v['isDefault'] ? '是' : '否';
            $v['statusCode'] = \Prj\Consts\BankCard::$enum[$v['statusCode']];
            $v['timeCreate'] = \Prj\Misc\View::fmtYmd($v['timeCreate'], 'time');
            $v['idCardType'] = \Prj\Consts\IdCardType::$enums[$v['idCardType']];
            $v['resultTime'] && $v['resultTime'] = date('Y-m-d H:i:s', strtotime($v['resultTime']));
            foreach ($fieldsMapArr as $kk => $vv) {
                if (empty($v[$kk]))
                    $v[$kk] = '';
                $tempArr[$kk] = $v[$kk];
            }
            $newArr[] = $tempArr;
        }
        $rs = $newArr;

        // var_log($rs);
        if ($isDownloadEXCEL) {
            foreach ($rs as $k => $v) {
                unset($rs[$k]['_pkey_val_']);
            }

            return $this->downEXCEL($rs, array_keys($header), null, true);
        }
        //输出
        $this->_view->assign('rs', $rs);
        $this->_view->assign('header', $header);
        $this->_view->assign('pager', $pager);
        $this->_view->assign('where', \Prj\Misc\View::encodePkey($where));
    }

    /**
     * 用户详情
     */
    public function detailAction() {
        $pkey = \Prj\Misc\View::decodePkey($this->_request->get('_pkey_val_'));
        $userId = $pkey['userId'];
        if (empty($userId)) {
            //return $this->returnError('no_userId');
            return $this->returnError(\Prj\Lang\Broker::getMsg('user.user_notfound'));
        }

        $formView = \Sooh\Base\Form\Broker::getCopy('default')->init(\Sooh\Base\Tools::uri('nullAction'), 'get', \Sooh\Base\Form\Broker::type_u);
        $formView->addItem('userId', form_def::factory('用户ID', '', form_def::constval))
                ->addItem('vipLevel', form_def::factory('用户等级', '', form_def::constval))
                ->addItem('ymdReg', form_def::factory('注册日期', '', form_def::constval))
                ->addItem('ymdFirstBuy', form_def::factory('首次购买日期', '', form_def::constval))
                ->addItem('ymdLastBuy', form_def::factory('最后购买日期', '', form_def::constval))
                ->addItem('ymdFirstCharge', form_def::factory('首次充值日期', '', form_def::constval))
                ->addItem('ymdBindcard', form_def::factory('首次绑卡日期', '', form_def::constval))
                ->addItem('ipReg', form_def::factory('注册IP', '', form_def::constval))
                ->addItem('ipLast', form_def::factory('最后登录IP', '', form_def::constval))
                ->addItem('dtLast', form_def::factory('最后登录日期', '', form_def::constval))
                ->addItem('phone', form_def::factory('手机号', '', form_def::constval))
                ->addItem('nickname', form_def::factory('昵称', '', form_def::constval))
                ->addItem('wallet', form_def::factory('钱包余额', '', form_def::constval))
                ->addItem('redPacket', form_def::factory('红包余额', '', form_def::constval))
                ->addItem('points', form_def::factory('积分', '', form_def::constval))
                ->addItem('copartnetId', form_def::factory('渠道ID', '', form_def::constval))
                ->addItem('contractId', form_def::factory('渠道协议ID', '', form_def::constval))
                ->addItem('protocol', form_def::factory('协议版本号', '', form_def::constval))
                ->addItem('inviteByUser', form_def::factory('邀请人', '', form_def::constval))
                ->addItem('inviteByParent', form_def::factory('父级邀请人', '', form_def::constval))
                ->addItem('inviteByRoot', form_def::factory('顶级邀请人', '', form_def::constval))
                ->addItem('myInviteCode', form_def::factory('我的邀请码', '', form_def::constval))
                ->addItem('checkinBook', form_def::factory('签到记录', '', \Sooh\Base\Form\Item::constval))
                ->addItem('idCard', form_def::factory('身份证号', '', \Sooh\Base\Form\Item::constval));
        $formView->fillValues();

        $user = User::getCopy($userId);
        $pkey = $user->load();
        if ($pkey === null) {
            //return $this->returnError('记录找不到');
            return $this->returnError(\Prj\Lang\Broker::getMsg('excode.record_unfound'));
        } else {
            $ks = array_keys($formView->items);
            foreach ($ks as $k) {
                if ($user->exists($k)) {
                    if ($k == 'phone') {
                        $formView->item($k)->value = substr($user->getField($k), 0, 3) . '****' . substr($user->getField($k), -4);
                    } elseif ($k == 'ymdReg' || $k == 'ymdFirstBuy' || $k == 'ymdLastBuy' || $k == 'ymdFirstCharge' || $k == 'ymdBindcard') {
                        if ($user->getField($k) == 0) {
                            $formView->item($k)->value = $user->getField($k);
                        } else {
                            $formView->item($k)->value = \Prj\Misc\View::fmtYmd($user->getField($k));
                        }
                    } elseif ($k == 'wallet') {
                        $formView->item($k)->value = sprintf('%.2f', $user->getField($k) / 100);
                    } elseif ($k == 'redPacket') {
                        $formView->item($k)->value = sprintf('%.2f', $user->getField($k) / 100);
                    } elseif ($k == 'dtLast') {
                        $formView->item($k)->value = \Prj\Misc\View::fmtYmd($user->getField($k), 'time');
                    } elseif ($k == 'checkinBook') {
                        $_value = $user->getField($k);
                        $formView->item($k)->value = empty($_value) ? '未签到' : (is_array($user->getField($k)) ? json_encode($user->getField($k)) : $user->getField($k));
                    } elseif ($k == 'idCard') {
                        $_idCard = $user->getField($k);
                        $formView->item($k)->value = empty($_idCard) ? '未绑卡' : substr_replace($_idCard, '********', strlen($_idCard) - 10, 8);
                    } else {
                        $formView->item($k)->value = $user->getField($k);
                    }
                }
            }
        }
    }

    /**
     * 绑卡未购买
     * @throws \Sooh\Base\ErrorException
     */
    public function bindCardNotInvestAction() {
        $fieldsMapArr = [
            'userId' => ['用户ID', '45'],
            'nickname' => ['用户名', '35'],
            'vipLevel' => ['vip等级', '20'],
            'ymdReg' => ['注册日期', '25'],
            'ipLast' => ['最后登入IP', '40'],
            'dtLast' => ['最后登入时间', '40'],
            'phone' => ['手机号', '35'],
            'wallet' => ['钱包余额', '30'],
            'points' => ['积分', '30'],
        ];

        $pageId = $this->_request->get('pageId', 1) - 0;
        $pageSize = $this->_request->get('pageSize', 10);
        $ids = $this->_request->get('ids');
        $isDownloadExcel = $this->_request->get('__EXCEL__') == 1;
        $search = $this->_request->get('where', []);

        $frm = \Sooh\Base\Form\Broker::getCopy('default')->init(\Sooh\Base\Tools::uri(), 'get', \Sooh\Base\Form\Broker::type_s);
        $frm->addItem('_phone_eq', form_def::factory('手机号', '', form_def::text))
                ->addItem('_ymdBindcard_g2', form_def::factory('绑卡时间大于', '', form_def::datepicker))
                ->addItem('_ymdBindcard_l2', form_def::factory('绑卡时间小于', '', form_def::datepicker))
                ->addItem('pageId', $pageId)
                ->addItem('pageSize', $pageSize);
        $frm->fillValues();
        if ($frm->flgIsThisForm) {
            $where = $frm->getWhere();
        } else {
            $where = [];
        }


        $pager = new \Sooh\DB\Pager($pageSize, $this->pageSizeEnum, false);
        $pager->init(-1, $pageId);

        $keys = is_array($ids) ? $ids : explode(',', $ids);
        if (!empty($ids)) {
            foreach ($keys as $k => $v) {
                $keys[$k] = \Prj\Misc\View::decodePkey($v)['userId'];
            }
            $where = array('userId' => $keys);
        }

        foreach ($where as $k => $v) {
            if (strpos($k, 'ymdBindcard') !== false) {
                $where[$k] = date('Ymd', strtotime($v));
            }
        }
        $where = array_merge($where, ['ymdFirstBuy' => '0', 'ymdBindcard!' => '0']);

        //全表导出
        if ($isDownloadExcel) {
            $where = array_merge($where, $search);
            $user = new User();
            $records = $user->db()->getRecords($user->tbname(), implode(',', array_keys($fieldsMapArr)), $where, 'rsort ymdBindcard');
        } else {
            $records = User::paged($pager, $where, null, implode(',', array_keys($fieldsMapArr)));
        }

        $header = [];
        foreach ($fieldsMapArr as $k => $v) {
            $header[$v[0]] = $v[1];
        }

        $temp = [];
        foreach ($records as $v) {
            foreach ($fieldsMapArr as $kk => $vv) {
                if ($kk == 'wallet') {
                    $temp[$kk] = sprintf('%.2f', $v[$kk] / 100);
                } elseif ($kk == 'phone') {
                    $temp[$kk] = substr($v[$kk], 0, 3) . '****' . substr($v[$kk], -4);
                } elseif ($kk == 'ymdReg') {
                    $temp[$kk] = \Prj\Misc\View::fmtYmd($v[$kk]);
                } elseif ($kk == 'dtLast') {
                    $temp[$kk] = \Prj\Misc\View::fmtYmd($v[$kk], 'time');
                } else {
                    $temp[$kk] = $v[$kk];
                }

                if (!$isDownloadExcel) {
                    $temp['_pkey_val_'] = \Prj\Misc\View::encodePkey(['userId' => $v['userId']]);
                }
            }
            $new[] = $temp;
        }
        $records = $new;

        if ($isDownloadExcel) {
            return $this->downExcel($records, array_keys($header));
        } else {
            $this->_view->assign('where', $where);
            $this->_view->assign('pager', $pager);
            $this->_view->assign('header', $header);
            $this->_view->assign('rs', $records);
        }
    }

    /**
     * 真实信息
     */
    public function honestInfoAction() {
        $fieldsArr = [
            'userId' => ['用户ID', '45'],
            'ymdReg' => ['注册日期', '25'],
            'dtLast' => ['最后登入时间', '45'],
            'phone' => ['手机号', '35'],
            'nickname' => ['姓名', '45'],
            'idCard' => ['身份证号', '65'],
        ];

        $pageId = $this->_request->get('pageId', 1) - 0;
        $pageSize = $this->_request->get('pageSize', 50);
        $ids = $this->_request->get('ids');
        $isDownloadExcel = $this->_request->get('__EXCEL__') == 1;
        //$search          = $this->_request->get('where', []);
        //配置搜索项
        $frm = \Sooh\Base\Form\Broker::getCopy('default')->init(\Sooh\Base\Tools::uri(), 'get', \Sooh\Base\Form\Broker::type_s);
        $frm->addItem('_userId_eq', form_def::factory('用户ID', '', form_def::text))
                ->addItem('_phone_eq', form_def::factory('手机号', '', form_def::text))
                ->addItem('_nickname_lk', form_def::factory('昵称', '', form_def::text))
                ->addItem('_idCard_eq', form_def::factory('身份证', '', form_def::text))
                ->addItem('pageId', $pageId)
                ->addItem('pageSize', $pageSize);
        $frm->fillValues();

        if ($frm->flgIsThisForm) {
            $where = $frm->getWhere();
        } else {
            $where = [];
        }

        $pager = new \Sooh\DB\Pager($this->_request->get('pageSize', 50), $this->pageSizeEnum, false);
        //$pager = new \Sooh\DB\Pager(10,$this->pageSizeEnum, false);
        $pager->init(-1, $pageId);

        $keys = is_array($ids) ? $ids : explode(',', $ids);
        if (!empty($ids)) {
            foreach ($keys as $k => $v) {
                $keys[$k] = \Prj\Misc\View::decodePkey($v)['userId'];
            }
            $where = ['userId' => $keys];
        }

// 		if ($isDownloadExcel) {
// 			$where = array_merge($where, $search);
// 			$user = new User();
// 			$records = $user->db()->getRecords($user->tbname(), implode(',', array_keys($fieldsArr)), $where, 'rsort ymdReg');
// 		} else {
// 			$records = User::paged($pager, $where, null, implode(',', array_keys($fieldsArr)));
// 		}
        //var_log($records,'##############');

        $pager->total = \Prj\Data\User::loopGetRecordsCount($where);
        if ($isDownloadEXCEL) {
            $records = \Prj\Data\User::loopAll($where);
        } else {
            if ($pager->pageid() == 1) {

                //  var_log(\Sooh\DB\Broker::lastCmd(false),'sql>>>');
                // var_log('this is page 1 >>>');
                $ret = \Prj\Data\User::loopGetRecordsPage(['dtLast' => 'rsort'], ['where' => $where], $pager);
            } else {
                //  var_log('this is not page '.$pager->pageid().' >>>');
                $lastPage = \Sooh\Base\Session\Data::getInstance()->get('wp_lastPage');
                // var_log($lastPage);
                $ret = \Prj\Data\User::loopGetRecordsPage(['dtLast' => 'rsort'], $lastPage, $pager);
            }
            \Sooh\Base\Session\Data::getInstance()->set('wp_lastPage', $ret['lastPage']);
            $records = $ret['records'];
        }

        $header = [];
        foreach ($fieldsArr as $k => $v) {
            $header[$v[0]] = $v[1];
        }

        foreach ($records as $v) {
            foreach ($fieldsArr as $kk => $vv) {

                if ($kk == 'phone') {
                    $temp[$kk] = substr($v[$kk], 0, 3) . '*****' . substr($v[$kk], -3);
                } elseif (($kk == 'idCard')) {

                    $temp[$kk] = empty($v[$kk]) ? '' : substr($v[$kk], 0, 6) . '*****' . substr($v[$kk], -3);
                } elseif ($kk == 'ymdReg') {
                    $temp[$kk] = \Prj\Misc\View::fmtYmd($v[$kk]);
                } elseif ($kk == 'dtLast') {
                    $temp[$kk] = \Prj\Misc\View::fmtYmd($v[$kk], 'time');
                } else {
                    $temp[$kk] = $v[$kk];
                }

                if (!$isDownloadExcel) {
                    $temp['_pkey_val_'] = \Prj\Misc\View::encodePkey(['userId' => $v['userId']]);
                }
            }
            $new[] = $temp;
        }
        $records = $new;
        var_log($header, '##############');
        if ($isDownloadExcel) {
            return $this->downExcel($records, array_keys($header));
        } else {
            $this->_view->assign('where', $where);
            $this->_view->assign('pager', $pager);
            $this->_view->assign('header', $header);
            $this->_view->assign('rs', $records);
        }
    }

    /**
     * 消息管理
     */
    public function mineMsgAction() {
        $fieldsArr = [
            'msgId' => ['消息ID', '30'],
            'title' => ['标题', '50'],
            'sendId' => ['发送者', '45'],
            'receiverId' => ['接收者', '45'],
            'createTime' => ['创建时间', '45'],
            'status' => ['状态', '20'],
            'type' => ['类型', '20'],
        ];

        $pageId = $this->_request->get('pageId', 1) - 0;
        $pageSize = $this->_request->get('pageSize', 10) - 0;
        $ids = $this->_request->get('ids');
        $isDownloadExcel = $this->_request->get('__EXCEL__') == 1;
        $search = $this->_request->get('where', []);
        $optStatusArr = [
            \Prj\Consts\Message::status_unread => '未读',
            \Prj\Consts\Message::status_read => '已读',
            \Prj\Consts\Message::status_abandon => '删除',
        ];
        $optTypeArr = [
            \Prj\Consts\Message::type_bid => '投标',
            \Prj\Consts\Message::type_contractIssued => '合同下发',
            \Prj\Consts\Message::type_repayment => '项目回款',
            \Prj\Consts\Message::type_withdrawal => '提现',
            \Prj\Consts\Message::type_redPacket => '红包',
            \Prj\Consts\Message::type_rebate => '返利',
            \Prj\Consts\Message::type_notice => '通知',
        ];


        $frm = \Sooh\Base\Form\Broker::getCopy('default')->init(\Sooh\Base\Tools::uri(), 'get', Sooh\Base\Form\Broker::type_s);
        $frm->addItem('_title_lk', form_def::factory('标题', '', form_def::text))
                ->addItem('_sendId_eq', form_def::factory('发送者ID', '', form_def::text))
                ->addItem('_receiverId_eq', form_def::factory('接收者ID', '', form_def::text))
                ->addItem('_status_eq', form_def::factory('状态', '', form_def::select)->initMore(new \Sooh\Base\Form\Options($optStatusArr, '不限')))
                ->addItem('_type_eq', form_def::factory('类型', '', form_def::select)->initMore(new \Sooh\Base\Form\Options($optTypeArr, '不限')))
                ->addItem('pageId', $pageId)
                ->addItem('pageSize', $pageSize);
        $frm->fillValues();
        if ($frm->flgIsThisForm) {
            $where = $frm->getWhere();
        } else {
            $where = [];
        }

        $keys = is_array($ids) ? $ids : explode(',', $ids);
        if (!empty($ids)) {
            foreach ($keys as $k => $v) {
                $keys[$k] = \Prj\Misc\View::decodePkey($v)['msgId'];
            }
            $where = ['msgId' => $keys];
        }

        $pager = new \Sooh\DB\Pager($pageSize, $this->pageSizeEnum, false);
        $pager->init(\Prj\Data\Message::loopGetRecordsCount($where), $pageId);
        // var_log(\Sooh\DB\Broker::lastCmd(),'sql>>>>>>>>>>>>>>>>');

        var_log($pager);

        if ($isDownloadExcel) {
            $where = array_merge($where, $search);
            //$message=new \Prj\Data\Message();
            //$records=$message->db()->getRecords($message->tbname(),implode(',', array_keys($fieldsArr),$where,'rsort createTime'));
            $records = \Prj\Data\Message::paged($pager, $where, 'rsort createTime', implode(',', array_keys($fieldsArr)));
        } else {
            $records = \Prj\Data\Message::paged($pager, $where, 'rsort createTime', implode(',', array_keys($fieldsArr)));
            // $records = \Prj\Data\Message::paged($pager, $where, 'rsort createTime', implode(',', array_keys($fieldsArr)));
        }

        $header = [];
        foreach ($fieldsArr as $k => $v) {
            $header[$v[0]] = $v[1];
        }

        $temp = [];
//var_log($records, 'records>>>>>');
        foreach ($records as $v) {
            foreach ($fieldsArr as $kk => $vv) {
                if ($kk == 'status') {
                    $temp[$kk] = $optStatusArr[$v[$kk]];
                } elseif ($kk == 'type') {
                    $temp[$kk] = $optTypeArr[$v[$kk]];
                } elseif ($kk == 'title') {
                    if (strlen($v[$kk]) > 20) {
                        $temp[$kk] = substr($v[$kk], 0, 18) . '...';
                    } else {
                        $temp[$kk] = $v[$kk];
                    }
                } elseif ($kk == 'createTime') {
                    $temp[$kk] = \Prj\Misc\View::fmtYmd($v[$kk], 'time');
                } elseif ($kk == 'sendId' && $v[$kk] == 0) {
                    $temp[$kk] = '平台';
                } else {
                    $temp[$kk] = $v[$kk];
                }

                if (!$isDownloadExcel) {
                    $temp['_pkey_val_'] = \Prj\Misc\View::encodePkey(['msgId' => $v['msgId']]);
                }
            }
            $new[] = $temp;
        }
        $records = $new;

        if ($isDownloadExcel) {
            return $this->downExcel($records, array_keys($header));
        } else {
            $this->_view->assign('where', $where);
            $this->_view->assign('pager', $pager);
            $this->_view->assign('header', $header);
            $this->_view->assign('rs', $records);
        }
    }

    public function sendWithdrawNumAction() {
        $where = \Prj\Misc\View::decodePkey($this->_request->get('_pkey_val_'));
        $type = $this->_request->get('_type');
        $frm = \Sooh\Base\Form\Broker::getCopy('default')
                ->init(\Sooh\Base\Tools::uri(), 'get', empty($where) ? \Sooh\Base\Form\Broker::type_c : \Sooh\Base\Form\Broker::type_u);

        $frm->addItem('phone', form_def::factory('用户手机号', '12312341234', form_def::text, [], ['data-rule' => 'required,length[~15],digits']))
                //->addItem('month', form_def::factory('月份', '2016-3-23', form_def::datepicker, [], ['data-rule' => 'required']))
                ->addItem('num', form_def::factory('次数', '1', form_def::text, [], ['data-rule' => 'required,digits']))
                ->addItem('exp', form_def::factory('说明', '', form_def::mulit, [], ['data-rule' => '']))
                ->addItem('_type', $type)
                ->addItem('_pkey_val_', $this->_request->get('_pkey_val_'));

        //todo 构造表单数据

        $frm->fillValues();
        //表单提交
        if ($frm->flgIsThisForm) {
            //审核通过
            if ($type == 'check') {
                
            }
            $where = \Prj\Misc\View::decodePkey($this->_request->get('_pkey_val_'));
            if (!empty($where)) {
                $frm->switchType(\Sooh\Base\Form\Broker::type_u);
            } else {
                $frm->switchType(\Sooh\Base\Form\Broker::type_c);
            }
            $op = '';
            try {
                $fields = $frm->getFields();
                try {
                    //todo 字段过滤
                    if (empty($fields['num'])) {
                        return $this->returnError('次数不能为0');
                    }
                    $rs = \Prj\Data\User::loopFindRecords(['phone' => $fields['phone']])[0];
                    if (empty($rs)) {
                        return $this->returnError('无效的手机号');
                    } else {
                        $fields['userId'] = $rs['userId'];
                        $user = \Prj\Data\User::getCopy($fields['userId']);
                        $user->load();
                        if (!$user->exists()) {
                            return $this->returnError(\Prj\Lang\Broker::getMsg('user.user_notfound'));
                        }
                    }
                    $fields['month'] = date('Ym', strtotime($fields['month']));
                } catch (\ErrorException $e) {
                    return $this->returnError($e->getMessage());
                }

                if ($frm->type() == \Sooh\Base\Form\Broker::type_c) { //add
                    $op = "新增";
                    //todo 插入数据库
                    /*
                      if(!$user->lock(__CLASS__.' '.__METHOD__)){
                      if(!$user->lock(__CLASS__.' '.__METHOD__)){
                      return $this->returnError(\Prj\Lang\Broker::getMsg('check.user_lock_fail'));
                      }
                      }
                     */

                    $withNum = \Prj\Data\WithdrawNum::add($fields['userId'], $fields['num'], date('Ym'), $fields['exp'], $this->manager->getField('loginName'));
                    if (empty($withNum)) {
                        return $this->returnError(\Prj\Lang\Broker::getMsg('check.sn_add_fail'));
                    } else {
                        $withLeft = $user->getField('withdrawLeft');
                        $item = new \Prj\Items\ItemGiver($fields['userId']);
                        $item->add('Withdraw', $fields['num']);
                        $item->give();
                        try {
                            $withNum->update();
                        } catch (\ErrorException $e) {
                            return $this->returnError('数据库错误' . $e->getMessage());
                        }
                        try {
                            $user->update();
                        } catch (\ErrorException $e) {
                            $withNum->setField('statusCode', \Prj\Consts\Tally::status_abandon);
                            $withNum->update();
                            return $this->returnError('数据库错误' . $e->getMessage());
                        }

                        $item->onUserUpdated();
                        return $this->returnOK('操作成功');
                    }
                } else { // update
                    $op = '更新';
                    //todo 更新数据库
                }
            } catch (\ErrorException $e) {
                return $this->returnError($op . '失败：冲突，相关记录已经存在？');
            }

            $this->closeAndReloadPage($this->tabname('index'));
            $this->returnOK($op . '成功');
            return;
        }

        if ($frm->type() == \Sooh\Base\Form\Broker::type_u) { //update show
            //todo 字段展示 设置item的value
            $arr = [];
            foreach ($frm->items as $k => $v) {
                if (array_key_exists($k, $arr))
                    $frm->items[$k]->value = $arr[$k];
            }
        }

        //var_dump($fields);
        //die();
        $this->_view->assign('FormOp', $op = '添加标的');
        $this->_view->assign('type', $type);
        $this->_view->assign('_pkey_val_', $this->_request->get('_pkey_val_'));
    }

    public function retryRechargeAction() {
        $where = \Prj\Misc\View::decodePkey($this->_request->get('_pkey_val_'));
        $type = $this->_request->get('_type');
        $frm = \Sooh\Base\Form\Broker::getCopy('default')
                ->init(\Sooh\Base\Tools::uri(), 'get', empty($where) ? \Sooh\Base\Form\Broker::type_c : \Sooh\Base\Form\Broker::type_u);

        $frm->addItem('ordersId', form_def::factory('流水号', '', form_def::text, [], ['data-rule' => 'required,length[~19]']))
                ->addItem('_type', $type)
                ->addItem('_pkey_val_', $this->_request->get('_pkey_val_'));

//todo 构造表单数据

        $frm->fillValues();
//表单提交
        if ($frm->flgIsThisForm) {
            //审核通过
            if ($type == 'check') {
                
            }
            $where = \Prj\Misc\View::decodePkey($this->_request->get('_pkey_val_'));
            if (!empty($where)) {
                $frm->switchType(\Sooh\Base\Form\Broker::type_u);
            } else {
                $frm->switchType(\Sooh\Base\Form\Broker::type_c);
            }
            $op = '';
            try {
                $fields = $frm->getFields();
                try {
                    //todo 字段过滤
                } catch (\ErrorException $e) {
                    return $this->returnError($e->getMessage());
                }

                if ($frm->type() == \Sooh\Base\Form\Broker::type_c) { //add
                    $op = "补充值记录";
                    //todo 插入数据库
                    /*
                      if(strlen($fields['uid'])==11){
                      $user = \Prj\Data\User::getByPhone($fields['uid']);
                      }else{
                      $user = \Prj\Data\User::getCopy($fields['uid']);
                      }
                      if(empty($user)){return $this->returnError('不存在的用户');}
                      $user->load();
                      if(!$user->exists()){return $this->returnError('不存在的用户');}
                     */
                    $recharge = \Prj\Data\Recharges::getCopy($fields['ordersId']);
                    $recharge->load();
                    if (!$recharge->exists()) {
                        return $this->returnError('不存在的流水号');
                    }
                    if ($recharge->getField('orderStatus') == \Prj\Consts\OrderStatus::done)
                        return $this->returnError('重复的操作');
                    $userId = $recharge->getField('userId');
                    $amount = $recharge->getField('amountAbs');
                    $user = \Prj\Data\User::getCopy($userId);
                    $user->load();
                    if (!$user->exists())
                        return $this->returnError('不存在的用户');
                    $retry = 3;
                    while ($retry >= 0 && !$user->lock(date('H:i:s') . '#retryRecharge#ordersId:' . $fields['ordersId'])) {
                        if ($retry == 0)
                            return $this->returnError('系统正忙,请稍后重试');
                        $user->reload();
                        $retry--;
                    }
                    $user->reload();
                    $walletInit = $user->getField('wallet');

                    \Prj\Misc\OrdersVar::$introForUser = '补充值订单_' . date('YmdHis');
                    \Prj\Misc\OrdersVar::$introForCoder = 'retryRecharge_' . date('YmdHis');

                    $tally = \Prj\Data\WalletTally::addTally($userId, $walletInit, $amount, 0, $fields['ordersId'], \Prj\Consts\OrderType::recharges);
                    $tally->setField('statusCode', \Prj\Consts\WalletTally::status_new);

                    $rechargeOrderStatus = $recharge->getField('orderStatus');
                    $rechargeExp = $recharge->getField('exp');
                    $recharge->setField('orderStatus', \Prj\Consts\OrderStatus::done);
                    $recharge->setField('exp', date('YmdHis') . '_retryRecharge');
                    $recharge->setField('payTime', date('YmdHis'));

                    $user->setField('wallet', $user->getField('wallet') + $amount);

                    try {
                        $tally->update();
                    } catch (\ErrorException $e) {
                        $user->unlock();
                        return $this->returnError('数据库错误' . $e->getMessage());
                    }

                    try {
                        $recharge->update();
                    } catch (\ErrorException $e) {
                        $user->unlock();
                        $tally->updStatus(\Prj\Consts\Tally::status_abandon)->update();
                        return $this->returnError('数据库错误' . $e->getMessage());
                    }

                    try {
                        $user->update();
                    } catch (\ErrorException $e) {
                        $tally->updStatus(\Prj\Consts\Tally::status_abandon)->update();
                        $recharge->setField('orderStatus', $rechargeOrderStatus);
                        $recharge->setField('exp', $rechargeExp);
                        $recharge->setField('payTime', 0);
                        $recharge->update();
                        $user->unlock();
                        return $this->returnError('数据库错误' . $e->getMessage());
                    }
                } else { // update
                    $op = '更新';
                    //todo 更新数据库
                    return $this->returnError('系统错误');
                }
            } catch (\ErrorException $e) {
                return $this->returnError($op . '失败：冲突，相关记录已经存在？');
            }

            $this->closeAndReloadPage($this->tabname('index'));
            $this->returnOK($op . '成功');
            return;
        }

        if ($frm->type() == \Sooh\Base\Form\Broker::type_u) { //update show
            //todo 字段展示 设置item的value
            /*
              $??? = \Prj\Data\???::getCopy($where['id']);
              $???->load();
              $arr = $???->dump();
              foreach($frm->items as $k=>$v){
              if(array_key_exists($k,$arr))$frm->items[$k]->value = $arr[$k];
              }
             */
        }

//var_dump($fields);
//die();
        $this->_view->assign('FormOp', $op = '添加商品');
        $this->_view->assign('type', $type);
        $this->_view->assign('_pkey_val_', $this->_request->get('_pkey_val_'));
    }

    public function cancelAmountAction() {
        $where = \Prj\Misc\View::decodePkey($this->_request->get('_pkey_val_'));
        $type = $this->_request->get('_type');
        $frm = \Sooh\Base\Form\Broker::getCopy('default')
                ->init(\Sooh\Base\Tools::uri(), 'get', empty($where) ? \Sooh\Base\Form\Broker::type_c : \Sooh\Base\Form\Broker::type_u);

        $frm->addItem('uid', form_def::factory('用户ID/手机号', '', form_def::text, [], ['data-rule' => 'required,length[~19]']))
                ->addItem('amount', form_def::factory('金额(元)', '', form_def::text, [], ['data-rule' => 'required,length[~19],number']))
                ->addItem('exp', form_def::factory('备注', '', form_def::mulit, [], ['data-rule' => 'required']))
                ->addItem('_type', $type)
                ->addItem('_pkey_val_', $this->_request->get('_pkey_val_'));

//todo 构造表单数据

        $frm->fillValues();
//表单提交
        if ($frm->flgIsThisForm) {
            //审核通过
            if ($type == 'check') {
                
            }
            $where = \Prj\Misc\View::decodePkey($this->_request->get('_pkey_val_'));
            if (!empty($where)) {
                $frm->switchType(\Sooh\Base\Form\Broker::type_u);
            } else {
                $frm->switchType(\Sooh\Base\Form\Broker::type_c);
            }
            $op = '';
            try {
                $fields = $frm->getFields();
                try {
                    //todo 字段过滤
                } catch (\ErrorException $e) {
                    return $this->returnError($e->getMessage());
                }

                if ($frm->type() == \Sooh\Base\Form\Broker::type_c) { //add
                    $op = "手动扣款";
                    //todo 插入数据库
                    if (strlen($fields['uid']) == 11) {
                        $user = \Prj\Data\User::getByPhone($fields['uid']);
                        if (empty($user))
                            return $this->returnError('不存在的用户');
                    }else {
                        $user = \Prj\Data\User::getCopy($fields['uid']);
                    }
                    $user->load();
                    if (!$user->exists())
                        return $this->returnError('不存在的用户');
                    $userId = $user->userId;
                    $amount = -1 * abs($fields['amount']) * 100;

                    $retry = 3;
                    while ($retry >= 0 && !$user->lock(date('H:i:s') . '#cancelAmount#userId:' . $userId)) {
                        if ($retry == 0)
                            return $this->returnError('系统正忙,请稍后重试');
                        $user->reload();
                        $retry--;
                    }
                    $user->reload();
                    $walletInit = $user->getField('wallet');

                    \Prj\Misc\OrdersVar::$introForUser = $fields['exp'];
                    \Prj\Misc\OrdersVar::$introForCoder = 'cancelAmount_' . date('YmdHis');

                    $tally = \Prj\Data\WalletTally::addTally($userId, $walletInit, $amount, 0, 0, \Prj\Consts\OrderType::manualCancel);
                    $tally->setField('statusCode', \Prj\Consts\WalletTally::status_new);

                    $user->setField('wallet', $user->getField('wallet') + $amount);

                    try {
                        $tally->update();
                    } catch (\ErrorException $e) {
                        $user->unlock();
                        return $this->returnError('数据库错误' . $e->getMessage());
                    }

                    try {
                        $user->update();
                    } catch (\ErrorException $e) {
                        $tally->updStatus(\Prj\Consts\Tally::status_abandon)->update();
                        $user->unlock();
                        return $this->returnError('数据库错误' . $e->getMessage());
                    }
                } else { // update
                    $op = '更新';
                    //todo 更新数据库
                    return $this->returnError('系统错误');
                }
            } catch (\ErrorException $e) {
                return $this->returnError($op . '失败：冲突，相关记录已经存在？');
            }

            $this->closeAndReloadPage($this->tabname('index'));
            $this->returnOK($op . '成功');
            return;
        }

        if ($frm->type() == \Sooh\Base\Form\Broker::type_u) { //update show
            //todo 字段展示 设置item的value
            /*
              $??? = \Prj\Data\???::getCopy($where['id']);
              $???->load();
              $arr = $???->dump();
              foreach($frm->items as $k=>$v){
              if(array_key_exists($k,$arr))$frm->items[$k]->value = $arr[$k];
              }
             */
        }

//var_dump($fields);
//die();
        $this->_view->assign('FormOp', $op = '添加商品');
        $this->_view->assign('type', $type);
        $this->_view->assign('_pkey_val_', $this->_request->get('_pkey_val_'));
    }

    /**
     * 往中间账户打款
     */
    public function transIndexAction() {
        $isDownloadEXCEL = $this->_request->get('__EXCEL__');
        //配置表格
        $fieldsMapArr = array(
            'transId' => ['流水号', '20'],
            'fromUid' => ['来源账户', '20'],
            'toUid' => ['目标账户', '20'],
            'statusCode' => ['状态', '20'],
            'exp' => ['备注', '20'],
            'updateYmd' => ['更新时间', '20'],
        );

        //配置分页
        $pageid = $this->_request->get('pageId', 1) - 0;
        $pager = new \Sooh\DB\Pager($this->_request->get('pageSize', 10), $this->pageSizeEnum, false);
        $pager->init(-1, $pageid);

        //配置搜索项
        $frm = \Sooh\Base\Form\Broker::getCopy('default')->init(\Sooh\Base\Tools::uri(), 'get', \Sooh\Base\Form\Broker::type_s);
        $frm->addItem('_userId_eq', form_def::factory('用户ID', '', form_def::text))
                ->addItem('pageId', $pageid)
                ->addItem('pageSize', $this->pager->page_size);
        $frm->fillValues();
        if ($frm->flgIsThisForm) {
            $where = $frm->getWhere();
        } else {
            $where = array();
        }
        //合并表单的查询条件
        $search = \Prj\Misc\View::decodePkey($this->_request->get('where'));
        $where = array_merge($search ? $search : [], $where);
        //拉取记录
        var_log($where, '查询条件>>>>>>>>>>>>>>>>>>');
        $order = 'rsort updateYmd';
        $rs = \Prj\Data\Trans::paged($pager, $where, $order);

        //格式配置
        $tempArr = array();
        $newArr = array();
        foreach ($fieldsMapArr as $kk => $vv) {
            $header[$vv[0]] = '';
        }
        foreach ($rs as $k => $v) {
            //选中项打印
//            if($ids == $this->_request->get('ids')){
//                $tmp = [];
//                foreach($ids as $vv){
//                    $tmp[] = \Prj\Misc\View::decodePkey($vv)['ordersId'];
//                }
//                //todo 主键匹配
//                if(!in_array($v['orderId'],$tmp)){
//                    continue;
//                }
//            }

            foreach ($fieldsMapArr as $kk => $vv) {
                $tempArr[$kk] = $v[$kk];
            }
            //todo 数据格式化
            //===
            $newArr[] = $tempArr;
        }
        $rs = $newArr;
        if ($isDownloadEXCEL)
            return $this->downEXCEL($rs, array_keys($header), null, true);
        //输出
        $this->_view->assign('rs', $rs);
        $this->_view->assign('header', $header);
        $this->_view->assign('pager', $pager);
        $this->_view->assign('where', \Prj\Misc\View::encodePkey($where));
    }

    public function rebateAction() {
        $isDownloadEXCEL = $this->_request->get('__EXCEL__');
        //配置表格
        $fieldsMapArr = array(
            'rebateId' => ['返利号', '20'],
            'userId' => ['用户ID', '20'],
            'childNickname' => ['受邀用户名', '20'],
            'investId' => ['订单号', '20'],
            'amount' => ['返利金额', '20'],
            'sumAmount' => ['累计金额', '20'],
            'statusCode' => ['状态', '20'],
            'updateYmd' => ['更新时间', '20'],
        );

        //配置分页
        $pageid = $this->_request->get('pageId', 1) - 0;
        $pager = new \Sooh\DB\Pager($this->_request->get('pageSize', 50), $this->pageSizeEnum, false);
        $pager->init(-1, $pageid);

        //配置搜索项
        $frm = \Sooh\Base\Form\Broker::getCopy('default')->init(\Sooh\Base\Tools::uri(), 'get', \Sooh\Base\Form\Broker::type_s);
        $frm->addItem('_userId_eq', form_def::factory('用户ID', '', form_def::text))
                ->addItem('pageId', $pageid)
                ->addItem('pageSize', $this->pager->page_size);
        $frm->fillValues();
        if ($frm->flgIsThisForm) {
            $where = $frm->getWhere();
        } else {
            $where = array();
        }
        //合并表单的查询条件
        $search = \Prj\Misc\View::decodePkey($this->_request->get('where'));
        $where = array_merge($search ? $search : [], $where);
        //拉取记录
        $where['statusCode>'] = \Prj\Consts\OrderStatus::created;
        var_log($where, '查询条件>>>>>>>>>>>>>>>>>>');
        $order = '';
        $rs = \Prj\Data\Rebate::paged($pager, $where, $order);

        //格式配置
        $tempArr = array();
        $newArr = array();
        foreach ($fieldsMapArr as $kk => $vv) {
            $header[$vv[0]] = '';
        }
        foreach ($rs as $k => $v) {
            //选中项打印
//            if($ids == $this->_request->get('ids')){
//                $tmp = [];
//                foreach($ids as $vv){
//                    $tmp[] = \Prj\Misc\View::decodePkey($vv)['ordersId'];
//                }
//                //todo 主键匹配
//                if(!in_array($v['orderId'],$tmp)){
//                    continue;
//                }
//            }

            foreach ($fieldsMapArr as $kk => $vv) {
                $tempArr[$kk] = $v[$kk];
            }
            //todo 数据格式化
            isset($tempArr['statusCode']) && $tempArr['statusCode'] = \Prj\Consts\OrderStatus::$enum[$tempArr['statusCode']];
            isset($tempArr['updateYmd']) && $tempArr['updateYmd'] = date('Y-m-d H:i:s', strtotime($tempArr['updateYmd']));
            isset($tempArr['amount']) && $tempArr['amount']/=100;
            isset($tempArr['sumAmount']) && $tempArr['sumAmount']/=100;
            //===
            $newArr[] = $tempArr;
        }
        $rs = $newArr;
        if ($isDownloadEXCEL)
            return $this->downEXCEL($rs, array_keys($header), null, true);
        //输出
        $this->_view->assign('rs', $rs);
        $this->_view->assign('header', $header);
        $this->_view->assign('pager', $pager);
        $this->_view->assign('where', \Prj\Misc\View::encodePkey($where));
    }

    public function rebateEditAction() {
        $this->closeAndReloadPage();
        $where = \Prj\Misc\View::decodePkey($this->_request->get('_pkey_val_'));
        $type = $this->_request->get('_type');
        $frm = \Sooh\Base\Form\Broker::getCopy('default')
                ->init(\Sooh\Base\Tools::uri(), 'get', empty($where) ? \Sooh\Base\Form\Broker::type_c : \Sooh\Base\Form\Broker::type_u);

        $frm->addItem('fromUid', form_def::factory('来源账户', '', form_def::text, [], ['data-rule' => 'required,digits']))
                ->addItem('toUid', form_def::factory('目标账户', '', form_def::select, ['0' => '中间账户'], ['data-rule' => 'required']))
                ->addItem('amount', form_def::factory('金额(元)', '', form_def::text, [], ['data-rule' => 'required,number']))
                ->addItem('_type', $type)
                ->addItem('_pkey_val_', $this->_request->get('_pkey_val_'));

        //todo 构造表单数据

        $frm->fillValues();
        //表单提交
        if ($frm->flgIsThisForm) {
            //审核通过
            if ($type == 'check') {
                
            }
            $where = \Prj\Misc\View::decodePkey($this->_request->get('_pkey_val_'));
            if (!empty($where)) {
                $frm->switchType(\Sooh\Base\Form\Broker::type_u);
            } else {
                $frm->switchType(\Sooh\Base\Form\Broker::type_c);
            }
            $op = '';
            try {
                $fields = $frm->getFields();
                $amount = round($fields['amount'] * 100);
                try {
                    //todo 字段过滤
                } catch (\ErrorException $e) {
                    return $this->returnError($e->getMessage());
                }

                if ($frm->type() == \Sooh\Base\Form\Broker::type_c) { //add
                    $op = "新增";
                    //todo 插入数据库
                    $trans = \Prj\Data\Trans::add($fields);
                    if (!$trans) {
                        return $this->returnError('系统错误:生成记录失败');
                    }
                    //调用网关
                    $rpc = \Sooh\Base\Ini::getInstance()->get('noGW') ? self::getRpcDefault('PayGW') : \Sooh\Base\Rpc\Broker::factory('PayGW');
                    $sys = \Lib\Services\PayGW::getInstance($rpc);
                    $sn = $trans->getPKey()['transId'];
                    try {
                        $trans->setField('statusCode', \Prj\Consts\PayGW::abondon);
                        $trans->update();
                        $trans->reload();
                    } catch (\ErrorException $e) {
                        return $this->returnError($e->getMessage());
                    }

                    try {
                        $data = [
                            $sn,
                            $amount,
                            $fields['fromUid'],
                            $fields['toUid'],
                        ];
                        //var_log($data,'发给网关的参数>>>>>>>>>>>>>>>>>>>>>');
                        $ret = call_user_func_array([$sys, 'manualTrans'], $data);
                    } catch (\Sooh\Base\ErrException $e) {
                        $trans->setField('exp', "网关错误:" . $e->getMessage());
                        $trans->setField('statusCode', \Prj\Consts\PayGW::failed);
                        try {
                            $trans->update();
                        } catch (\ErrorException $e) {
                            var_log("[error]满标转账网关错误#sn:" . $sn . " waresId:" . $fields['waresId'] . " error:" . $e->getMessage());
                            return $this->returnError($e->getMessage());
                        }
                        return $this->returnError("网关错误:" . $e->getMessage());
                    }

                    if (in_array($ret['status'], [\Prj\Consts\PayGW::accept, \Prj\Consts\PayGW::success])) {
                        $trans->setField('exp', "网关已受理");
                        $trans->setField('statusCode', \Prj\Consts\PayGW::accept);
                    } else {
                        $trans->setField('exp', "处理失败:" . $ret['reason']);
                        $trans->setField('statusCode', \Prj\Consts\PayGW::failed);
                    }

                    try {
                        $trans->update();
                    } catch (\ErrorException $e) {
                        var_log("[error]满标转账网关错误#sn:" . $sn . " waresId:" . $fields['waresId'] . " error:" . $e->getMessage());
                        return $this->returnError($e->getMessage());
                    }
                    return $this->returnOK('操作完成');
                } else { // update
                    $op = '更新';
                    //todo 更新数据库
                }
            } catch (\ErrorException $e) {
                return $this->returnError($op . '失败：冲突，相关记录已经存在？');
            }

            $this->closeAndReloadPage($this->tabname('index'));
            $this->returnOK($op . '成功');
            return;
        }

        if ($frm->type() == \Sooh\Base\Form\Broker::type_u) { //update show
            //todo 字段展示 设置item的value
            /*
              $??? = \Prj\Data\???::getCopy($where['id']);
              $???->load();
              $arr = $???->dump();
              foreach($frm->items as $k=>$v){
              if(array_key_exists($k,$arr))$frm->items[$k]->value = $arr[$k];
              }
             */
        }

        //var_dump($fields);
        //die();
        $this->_view->assign('FormOp', $op = '添加商品');
        $this->_view->assign('type', $type);
        $this->_view->assign('_pkey_val_', $this->_request->get('_pkey_val_'));
    }

    /* public function phoneAction() {
      $phone = $this->_request->get('phone');
      $frm = \Sooh\Base\Form\Broker::getCopy('default')->init(\Sooh\Base\Tools::uri(), 'get', \Sooh\Base\Form\Broker::type_c);
      $frm->addItem('userId', form_def::factory('用户id', '', form_def::text));
      $this->_view->assign('FormOp', $op = '查找');
      $frm->fillValues();
      $frm->flgIsThisForm;
      $frm->getWhere();
      $fields = $frm->getFields();
      $userid = $fields['userId'];
      if (!empty($phone)) {
      $userId = \Prj\Data\User::loopFindRecordsByFields([
      'phone' => $phone
      ], 'userId');
      }
      $records = [];

      foreach ($userId as $r) {
      $userid = $r['userId'];
      $records = $userid;
      }
      $this->_view->assign('rs', $records);
      $this->_view->assign('phone', $phone);
      } */
}
