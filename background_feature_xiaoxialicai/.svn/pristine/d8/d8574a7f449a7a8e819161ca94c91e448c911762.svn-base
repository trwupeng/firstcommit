<?php
/**
 * 支付网关 查询   
 * 显示公司流水记录
 * @author wu.peng
 * Data 2016/5/10
 * Time 2:50
 **/


class CompanyprofitController extends \Prj\ManagerCtrl{
    
    protected $pageSizeEnum = [20, 50, 100];
    
    public function indexAction(){
       
        $pageid = $this->_request->get('pageId', 1) - 0;
        $pagesize = $this->_request->get('pageSize', current($this->pageSizeEnum)) - 0;
        $pager = new \Sooh\DB\Pager($pagesize, $this->pageSizeEnum, false);
        
        $borrowerId = $this->_request->get('borrowerId');
        $waresId = $this->_request->get('waresId');
        
        if(!empty($borrowerId)) {
            $where['borrowerId'] = $borrowerId;
        }
        if(!empty($waresId)){
            $where['waresId'] = $waresId;
        }
        
          $fieldsMap = [
            'SN' => ['SN', 50],
            'borrowerId' => ['借款人id', 50],
            'step' => ['注释', 40],
            'amountFee' => ['手续费',20],
            'orderAmount' => ['订单金额', 20],
            'waresId' => ['标号',60],
            'requestTime' => ['请求时间', 60],
            'summary' => ['订单描述', 60],
            'collectRandomSN' => ['扣款请求号', 60],
        ];
        
        $header = [];
        foreach($fieldsMap as $v) {
            $header[$v[0]] = $v[1];
        }
        
        
       // reset($fieldsMap);
        $tmp=[];
        $records=[];
        $arr=[];
        if(!empty($where)) {
            $db = \Sooh\DB\Broker::getInstance();
            $recordsCount=$db->getRecordCount('db_p2ppay.db_company_profit',$where);
            $pager->init($recordsCount, $pageid);
            $record = $db->getRecords('db_p2ppay.db_company_profit', implode(',', array_keys($fieldsMap)), $where,'requestTime',$pager->page_size,$pager->rsFrom());
//             var_log(\Sooh\DB\Broker::lastCmd(),'sql>>>>>>>>>>>>>>>');
            foreach ($record as $v){
                foreach ($fieldsMap as $kk=>$vv){
                  $tmp[$kk]=$v[$kk];
                }
                $new[]=$tmp;
                
            }
          $arr=$new;
          foreach ($arr as $v){
              if($v['step']==1){
                  $v['step']='满标转账收取';
              }else{
                  $v['step']='借款人还钱收取';
              }
              $v['amountFee']/=100;
              $v['orderAmount']/=100;
              $rpt[]=$v;
          }
          $records=$rpt;
          
        $this->_view->assign('records', $records);
        $this->_view->assign('tableHeaders', $header);
        $this->_view->assign('borrowerId', $borrowerId);
        $this->_view->assign('waresId', $waresId);
        $this->_view->assign('pager', $pager);
        $this->_view->assign('pageid', $pageid);
        $this->_view->assign('pagesize', $pagesize);
    }else{
        $db = \Sooh\DB\Broker::getInstance();
        $record = $db->getRecords('db_p2ppay.db_company_profit', implode(',', array_keys($fieldsMap)),null,'rsort requestTime','20',$pager->rsFrom());
        foreach ($record as $v){
            foreach ($fieldsMap as $kk=>$vv){
                $tmp[$kk]=$v[$kk];
            }
            $new[]=$tmp;
        
        }
        $arr=$new;
        foreach ($arr as $v){
            if($v['step']==1){
                $v['step']='满标转账收取';
            }else{
                $v['step']='借款人还钱收取';
            }
            $v['amountFee']/=100;
            $v['orderAmount']/=100;
            $rpt[]=$v;
        }
        $records=$rpt;
        
        $this->_view->assign('records', $records);
        $this->_view->assign('tableHeaders', $header);
        $this->_view->assign('borrowerId', $borrowerId);
        $this->_view->assign('waresId', $waresId);
        $this->_view->assign('pager', $pager);
        $this->_view->assign('pageid', $pageid);
        $this->_view->assign('pagesize', $pagesize);
    }
    
    }
    
    
    /**
     * 支付网关 查询
     * 显示投资记录
     * @author wu.peng
     * Data 2016/5/10
     * Time 7:50
     **/
    
