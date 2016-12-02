<?php
/**
 * 网站协议文档接口
 *
 * @author simon.wang
 */
class LicenceController extends \Prj\BaseCtrl{
	public function init() {
		parent::init();
		$this->ini->viewRenderType('echo');
	}

	/**
	 * 注册协议
	 * @input int $clientType
	 * @output HTML
	 */
	public function registerAction()
	{
		echo \Prj\Misc\Licence::register();
	}
	/**
	 * 绑卡协议
	 * @input int $clientType
	 * @output HTML
	 */
	public function bindingAction()
	{
		echo \Prj\Misc\Licence::binding();
	}
	/**
	 * 根据参数，自动拼凑显示对应的协议内容
	 * @input string $userId 用户id
	 * @input string $bankCardIndex 银行卡卡号索引 
	 * @input string $name 协议名称,比如 invest
	 * @input string $ver 协议版本号,比如 1
	 * @input string $waresId  标的id
	 * @input string $waresName  标的名称
	 * @input string $ymd 签署协议的日期;比如2015-4-1
	 */
	public function autoAction()
	{
		var_log($this->_request->getQuery(),"=================================auto");
		switch ($this->_request->get('name')){
			case 'invest':
				
				$this->investAction();
				break;
			case 'recharges':
				$this->rechargesAction();
				break;
		}
	}
	
	
	/**
	 * 购买理财协议
	 * @input string $waresId 标的id
	 * @input int $clientType
	 * @output HTML
	 */
	public function investAction()
	
	{
	    
	    /**
	     *数字金额转换成中文大写金额的函数
	     *String Int  $num  要转换的小写数字或小写字符串
	     *return 大写字母
	     *小数位为两位
	     **/
	    function num_to_rmb($num){
	        $c1 = "零壹贰叁肆伍陆柒捌玖";
	        $c2 = "分角元拾佰仟万拾佰仟亿";
	        //精确到分后面就不要了，所以只留两个小数位
	        $num = round($num, 2);
	        //将数字转化为整数
	        $num = $num * 100;
	        if (strlen($num) > 10) {
	            return "金额太大，请检查";
	        }
	        $i = 0;
	        $c = "";
	        while (1) {
	            if ($i == 0) {
	                //获取最后一位数字
	                $n = substr($num, strlen($num)-1, 1);
	            } else {
	                $n = $num % 10;
	            }
	            //每次将最后一位数字转化为中文
	            $p1 = substr($c1, 3 * $n, 3);
	            $p2 = substr($c2, 3 * $i, 3);
	            if ($n != '0' || ($n == '0' && ($p2 == '亿' || $p2 == '万' || $p2 == '元'))) {
	                $c = $p1 . $p2 . $c;
	            } else {
	                $c = $p1 . $c;
	            }
	            $i = $i + 1;
	            //去掉数字最后一位了
	            $num = $num / 10;
	            $num = (int)$num;
	            //结束循环
	            if ($num == 0) {
	                break;
	            }
	        }
	        $j = 0;
	        $slen = strlen($c);
	        while ($j < $slen) {
	            //utf8一个汉字相当3个字符
	            $m = substr($c, $j, 6);
	            //处理数字中很多0的情况,每次循环去掉一个汉字“零”
	            if ($m == '零元' || $m == '零万' || $m == '零亿' || $m == '零零') {
	                $left = substr($c, 0, $j);
	                $right = substr($c, $j + 3);
	                $c = $left . $right;
	                $j = $j-3;
	                $slen = $slen-3;
	            }
	            $j = $j + 3;
	        }
	        //这个是为了去掉类似23.0中最后一个“零”字
	        if (substr($c, strlen($c)-3, 3) == '零') {
	            $c = substr($c, 0, strlen($c)-3);
	        }
	        //将处理的汉字加上“整”
	        if (empty($c)) {
	            return "零元";
	        }else{
	            return $c;
	        }
	    }
	  
	  
	    
	    
	    
	    $type=$this->_request->get('type');
	    $amount= $this->_request->get('amount');
		var_log($amount,"++++++++++++++++++++++++++++");
	    $uid = \Sooh\Base\Session\Data::getInstance()->get('accountId');
		$arr=['ymd'=>date('Y-m-d'),'userName'=>'','userPhone'=>'','userIdCard'=>'','userId'=>'','borrowerId'=>'',
			'borrowerIdCard'=>'','borrowerName'=>'','amount'=>$amount,'amount1'=>'','amount2'=>'','waresName'=>'','waresId'=>'','yieldStatic'=>''];
		$wares = \Prj\Data\Wares::getCopy($this->_request->get('waresId'));
		$wares->load();
		if($wares->exists()){
		    
		    $introDisplay=$wares->getField('introDisplay');
		    $borrowerName=$introDisplay['b']['name'];
		    $arr['borrowerName']= $borrowerName;
		    $arr['borrowerIdCard']= $introDisplay['b']['idCard'];
			$arr['waresName']=$wares->getField('waresName');
			$arr['borrowerId']=$wares->getField('borrowerId');
			$arr['waresId']=$wares->getField('waresId');
			$arr['yieldStatic']=$wares->getField('yieldStatic');
			$arr['yieldStatic']*=100;
			$arr['timeDur'] = $wares->getField('deadLine').$wares->getField('dlUnit');
			$amount/=100;
			
			$interestTotal1=num_to_rmb($amount);
			$arr['amount1']=$interestTotal1;
		
			$arr['amount2']=$amount;
		} 
		var_log($amount,"++++++++++++++++++++++++++++");
		if(empty($amount)){
			$arr['ymd']='合同签署之日';
			$arr['timeDur']='　　　';
			$arr['yieldStatic']='　　　';
			$arr['amount1']='　　　';
			$arr['amount2']='　　　';
		}
		//var_log($arr,'>>>>>>>>>>>>>>>>>>>>>>');
		
		if($uid){
			//if($this->_request->get('userId')==$uid){
				$user = \Prj\Data\User::getCopy($uid);
				$user->load();
				
				if($user->exists()){

				    $arr['userId']=$uid;
					$arr['userName']=$user->getField('nickname');
					$arr['userPhone']=$user->getField('phone');
					$arr['userIdCard']=$user->getField('idCard');

				}
			//}
			
		}
		var_log($arr,'>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>');
		
		if($type==1){
		    foreach ($arr as $k=>$v){
		        $arr[$k]='';
		    }
		}elseif ($type==2){
		    foreach ($arr as $k=>$v){
		        if($k=='borrowerName' or $k=='borrowerIdCard' or $k=='borrowerId'){
		            $arr[$k]='***投资成功才可查看';
		          
		        }
		        else{
		            $arr[$k]=$v;
		        }
		    }
		}
		var_log($arr,'>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>');
		echo \Prj\Misc\Licence::invest($arr,$ver=1);
	}
	
	
	
