<?php
use Sooh\Base\Form\Item as form_def;
use Prj\Consts\Wares;

/**
 * 回款
 */
class PaybackController extends \Prj\ManagerCtrl
{
    public function indexAction()
    {
        // \Sooh\Base\Ini::getInstance()->viewRenderType('json');

        $pageid = $this->_request->get('pageId', 1) - 0;
        $isDownloadEXCEL = $this->_request->get('__EXCEL__');
        $pager = new \Sooh\DB\Pager(10, $this->pageSizeEnum, false);
        $pager->page_size = $this->_request->get('pageSize', 50);
        //search
        $frm = \Sooh\Base\Form\Broker::getCopy('default')
            ->init(\Sooh\Base\Tools::uri(), 'get', \Sooh\Base\Form\Broker::type_s);
        $frm->addItem('_waresname_lk', form_def::factory('名称关键字', '', form_def::text))
            ->addItem('_interestStartType_eq', form_def::factory('起息方式', '', form_def::select, (\Prj\Consts\InterestStart::$enum + ['' => '全部'])))
            ->addItem('_returnType_eq', form_def::factory('还款方式', '', form_def::select, (\Prj\Consts\ReturnType::$enum + ['' => '全部'])))
            ->addItem('_shelfId_eq', form_def::factory('类型', '', form_def::select, (\Prj\Consts\Wares::$shilfIdName + ['' => '全部'])))
            // ->addItem('_statusCode_eq', form_def::factory('订单状态', '', form_def::select,(\Prj\Consts\Wares::$enum+[''=>'全部'])))
            ->addItem('pageid', $pageid)
            ->addItem('pagesize', $pager->page_size);
        $frm->fillValues();
        if ($frm->flgIsThisForm) { //submit
            $where = $frm->getWhere();
        } else {
            $where = array();
        }

        if ($ids = $this->_request->get('ids')) {
            if (!is_array($ids)) {
                $ids = explode(',', $ids);
            }
            foreach ($ids as $v) {
                $arr[] = \Prj\Misc\View::decodePkey($v)['waresId'];
            }
            $where['waresId'] = $arr;
        }

        if ($type == "check") //审核页面
        {
            $where['statusCode'] = \Prj\Consts\Wares::status_new;
        }
        $ware = \Prj\Data\Wares::getCopy('');
        $pager->init(-1, $pageid);
        $fieldsMap = array(
            'waresId' => array('标的编号', 135),
            'waresName' => array('标的名称', 130),
            'tags' => array('标签', 35),
            'interestStartType' => array('起息方式/还款方式', 115),
            'returnType' => array('还款方式', 70),
            'deadLine' => array('期限', 35),
            'dlUnit' => array('单位', 35),
            //'yieldStatic' => strstr(array('利率', 35),'.',2),
            'yieldStatic' => array('利率%', 52),
            'yieldStaticAdd' => array('活动加息%', 70),
            'shelfId' => array('类型', 35),
            'amount' => array('总额(元)', 60),
            'remain' => array('余额(元)', 60),
            'timeStartPlan' => array('计划上架时间/计划还款时间', 220),
            'timeEndReal' => array('实际满标时间/满标转账时间', 160),
            'payYmd' => array('满标转账时间', 'auto'),
            'ymdPayPlan' => array('计划还款时间', 'auto'),
            'statusCode' => array('状态/备注', 165),
            'payStatus' => array('网关状态/订单号', 140),
            'paySn' => array('网关订单号', 80),
            'exp' => array('备注', 'auto'),
        );
        
        foreach ($fieldsMap as $k => $v) {
            $headers[$v[0]] = $v[1];
            $fields[] = $k;
            if ($k == 'dlUnit') unset($headers[$v[0]]);
        }
        
        unset($headers['还款方式']);
        unset($headers['计划还款时间']);
        unset($headers['满标转账时间']);
        unset($headers['备注']);
        unset($headers['网关订单号']);
        
        $where['statusCode]'] = \Prj\Consts\Wares::status_go;
        
        $pager->total=\Prj\Data\Wares::loopGetRecordsCount($where);
        $total=$pager->total;
      
        if ($isDownloadEXCEL == 1) //不分页打印
        {
            $search = $this->_request->get('where') ? $this->_request->get('where') : array();
            $where = array_merge($where, $search);
            $records = $ware->db()->getRecords($ware->tbname(), $fields, $where, 'rsort timeStartPlan');
        } else {
            $records = $ware->db()->getRecords($ware->tbname(), $fields, $where, 'rsort timeStartPlan', $pager->page_size, $pager->rsFrom());
        }
        //records 处理
        $new = array();
        $statusName = $this->statusHtml;
        if (!empty($records)) {
            foreach ($records as $k => $v) {
                $_pkey_val_ = \Prj\Misc\View::encodePkey(array('waresId' => $v['waresId']));
                if (!$isDownloadEXCEL) $v['_pkey_val_'] = $_pkey_val_;
                $v['yieldStatic']=sprintf("%.2f", $v['yieldStatic']*100);
                $v['yieldStaticAdd']=sprintf("%.2f", $v['yieldStaticAdd']*100);
                $v['shelfId'] = \Prj\Consts\Wares::$shilfIdName[$v['shelfId']];
                $v['deadLine'] .= $v['dlUnit'];
                $v['amount']/=100;
                $v['remain']/=100;
                $v['interestStartType'] = \Prj\Consts\InterestStart::$enum[$v['interestStartType']];
                $v['returnType'] = \Prj\Consts\ReturnType::$enum[$v['returnType']];
                $v['timeStartPlan'] = \Prj\Misc\View::fmtYmd($v['timeStartPlan'],'time');
                if(!empty($v['payYmd'])){
                $v['payYmd'] = \Prj\Misc\View::fmtYmd($v['payYmd'],'time');
                }
                $v['ymdPayPlan'] = \Prj\Misc\View::fmtYmd($v['ymdPayPlan']);
                $v['statusCode'] = \Prj\Consts\Wares::$enum[$v['statusCode']];
                $v['timeEndReal'] = \Prj\Misc\View::fmtYmd($v['timeEndReal'],'time');
                $v['payStatus'] = \Prj\Consts\PayGW::$status[$v['payStatus']];
                unset($v['dlUnit']);
                $new[$_pkey_val_] = $v;
            }
        }
        $records = $new;
        //==
        
        $replace=array();
        foreach ($records as $k=>$v){
            $v['interestStartType']=$v['interestStartType'].'<br>'.$v['returnType'];
            $v['timeStartPlan']=$v['timeStartPlan'].'/'.$v['ymdPayPlan'];
            if(!empty($v['timeEndReal'])&&!empty($v['payYmd'])){
                $v['timeEndReal']=$v['timeEndReal'].'<br>'.$v['payYmd'];
            }elseif(!empty($v['timeEndReal'])&&empty($v['payYmd'])){
                $v['timeEndReal']=$v['timeEndReal'];
            }
            $v['statusCode']=$v['statusCode'].'<br>'.$v['exp'];
            $v['payStatus']=$v['payStatus'].'<br>'.$v['paySn'];
            unset($v['paySn']);
            unset($v['exp']);
            unset($v['payYmd']);
            unset($v['ymdPayPlan']);
            unset($v['returnType']);
            $replace[]=$v;
        }
        $records=$replace;
        
       // var_log($records,'######################');
        if ($isDownloadEXCEL) {
//             foreach ($records as $k => $v) {
//                 //TODO 过滤
//             }
//             $this->_view->assign('records', $records);
            //return;
          //  var_log($records,'######################');
            return $this->downEXCEL($records, array_keys($headers), null, false);
        } else {
            $this->_view->assign('headers', $headers);
            $this->_view->assign('records', $records);
            $this->_view->assign('pager', $pager);
            $this->_view->assign('type', $type);
            $this->_view->assign('where', $where);
        }

    }

