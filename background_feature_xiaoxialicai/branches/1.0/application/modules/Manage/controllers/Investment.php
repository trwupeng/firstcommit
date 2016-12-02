<?php
use Sooh\Base\Form\Item as form_def;
use Prj\Data\Investment as Investment;

/**
 * 订单
 * By Hand
 */
class InvestmentController extends \Prj\ManagerCtrl
{
    public function indexAction()
    {
        $lastPage = $this->_request->get('lastPage');
        $pageid = $this->_request->get('pageId', 1) - 0;
        $isDownloadEXCEL = $this->_request->get('__EXCEL__');
        $pager = new \Sooh\DB\Pager(10, $this->pageSizeEnum, false);
        $pager->page_size = $this->_request->get('pageSize', 50);
        $pager->init(-1,$pageid);
        //>>>search
        $frm = \Sooh\Base\Form\Broker::getCopy('default')
            ->init(\Sooh\Base\Tools::uri(), 'get', \Sooh\Base\Form\Broker::type_s);
        $frm->addItem('_ordersId_eq', form_def::factory('订单号', '', form_def::text))
            ->addItem('_orderStatus_eq', form_def::factory('状态', '', form_def::select,\Prj\Consts\OrderStatus::$enum+[''=>'']))
            ->addItem('_userId_eq', form_def::factory('用户ID', '', form_def::text))
            ->addItem('_waresId_eq', form_def::factory('标的号', '', form_def::text))
            ->addItem('pageid', $pageid)
            ->addItem('pagesize', $this->pager->page_size);
        $frm->fillValues();
        if ($frm->flgIsThisForm) { //submit
            $where = $frm->getWhere();
            if($_pkey=$this->_request->get('_pkey_val_'))
            {
                var_log($_pkey,'pkey>>>>>>>>');
                $where['ordersId'] = \Prj\Misc\View::decodePkey($_pkey)['ordersId'];
            }
        } else {
            $where = array();
        }
      
        //>>>

        $fieldsMapArr = array(
            'ordersId'=>['订单号','160'],
            'waresId'=>['标的号','137'],
            'waresName'=>['标的名称','120'],
            'userId'=>['用户ID','120'],
            'amount'=>['实投金额/红包','90'],
            'amountExt'=>['红包','35'],
            //'amountFake'=>['券金','35'],
            'yieldStatic'=>['固定年化/活动加息','115'],
            'yieldStaticAdd'=>['活动加息','60'],
            'interest'=>['本金收益/奖励收益','115'],
            'interestExt'=>['奖励收益','60'],
            //'extDesc'=>['赠送说明','105'],
            'orderTime'=>['下单时间/下次还款日','210'],
            'orderStatus'=>['订单状态','120'],
            'returnType'=>['还款方式','112'],
            'returnNext'=>['下次还款日','60'],
        );
        $search = $this->_request->get('where');
        //$where['orderStatus!'] = \Prj\Consts\OrderStatus::abandon;
        if(!empty($search))$where = array_merge($where,$search);
        //$rs = Investment::loopAll($where);

        //if(empty())
      //  $where['orderStatus!'] = \Prj\Consts\Wares::status_abandon;
        $where['orderStatus!']=\Prj\Consts\OrderStatus::abandon;
        $total = Investment::loopGetRecordsCount($where);
        $pager->total = $total;
       // var_log($where,'where >>> ');
       // var_log($total,'total>>>');
       
       if($isDownloadEXCEL){
           $rs=Investment::loopAll($where);
          // var_log($rs,'>>>>>>>>>>>>>>>>>>>>>>');
       }else{
        if($pager->pageid()==1){
            //var_log('>>>>>> pageId = 1');
            $ret = Investment::loopGetRecordsPage(['orderTime'=>'rsort'],['where'=>$where],$pager);
           // var_log(\Sooh\DB\Broker::lastCmd(false),'sql>>>');
        }else{
           // var_log('>>>>>> pageId = '.$pager->pageid());
            $lastPage = \Sooh\Base\Session\Data::getInstance()->get('tgh_lastPage');
           // var_log($lastPage,'lastPage>>>');
            $ret = Investment::loopGetRecordsPage(['orderTime'=>'rsort'],$lastPage,$pager);
        }
        \Sooh\Base\Session\Data::getInstance()->set('tgh_lastPage',$ret['lastPage']);
       // var_log($ret);
        $rs = $ret['records'];
       }
       
       //  var_log($rs,'>>>>>>>>>>>>>>>>>>>>>>>>');
       
        $header = array();
        foreach($fieldsMapArr as $k=>$v)
        {
            $header[$v[0]] = $v[1];
        }
        
        unset($header['红包']);
        unset($header['活动加息']);
        unset($header['奖励收益']);
        unset($header['下次还款日']);
        $ids = $this->_request->get('ids');
        if(!empty($ids)){
            $tmp = [];
            foreach($ids as $v){
                $tmp[] = \Prj\Misc\View::decodePkey($v)['ordersId'];
            }
        }
       
        foreach($rs as $v)
        {
            if(!empty($ids)){
                if(!in_array($v['ordersId'],$tmp))continue;
            }
            foreach($fieldsMapArr as $kk=>$vv)
            {
                $temp[$kk] = $v[$kk];
                if(empty($temp[$kk]))$temp[$kk] = '';
            }
            // $orderStatus=$temp['orderStatus'];
            // var_log($orderStatus,'sq>>>>>>>>>>>>>>>>>>>>>>>>>>>>>');
          
            $temp['amount']/=100;
            $temp['amountExt']/=100;
            //$temp['amountFake']/=100;
            $temp['interest']/=100;
            $temp['interestExt']/=100;
            $temp['orderTime'] = \Prj\Misc\View::fmtYmd( $temp['orderTime'],'time');
            $temp['yieldStatic'] = (string)(sprintf("%.2f", $temp['yieldStatic']*100)).'%';
            $temp['yieldStaticAdd'] =sprintf("%.2f", $temp['yieldStaticAdd']*100)!='0.00'?(string)(sprintf("%.2f", $temp['yieldStaticAdd']*100)).'%':'';
            $temp['orderStatus'] = \Prj\Consts\OrderStatus::$enum[$temp['orderStatus']];
            $temp['returnType'] = \Prj\Consts\ReturnType::$enum[$temp['returnType']];
            $temp['_pkey_val_'] = \Prj\Misc\View::encodePkey(['ordersId'=>$v['ordersId']]);
            if(!empty($temp['returnNext']))$temp['returnNext'] = date('Y-m-d',strtotime($temp['returnNext']));
            
            $new[] = $temp;
        }
        $rs = $new;
       // var_log($rs,'sql>>>>>>>>>>>>>>>>>>>>>>>>>');
//          foreach ($rs as $v){
//              if($v['orderStatus']=='失效'){
//                  unset($rs);
//              }
//          }
      //    var_log($rs,'################');
        if(!empty($rs))
        {
            /*
            usort($rs,function($a,$b){
                if($a['orderStatus']==$b['orderStatus'])return 0;
                return $a['orderStatus']>$b['orderStatus']?-1:1;
            });
            */
        }
        if($isDownloadEXCEL){
            foreach($rs as $k=>$v){
                unset($rs[$k]['_pkey_val_']);
            }
            return $this->downEXCEL($rs, array_keys($header),null,true);
        }
        $this->_view->assign('header',$header);
        $this->_view->assign('rs',$rs);
        $this->_view->assign('where',$where);
        $this->_view->assign('pager',$pager);
    }

}