	/**
	 * 新版本的
	 * 购买理财协议
	 * @input string $waresId 标的id
	 * @input int $clientType
	 * @output HTML
	 */
	public function newinvestAction()
	
	{
	     
	    /**
	     *数字金额转换成中文大写金额的函数
	     *String Int  $num  要转换的小写数字或小写字符串
	     *return 大写字母
	     *小数位为两位
	     **/
	    function num_to_rmb($num){
	        $c1 = "零壹贰叁肆伍陆柒捌玖";
	        $c2 = "分角元拾佰仟万拾佰仟亿";
	        //精确到分后面就不要了，所以只留两个小数位
	        $num = round($num, 2);
	        //将数字转化为整数
	        $num = $num * 100;
	        if (strlen($num) > 10) {
	            return "金额太大，请检查";
	        }
	        $i = 0;
	        $c = "";
	        while (1) {
	            if ($i == 0) {
	                //获取最后一位数字
	                $n = substr($num, strlen($num)-1, 1);
	            } else {
	                $n = $num % 10;
	            }
	            //每次将最后一位数字转化为中文
	            $p1 = substr($c1, 3 * $n, 3);
	            $p2 = substr($c2, 3 * $i, 3);
	            if ($n != '0' || ($n == '0' && ($p2 == '亿' || $p2 == '万' || $p2 == '元'))) {
	                $c = $p1 . $p2 . $c;
	            } else {
	                $c = $p1 . $c;
	            }
	            $i = $i + 1;
	            //去掉数字最后一位了
	            $num = $num / 10;
	            $num = (int)$num;
	            //结束循环
	            if ($num == 0) {
	                break;
	            }
	        }
	        $j = 0;
	        $slen = strlen($c);
	        while ($j < $slen) {
	            //utf8一个汉字相当3个字符
	            $m = substr($c, $j, 6);
	            //处理数字中很多0的情况,每次循环去掉一个汉字“零”
	            if ($m == '零元' || $m == '零万' || $m == '零亿' || $m == '零零') {
	                $left = substr($c, 0, $j);
	                $right = substr($c, $j + 3);
	                $c = $left . $right;
	                $j = $j-3;
	                $slen = $slen-3;
	            }
	            $j = $j + 3;
	        }
	        //这个是为了去掉类似23.0中最后一个“零”字
	        if (substr($c, strlen($c)-3, 3) == '零') {
	            $c = substr($c, 0, strlen($c)-3);
	        }
	        //将处理的汉字加上“整”
	        if (empty($c)) {
	            return "零元";
	        }else{
	            return $c;
	        }
	    }
	     
	     
	     
	     
	     
	    $type=$this->_request->get('type');
	    $amount= $this->_request->get('amount');
	    $ordersId=$this->_request->get('ordersId');
	   // var_log($ordersId,'>>>>>>>>>>>>>>>>>>');
	    $uid = \Sooh\Base\Session\Data::getInstance()->get('accountId');
	    $arr=['ymdft'=>'','ymdsd'=>'','ymdtd'=>'','userName'=>'','userPhone'=>'','userIdCard'=>'','userId'=>'','borrowerId'=>'',
	        'borrowerIdCard'=>'','borrowerName'=>'','amount'=>$amount,'amount1'=>'','amount2'=>'','waresName'=>'','waresId'=>'','yieldStatic'=>'','timeDur'=>''];
	    $waresId=$this->_request->get('waresId');
	    $wares = \Prj\Data\Wares::getCopy($waresId);
	    $wares->load();
	    if($wares->exists()){
	        $deadLine=$wares->getField('deadLine');
	        $dlUnit=$wares->getField('dlUnit');
	        $borrowerId=$wares->getField('borrowerId');
	        $arr['timeDur']=$deadLine.$dlUnit;
	        $introDisplay=$wares->getField('introDisplay');
	        $arr['borrowerIdCard']= $introDisplay['b']['idCard'];
	        $arr['waresName']=$wares->getField('waresName');
	        $arr['borrowerId']=$borrowerId;
	        $arr['yieldStatic']=$wares->getField('yieldStatic');
	        $arr['yieldStatic']*=100;
	        $arr['timeDur'] = $wares->getField('deadLine').$wares->getField('dlUnit');
	        $amount/=100;
	        	
	        $interestTotal1=num_to_rmb($amount);
	        $arr['amount1']=$interestTotal1;
	        $arr['amount2']=$amount;
	        
	        
	        $returnNext=$wares->getField('payYmd');
	       // var_log($time1,'$returnNext>>>>>>>>>>>>>>>>>>>>>>>');
	        $returnNext1=date('Y-m-d H:i:s',strtotime($returnNext));
	        $returnNext2=strtotime($returnNext2);
	        $returnNext3=date('Y-m-d',strtotime($returnNext));
	        $time=date('Y-m-d H:i:s',time());
	        $time1=strtotime($time);
	       //var_log($time1,'time1>>>>>>>>>>>>>>>>>>>>>>>');
	       if(!empty($returnNext)){
	        if($time1<$returnNext2){
	            $arr['ymdft']='合同签署之日';
	        }else{
	            $arr['ymdft']=$returnNext3;
	        }
	       }else{
	           $arr['ymdft']='合同签署之日';
	       }

	    }
	    //	var_log($amount,"++++++++++++++++++++++++++++");
	
	    // 		if(empty($amount)){
	
	    // 			$arr['timeDur']='　　　';
	    // 			$arr['yieldStatic']='　　　';
	    // 			$arr['amount1']='　　　';
	    // 			$arr['amount2']='　　　';
	    // 		}
	    // 		else{
	    // 			$arr['ymd']='合同签署之日';
	    // 		}
	    //var_log($arr,'>>>>>>>>>>>>>>>>>>>>>>');
	
// 	    if($uid){
// 	        //if($this->_request->get('userId')==$uid){
// 	        $user = \Prj\Data\User::getCopy($uid);
// 	        $user->load();
	
// 	        if($user->exists()){
	             
// 	            $arr['userId']=$uid;
// 	            $arr['userName']=$user->getField('nickname');
// 	            $arr['userPhone']=$user->getField('phone');
// 	            $arr['userIdCard']=$user->getField('idCard');
	
// 	        }
// 	        //}
	        	
// 	    }
	    if($ordersId){
	        $investment=\Prj\Data\Investment::getCopy($ordersId);
	        $investment->load();
	        $orderTime=$investment->getField('orderTime');
	        $orderTime=\Prj\Misc\View::fmtYmd($orderTime);
	        $arr['ymdsd']=$orderTime;
	        $arr['ymdtd']=$orderTime;
	        $licence=$investment->getField('licence');
	        $ver=$licence[0]['ver'];
	        
	      $userId=$investment->getField('userId');
	      if(!empty($userId)){
	          $user=\Prj\Data\User::getCopy($userId);
	          $user->load();
	          if($user->exists()){
	              $arr['userId']=$userId;
	              $arr['userName']=$user->getField('nickname');
	              $arr['userPhone']=$user->getField('phone');
	              $arr['userIdCard']=$user->getField('idCard');
	          }
	      }  
	        	
	    }else{
	       if(!empty($uid)){
	           $user=\Prj\Data\User::getCopy($uid);
	           $user->load();
	           if($user->exists()){
	                    $arr['userId']=$uid;
           	            $arr['userName']=$user->getField('nickname');
           	            $arr['userPhone']=$user->getField('phone');
           	            $arr['userIdCard']=$user->getField('idCard');
	           }
	       } 
	    }
	    
	    
	    if($borrowerId){
	        $user=\Prj\Data\User::getCopy($borrowerId);
	        $user->load();
	        if($user->exists()){
	            $arr['borrowerName']=$user->getField('nickname');
	        }
	    }
	    //	var_log($arr,'>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>');
	
	    if($type==1){
	        foreach ($arr as $k=>$v){
	
	            $arr['borrowerName']='&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	            $arr['borrowerIdCard']='&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	            $arr['borrowerId']='&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	            $arr['userId']='&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	            $arr['userName']='&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	            $arr['userIdCard']='&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	            $arr['amount1']='&nbsp;&nbsp;&nbsp;&nbsp;';
	            $arr['amount2']='&nbsp;&nbsp;&nbsp;&nbsp;';
	            $arr['yieldStatic']='&nbsp;&nbsp;&nbsp;&nbsp;';
	            $arr['timeDur']='&nbsp;&nbsp;&nbsp;&nbsp;';
	            $arr['ymdft']='';
	            $arr['ymdsd']='';
	            $arr['ymdtd']='';
	             
	        }
	    }elseif ($type==2){
	        foreach ($arr as $k=>$v){
	            if($k=='borrowerName' or $k=='borrowerIdCard' or $k=='borrowerId'){
	                $arr[$k]='***投资成功才可查看';
	
	            }
	            else{
	                $arr['ymdft']='合同签署之日';
	                $arr['ymdsd']=date('Y-m-d',time());
	                $arr['ymdtd']=date('Y-m-d',time());
	                $arr[$k]=$v;
	            }
	        }
	    }
	    //var_log($arr,'>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>');
	    echo \Prj\Misc\Licence::invest($arr,$ver);
	}
	
	
	/**
	 * 充值许可协议
	 * @input int $amount 充值金额
	 * @input string $bankCard 使用的银行卡
	 * @input int $clientType
	 */
	public function rechargesAction()
	{    
	    $type=$this->_request->get('type');
		$amount = $this->_request->get('amount');
		$bankCard = $this->_request->get('bankCard');
		$uid = \Sooh\Base\Session\Data::getInstance()->get('accountId');
		$arr=['ymd'=>date('Y-m-d'),'amount'=>$amount/100,'userName'=>'','userPhone'=>'','userIdCard'=>'',
			'userId'=>'','bankId'=>'','bankCard'=>''];
		if($uid){
			$user = \Prj\Data\User::getCopy($uid);
			$user->load();
			if($user->exists()){
				$arr['userName'] = $user->getField('nickname');
				$arr['userPhone'] = $user->getField('phone');
				$arr['userIdCard'] = $user->getField('idCard');
				$arr['userId'] = $uid;
				
				$bank = \Prj\Data\BankCard::getList($uid,['statusCode'=>\Prj\Consts\BankCard::enabled]);
                var_log(\Sooh\DB\Broker::lastCmd(false),'sql>>>');
                var_log($bank,'bank>>>');
				if(!empty($bank)){
					if($bankCard){
						foreach($bank as $r){
							if($r['bankCard']==$bankCard){
                                var_log('>>>>>>>>>>>');
								$arr['bankId'] = \Prj\Consts\Banks::$enums[$r['bankId']][0];
								$arr['bankCard'] = $r['bankCard'];
							}
						}
                        /*
						$r = current($bank);
						$arr['bankId'] = \Prj\Consts\Banks::$enums[$r['bankId']];
						$arr['bankCard'] = $r['bankCard'];
                        */
					}
				}
			}
			var_log($arr);
		}
	
		if($type==1){
		   
 	foreach($arr as $k=>$v){
 	    $arr[$k]='';
 	}
		}elseif ($type==2){
		  foreach ($arr as $k=>$v){
		      $arr[$k]=$v;
		      }
		  }
		
		
		echo \Prj\Misc\Licence::recharges($arr,$ver=1);
	}
	
	
	/**
	 * 新的版本
	 * 充值许可协议
	 * @input int $amount 充值金额
	 * @input string $bankCard 使用的银行卡
	 * @input int $clientType
	 */
	public function newrechargesAction()
	{
	    $type=$this->_request->get('type');
	    $amount = $this->_request->get('amount');
	    $bankCard = $this->_request->get('bankCard');
	    $uid = \Sooh\Base\Session\Data::getInstance()->get('accountId');
	    $arr=['ymd'=>date('Y-m-d'),'amount'=>$amount/100,'userName'=>'','userPhone'=>'','userIdCard'=>'',
	        'userId'=>'','bankId'=>'','bankCard'=>''];
	    if($uid){
	        $user = \Prj\Data\User::getCopy($uid);
	        $user->load();
	        if($user->exists()){
	            $arr['userName'] = $user->getField('nickname');
	            $arr['userPhone'] = $user->getField('phone');
	            $arr['userIdCard'] = $user->getField('idCard');
	            $arr['userId'] = $uid;
	
	            $bank = \Prj\Data\BankCard::getList($uid,['statusCode'=>\Prj\Consts\BankCard::enabled]);
	            var_log(\Sooh\DB\Broker::lastCmd(false),'sql>>>');
	            var_log($bank,'bank>>>');
	            if(!empty($bank)){
	                if($bankCard){
	                    foreach($bank as $r){
	                        if($r['bankCard']==$bankCard){
	                            var_log('>>>>>>>>>>>');
	                            $arr['bankId'] = \Prj\Consts\Banks::$enums[$r['bankId']][0];
	                            $arr['bankCard'] = $r['bankCard'];
	                        }
	                    }
	                    /*
	                     $r = current($bank);
	                     $arr['bankId'] = \Prj\Consts\Banks::$enums[$r['bankId']];
	                     $arr['bankCard'] = $r['bankCard'];
	                     */
	                }
	            }
	        }
	        //var_log($arr);
	    }
	
	    if($type==1){
	         
	        foreach($arr as $k=>$v){
	
	            $arr['userId']='&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
 	            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	            $arr['userName']='&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
 	            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	            $arr['userIdCard']='&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
 	            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	            $arr['bankCard']='&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
 	            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	            $arr['bankId']='&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
 	            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	            $arr['ymd']='&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
 	            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	            $arr['amount']='&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	             
	        }
	    }elseif ($type==2){
	        foreach ($arr as $k=>$v){
	            $arr[$k]=$v;
	        }
	    }
	
	
	    echo \Prj\Misc\Licence::recharges($arr,$ver=1);
	}
	
	
	
	
    /**
     * 抵押合同
     */
    public function diyaAction(){
        $arr = [];
        $waresId = $this->_request->get('waresId');
        if(!empty($waresId)){
            $ware = \Prj\Data\Wares::getCopy($waresId);
            $ware->load();
            if($ware->exists()){
                $className = '';

            }
        }
        echo \Prj\Misc\Licence::diya($arr);
    }

    /**
     * 债权转让合同
     */
    public function zhaiquanAction(){
        echo \Prj\Misc\Licence::zhaiquan([]);
    }

    /**
     * 借款合同
     */
    public function jiekuanAction(){
        echo \Prj\Misc\Licence::jiekuan([]);
    }
}