    /**
     * 还款计划
     */
    public function returnPlanAction()
    {
        // \Sooh\Base\Ini::getInstance()->viewRenderType('json')
       // $_pkey=$this->_request->get('_pkey_val_');
        
        $pager = new \Sooh\DB\Pager(10, $this->pageSizeEnum, false);
        $pageid = $this->_request->get('pageId',1)-0;
        $pager->page_size = $this->_request->get('pageSize', 50);
       // $pager->page_size = $this->_request->get('pageSize', current($this->pageSizeEnum));
        $ids             = $this->_request->get('ids');
      //  $pager->init(-1,$pageid);
         
        $frm = \Sooh\Base\Form\Broker::getCopy('default')->init(\Sooh\Base\Tools::uri(), 'get', \Sooh\Base\Form\Broker::type_s);
        $frm->addItem('id',form_def::factory('期数','',form_def::text))
            ->addItem('_payStatus_eq', form_def::factory('状态', '', form_def::select,\Prj\Consts\PayGW::$status+[''=>'']))
            ->addItem('pageid', $pageid)
            ->addItem('pagesize', $this->pager->page_size);
        
        $frm->fillValues();
       if($frm->flgIsThisForm){
           
            $where=$frm->getWhere();
            if($_pkey=$this->_request->get('_pkey_val_')){
                $where['waresId']=\Prj\Misc\View::decodePkey($_pkey)['warseId'];
            }else{
              $where=array();  
            }
        }
        
        $where = \Prj\Misc\View::decodePkey($this->_request->get('_waresId_eq'));
       
        $waresId = $where['waresId'];
        $ware = \Prj\Data\Wares::getCopy($waresId);
        $ware->load();
        
        var_log($where,'>>>>>>>>>>>>>>>>>>>>>>');
        
        $search=$this->_request->get('where');
        if(!empty($search))$where=array_merge($where,$search);
       
        
       // var_log($waresId,'>>>>>>>>>>>>>>>>>>>>>>>');
        
       // var_log($pager,'>>>>>>>>>>>>>>>>>>>>>>>>>');
       // $pager->init($records[$k]['id'], $pageid);
      
//     $keys = is_array($ids) ? $ids : explode(',', $ids);
		
// 		if (!empty($ids)) {
// 			  foreach ($keys as $v) {
//                 $arr[] = \Prj\Misc\View::decodePkey($this->_request->get('_waresId_eq'));
//             }
//             $where['waresId'] = $arr;
// 		}
		
        
        
        $returnPlan = $ware->getField('returnPlan');
        if (!empty($returnPlan)) {
            $rp = \Prj\ReturnPlan\All01\ReturnPlan::createPlan($returnPlan);
        } else {
            try {
                $rp = \Prj\ReturnPlan\All01\ReturnPlan::calendar($where['waresId']);
                $ware->setField('returnPlan', $rp->decode());
                $ware->update();
            } catch (\ErrorException $e) {
                return $this->returnError($e->getMessage());
            }
        }

        if (!empty($rp)) $plan = $rp->decode();

        $fieldsMap = array(
            'id' => ['期数', '35'],
            'waresId' => ['标的ID', 'auto'],
            'waresName' => ['标的名称', 'auto'],
            'days' => ['天数', '35'],
            'interest' => ['利息', '52'],
            'amount' => ['本金', '80'],
            'realPay' => ['实际支付金额', 'auto'],
            'planDateYmd' => ['计划支付时间', '83'],
            'realDateYmd' => ['实际支付时间', '83'],
            'isPay' => ['是否支付', '70'],
            'sn' => ['网关流水号', 'auto'],
            'status' => ['状态', '60'],
            'exp' => ['处理结果', '60'],
            'ahead'=>['是否提前', '60'],
            'returnFundStatus'=>['批量回款情况', '90'],
            'waitNum'=>['批量待回款订单', '100'],
            'batchId'=>['批量号', 'auto'],
            'remitStatus'=>['打款状态', 'auto'],
            'remitSN'=>['打款ID', 'auto'],
            'remitAmount'=>['打款金额', 'auto'],
        );
        
        
        $records = [];
        foreach ($plan['calendar'] as $v) {
            foreach ($fieldsMap as $kk => $vv) {
                $temp[$kk] = $v[$kk];
            }
            $temp['remitAmount']/=100;
            $temp['remitAmount'] = $temp['remitAmount']?$temp['remitAmount']:'';
            $temp['_isPay'] = $temp['isPay'];
            $temp['_ahead'] = $temp['ahead'];
            $temp['_status'] = $temp['status'];
            $temp['_remitStatus'] = $temp['remitStatus'];
            $temp['ahead'] = $temp['ahead']?'是':'不是';
            $temp['isPay'] = $temp['isPay']?'已支付':'未支付';
            $temp['interest']/=100;
            $temp['interest'] = sprintf('%.2f',$temp['interest']);
            $temp['amount']/=100;
            $temp['amount'] = sprintf('%.2f',$temp['amount']);
            $temp['realPay']/=100;
            $temp['realPay'] = sprintf('%.2f',$temp['realPay']);
            $temp['status'] = $temp['status']?\Prj\Consts\PayGW::$status[$temp['status']]:'等待操作';
            $temp['status'].= ' '.\Prj\Misc\View::retryBtn($v['retryUrl'],$v['retryBtnShow']);
            $temp['returnFundStatus'] = $temp['returnFundStatus']?\Prj\Consts\PayGW::$status[$temp['returnFundStatus']]:'';
            $temp['returnFundStatus'].= ' '.\Prj\Misc\View::retryBtn($v['retryUrl1'],$v['retryBtnShow1']);
            $temp['planDateYmd'] = \Prj\Misc\View::fmtYmd($temp['planDateYmd']);
            $temp['realDateYmd'] = $temp['realDateYmd']?\Prj\Misc\View::fmtYmd($temp['realDateYmd']):'';
            $temp['exp'] = $temp['exp'];
            $temp['remitStatus'] = \Prj\Consts\PayGW::$status[$temp['remitStatus']];
            $temp['days'] = floor($temp['days'])?floor($temp['days']):1;
            $records[] = $temp;
        }
        
        foreach ($records as $k=>$v){
            $pager->init(count($records),$pageid);
        }
        $this->_view->assign('headers', $fieldsMap);
        $this->_view->assign('records', $records);
        $this->_view->assign('pager', $pager);
    }

