<?php

class ModetestController extends \Prj\BaseCtrl {
public function indexAction(){
    
    $str=new \Lib\SMS\Liulangmao();
    $rs=$str->sendMarket('13916697284,15656189809','好厉害！');
    var_log($rs,'rs>>>>>>rs<<<<<<<<');
}
public function ceAction(){
    
    $where=[
        'amountExt]'=>'0',
        'amountExt['=>'400.99',
        'ymd]'=>'20160426',
        'orderStatus!'=>['-1','-4','0','4'],
    ];
    
    $db_rpt = \Sooh\DB\Broker::getInstance();
    
    $arr_user_id=$db_rpt->getCol('db_p2prpt.tb_orders_final','DISTINCT userId',$where);
    
    
    $i=1;
    $total=sizeof($arr_user_id);
    
    
    foreach ($arr_user_id as $userId){
        
        $user=$db_rpt->getCol('db_p2prpt.tb_user_final','amountSecBuy',['userId'=>$userId]);
        
        if(!empty($user)){
            $u[]=$user;
        }
        
    }
    $u=sizeof($u);
    
    $where1=[
        'amountSecBuy!'=>'',
        //'ymdReg]'=>'20160426',
    ];
    $userId=$db_rpt->getCol('db_p2prpt.tb_user_final','amountSecBuy',$where1);
    $userId=sizeof($userId);
    
    $after_investment_rate= sprintf('%.2f',($u/$userId*100)).'%';
    
    error_log('C类用户数：'.$total.'    C类复投用户数'.$u.'    C类用户复投比例'.$after_investment_rate);
    
    
}

public  function addAction(){
    
    $db_rpt = \Sooh\DB\Broker::getInstance();
    
  
    
    $j=date('Ymd',time());
    
    
   for($i='20160318';$i<=$j;$i++){
       
       $where1=[
           'descCreate'=>'签到奖励',
           'ymdCreate'=>$i,
       ];
       
       $where2=[
           'descCreate'=>'签到奖励',
           'ymdCreate'=>$i,
           'ymdUsed'=>'',
       ];
       
       $ymdCreate=$db_rpt->getCol('db_p2prpt.tb_vouchers_final','count(*)',$where1);
       $ymdCreate=$ymdCreate[0];
   
       if($ymdCreate=='0'){ 
           continue;
       }else{
 
       $ymdUse=$db_rpt->getCol('db_p2prpt.tb_vouchers_final','count(*)',$where2);
       $ymdUse=$ymdUse[0];
           
       $Failurerate=sprintf('%.2f',($ymdUse/$ymdCreate*100)).'%';
           
           $rs=[
               'ymd'=>$i,
               'ymdCreateAmount'=>$ymdCreate,
               'ymdUsedAmount' =>$ymdUse,
               'Failurerate'=>$Failurerate,
              
           ];
           
           $tmp[]=$rs;
       }

   }

}


public  function delAction(){
    
        $rs=new \Lib\Api\Umeng\Umeng();
        //$result=$rs->get_token();
        $result=$rs->auth_token(10, 1);
       // $result=$rs->new_users();
        // $result=base64_encode("zhaoyuguang@kkdai.com.cn:zyg315");
         
        var_log($result, 'record>>>>>>>>>>>>');
}

}