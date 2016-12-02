<?php
/**
 * 支付网关 查询   
 * 显示扣款列表
 * @author wu.peng
 * Data 2016/5/10
 * Time 2:09
 **/


class CollecttradeController extends \Prj\ManagerCtrl{
    
    protected $pageSizeEnum = [20, 50, 100];
    
    public function indexAction(){
        
        $pageid = $this->_request->get('pageId', 1) - 0;
        $pagesize = $this->_request->get('pageSize', current($this->pageSizeEnum)) - 0;
        $pager = new \Sooh\DB\Pager($pagesize, $this->pageSizeEnum, false);
        
        $out_trade_no = $this->_request->get('out_trade_no');
        $payer_id = $this->_request->get('payer_id');
        
        var_log($payer_id,'payerid>>>>>>>>>>>>>>>>>>');
        if(!empty($out_trade_no)) {
            $where['out_trade_no'] = $out_trade_no;
        }
        if(!empty($payer_id)){
            $where['payer_id'] = $payer_id;
        }
        
        $fieldsMap = [
            'out_trade_no' => ['支付流水号', 90],
            'out_trade_code' => ['交易code',30],
            'summary' => ['注释', 100],
            'trade_close_time' => ['交易关闭时间',50],
            'payer_id' => ['付款人id', 60],
            'invest_sn' => ['投资单号',60],
            'remark' => ['请求时间', 80],
            'amount' => ['金额', 30],
            'randomSN' => ['企业垫付扣款流水号', 60],
            'transSN' => ['满标转账流水号', 50],
        ];
        
        $header = [];
        foreach($fieldsMap as $v) {
            $header[$v[0]] = $v[1];
        }
        
        
        //reset($fieldsMap);
        $tmp=[];
        $arr=[];
        $records=[];
        if(!empty($where)) {
            $db = \Sooh\DB\Broker::getInstance();
            $recordsCount=$db->getRecordCount('db_p2ppay.collect_trade',$where);
            $pager->init($recordsCount, $pageid);
            $record = $db->getRecords('db_p2ppay.collect_trade', implode(',', array_keys($fieldsMap)), $where,'rsort remark',$pager->page_size,$pager->rsFrom());
          //  var_log(\Sooh\DB\Broker::lastCmd(),'sql>>>>>>>>>>>>>>>');
            foreach ($record as $v){
                foreach ($fieldsMap as $kk=>$vv){
                  $tmp[$kk]=$v[$kk];
                }
                $new[]=$tmp;
                
            }
         $arr=$new;
        foreach ($arr as $v){
           $v['remark']=date('Y-m-d H:i:s', strtotime($v['remark']));
            $rpt[]=$v;
        }
        $records=$rpt;
       // var_log($records,'records>>>>>>>>>>>>>>>>>>>');
        $this->_view->assign('records', $records);
        $this->_view->assign('tableHeaders', $header);
        $this->_view->assign('out_trade_no', $out_trade_no);
        $this->_view->assign('payer_id', $payer_id);
        $this->_view->assign('pager', $pager);
        $this->_view->assign('pageid', $pageid);
        $this->_view->assign('pagesize', $pagesize);
    }
    
    else{
        $db = \Sooh\DB\Broker::getInstance();
        $record = $db->getRecords('db_p2ppay.collect_trade', implode(',', array_keys($fieldsMap)),null,' rsort remark','20',$pager->rsFrom());
        
        foreach ($record as $v){
            foreach ($fieldsMap as $kk=>$vv){
                $tmp[$kk]=$v[$kk];
            }
            $new[]=$tmp;
        
        }
        $arr=$new;
        foreach ($arr as $v){
            $v['remark']=date('Y-m-d H:i:s', strtotime($v['remark']));
            $rpt[]=$v;
        }
        $records=$rpt;
        // var_log($records,'records>>>>>>>>>>>>>>>>>>>');
        $this->_view->assign('records', $records);
        $this->_view->assign('tableHeaders', $header);
        $this->_view->assign('out_trade_no', $out_trade_no);
        $this->_view->assign('payer_id', $payer_id);
        $this->_view->assign('pager', $pager);
        $this->_view->assign('pageid', $pageid);
        $this->_view->assign('pagesize', $pagesize);
        }
    
    }
  
}