    /**
     * 企业回款确认
     */
    public function confirmAction()
    {
        // \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $ahead = $this->_request->get('ahead');
        $this->loger->userId = 'confirm';
        $_pkey = $this->_request->get('_pkey');
        $where = \Prj\Misc\View::decodePkey($_pkey);
        var_log($where,'where >>> ');
        if(!empty($where['ymd']))$where['ymd'] = date('Ymd',strtotime($where['ymd']));
        $wares = \Prj\Data\Wares::getCopy($where['waresId']);
        $wares->load();
        var_log($where['waresId'],'waresId >>> ');
        $borrowerId = $wares->getField('borrowerId');
        $bor = \Prj\Data\User::getCopy($borrowerId);
        $bor->load();
        $borrowerName = $bor->exists()?$bor->getField('nickname'):'';

        //状态检查
        if($wares->getField('payStatus')!=\Prj\Consts\PayGW::success)return $this->returnError(\Prj\Lang\Broker::getMsg('payback.transfer_error'));
        if($wares->getField('payStatus')==\Prj\Consts\PayGW::accept)return $this->returnError(\Prj\Lang\Broker::getMsg('payback.transfer_error'));

        $r = $wares->dump();
        $payArr = [
            'interest'=>0,
            'amount'=>0,
        ];

        $rp = \Prj\ReturnPlan\All01\ReturnPlan::createPlan($wares->getField('returnPlan'),$wares);
        $plan = $rp->getPlanByMonth($where['ymd']);
        //todo if($plan['remitStatus']!=\Prj\Consts\PayGW::success)return $this->returnError('该借款人账户尚未打款');
        if($ahead){
            $interestArr = $rp->getAheadInterest($plan['id']);
            $interest = $interestArr['ahead'];
            $formula = $interestArr['formula'];
            var_log($interest,'interest>>>');

            $payArr['interest'] = $interest/100;
            $payArr['amount'] = $r['amount']/100;

        }else{

            $rp = \Prj\ReturnPlan\All01\ReturnPlan::createPlan($wares->getField('returnPlan'));
            var_log($rp,'>>>>');
            $formula = $rp->getFormula();

            $payArr['interest'] = $where['interest'];
            $payArr['amount'] = $where['amount'];

        }



        $frm = \Sooh\Base\Form\Broker::getCopy('default')
            ->init(\Sooh\Base\Tools::uri(), 'get', empty($where) ? \Sooh\Base\Form\Broker::type_c : \Sooh\Base\Form\Broker::type_u);
        $frm->addItem('waresId', form_def::factory('标的ID', $where['waresId'], form_def::constval, [], []))
            ->addItem('waresName', form_def::factory('标的名称', $r['waresName'], form_def::constval, [], []))
            ->addItem('interest', form_def::factory('利息', $payArr['interest'], form_def::constval, [], []))
            ->addItem('amount', form_def::factory('本金', $payArr['amount'], form_def::constval, [], []))
            ->addItem('realPay', form_def::factory('支付本息(元)', $payArr['interest'] + $payArr['amount'], form_def::text, [], []))
            ->addItem('servicePay', form_def::factory('手续费(元)', $r['managementConfirm']/100, form_def::text, [], []))
            ->addItem('giftPay', form_def::factory('平台垫付(元)', 0, form_def::text, [], []))
            //->addItem('formula', form_def::factory('收益公式(分)', $formula, form_def::constval, [], []))
            ->addItem('ahead',$ahead)
            ->addItem('_pkey', $_pkey);
        $frm->fillValues();
        if ($frm->flgIsThisForm) {
            $this->closeAndReloadPage();
            if($r['payStatus']!=\Prj\Consts\PayGW::success)return $this->returnError('该标的尚未完成转账');
            if(in_array($plan['status'],[\Prj\Consts\PayGW::accept,\Prj\Consts\PayGW::success]))
                return $this->returnError(\Prj\Lang\Broker::getMsg('payback.Repeat_submit'));
            $lastPlan = $rp->getPlanById($plan['id']-1);
            if(!empty($lastPlan)){
                if($lastPlan['isPay']==0){
                    return $this->returnError('上一期的款项尚未支付');
                }
                if($lastPlan['ahead']==1){
                    return $this->returnError('该项目已经申请提前还款');
                }
            }
            //调用网关
            $fields = $frm->getFields();
            var_log($fields,'fields>>>>>>>>');
            $rpc = \Sooh\Base\Ini::getInstance()->get('noGW') ? null : \Sooh\Base\Rpc\Broker::factory('PayGW');
            $sys = \Lib\Services\PayGW::getInstance($rpc);
            $sn = $plan['sn']?$plan['sn']:$this->_creatID();
            $where['interest']*=100;
            $where['amount']*=100;
            $fields['realPay']*=100;
            $fields['servicePay']*=100;
            $fields['giftPay']*=100;

            $fields['realPay'] =  round($fields['realPay']);
            $fields['servicePay'] =  round($fields['servicePay']);
            $fields['giftPay'] =  round($fields['giftPay']);
            var_log($ahead,'ahead >>> ');
           //todo 修复  if($where['interest'] + $where['amount'] < $fields['realPay'] && !$ahead )return $this->returnError(\Prj\Lang\Broker::getMsg('payback.pay_over_realPay'));

            $data = [
                'SN' => $sn,
                'waresId' => $where['waresId'],
                'amountPlan' => $where['interest'] + $where['amount'],
                'amountReal' => $fields['realPay'],
                'amountLeft' => $where['interest'] + $where['amount'] - $fields['realPay'],
                'borrowerId' => $borrowerId,
                'borrowerName' => $borrowerName,
                'borrowerTunnel'=>'auto',
                'servicePay' => $fields['servicePay'], //佣金
                'giftPay'=>$fields['giftPay'], //平台垫付
            ];
            $newApi = 1;  //正式服请勿开启新接口开关

            if($newApi){
                try{
                    \Prj\Wares\Wares::doConfirm($sn,$fields['realPay'],$fields['giftPay'],$fields['servicePay'],$where['ymd'],$ahead,0,$wares);
                }catch (\ErrorException $e){
                    $this->closeAndReloadPage();
                    return $this->returnError($e->getMessage());
                }
                return $this->returnOK('新浪网关已受理');
            }else{
                try {
                    var_log($data, '传给支付网关的参数：');
                    //return $this->returnError();
                    $ret = call_user_func_array([$sys, 'confirm'], $data);
                    $this->_view->assign('retAll', ['ret' => 'ok', 'got' => $ret]);
                } catch (\Sooh\Base\ErrException $e) {
                    $code = $e->getCode();
                    if ($code == 400) {
                        //return $this->returnError($e->getMessage());
                    } elseif ($code == 500) {
                        return $this->returnError($e->getMessage());
                    }
                    return $this->returnError('gw_error');
                }
                $logerError = "sn:$sn ";
                if($ret['code']==400){
                    return $this->returnError('网关信息:'.$ret['reason']);
                }

                if ($ret['status'] == 1 || $ret['status'] == 8 ) {

                    if($plan['waitNum']==null){
                        $investList = \Prj\Data\Investment::loopFindRecords(['waresId' => $where['waresId'], 'orderStatus]' => \Prj\Consts\OrderStatus::payed]);
                        var_log(count($investList),'count >>> ');
                        $rp->updatePlanByMonth('waitNum', count($investList), $where['ymd']);
                    }
                    $rp->updatePlanByMonth('sn', $sn, $where['ymd']);
                    $rp->updatePlanByMonth('exp', '网关已受理', $where['ymd']);
                    if($ahead){
                        $rp->updatePlanByMonth('ahead', '1', $where['ymd']);
                        $rp->updatePlanByMonth('compensatePay', $fields['compensatePay'], $where['ymd']);

                    }


                    $rp->updatePlanByMonth('servicePay', $fields['servicePay'], $where['ymd']);
                    $rp->updatePlanByMonth('giftPay', $fields['giftPay'], $where['ymd']);

                    $rp->updatePlanByMonth('status', \Prj\Consts\PayGW::accept, $where['ymd']);
                    $rp->updatePlanByMonth('realPay', $fields['realPay']+$fields['servicePay'], $where['ymd']);

                    //todo 记录retryUrl
                    $rp->updatePlanByMonth('retryUrl', \Prj\Misc\JavaService::$lastUrl, $where['ymd']);
                    $rp->updatePlanByMonth('retryBtnShow', 1, $where['ymd']);

                    try {
                        $returnPlan = $rp->decode();
                        if(empty($returnPlan['calendar']))throw new \ErrorException('还款计划异常');
                        $wares->setField('returnPlan', $returnPlan);
                        $wares->update();
                    } catch (\ErrorException $e) {
                        $this->loger->error($logerError.$e->getMessage());
                        return $this->returnError('数据库错误，请联系运维:'.$e->getMessage());
                    }

                    if(\Sooh\Base\Ini::getInstance()->get('noGW'))
                    {
                        //todo 自我回调
                        $url = \Sooh\Base\Ini::getInstance()->get('RpcConfig')['urls'][0];
                        //$sn,$waresId,$status
                        $data = [
                            'sn'=>$sn,
                            'waresId'=>$where['waresId'],
                            'status'=>'success',
                            'msg'=>'success',
                        ];
                        $time = time();
                        $url.=('&service=PayGW&cmd=confirmResult&dt='.$time.'&sign='.md5($time.'asgdfw4872hfhjksdhr8732trsj').'&args='.json_encode($data));
                        $result = \Prj\Tool\Func::curl_post($url);
                        var_log($result,'企业还款自我回调>>>>>>>>>>>>>>>>>>>>>>>');
                    }

                    $this->closeAndReloadPage();
                    return $this->returnOK('处理成功');
                } elseif ($ret['status'] == 4) {
                    //$rp->updatePlanByMonth('sn', $sn, $where['ymd']);
                    $rp->updatePlanByMonth('exp', $ret['reason'], $where['ymd']);
                    $rp->updatePlanByMonth('status', \Prj\Consts\PayGW::failed, $where['ymd']);
                    //$rp->updatePlanByMonth('realPay', $fields['realPay'], $where['ymd']);
                    //$rp->updatePlanByMonth('realDateYmd', \Sooh\Base\Time::getInstance()->YmdFull, $where['ymd']);
                    try {
                        $returnPlan = $rp->decode();
                        if(empty($returnPlan['calendar']))throw new \ErrorException('还款计划异常');
                        $wares->setField('returnPlan', $returnPlan);
                        $wares->update();
                    } catch (\ErrorException $e) {
                        $this->loger->error($logerError);
                        return $this->returnError('数据库错误，请联系运维:'.$e->getMessage());
                    }
                    $this->closeAndReloadPage();
                    return $this->returnError('处理失败');
                } else {
                    //return $this->returnError('网关错误(未知的状态)');
                    return $this->returnError(\Prj\Lang\Broker::getMsg('system.gw_error'));
                }
            }
        }
    }

