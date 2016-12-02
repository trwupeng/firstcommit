<?php
/**
 * 支付网关 查询   
 * 显示绑卡一览
 * @author wu.peng
 * Data 2016/5/10
 * Time 2:00
 **/


class BindingbankcardController extends \Prj\ManagerCtrl{
    
    
    protected $pageSizeEnum = [20, 50, 100];
    public function indexAction(){
        
        $pageid = $this->_request->get('pageId', 1) - 0;
        $pagesize = $this->_request->get('pageSize', current($this->pageSizeEnum)) - 0;
        $pager = new \Sooh\DB\Pager($pagesize, $this->pageSizeEnum, false);
        
        
        $userId = $this->_request->get('userId');
        $idCode = $this->_request->get('idCode');
        
       // var_log($idCode,'idcode>>>>>>>>>>>>>>>>>>');
        
        if(!empty($userId)) {
            $where['userId'] = $userId;
        }
        if(!empty($idCode)){
            $where['idCode'] = $idCode;
        }
        
        $fieldsMap = [
            'SN' => ['绑卡流水号码', 50],
            'userId' => ['用户ID',50],
            'realname' => ['真实姓名', 30],
            'phone' => ['手机号码', 40],
            'idCode' => ['身份证账号', 50],
            'bankId' => ['银行卡类型', 30],
            'bankCard' => ['银行卡账号', 55],
            'userIP' => ['用户ip', 40],
            'cardId' => ['绑卡id', 30],
            'requestTime' => ['请求时间', 50],
            'status'=>['绑卡状态',60]
        ];
        
        $header = [];
        foreach($fieldsMap as $v) {
            $header[$v[0]] = $v[1];
        }
        
        
        reset($fieldsMap);
        $tmp=[];
        $records=[];
        $arr=[];
        if(!empty($where)) {
            $db = \Sooh\DB\Broker::getInstance();
            $recordsCount=$db->getRecordCount('db_p2ppay.bindingBankCard',$where);
            $pager->init($recordsCount, $pageid);
            $record = $db->getRecords('db_p2ppay.bindingBankCard', implode(',', array_keys($fieldsMap)), $where,'rsort requestTime',$pager->page_size,$pager->rsFrom());
           // var_log(\Sooh\DB\Broker::lastCmd(),'sql>>>>>>>>>>>>>>>>>>');
         // var_log($record,'record>>>>>>>>>>>>>>>>>>');
           foreach ($record as $v){
                foreach ($fieldsMap as $kk=>$vv){
                  $tmp[$kk]=$v[$kk];
                }
                $new[]=$tmp;
                
            }
          $arr=$new;
       
        foreach ($arr as $v){
            $v['bankCard'] = substr_replace($v['bankCard'], '**********', 6, 10);
            $v['idCode'] = substr_replace($v['idCode'], '********', 6, 8);
            $v['phone']    = substr_replace($v['phone'], '****', 3, 4);
            $v['bankId']= \Prj\Consts\Banks::$enums[strtolower($v['bankId'])][0];
            $v['requestTime']=date('Y-m-d H:i:s', strtotime($v['requestTime']));
            if($v['status']=='A'){
                $v['status']='激活成功';
            }elseif($v['status']=='R'){
                $v['status']='实名成功';
            }elseif($v['status']=='V'){
                $v['status']='认证成功';
            }elseif($v['status']=='B'){
                $v['status']='绑卡初步成功';
            }elseif($v['status']=='S'){
                $v['status']='绑卡成功';
            }
            $rpt[]=$v;
        }
        $records=$rpt;
      //  var_log($records,'records>>>>>>>>>>>>>>>>>>>>>>>>>>>>');
        $this->_view->assign('records', $records);
        $this->_view->assign('tableHeaders', $header);
        $this->_view->assign('userId', $userId);
        $this->_view->assign('idCode', $idCode);
        $this->_view->assign('pager', $pager);
        $this->_view->assign('pageid', $pageid);
        $this->_view->assign('pagesize', $pagesize);
    }else{
        $db = \Sooh\DB\Broker::getInstance();
        $record = $db->getRecords('db_p2ppay.bindingBankCard', implode(',', array_keys($fieldsMap)),null,'rsort requestTime','20',$pager->rsFrom());
        
        
        foreach ($record as $v){
            foreach ($fieldsMap as $kk=>$vv){
                $tmp[$kk]=$v[$kk];
            }
            $new[]=$tmp;
        
        }
        $arr=$new;
         
        foreach ($arr as $v){
            $v['bankCard'] = substr_replace($v['bankCard'], '**********', 6, 10);
            $v['idCode'] = substr_replace($v['idCode'], '********', 6, 8);
            $v['phone']    = substr_replace($v['phone'], '****', 3, 4);
            $v['bankId']= \Prj\Consts\Banks::$enums[strtolower($v['bankId'])][0];
            $v['requestTime']=date('Y-m-d H:i:s', strtotime($v['requestTime']));
            if($v['status']=='A'){
                $v['status']='激活成功';
            }elseif($v['status']=='R'){
                $v['status']='实名成功';
            }elseif($v['status']=='V'){
                $v['status']='认证成功';
            }elseif($v['status']=='B'){
                $v['status']='绑卡初步成功';
            }elseif($v['status']=='S'){
                $v['status']='绑卡成功';
            }
            $rpt[]=$v;
        }
        $records=$rpt;
       // var_log($records,'records>>>>>>>>>>>>>>>>>>>>>>>>>>>>');
        $this->_view->assign('records', $records);
        $this->_view->assign('tableHeaders', $header);
        $this->_view->assign('userId', $userId);
        $this->_view->assign('idCode', $idCode);
        $this->_view->assign('pager', $pager);
        $this->_view->assign('pageid', $pageid);
        $this->_view->assign('pagesize', $pagesize);
    }
 }
}