<?php
namespace Lib\Api\Publishdata;

class wdzj extends Base
{

    /**
     * request 第一步 认证
     * return {"return":1, "data":{" token":"0b11ab07b1f290cb0c32bcd7acce77d9" }}
     * return {"return":0, "error":"login failed"}
     */
    public  function auth()
    {
        $token = $this->_auth('wdzj', $this->_request->get('username'),$this->_request->get('password'));
        if (date('Hi') < 15) {
            $this->_view->assign('return', 0);
            $this->_view->assign('error', 'time forbidden');
        } elseif ($token) {
           // $this->_view->assign('return', 1);
            $this->_view->assign('data', array(
                'token' => $token
            ));
        } else {
            $this->_view->assign('return', 0);
            $this->_view->assign('error', 'login failed');
        }
    }

    /**
     * 第二步：获取指定日期满标的数据
     * 返回：没满标的情况 { "totalPage":"1", "currentPage":"1","totalCount":"0","totalAmount":"0","borrowList":[]}
     * 认证失败 {"error":"token值无效"}
     * 有记录：{"totalPage":"2", "currentPage":"1","totalCount":"2","totalAmount":"160000",
     * "borrowList":[{
     * "title":"第18期 东坡区停车场经营权和苗圃全责任质押担保借款",
     * "amount":"80000.00",
     * ........
     * "successTime":"2014-08-15 10:08:34",
     * "subscribes":[
     * {"status":1,"amount":"45000.00","type":0,"validAmount":"45000.00","addDate":"2014-08-14 17:19:54","subscribeUserName":"savoury"},
     * .........
     * ]
     * },
     * {
     * "title":"第19期 东坡区停车场经营权和苗圃全责任质押担保借款",
     * "amount":"80000.00",
     * "schedule":"100.00",
     * ……
     * }]
     */
    protected $xx_md5;

    protected $xx_warseId;

    protected $pageSize = 0;

    protected $summary = array(
        'totalPage' => 0,
        'currentPage' => 0,
        'totalCount' => 0,
        'totalAmount' => 0
    );

    protected $xx_orderInfo = array(
        '总金额',
        '最早购买时间',
        '最晚购买时间',
        '查的是哪一天'
    );

    public function oneday()
    {
        $date = $this->_request->get('date');
        if (date('Hi') < 15) {
            $this->_view->assign('return', 0);
            $this->_view->assign('error', 'time forbidden');
            return;
        }
        if ($this->_login($this->_request->get('token'))) {
            $rs=$this->_login($this->_request->get('token'));
            $expired=$rs['expired'];
            $time=time();
            if($expired<$time) {
             return $this->_view->assign('error', 'timeout');
            }
            $this->pageSize = $this->_request->get('pageSize') - 0;
            if ($date == 'yyyy-mm-dd')
                $date = date('Y-m-d', \Sooh\Base\Time::getInstance()->timestamp());
            $date = strtotime($date);
            $this->xx_md5 = md5('XX_' . date('Y-m-d', $date));
            $this->fillSummaryAndGetPrdtList($date);
            $this->summary['borrowList'] = $this->getProducts();
            if(empty($this->summary['borrowList'])){
                $this->summary['borrowList']=json_decode([]);
            }
            foreach ($this->summary as $i => $r) {
             
                $this->_view->assign($i, $r);
            }
        } else {
            $this->_view->assign('error', 'token值无效');
        }
    }

    /**
     * 填充记录数，总额，计算页数，根据当前页，算出本次调用要查询的是哪些产品信息并填入this->findThese 供 getProducts()使用
     * 
     * @param $dt 要查询的日期            
     * @var xx_warseId 小虾的产品id
     */
    
    public  function  fillSummaryAndGetPrdtList($dt){
        
        $this->summary['currentPage']=$this->_request->get('page')-0;
        if($this->summary['currentPage']<1)$this->summary['currentPage']=1;
        $db=\Sooh\DB\Broker::getInstance();
        
        $warse=array('1461603176837424',
       '1461504375158925','1461504572115487',
       '1461505572576236','1461505621113109',
       '1461505670447479','1461608291822260',
       '1461611444978464');
        
        $this->xx_orderInfo[3]=date("Y-m-d",$dt);
        $where=array('ymdEndReal'=>date('Ymd',$dt),
        'waresId!'=>$warse);//TDD需要确认标的时间
        
        $prdList=$db->getPair(\Rpt\Tbname::tb_products_final,'waresId', 'amount', $where);
       // var_log(\Sooh\DB\Broker::lastCmd(),'sql>>>>>>>>>>>>>>>');
        $prdIDList=array_keys($prdList);
        
        //验证order表的总金额与product表的总金额是否一致
        foreach ($prdIDList as $k=>$warseId)
        {
            $order_amount=$db->getOne(\Rpt\Tbname::tb_orders_final,'sum(amount+amountExt)',array('waresId'=>$warseId,'orderStatus!'=>['0','-1','-4','4']));
            $product_amount=$db->getOne(\Rpt\Tbname::tb_products_final,'amount',array('waresId'=>$warseId));
            if($order_amount!=$product_amount){
                unset($prdIDList[$k]);
            }
        }  
            //$str=implode('|',$prdIDList);
            // $prdIDList=explode('|', $str);
            $this->summary['totalCount']=sizeof($prdIDList);
            
            if(empty($prdIDList))$this->summary['totalAmount']=0;
            else {
                $values=$db->getCol(\Rpt\Tbname::tb_orders_final,'amount+amountExt',
                array('waresId'=>$prdIDList,'orderStatus!'=>['0','-1','-4','4']));
        
                $this->summary['totalAmount']=(array_sum($values)/100);
//                 if(!empty($values)) $this->summary['totalAmount']=array_sum($values);
//                 else $this->summary['totalAmount']=0;
            }
            
           $rsForm=$this->pageSize*($this->summary['currentPage']-1);
           $rsTo=min(array($rsForm+$this->pageSize,sizeof($prdIDList)));
           for ($i=$rsForm;$i<$rsTo;$i++){
               $this->findThese[]=$prdIDList[$i];
           } 
        
           if(empty($this->summary['totalCount'])){
               $this->summary['totalPage']=1;
           }else{
           $this->summary['totalPage']=ceil($this->summary['totalCount']/$this->pageSize);
           }
           return array($k);
        }   
    