    /**
     * 打款给借款人账户
     */
    public function remitAction()
    {
        // \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $ahead = $this->_request->get('ahead');
        $this->loger->userId = 'confirm';
        $_pkey = $this->_request->get('_pkey');
        $this->_view->assign('_pkey',$_pkey);
        $where = \Prj\Misc\View::decodePkey($_pkey);
        var_log($where,'where >>> ');
        if(!empty($where['ymd']))$where['ymd'] = date('Ymd',strtotime($where['ymd']));
        $wares = \Prj\Data\Wares::getCopy($where['waresId']);
        $wares->load();
        var_log($where['waresId'],'waresId >>> ');
        $borrowerId = $wares->getField('borrowerId');
        $bor = \Prj\Data\User::getCopy($borrowerId);
        $bor->load();
        $borrowerName = $bor->exists()?$bor->getField('nickname'):'';

        //状态检查
        if($wares->getField('payStatus')!=\Prj\Consts\PayGW::success && $wares->getField('payStatus')!=\Prj\Consts\PayGW::accept)return $this->returnError(\Prj\Lang\Broker::getMsg('payback.transfer_error'));

        $r = $wares->dump();
        $payArr = [
            'interest'=>0,
            'amount'=>0,
        ];

        $rp = \Prj\ReturnPlan\All01\ReturnPlan::createPlan($wares->getField('returnPlan'),$wares);
        $plan = $rp->getPlanByMonth($where['ymd']);

        if($ahead){
            $interestArr = $rp->getAheadInterest($plan['id']);
            $interest = $interestArr['ahead'];
            $formula = $interestArr['formula'];
            var_log($interest,'interest>>>');

            $payArr['interest'] = $interest/100;
            $payArr['amount'] = $r['amount']/100;

        }else{

            $rp = \Prj\ReturnPlan\All01\ReturnPlan::createPlan($wares->getField('returnPlan'));
            var_log($rp,'>>>>');
            $formula = $rp->getFormula();

            $payArr['interest'] = $where['interest'];
            $payArr['amount'] = $where['amount'];

        }

        $frm = \Sooh\Base\Form\Broker::getCopy('default')
            ->init(\Sooh\Base\Tools::uri(), 'get', empty($where) ? \Sooh\Base\Form\Broker::type_c : \Sooh\Base\Form\Broker::type_u);
        $frm->addItem('waresId', form_def::factory('标的ID', $where['waresId'], form_def::constval, [], []))
            ->addItem('waresName', form_def::factory('标的名称', $r['waresName'], form_def::constval, [], []))
            ->addItem('interest', form_def::factory('利息', $payArr['interest'], form_def::constval, [], []))
            ->addItem('amount', form_def::factory('本金', $payArr['amount'], form_def::constval, [], []))
            ->addItem('realPay', form_def::factory('支付本息(元)', $payArr['interest'] + $payArr['amount'], form_def::constval, [], []))
            ->addItem('servicePay', form_def::factory('手续费(元)', $r['managementConfirm']/100, form_def::constval, [], []))
            ->addItem('remitAmount', form_def::factory('需要打款(元)', $payArr['interest'] + $payArr['amount']+$r['managementConfirm']/100, form_def::text, [], []))
            //->addItem('formula', form_def::factory('收益公式(分)', $formula, form_def::constval, [], []))
            ->addItem('ahead',$ahead)
            ->addItem('_pkey', $_pkey);
        $frm->fillValues();
        if ($frm->flgIsThisForm) {
            if($r['payStatus']!=\Prj\Consts\PayGW::success)return $this->returnError('该标的尚未完成转账');
            if(in_array($plan['remitStatus'],[\Prj\Consts\PayGW::accept,\Prj\Consts\PayGW::success]))
                return $this->returnError(\Prj\Lang\Broker::getMsg('payback.Repeat_submit'));
            $lastPlan = $rp->getPlanById($plan['id']-1);
            if(!empty($lastPlan)){
                /*
                if($lastPlan['isPay']==0){
                    return $this->returnError('上一期的款项尚未支付');
                }
                */
                if($lastPlan['ahead']==1){
                    return $this->returnError('该项目已经申请提前还款');
                }
            }
            //调用网关
            $fields = $frm->getFields();
            var_log($fields,'fields>>>>>>>>');
            $rpc = \Sooh\Base\Ini::getInstance()->get('noGW') ? self::getRpcDefault('PayGW') : \Sooh\Base\Rpc\Broker::factory('PayGW');
            $sys = \Lib\Services\PayGW::getInstance($rpc);
            $sn = $this->_creatID();
            $where['interest']*=100;
            $where['amount']*=100;
            $fields['realPay']*=100;
            $fields['servicePay']*=100;
            $fields['giftPay']*=100;
            $fields['remitAmount']*=100;
            $payArr['interest']*=100;
            $payArr['amount']*=100;
            $payArr['amount'] = round($payArr['amount']);
            $payArr['interest'] = round($payArr['interest']);
            $fields['remitAmount'] = round($fields['remitAmount']);
            $fields['realPay'] =  round($fields['realPay']);
            $fields['servicePay'] =  round($fields['servicePay']);
            $fields['giftPay'] =  round($fields['giftPay']);
            var_log($ahead,'ahead >>> ');
            //todo 修复 if($where['interest'] + $where['amount'] < $fields['realPay'] && !$ahead )return $this->returnError(\Prj\Lang\Broker::getMsg('payback.pay_over_realPay'));

            try {
                $data = [
                    'SN' => $sn,
                    'waresId' => $where['waresId'],
                    'id' => $plan['id'],
                    'amountPlan' => $payArr['amount'],
                    'interestPlan' => $payArr['interest'],
                    'servicePay' => $fields['servicePay'], //佣金
                    'realPay' => $fields['remitAmount'],
                    'borrowerId' => $borrowerId,
                    'borrowerName' => $borrowerName,
                    'borrowerTunnel'=>'auto',
                ];
                var_log($data, '传给支付网关的参数：');
                //return $this->returnError();
                //return $this->returnError('xxx');
                $ret = call_user_func_array([$sys, 'remit'], $data);
                $this->_view->assign('retAll', ['ret' => 'ok', 'got' => $ret]);
            } catch (\Sooh\Base\ErrException $e) {
                $code = $e->getCode();
                if ($code == 400) {
                    //return $this->returnError($e->getMessage());
                } elseif ($code == 500) {
                    return $this->returnError($e->getMessage());
                }
                return $this->returnError('gw_error');
            }
            $logerError = "sn:$sn ";
            if($ret['code']==400){
                return $this->returnError('网关信息:'.$ret['reason']);
            }


            if ($ret['status'] == 1 || $ret['status'] == 8 ) {


                $rp->updatePlanByMonth('remitSN', $sn, $where['ymd']);

                if($ahead){
                    $rp->updatePlanByMonth('ahead', '1', $where['ymd']);
                }
                $rp->updatePlanByMonth('remitStatus', \Prj\Consts\PayGW::accept, $where['ymd']);
                $rp->updatePlanByMonth('remitAmount', $fields['remitAmount'], $where['ymd']);
                try {
                    $wares->setField('returnPlan', $rp->decode());
                    $wares->update();
                } catch (\ErrorException $e) {
                    $this->loger->error($logerError.$e->getMessage());
                    return $this->returnError('数据库错误，请联系运维:'.$e->getMessage());
                }

                if(\Sooh\Base\Ini::getInstance()->get('noGW'))
                {
                    /*
                    //todo 自我回调
                    $url = \Sooh\Base\Ini::getInstance()->get('RpcConfig')['urls'][0];
                    //$sn,$waresId,$status
                    $data = [
                        'sn'=>$sn,
                        'waresId'=>$where['waresId'],
                        'status'=>'success',
                        'msg'=>'success',
                    ];
                    $time = time();
                    $url.=('&service=PayGW&cmd=confirmResult&dt='.$time.'&sign='.md5($time.'asgdfw4872hfhjksdhr8732trsj').'&args='.json_encode($data));
                    $result = \Prj\Tool\Func::curl_post($url);
                    var_log($result,'企业还款自我回调>>>>>>>>>>>>>>>>>>>>>>>');
                    */
                }

                $this->closeAndReloadPage();
                return $this->returnOK('处理成功');
            } elseif ($ret['status'] == 4) {
                //$rp->updatePlanByMonth('sn', $sn, $where['ymd']);
                $rp->updatePlanByMonth('exp', $ret['reason'], $where['ymd']);
                $rp->updatePlanByMonth('remitStatus', \Prj\Consts\PayGW::failed, $where['ymd']);
                //$rp->updatePlanByMonth('realPay', $fields['realPay'], $where['ymd']);
                //$rp->updatePlanByMonth('realDateYmd', \Sooh\Base\Time::getInstance()->YmdFull, $where['ymd']);
                try {
                    $wares->setField('returnPlan', $rp->decode());
                    $wares->update();
                } catch (\ErrorException $e) {
                    $this->loger->error($logerError);
                    return $this->returnError('数据库错误，请联系运维:'.$e->getMessage());
                }
                $this->closeAndReloadPage();
                return $this->returnError('处理失败');
            } else {
                //return $this->returnError('网关错误(未知的状态)');
                return $this->returnError(\Prj\Lang\Broker::getMsg('system.gw_error'));
            }
            return $this->returnError(\Prj\Lang\Broker::getMsg('payback.intercept'));
        }
    }