    public function investmentAction(){
        
        $userid=$this->_request->get('_userId');
        
        
        $pageid = $this->_request->get('pageId', 1) - 0;
        $pagesize = $this->_request->get('pageSize', current($this->pageSizeEnum)) - 0;
        $pager = new \Sooh\DB\Pager($pagesize, $this->pageSizeEnum, false);
        
        
        $orderId = $this->_request->get('orderId');
        $waresId = $this->_request->get('waresId');
        $userId = $this->_request->get('userId');
        
        var_log($userId,'>>>>>>>>>>>>>>>>>>>>>>>>>>>');
        
        if(!empty($orderId)) {
            $where['orderId'] = $orderId;
        }
        if(!empty($waresId)){
            $where['waresId'] = $waresId;
        }
        if(!empty($userId)){
            $where['userId'] = $userId;
        }
        
        $fieldsMap = [
            'orderId' => ['购买流水号', 60],
            'waresId' => ['商品id', 50],
            'userId' => ['用户id', 50],
            'amount' => ['金额',30],
            'amountExt' => ['红包金额(可提现)', 50],
            'amountFake' => ['红包金额(不可提现)',60],
            'unfreezeOrderId' => ['解冻订单号', 60],
            'extDesc' => ['描述', 60],
            'orderTime' => ['订单时间', 80],
            'orderStatus' => ['订单状态', 60],
            'remark' => ['处理完成时间', 80],
        ];
        
        $header = [];
        foreach($fieldsMap as $v) {
            $header[$v[0]] = $v[1];
        }
        
        
        // reset($fieldsMap);
        $tmp=[];
        $records=[];
        $arr=[];
        if(!empty($where)) {
            $db = \Sooh\DB\Broker::getInstance();
            $recordsCount=$db->getRecordCount('db_p2ppay.investment',$where);
            $pager->init($recordsCount, $pageid);
            $record = $db->getRecords('db_p2ppay.investment', implode(',', array_keys($fieldsMap)), $where,'rsort orderTime',$pager->page_size,$pager->rsFrom());
            //             var_log(\Sooh\DB\Broker::lastCmd(),'sql>>>>>>>>>>>>>>>');
            foreach ($record as $v){
                foreach ($fieldsMap as $kk=>$vv){
                    $tmp[$kk]=$v[$kk];
                }
                $new[]=$tmp;
        
            }
            $arr=$new;
            
            foreach ($arr as $v){
               $v['amount']/=100;
               $v['amountExt']/=100;
               $v['amountFake']/=100;
               $v['orderTime']=date('Y-m-d H:i:s', strtotime($v['orderTime']));
               $v['remark']=date('Y-m-d H:i:s', strtotime($v['remark']));
               if($v['orderStatus']=='2'){
                   $v['orderStatus']='购买成功';
               }elseif($v['orderStatus']=='PAY_FINISHED' or $v['orderStatus']='TRADE_FINISHED'){
                   $v['orderStatus']='交易完成';
               }
              $rpt[]=$v;
            }
             
            $records=$rpt;
           // var_log($records,'>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>');
            $this->_view->assign('records', $records);
            $this->_view->assign('tableHeaders', $header);
            $this->_view->assign('orderId', $orderId);
            $this->_view->assign('userId', $userId);
            $this->_view->assign('waresId', $waresId);
            $this->_view->assign('pager', $pager);
            $this->_view->assign('pageid', $pageid);
            $this->_view->assign('pagesize', $pagesize);
    }else{
       
        if(empty($userid)){
        $db = \Sooh\DB\Broker::getInstance();
        $record = $db->getRecords('db_p2ppay.investment', implode(',', array_keys($fieldsMap)), null,'rsort orderTime','20',$pager->rsFrom());
        }else{
          
            $where['userId']=$userid;
            var_log($userid,'>>>>>>>>>>>>>>>>>>>>>>>');
            $db = \Sooh\DB\Broker::getInstance();
            $recordsCount=$db->getRecordCount('db_p2ppay.investment',$where);
            $pager->init($recordsCount, $pageid);
            $record = $db->getRecords('db_p2ppay.investment', implode(',', array_keys($fieldsMap)),$where,'rsort orderTime',$pager->total,$pager->rsFrom());
            error_log('__CLASS__'.\Sooh\DB\Broker::lastCmd());
            
        }
        foreach ($record as $v){
            foreach ($fieldsMap as $kk=>$vv){
                $tmp[$kk]=$v[$kk];
            }
            $new[]=$tmp;
        
        }
        $arr=$new;
        
        foreach ($arr as $v){
            $v['amount']/=100;
            $v['amountExt']/=100;
            $v['amountFake']/=100;
            $v['orderTime']=date('Y-m-d H:i:s', strtotime($v['orderTime']));
            $v['remark']=date('Y-m-d H:i:s', strtotime($v['remark']));
            if($v['orderStatus']=='2'){
                $v['orderStatus']='购买成功';
            }elseif($v['orderStatus']=='PAY_FINISHED' or $v['orderStatus']='TRADE_FINISHED'){
                $v['orderStatus']='交易完成';
            }
            $rpt[]=$v;
        }
         
        $records=$rpt;
      //  var_log($records,'>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>');
        $this->_view->assign('records', $records);
        $this->_view->assign('tableHeaders', $header);
//         $this->_view->assign('orderId', $orderId);
//         $this->_view->assign('userId', $userId);
//         $this->_view->assign('waresId', $waresId);
//         $this->_view->assign('pager', $pager);
//         $this->_view->assign('pageid', $pageid);
//         $this->_view->assign('pagesize', $pagesize);
    }
}

/**
 * 支付网关 查询
 * 显示充值记录
 * @author wu.peng
 * Data 2016/5/11
 * Time 10:50
 **/

public function rechargeAction(){
    
    $userid=$this->_request->get('_userId');
    
    $pageid = $this->_request->get('pageId', 1) - 0;
    $pagesize = $this->_request->get('pageSize', current($this->pageSizeEnum)) - 0;
    $pager = new \Sooh\DB\Pager($pagesize, $this->pageSizeEnum, false);
    
    
    $fieldsMap = [
        'SN' => ['订单号', 60],
        'userId' => ['用户id', 50],
        'bankId' => ['银行卡类型',30],
        'bankCard' => ['银行卡号', 70],
        'amount' => ['金额',60],
        'userIP' => ['用户ip', 60],
        'cardId' => ['绑卡卡号', 60],
        'remark1' => ['处理完成时间', 50],
        'status' => ['状态', 60],
    ];
    
    $SN = $this->_request->get('SN');
    $userId = $this->_request->get('userId');
    
    if(!empty($SN)) {
        $where['SN'] = $SN;
    }
    if(!empty($userId)){
        $where['userId'] = $userId;
    }
 
    $header = [];
    foreach($fieldsMap as $v) {
        $header[$v[0]] = $v[1];
    }
    
    $tmp=[];
    $records=[];
    $arr=[];
    if(!empty($where)) {
        $db = \Sooh\DB\Broker::getInstance();
        $recordsCount=$db->getRecordCount('db_p2ppay.recharge',$where);
        $pager->init($recordsCount, $pageid);
        $record = $db->getRecords('db_p2ppay.recharge', implode(',', array_keys($fieldsMap)), $where,'rsort remark1',$pager->page_size,$pager->rsFrom());
        //             var_log(\Sooh\DB\Broker::lastCmd(),'sql>>>>>>>>>>>>>>>');
        foreach ($record as $v){
            foreach ($fieldsMap as $kk=>$vv){
                $tmp[$kk]=$v[$kk];
            }
            $new[]=$tmp;
    
        }
        $arr=$new;
    
    foreach ($arr as $v){
       $v['bankCard']= substr_replace($v['bankCard'], '**********', 6, 10);
       $v['bankId']= \Prj\Consts\Banks::$enums[strtolower($v['bankId'])][0];
       $v['amount']/=100;
       $v['remark1']=date('Y-m-d H:i:s', strtotime($v['remark1']));
       if($v['status']=='P'){
           $v['status']='购买完成未回调';
       }elseif($v['status']=='SUCCESS'){
           $v['status']='购买成功';
       }
       $rpt[]=$v;
    }
    
    $records=$rpt;
    $this->_view->assign('records', $records);
    $this->_view->assign('tableHeaders', $header);
    $this->_view->assign('SN', $SN);
    $this->_view->assign('userId', $userId);
    $this->_view->assign('pager', $pager);
    $this->_view->assign('pageid', $pageid);
    $this->_view->assign('pagesize', $pagesize);
}else{
    
    if(empty($userid)){
    $db = \Sooh\DB\Broker::getInstance();
    $record = $db->getRecords('db_p2ppay.recharge', implode(',', array_keys($fieldsMap)),null,'rsort remark1','20',$pager->rsFrom());
    }else{
        $db = \Sooh\DB\Broker::getInstance();
        $recordsCount=$db->getRecords('db_p2ppay.recharge', implode(',', array_keys($fieldsMap)),['userId'=>$userid]);
        $pager->init(count($recordsCount), $pageid);
        $record = $db->getRecords('db_p2ppay.recharge', implode(',', array_keys($fieldsMap)),['userId'=>$userid],'rsort remark1',$pager->total,$pager->rsFrom());
    }
 
    foreach ($record as $v){
        foreach ($fieldsMap as $kk=>$vv){
            $tmp[$kk]=$v[$kk];
        }
        $new[]=$tmp;
    
    }
    $arr=$new;
    
    foreach ($arr as $v){
        $v['bankCard']= substr_replace($v['bankCard'], '**********', 6, 10);
        $v['bankId']= \Prj\Consts\Banks::$enums[strtolower($v['bankId'])][0];
        $v['amount']/=100;
        $v['remark1']=date('Y-m-d H:i:s', strtotime($v['remark1']));
        if($v['status']=='P'){
            $v['status']='购买完成未回调';
        }elseif($v['status']=='SUCCESS'){
            $v['status']='购买成功';
        }
        $rpt[]=$v;
    }
    
    $records=$rpt;
    $this->_view->assign('records', $records);
    $this->_view->assign('tableHeaders', $header);
//     $this->_view->assign('SN', $SN);
//     $this->_view->assign('userId', $userId);
//     $this->_view->assign('pager', $pager);
//     $this->_view->assign('pageid', $pageid);
//     $this->_view->assign('pagesize', $pagesize);
}

}


/**
 * 支付网关 查询
 * 显示提现记录
 * @author wu.peng
 * Data 2016/5/11
 * Time 11:30
 **/
public function withdrawAction(){
    
    
    $userid=$this->_request->get('userId');
    
    $pageid = $this->_request->get('pageId', 1) - 0;
    $pagesize = $this->_request->get('pageSize', current($this->pageSizeEnum)) - 0;
    $pager = new \Sooh\DB\Pager($pagesize, $this->pageSizeEnum, false);
    
    $fieldsMap = [
        'BatchId' => ['批次号', 70],
        'out_trade_no' => ['订单号', 80],
        'fee' => ['手续费',30],
        'account_type' => ['账号类型', 70],
        'amount' => ['金额',60],
        'card_id' => ['绑卡id', 60],
        'userId' => ['用户id', 60],
        'success_time' => ['成功时间', 80],
        'withdraw_status' => ['提现状态', 40],
        'remark' => ['提现说明', 40],
        'requestTime' => ['请求时间', 80],
    ];
    
    $out_trade_no = $this->_request->get('out_trade_no');
    $userId = $this->_request->get('userId');
    
    if(!empty($out_trade_no)) {
        $where['out_trade_no'] = $out_trade_no;
    }
    if(!empty($userId)){
        $where['userId'] = $userId;
    }
    
    $header = [];
    foreach($fieldsMap as $v) {
        $header[$v[0]] = $v[1];
    }
    
    
    
    $tmp=[];
    $records=[];
    $arr=[];
    if(!empty($where)) {
        $db = \Sooh\DB\Broker::getInstance();
        $recordsCount=$db->getRecordCount('db_p2ppay.withdraw',$where);
        $pager->init($recordsCount, $pageid);
        $record = $db->getRecords('db_p2ppay.withdraw', implode(',', array_keys($fieldsMap)), $where,'rsort success_time',$pager->page_size,$pager->rsFrom());
        //             var_log(\Sooh\DB\Broker::lastCmd(),'sql>>>>>>>>>>>>>>>');
        foreach ($record as $v){
            foreach ($fieldsMap as $kk=>$vv){
                $tmp[$kk]=$v[$kk];
            }
            $new[]=$tmp;
    
        }
        $arr=$new;
        
        foreach ($arr as $v){
            $v['fee']/=100;
            $v['amount']/=100;
            $v['requestTime']=date('Y-m-d H:i:s', strtotime($v['requestTime']));
            if($v['withdraw_status']=='SUCCESS'){
                $v['withdraw_status']='成功';
            }elseif($v['withdraw_status']=='failed' or $v['withdraw_status']=='400' ){
                $v['withdraw_status']='失败';
            }
            $rpt[]=$v;
        }
        
        $records=$rpt;
        $this->_view->assign('records', $records);
        $this->_view->assign('tableHeaders', $header);
        $this->_view->assign('SN', $SN);
        $this->_view->assign('userId', $userId);
        $this->_view->assign('pager', $pager);
        $this->_view->assign('pageid', $pageid);
        $this->_view->assign('pagesize', $pagesize);
    }else{
        if(empty($userid)){
        $db = \Sooh\DB\Broker::getInstance();
        $record = $db->getRecords('db_p2ppay.withdraw', implode(',', array_keys($fieldsMap)),null,'rsort success_time','20',$pager->rsFrom());
        }else{
            $db = \Sooh\DB\Broker::getInstance();
            $recordsCount=$db->getRecords('db_p2ppay.withdraw',implode(',', array_keys($fieldsMap)),['userId'=>$userId]);
            $pager->init(count($recordsCount), $pageid);
            $record = $db->getRecords('db_p2ppay.withdraw', implode(',', array_keys($fieldsMap)),['userId'=>$userId],'rsort success_time',$pager->total,$pager->rsFrom());
        }
        foreach ($record as $v){
            foreach ($fieldsMap as $kk=>$vv){
                $tmp[$kk]=$v[$kk];
            }
            $new[]=$tmp;
        
        }
        $arr=$new;
        
        foreach ($arr as $v){
            $v['fee']/=100;
            $v['amount']/=100;
            $v['requestTime']=date('Y-m-d H:i:s', strtotime($v['requestTime']));
            if($v['withdraw_status']=='SUCCESS'){
                $v['withdraw_status']='成功';
            }elseif($v['withdraw_status']=='failed' or $v['withdraw_status']=='400'){
                $v['withdraw_status']='失败';
            }
            $rpt[]=$v;
        }
        
        $records=$rpt;
        $this->_view->assign('records', $records);
        $this->_view->assign('tableHeaders', $header);
//         $this->_view->assign('SN', $SN);
//         $this->_view->assign('userId', $userId);
//         $this->_view->assign('pager', $pager);
//         $this->_view->assign('pageid', $pageid);
//         $this->_view->assign('pagesize', $pagesize);
    }
}


/**
 * 支付网关 查询
 * 显示流水记录
 * @author wu.peng
 * Data 2016/5/11
 * Time 13:40
 **/


public  function  recordAction(){
    
    $pageid = $this->_request->get('pageId', 1) - 0;
    $pagesize = $this->_request->get('pageSize', current($this->pageSizeEnum)) - 0;
    $pager = new \Sooh\DB\Pager($pagesize, $this->pageSizeEnum, false);
   
    $fieldsMap = [
        'SN' => ['流水号', 70],
        'userId' => ['用户id', 80],
        'service' => ['服务描述',50],
        'amount' => ['金额',30],
        'sumarry' => ['描述', 30],
        'req_time' => ['请求时间', 50],
    ];
    
    $SN = $this->_request->get('SN');
    $userId = $this->_request->get('userId');
    
    
    if(!empty($SN)) {
        $where['SN'] = $SN;
    }
    if(!empty($userId)){
        $where['userId'] = $userId;
    }
    
    $header = [];
    foreach($fieldsMap as $v) {
        $header[$v[0]] = $v[1];
    }
    
    
    
    $tmp=[];
    $records=[];
    $arr=[];
    if(!empty($where)) {
        $db = \Sooh\DB\Broker::getInstance();
        $recordsCount=$db->getRecordCount('db_p2ppay.db_record',$where);
        $pager->init($recordsCount, $pageid);
        $record = $db->getRecords('db_p2ppay.db_record', implode(',', array_keys($fieldsMap)), $where,'rsort req_time',$pager->page_size,$pager->rsFrom());
       //  var_log(\Sooh\DB\Broker::lastCmd(),'sql>>>>>>>>>>>>>>>');
        foreach ($record as $v){
            foreach ($fieldsMap as $kk=>$vv){
                $tmp[$kk]=$v[$kk];
            }
            $new[]=$tmp;
    
        }
        $arr=$new;
        
        foreach ($arr as $v){
            $v['amount']/=100;
            $rpt[]=$v;
        }
        
        $records=$rpt;
        $this->_view->assign('records', $records);
        $this->_view->assign('tableHeaders', $header);
        $this->_view->assign('SN', $SN);
        $this->_view->assign('userId', $userId);
        $this->_view->assign('pager', $pager);
        $this->_view->assign('pageid', $pageid);
        $this->_view->assign('pagesize', $pagesize);
}
else{
        $db = \Sooh\DB\Broker::getInstance();
        $record = $db->getRecords('db_p2ppay.db_record', implode(',', array_keys($fieldsMap)),null,'rsort req_time','20',$pager->rsFrom());
        //             var_log(\Sooh\DB\Broker::lastCmd(),'sql>>>>>>>>>>>>>>>');
        foreach ($record as $v){
            foreach ($fieldsMap as $kk=>$vv){
                $tmp[$kk]=$v[$kk];
            }
            $new[]=$tmp;
    
        }
        $arr=$new;
    
        foreach ($arr as $v){
            $v['amount']/=100;
            $rpt[]=$v;
        }
    
        $records=$rpt;
        
        $this->_view->assign('records', $records);
        $this->_view->assign('tableHeaders', $header);
        $this->_view->assign('SN', $SN);
        $this->_view->assign('userId', $userId);
        $this->_view->assign('pager', $pager);
        $this->_view->assign('pageid', $pageid);
        $this->_view->assign('pagesize', $pagesize);
}
}
}