    protected  $findThese=array();
    
    /**
     * 
     * 获取thi->findThese 指定的产品
     * 
     * */
    
    protected  function  getProducts(){
        
        if(empty($this->findThese)){
            return array();
        }else
        {
            $db=\Sooh\DB\Broker::getInstance();
            $rs=$db->getRecords(\Rpt\Tbname::tb_products_final,array('waresId','waresName','yieldStatic','deadLine','dlUnit','amount','yieldStaticAdd','introDisplay'),
                array('waresId'=>$this->findThese));
            var_log($rs,'>>rs>>>>');
            $ids=array();
            foreach ($rs as $r)
            { 
                $ids[]=$r['waresId'];
            }
            $amount=array();
            //$providers=array();
            $tmp=$db->getRecords(\Rpt\Tbname::tb_orders_final,'*',array('waresId'=>$ids,'orderStatus!'=>['0','-1','-4','4']));
            //var_log(\Sooh\DB\Broker::lastCmd(),'tmp>>>>>>>>>');
            foreach ($tmp as $r){
                $amount[$r['waresId']]+=($r['amount']+$r['amountExt'])/100;
            }
          
            $ret=array();
            foreach ($rs as $r){
                $v=array();
                $id=$r['waresId'];
          
                $v['projectId']=$id;
                $v['title']=$r['waresName'];
                $v['amount']=sprintf("%.2f",$r['amount']/100);
                $v['schedule']='100.00';
                $v['interestRate']=sprintf('%.2f',$r['yieldStatic']*100)."%";
                $v['deadline']=$r['deadLine'];
                $v['deadLineUnit']=$r['dlUnit'];
                $type='抵押标';
                $v['type']=$type;
                $v['repaymentType'] =1;
                $userName=json_decode($r['introDisplay'],true);
                $userName=$userName['b']['name'];
                $v['userName']=md5('xiaoxia'.$userName);
                //$v['userName'] =md5('xiaoxia'.$id);       
                $v['loanUrl']="http://www.xiaoxialicai.com";
                //可选空值
                $v['province']='';$v['city']='';$v['userAvatarUrl']='';
                $v['amountUsedDesc']='';$v['revenue']='';
                $v['plateType']='';$v['guarantorsType']='';
                $startTimeRecord=$db->getRecords(\Rpt\Tbname::tb_orders_final,array('ymd','hhiiss'),array('waresId'=>$id),'sort ymd sort hhiiss',1);
                $endTimeRecord=$db->getRecords(\Rpt\Tbname::tb_orders_final,array('ymd','hhiiss'),array('waresId'=>$id),'rsort ymd rsort hhiiss',1);
                $v['successTime']=$this->YmdhhiissConv($endTimeRecord[0]['ymd'],$endTimeRecord[0]['hhiiss']);
                $v['publishTime']=$this->YmdhhiissConv($startTimeRecord[0]['ymd'],$startTimeRecord[0]['hhiiss']);
                $v['reward']=$this->ExtraInterest>0?sprintf("%.2f",  $this->ExtraInterest/$v['amount']*100):0;
                $v['subscribes']=$this->getRoles($id);
                $ret[]=$v;

            }
            //var_log($ret,'ret>>>>');
            return $ret;
            
           
        }
    }
    
    protected  $ExtraInterest=0;
    
    /**
     * 获取对应标的所有购买用户的信息列表
     * 
     * @param $warseId
     * @return int
     * */
    protected  function getRoles($warseId){
        
        $db=\Sooh\DB\Broker::getInstance();
        $fields=array('amount','amountExt','userId','realname','ymd','hhiiss','interestExt');
        
       $rs=$db->getRecords(\Rpt\Tbname::tb_orders_final,$fields,array('ymd]'=>20160425,'ymd['=>20880101,
       'waresId'=>$warseId,'orderStatus!'=>['0','-1','-4','4']));
       
       $roles=array();
       $amount=0;
       $this->ExtraInterest=0;
       foreach ($rs as $r){
           $v=array();
           $v['status']=1;
           $v['amount']=sprintf('%.2f',($r['amount']+$r['amountExt'])/100);
           $v['subscribeUserName']=md5($r['userId']);
           $v['validAmount']=$v['amount'];
           $amount+=$v['validAmount'];
           $v['addDate']=$this->YmdhhiissConv ($r['ymd'], $r['hhiiss']); 
           $v['type']=0;
           $this->ExtraInterest=$r['interestExt'];
           //可为空值
           $v['sourceType']='';
           $roles[]=$v;
       }
       $this->xx_orderInfo[0] = $amount;
       
       if(empty($roles)){
           throw new \ErrorException("no order found for product".$warseId);          
       }
       return $roles;
    }
    
    public  function  YmdhhiissConv($ymd,$hhiiss){
        $hhiiss=sprintf("%06d",$hhiiss);
        if(strpos($ymd,'-'))return ($ymd.' '.implode(':', str_split($hhiiss,2)));
        else return date('Y-m-d',strtotime($ymd)).' '.implode(':',str_split($hhiiss,2));
    }
    
    public  function  error(){
        $this->_view->assign('error',$this->_request->get('error'));
    }
    
   
}