    /**
     * 企业提前回款确认
     */
    public function aheadConfirmAction()
    {
   }
    protected function _creatID(){return time() . rand(100000, 999999);}

    protected $_headers = array(
        'id' => ['期数', '35'],
        'days' => ['天数', '35'],
        'ordersId' => ['订单号', 'auto'],
        'waresId' => ['标的ID', 'auto'],
        'waresName' => ['标的名称', 'auto'],
        'userId' => ['用户ID', 'auto'],
        'interestStatic' => ['基本收益', '70'],
        'interestAdd' => ['活动收益', '70'],
        //'interestExt' => ['加成收益', '70'],
        //'interestFloat' => ['浮动收益', '70'],
        'interestSub' => ['平台贴息', '70'],
        'amount' => ['本金', '70'],
        'amountExt' => ['红包', '35'],
        'planDateYmd' => ['计划支付日期', '80'],
        'realDateYmd' => ['实际支付日期', '80'],
        'realPayAmount' => ['实际支付本金', '80'],
        'realPayInterest' => ['实际支付利息', '80'],
        'realPayinterestSub' => ['实际支付贴息', '80'],
        'status' => ['状态', 'auto'],
        'sn' => ['网关流水号', 'auto'],
        'isPay' => ['是否支付', '70'],
        'exp' => ['备注', 'auto'],
    );

    protected $moneyArr = ['interestStatic','interestAdd','interestExt','interestFloat','amount','amountExt','realPayAmount','realPayInterest','interestSub','realPayinterestSub'];
    protected $dateArr = ['planDateYmd','realDateYmd'];

    public function  returnPlanUserAction()
    {   
        
        
        
        $pager = new \Sooh\DB\Pager(10, $this->pageSizeEnum, false);
        $pageid = $this->_request->get('pageId',1)-0;
        $pager->page_size = $this->_request->get('pageSize', 100);
        $ids             = $this->_request->get('ids');
         
         
        $frm = \Sooh\Base\Form\Broker::getCopy('default')->init(\Sooh\Base\Tools::uri(), 'get', \Sooh\Base\Form\Broker::type_s);
        $frm->addItem('id',form_def::factory('期数','',form_def::text))
        ->addItem('pageid', $pageid)
        ->addItem('pagesize', $this->pager->page_size);
        
        $frm->fillValues();
        $frm->flgIsThisForm;
        $where=$frm->getWhere();
        
        $headers = $this->_headers;
        $_pkey = $this->_request->get('_pkey');
        $where = \Prj\Misc\View::decodePkey($_pkey);
        var_log($where,'returnPlanUser where>>>');
        $this->_view->assign('ahead',$where['ahead']);
        $waresId = $where['waresId'];
        $rid = $where['id'];
        $investList = \Prj\Data\Investment::loopFindRecords(['waresId' => $waresId, 'orderStatus]' => \Prj\Consts\OrderStatus::payed]);
        $records = [];
        foreach ($investList as $v) {
            if(empty($v['returnPlan']))
            {
                $rp = \Prj\ReturnPlan\Std01\ReturnPlan::calendar($v['ordersId']);
            }
            else
            {
                $rp = \Prj\ReturnPlan\Std01\ReturnPlan::createPlan($v['returnPlan']);
            }
            $temp = $rp->getPlanById($rid);
            $temp['userId'] = $v['userId'];
            $tempAhead = $rp->getPlanById($rid-0.5);
            $records[] = $temp;
            if($tempAhead){
                $tempAhead['userId'] = $v['userId'];
                $records[] = $tempAhead;
            }
            unset($temp);
        }

        $records = \Prj\Tool\Func::paged($records,'',$pager);

        foreach ($records as $v) {
            foreach ($headers as $kk => $vv) {
                $temp[$kk] = $v[$kk];
                if(in_array($kk,$this->moneyArr))$temp[$kk]/=100;
                if(in_array($kk,$this->dateArr))$temp[$kk] = $temp[$kk]?date('Y-m-d',strtotime($temp[$kk])):'';
            }
            $temp['status'] = \Prj\Consts\PayGW::$status[$temp['status']]?\Prj\Consts\PayGW::$status[$temp['status']]:'等待操作';
            $temp['isPay'] = $temp['isPay']?'已支付':'未支付';
            $newRecords[] = $temp;
            unset($temp);
        }
        
        
        
        $this->_view->assign('headers', $headers);
        $this->_view->assign('records', $newRecords);
        $this->_view->assign('pager', $pager);
        //var_log($records,'records>>>>>>>>');
    }

    public function  returnPlanUserAllAction()
    {
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $sendData = []; //---发给网关的数据
        $rpInit = [];
        $failedData = []; //---处理失败的数据
        $_pkey = $this->_request->get('_pkey');
        $where = \Prj\Misc\View::decodePkey($_pkey);
        var_log($where,'returnPlanUser where>>>');
        $id = $where['id'];//---还款期数
        $ahead = $where['ahead'];
        $confirmSN = $where['confirmSN'];
        $planYmd = date('Ymd',strtotime($where['ymd']));
        $waresId = $where['waresId'];
        try{
            $ret = \Prj\Wares\Wares::getCopy()->returnFundAll($where['id'],$waresId,$confirmSN,$ahead);
        }catch (\ErrorException $e){
            return $this->returnError($e->getMessage());
        }
        return $this->returnOK($ret);
    }

    protected function returnPlanUserAll_rollBack($rpInit){
        if(!empty($rpInit)){
            foreach($rpInit as $k=>$v){
                $invest = \Prj\Data\Investment::getCopy($k);
                $invest->load();
                if($invest->exists()){
                    $invest->setField('returnPlan',$v);
                    try{
                        $invest->update();
                    }catch (\ErrorException $e){
                        var_log('[error]returnPlanUserAll_rollBack 回滚失败# ordersId:'.$k);
                    }
                }
            }
        }
    }

    public function returnFundAction()
    {
        $this->loger->userId = 'returnFund';
        $_pkey = $this->_request->get('_pkey');
        $where = \Prj\Misc\View::decodePkey($_pkey);
        $ahead = $where['ahead'];
        var_log($where, 'where>>>>>>>>>>>>>>>');
        $ordersId = $where['ordersId'];
        $invest = \Prj\Data\Investment::getCopy($ordersId);
        $invest->load();
        $investArr = $invest->dump();
        $rp = \Prj\ReturnPlan\Std01\ReturnPlan::createPlan($investArr['returnPlan'],$invest);
        $plan = $rp->getPlanById($where['rid']);
        if(empty($plan)){
            /*
            $rp = \Prj\ReturnPlan\Std01\ReturnPlan::calendar($where['ordersId']);
            $plan = $rp->getPlanById($where['rid']);
            */
            return $this->returnError(\Prj\Lang\Broker::getMsg('payback.returnFund_fail'));
        }
        var_log($plan,'plan>>>>>>>>>>>>>>>>');
        //if($plan['isPay'])return $this->returnError('已经支付过！');
        if($plan['isPay'])return $this->returnError(\Prj\Lang\Broker::getMsg('payback.pay_over'));

        //var_log($plan, 'plan>>>>>>>>>>>>>>>>>');
        $realPayInterest = $plan['interestStatic'] + $plan['interestAdd'] +
                          $plan['interestExt'] + $plan['interestFloat'] ;
        $realPayAmount  =    $plan['amount'] ;

        if($ahead){
            $realPayInterest = 0;
            $aheadInterestArr = $rp->getAheadInterest($plan['id']);
            $aheadInterest = $aheadInterestArr['ahead'];
            foreach($aheadInterest as $k=>$v){
                $plan[$k] = $v;
                $realPayInterest+=$v;
            }

            $plan['amount'] = $invest->getField('amount');
            $plan['amountExt'] = $invest->getField('amountExt');
            $plan['interestSub'] = $aheadInterestArr['interestSub'];
            $realPayAmount = $plan['amount']  ;
        }

        foreach($plan as $k=>$v){
            if(in_array($k,$this->moneyArr))$plan[$k]/=100;
        }

        $wares = \Prj\Data\Wares::getCopy($investArr['waresId']);
        $wares->load();

        $realPayAmount/=100;
        $realPayInterest/=100;

        if($ahead){
            $formula = $aheadInterestArr['formula'];
        }else{
            $formula = $rp->getFormula();
        }
        $frm = \Sooh\Base\Form\Broker::getCopy('default')
            ->init(\Sooh\Base\Tools::uri(), 'get', empty($where) ? \Sooh\Base\Form\Broker::type_c : \Sooh\Base\Form\Broker::type_u);
        $frm->addItem('ordersId', form_def::factory('订单号', $investArr['ordersId'], form_def::constval, [], []))
            ->addItem('waresId', form_def::factory('标的ID', $investArr['waresId'], form_def::constval, [], []))
            ->addItem('waresName', form_def::factory('标的名称', $investArr['waresName'], form_def::constval, [], []))
            ->addItem('interestStatic', form_def::factory('基本收益', $plan['interestStatic'], form_def::constval, [], []))
            ->addItem('interestAdd', form_def::factory('活动收益', $plan['interestAdd'], form_def::constval, [], []))
            ->addItem('interestExt', form_def::factory('加成收益', $plan['interestExt'], form_def::constval, [], []))
            //->addItem('interestFloat', form_def::factory('浮动收益', $plan['interestFloat'], form_def::constval, [], []))
            ->addItem('amount', form_def::factory('本金', $plan['amount'] - 0, form_def::constval, [], []))
            ->addItem('amountExt', form_def::factory('红包', $plan['amountExt'] - 0, form_def::constval, [], []))
            ->addItem('userId', form_def::factory('用户ID', $investArr['userId'], form_def::constval, [], []))
            ->addItem('realPayAmount', form_def::factory('支付金额', $realPayAmount+$plan['amountExt'], form_def::constval, [], []))
            ->addItem('realPayInterest', form_def::factory('支付利息', $realPayInterest, form_def::constval, [], []))
            ->addItem('interestSub', form_def::factory('平台贴息', $plan['interestSub']-0, form_def::constval, [], []))
            //->addItem('formula', form_def::factory('收益公式(分)',  $formula, form_def::constval, [], []))
            ->addItem('_pkey', $_pkey);
        //if($ahead)$frm->addItem('compensatePay', form_def::factory('补偿金', 0, form_def::constval, [], []));

        $frm->fillValues();
        if ($frm->isThisFormSubmited()) {
            $fields = $frm->getFields();
            if($ahead){$fields['compensatePay']*=100;}
            //if(in_array($plan['status'],[1,2]))return $this->returnError(\Prj\Lang\Broker::getMsg('payback.Repeat_submit'));
            $realPayArr = [
                "realPayAmount" => $this->_request->get('realPayAmount'),
                "realPayInterest" => $this->_request->get('realPayInterest'),
            ];
            var_log($realPayAmount.'/'.$plan['amountExt'],'>>>>>');
           // if($realPayArr['realPayAmount']>$realPayAmount+$plan['amountExt'])return $this->returnError('本金支付超额！');
            //todo 待修复 if($realPayArr['realPayAmount']>$realPayAmount+$plan['amountExt'])return $this->returnError(\Prj\Lang\Broker::getMsg('payback.realPayAmount_over_amountExt'));
           // if($realPayArr['realPayInterest']>$realPayInterest)return $this->returnError('利息支付超额！');
            var_log('>>>>>>');
            //todo 待修复 if($realPayArr['realPayInterest']>$realPayInterest)return $this->returnError(\Prj\Lang\Broker::getMsg('payback.realPayInterest_over_amountExt'));

            $borrowerId = $wares->getField('borrowerId');
            $bor = \Prj\Data\User::getCopy($borrowerId);
            $bor->load();
            $borrowerName = $bor->exists()?$bor->getField('nickname'):'';

            $waresRp = \Prj\ReturnPlan\All01\ReturnPlan::createPlan($wares->getField('returnPlan'));
            //if($waresRp->getPlanById($where['rid'])['status']!=8)return $this->returnError('借款方还未还款！');
            if($waresRp->getPlanById($where['rid'])['status']!=8)return $this->returnError(\Prj\Lang\Broker::getMsg('payback.beBorrower_not_reimbursement'));
            $confirmSN = $waresRp->getPlanById($where['rid'])['sn'];
            $sn = $plan['sn'];
            if(empty($sn)){
                //todo 记录还款数据
                try{
                    $planFields = $plan;
                    $planFields['periods'] = $plan['id'];
                    unset($planFields['id']);
                    $returnPlanData = \Prj\Data\ReturnPlan::add($planFields,$fields['userId']);
                }catch (\ErrorException $e){
                    error_log('error#returnFund#'.$fields['ordersId'].'#'.$fields['periods'].'#'.$e->getMessage());
                    return $this->returnError($e->getMessage());
                }
                $sn = $returnPlanData->getField('sn');
            }
            var_log($confirmSN,'confirmSN>>>>>>>>');
            //调用网关
            $rpc = \Sooh\Base\Ini::getInstance()->get('noGW')?self::getRpcDefault('PayGW'):\Sooh\Base\Rpc\Broker::factory('PayGW');
            $sys = \Lib\Services\PayGW::getInstance($rpc);
            foreach($realPayArr as $k=>$v){
                $realPayArr[$k]*=100;
            }
            $plan['interestSub']*=100;
            try {
                $data = [
                    $sn,
                    $confirmSN,
                    $ordersId,
                    $investArr['waresId'],
                    $investArr['userId'],
                    $realPayArr['realPayAmount'],
                    $realPayArr['realPayInterest']+$fields['compensatePay'],
                    $plan['interestSub'],
                    $borrowerId,
                    $borrowerName
                ];
                var_log($data,'发送给网关的参数>>>>>>>>>>>>>>>>>>>>');
               // return $this->returnError('中断>>>');
                $ret = call_user_func_array([$sys,'returnFund'],$data);
                $this->_view->assign('retAll', ['ret' => 'ok', 'got' => $ret]);
            } catch (\Sooh\Base\ErrException $e) {
                $code = $e->getCode();
                if ($code == 400) {
                    return $this->returnError($e->getMessage());
                } elseif ($code == 500) {
                    return $this->returnError($e->getMessage());
                }
               // return $this->returnError('gw_error');
                return $this->returnError(\Prj\Lang\Broker::getMsg('system.gw_error'));
            }
            $logerError = "sn:$sn";
            if($ret['code']!=200){
                //return $this->returnError('网关错误:'.$ret['reason']);
            }
            if($ret['status']==4)
            {
                //$rp->updatePlanByMonth('sn',$sn,$plan['planDateYmd']);
                $rp->updatePlanByMonth('status',\Prj\Consts\PayGW::failed,$plan['planDateYmd']);
                $rp->updatePlanByMonth('exp',$ret['reason'],$plan['planDateYmd']);
                try{
                    //throw new \ErrorException('哈哈哈');
                    $invest->setField('returnPlan',$rp->decode());
                    $invest->update();
                }catch (\ErrorException $e){
                    $this->loger->error($logerError.'#'.$e->getMessage());
                    return $this->returnError('数据库错误，请联系运维:'.$e->getMessage());
                }
                return $this->returnError('网关处理失败:'.$ret['reason']);
            }
            if($ret['status']==1 || $ret['status']==8)
            {
                var_log($plan['planDateYmd'],'>>>>>>>>');
                $rp->updatePlanByMonth('sn',$sn,$plan['planDateYmd']);
                //$rp->updatePlanByMonth('realDateYmd', \Sooh\Base\Time::getInstance()->YmdFull,$plan['planDateYmd']);
                $rp->updatePlanByMonth('realPayAmount',$realPayArr['realPayAmount'],$plan['planDateYmd']);
                $rp->updatePlanByMonth('realPayinterestSub',$plan['interestSub'],$plan['planDateYmd']);
                $rp->updatePlanByMonth('realPayInterest',$realPayArr['realPayInterest'],$plan['planDateYmd']);
                $rp->updatePlanByMonth('exp','网关已受理',$plan['planDateYmd']);
                $rp->updatePlanByMonth('status',\Prj\Consts\PayGW::accept,$plan['planDateYmd']);
                if($ahead){
                    $rp->updatePlanByMonth('ahead',1,$plan['planDateYmd']);
                    $rp->updatePlanByMonth('interestSub',$plan['interestSub'],$plan['planDateYmd']);
                    $rp->updatePlanByMonth('compensatePay',$fields['compensatePay'],$plan['planDateYmd']);
                    $rp->updatePlanByMonth('exp','提前还款#网关已受理',$plan['planDateYmd']);
                }
                try{
                    //throw new \ErrorException('哈哈哈');
                    var_log($rp->decode(),'>>>>>>>>>>>');
                    $invest->setField('returnPlan',$rp->decode());
                    $invest->update();
                }catch (\ErrorException $e){
                    $this->loger->error($logerError.'#'.$e->getMessage());
                    return $this->returnError('数据库错误，请联系运维:'.$e->getMessage());
                }
                $this->closeAndReloadPage();
                return $this->returnOK('回款成功，等待网关转账');
            }
            else
            {
               // return $this->returnError('网关返回了错误的状态');
                return $this->returnError(\Prj\Lang\Broker::getMsg('system.gw_error'));
            }
        }
    }

    function subInterestFixAction(){
        $addInterestSub = $this->_request->get('addInterestSub'); //补计划
        $sendInterestSub = $this->_request->get('sendInterestSub'); //发贴息金额
        $pageid = $this->_request->get('pageId');
        $pager = new \Sooh\DB\Pager(10, $this->pageSizeEnum, false);
        $pager->page_size = $this->_request->get('pageSize', 50);
        $frm = \Sooh\Base\Form\Broker::getCopy('default')
            ->init(\Sooh\Base\Tools::uri(), 'get', \Sooh\Base\Form\Broker::type_s);
        $frm->addItem('_waresId_eq', form_def::factory('标的ID', '', form_def::text))
            ->addItem('_orderStatus_eq', form_def::factory('类型', '39', form_def::select, ([39=>'需要补发',10=>'尚未发放'])))
            // ->addItem('_statusCode_eq', form_def::factory('订单状态', '', form_def::select,(\Prj\Consts\Wares::$enum+[''=>'全部'])))
            ->addItem('pageid', $pageid)
            ->addItem('pagesize', $pager->page_size);
        $frm->fillValues();
        if($addInterestSub){
            $where['orderStatus'] = [\Prj\Consts\OrderStatus::done,\Prj\Consts\OrderStatus::going];
        }else{
            if ($frm->flgIsThisForm) { //submit
                $where = $frm->getWhere();
            } else {
                $where = [
                    'orderStatus'=>\Prj\Consts\OrderStatus::done,
                ];
            }
        }
        if($sendInterestSub)$where['orderStatus'] = \Prj\Consts\OrderStatus::done;
        $where['LEFT(transTime,8)-LEFT(orderTime,8)>'] = 1;

        $list = \Prj\Data\Investment::loopFindRecords($where);
        $header = [
            'ordersId' => array('订单ID', 160),
            'orderStatus' => array('订单状态', 85),
            'waresId' => array('标的ID', 135),
            'waresName' => array('标的名称', 135),
            'userId' => array('用户ID', 135),
            'orderTime' => array('下单时间', 150),
            'transTime' => array('转账时间', 150),
            'amount' => array('金额', 135),
            'interestSubOld' => array('已生成贴息金额', 135),
            'interestSub' => array('需要贴息金额', 135),
            'isPay' => array('是否已回款', 135),
            'planDateYmd' => array('计划支付时间', 135),
            'fix'=>array('是否修复', 135),
            'repay'=>array('是否补发贴息', 135),
        ];
        $newList = [];
        $successNum = 0;
        $waresArr = [];
        if($list){
            array_walk($list,function(&$v,$k)use(&$newList,$header,$addInterestSub,&$successNum,$sendInterestSub,&$waresArr){
                $ordersId = $v['ordersId'];
                $returnPlan = \Prj\ReturnPlan\Std01\ReturnPlan::calendar($v['ordersId']);
                //
                if($plans = $returnPlan->calendar){
                    $plan =  current(array_slice($plans, -1));
                   // $oldPlan = current(array_slice($oldReturnPlan->calendar,-1));
                    //var_log($oldPlan);
                   // var_log($plan);
                    if($plan['interestSub']>0){
                        $oldReturnPlan = \Prj\ReturnPlan\Std01\ReturnPlan::createPlan($v['returnPlan']);
                        $oldPlan = current(array_slice($oldReturnPlan->calendar,-1));
                        // var_log($oldPlan);
                        if(empty($oldPlan['interestSub']) || $oldPlan['fix']){
                            $v['interestSub'] = $plan['interestSub'];
                            $v['orderStatus'] = \Prj\Consts\OrderStatus::$enum[$v['orderStatus']];
                            $v['amount']/=100;
                            $v['interestSub']/=100;
                            $v['interestSubOld'] = $oldPlan['interestSub']/100;
                            $v['orderTime'] = date('Y-m-d H:i:s',strtotime($v['orderTime']));
                            $v['transTime'] = date('Y-m-d H:i:s',strtotime($v['transTime']));
                            $v['isPay'] = $oldPlan['isPay']?'是':'否';
                            $v['planDateYmd'] = date('Y-m-d',strtotime($oldPlan['planDateYmd']));
                            $v['fix'] = $oldPlan['fix']?'是':'否';
                            $v['repay'] = \Prj\Consts\PayGW::$status[$oldPlan['repay']];
                            $tmp = [];
                            foreach($header as $kk=>$vv){
                                $tmp[$kk] = $v[$kk];
                            }
                            $newList[] = $tmp;
                        }
                        if($addInterestSub || $sendInterestSub){
                            if(!$oldPlan['fix'] || $sendInterestSub){
                                if(empty($oldPlan['amount'])){
                                    return $this->returnError(__LINE__.'#代码错误!');
                                }
                                $key = end(array_keys($oldReturnPlan->calendar));
                                $oldReturnPlan->calendar[$key]['interestSub'] = $plan['interestSub'];
                                $oldReturnPlan->calendar[$key]['fix'] = 1;
                                if($oldPlan['isPay']==1 && $sendInterestSub)$oldReturnPlan->calendar[$key]['repay'] = 1;
                                $invest = \Prj\Data\Investment::getCopy($ordersId);
                                $invest->load();
                                if(!$invest->exists() || empty($oldReturnPlan->calendar[$key])){
                                    return $this->returnError(__LINE__.'#代码错误!');
                                }
                                try{
                                    $invest->setField('returnPlan',$oldReturnPlan->decode());
                                    $invest->update();
                                    \Prj\Data\UserChangeLog::addLog('addInterestSub',0,[
                                        'ordersId'=>$ordersId,
                                        'interestSub'=>$plan['interestSub'],
                                    ]);
                                    $successNum++;
                                }catch (\ErrorException $e){
                                    var_log('error#更新失败#'.$ordersId);
                                }
                            }

                            if($sendInterestSub){
                                if($oldPlan['isPay']!=1){
                                    return;
                                }
                                if($oldPlan['repay']!=8){
                                    //发送请求
                                    $waresArr[$oldPlan['waresId']] = 1;
                                }
                            }
                        }

                    }else{

                    }
                }else{

                }
            });
        }
        if($addInterestSub){
            return $this->returnOK('共计修复了'.$successNum.'条记录');
        }
        //调用网关
        $rpc = \Sooh\Base\Ini::getInstance()->get('noGW')?self::getRpcDefault('PayGW'):\Sooh\Base\Rpc\Broker::factory('PayGW');
        $sys = \Lib\Services\PayGW::getInstance($rpc , true);
        if($sendInterestSub){
            $num = 0;
            $errorNum = 0;
            if($waresArr){
                foreach($waresArr as $k=>$v){
                    try {
                        $data = [
                            $k
                        ];
                        var_log($data,'发送给网关的参数>>>>>>>>>>>>>>>>>>>>');
                        // return $this->returnError('中断>>>');
                        $ret = call_user_func_array([$sys,'returnInterestSub'],$data);
                        $this->_view->assign('retAll', ['ret' => 'ok', 'got' => $ret]);
                    } catch (\Sooh\Base\ErrException $e) {
                        $errorNum ++ ;
                        $code = $e->getCode();
                        if ($code == 400) {
                           // return $this->returnError($e->getMessage());
                            continue;
                        } elseif ($code == 500) {
                           // return $this->returnError($e->getMessage());
                            continue;
                        }
                        // return $this->returnError('gw_error');
                        //return $this->returnError(\Prj\Lang\Broker::getMsg('system.gw_error'));
                        continue;
                    }
                    if($ret['status'] == 1){
                        $num ++ ;
                    }else{
                        $errorNum ++ ;
                    }
                }
            }
            return $this->returnOK($num.'个标的处理成功,'.$errorNum.'个标的处理失败');
        }
       // var_log($newList,'newList >>> ');
        $this->_view->assign('rs',$newList);
        $this->_view->assign('header',$header);
    }


}