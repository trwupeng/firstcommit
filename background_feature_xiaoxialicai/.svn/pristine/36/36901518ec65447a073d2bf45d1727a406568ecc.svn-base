<?php
use Sooh\Base\Form\Item as form_def;
use Sooh\Base\Form\Options as options_def;
use \Rpt\DataDig\CopartnerWorthDig as CopartnerWorthDig;

class CopartnerwithdrawController extends \Prj\ManagerCtrl{
	
	public function init() {
		parent::init();
	}
	
	protected $pageSizeEnum = [50,100, 150, 300, 500, 1000];

	public function indexAction() {
		$saveAsExcel = $this->_request->get('__EXCEL__');
		$pageid = $this->_request->get('pageId',1)-0;
		$pagesize = $this->_request->get('pageSize',current($this->pageSizeEnum))-0;
		$pager = new \Sooh\DB\Pager($pagesize,$this->pageSizeEnum,false);

		$ymdDefault = \Sooh\Base\Time::getInstance()->yesterday('Y-m-d');
		$form = \Sooh\Base\Form\Broker::getCopy('default')
			->init(\Sooh\Base\Tools::uri(),'get',\Sooh\Base\Form\Broker::type_s);
		$form->addItem('_ymd_eq', form_def::factory('日期', $ymdDefault, form_def::datepicker))
			->addItem('pageid', $pageid)
			->addItem('pagesize', $pager->page_size);

		$form->fillValues();
		$where = $form->getWhere();
		$where['ymd='] = date('Ymd', strtotime($where['ymd=']));
	
		$db_p2p=\Sooh\DB\Broker::getInstance();
		$db_rpt= \Sooh\DB\Broker::getInstance('dbForRpt');
	
		$fielsdMap=[
		    
		    'copartnerId'=>['渠道号',null],
		    'copartnerName'=>['渠道名称',null],
		    'withdrawAmount'=>['提现金额',null],
		    'rate'=>['百分比',null],
	
	    ];
		
		$headers = [];
		foreach($fielsdMap as $r) {
		    $headers[$r[0]] = $r[1];
		}
		
		if($saveAsExcel){
		
	    $borrower=$db_p2p->getOne('db_p2p.tb_config','v',['k'=>'borrower']);
	     
	    $borrowers=json_decode($borrower,true);
	     
	    foreach ($borrowers as $k=>$v){
	         
	        $uids.='"'.$k.'"'.',';
	    }
	    $uids= rtrim($uids,',');
	    
	    $where = $this->_request->get('where');
		    
		$sql='SELECT copartnerId,SUM(amount) as withdrawAmount  from db_p2prpt.tb_recharges_final as a '.
		     'LEFT JOIN db_p2prpt.tb_user_final as b '.'on a.userId=b.userId '.
		     'where a.orderStatus=39 '.'and a.amount<=0 '.' and b.userId not in '.'('.$uids.')'.'and a.finishYmd='.$where['ymd=']
		     .' GROUP BY b.copartnerId';
		
		$rs = $db_rpt->execCustom(['sql'=>$sql]);
		$rs=$db_rpt->fetchAssocThenFree($rs);
		$pager->init(count($rs), $pageid);
		
		$rsform=$pager->rsFrom();
		//$rsto=$rsform+$pagesize;
		
		$sql1='SELECT copartnerId,SUM(amount) as withdrawAmount  from db_p2prpt.tb_recharges_final as a '.
		    'LEFT JOIN db_p2prpt.tb_user_final as b '.'on a.userId=b.userId '.
		    'where a.orderStatus=39 '.'and a.amount<=0 '.' and b.userId not in '.'('.$uids.')'.'and a.finishYmd='.$where['ymd=']
		    .' GROUP BY b.copartnerId'.' order by withdrawAmount '.' limit '.$rsform.','.$pagesize;
		
		$rs1 = $db_rpt->execCustom(['sql'=>$sql1]);
		$rs1=$db_rpt->fetchAssocThenFree($rs1);
		
	    $sqlAll='SELECT SUM(amount) as withdrawAmount from db_p2prpt.tb_recharges_final'.' where orderStatus=39 '
	              .'and amount<=0 '.' and userId not in '.'('.$uids.')'.' and finishYmd='.$where['ymd='];

	    $rsAll = $db_rpt->execCustom(['sql'=>$sqlAll]);
	    $rsAll=$db_rpt->fetchAssocThenFree($rsAll);
	    //var_log($rsAll,'rsall>>>>>>>>>');
	    
	    if(!empty($rs1) && !empty($rsAll)){
	        
	        foreach ($rs1 as $v){
	            
	        if($v['copartnerId']==0){
	              $copartnername='自然量';
	          }else{
	          $copartnername=$db_p2p->getOne('db_p2p.tb_copartner_0','copartnerName',['copartnerId'=>$v['copartnerId']]);
	          if($copartnername==''){
	              $copartnername='渠道管理中找不到此渠道号';
	          }
	          }
	           
	           $records[]=[
	               'copartnerId'=>$v['copartnerId'],
	               'copartnerName'=>$copartnername,
	               'withdrawAmount'=>abs($v['withdrawAmount']/100),
	               'rate'=>sprintf('%.2f',($v['withdrawAmount']/$rsAll[0]['withdrawAmount']*100)).'%',
	               '_pkey_' =>$v['copartnerId'],
	           ];
	           
	        }
	    }else{
	        

	        foreach ($rs1 as $v){
	            
	        if($v['copartnerId']==0){
	              $copartnername='自然量';
	          }else{
	          $copartnername=$db_p2p->getOne('db_p2p.tb_copartner_0','copartnerName',['copartnerId'=>$v['copartnerId']]);
	          if($copartnername==''){
	              $copartnername='渠道管理中找不到此渠道号';
	          }
	          }
	            $records[]=[
	                'copartnerId'=>$v['copartnerId'],
	                'copartnerName'=>$copartnername,
	                'withdrawAmount'=>abs($v['withdrawAmount']/100),
	                'rate'=>'0%',
	                '_pkey_' =>$v['copartnerId'],
	            ];
	        
	        }
	        
	    }

		}else{
		    
		    var_log($where,'records>>>>>>>>');
		    $borrower=$db_p2p->getOne('db_p2p.tb_config','v',['k'=>'borrower']);
		     
		    $borrowers=json_decode($borrower,true);
		     
		    foreach ($borrowers as $k=>$v){
		         
		        $uids.='"'.$k.'"'.',';
		    }
		    $uids= rtrim($uids,',');
		    var_log($uids,'uid>>>>>>>>>>');
		    
		    $sql='SELECT copartnerId,sum(amount) as withdrawAmount  from db_p2prpt.tb_recharges_final as a '.
		        'LEFT JOIN db_p2prpt.tb_user_final as b '.'on a.userId=b.userId '.
		        'where a.orderStatus=39 '.'and a.amount<=0 '.' and b.userId not in '.'('.$uids.')'.'and a.finishYmd='.$where['ymd=']
		        .' GROUP BY b.copartnerId';
		    
		    $rs = $db_rpt->execCustom(['sql'=>$sql]);
		    $rs=$db_rpt->fetchAssocThenFree($rs);
		    $pager->init(count($rs), $pageid);
		    
		    $rsform=$pager->rsFrom();
		    //$rsto=$rsform+$pagesize;
		    
		    $sql1='SELECT copartnerId,sum(amount) as withdrawAmount  from db_p2prpt.tb_recharges_final as a '.
		        'LEFT JOIN db_p2prpt.tb_user_final as b '.'on a.userId=b.userId '.
		        'where a.orderStatus=39 '.'and a.amount<=0 '.' and b.userId not in '.'('.$uids.')'.'and a.finishYmd='.$where['ymd=']
		        .' GROUP BY b.copartnerId'.' order by withdrawAmount '.' limit '.$rsform.','.$pagesize;
		      
		    $rs1 = $db_rpt->execCustom(['sql'=>$sql1]);
		    $rs1=$db_rpt->fetchAssocThenFree($rs1);
		    
		    $sqlAll='SELECT sum(amount) as withdrawAmount from db_p2prpt.tb_recharges_final'.' where orderStatus=39 '
		        .'and amount<=0 '.' and userId not in '.'('.$uids.')'.' and finishYmd='.$where['ymd='];
		    
		    $rsAll = $db_rpt->execCustom(['sql'=>$sqlAll]);
		    $rsAll=$db_rpt->fetchAssocThenFree($rsAll);
		    var_log($rsAll,'rsall>>>>>>>>>');
		     
		    if(!empty($rs1) && !empty($rsAll)){
		         
		        foreach ($rs1 as $v){
		             
		            if($v['copartnerId']==0){
		                $copartnername='自然量';
		            }else{
		                $copartnername=$db_p2p->getOne('db_p2p.tb_copartner_0','copartnerName',['copartnerId'=>$v['copartnerId']]);
		                if($copartnername==''){
		                    $copartnername='渠道管理中找不到此渠道号';
		                }
		            }
		    
		            $records[]=[
		                'copartnerId'=>$v['copartnerId'],
		                'copartnerName'=>$copartnername,
		                'withdrawAmount'=>$v['withdrawAmount'],
		                'rate'=>sprintf('%.2f',($v['withdrawAmount']/$rsAll[0]['withdrawAmount']*100)).'%',
		                '_pkey_' =>$v['copartnerId'],
		            ];
		    
		        }
		    }else{
		         
		    
		        foreach ($rs1 as $v){
		             
		            if($v['copartnerId']==0){
		                $copartnername='自然量';
		            }else{
		                $copartnername=$db_p2p->getOne('db_p2p.tb_copartner_0','copartnerName',['copartnerId'=>$v['copartnerId']]);
		                if($copartnername==''){
		                    $copartnername='渠道管理中找不到此渠道号';
		                }
		            }
		            $records[]=[
		                'copartnerId'=>$v['copartnerId'],
		                'copartnerName'=>$copartnername,
		                'withdrawAmount'=>$v['withdrawAmount'],
		                'rate'=>'0%',
		                '_pkey_' =>$v['copartnerId'],
		            ];
		             
		        }
		        
		}   
		
		}
	   
		
	    if($saveAsExcel){
	       // var_log($records,'records>>>>>>>>');
	        return $this->downExcel($records, array_keys($headers));
	    }
	    
	    $this->_view->assign('headers', $headers);
	    $this->_view->assign('records', $records);
	    $this->_view->assign('pager', $pager);
	    $this->_view->assign('where', $where);
	    
	}


	public function copartnerwithdailyAction () {
		$saveAsExcel = $this->_request->get('__EXCEL__');
		$pageid = $this->_request->get('pageId',1)-0;
		$pagesize = $this->_request->get('pageSize',current($this->pageSizeEnum))-0;
		$pager = new \Sooh\DB\Pager($pagesize,$this->pageSizeEnum,false);
		$dt_instance = \Sooh\Base\Time::getInstance();
		$ymdFrom = date('Y-m-d', $dt_instance->timestamp(-30));
		$ymdTo = $dt_instance->yesterday('Y-m-d');
		$copartnerId = $this->_request->get('_pkey_');
		
		
		$form = \Sooh\Base\Form\Broker::getCopy('default')
				->init(\Sooh\Base\Tools::uri(),'get',\Sooh\Base\Form\Broker::type_s);
		$form->addItem('_ymd_g2', form_def::factory('日期从', $ymdFrom, form_def::datepicker))
				->addItem('_ymd_l2', form_def::factory('到', $ymdTo, form_def::datepicker))
				->addItem('_copartnerId_eq', form_def::factory('渠道Id', $copartnerId, form_def::hidden))
				->addItem('pageid', $pageid)
				->addItem('pagesize', $pager->page_size);
		$form->fillValues();
		$where = $form->getWhere();
	
		if($where['ymd]']) {
			$where['ymd]'] = date('Ymd', strtotime($where['ymd]']));
		}
		if($where['ymd[']){
			$where['ymd['] = date('Ymd', strtotime($where['ymd[']));
		}
		
		$fielsdMap=[
		    'ymd'=>['日期',null],
		    'copartnerId'=>['渠道号',null],
		    'copartnerName'=>['渠道名称',null],
		    'withdrawAmount'=>['提现金额',null],
		
		];
		
		$headers = [];
		foreach($fielsdMap as $r) {
		    $headers[$r[0]] = $r[1];
		}
		
		$db_p2p=\Sooh\DB\Broker::getInstance();
		$db_rpt= \Sooh\DB\Broker::getInstance('dbForRpt');
		
           if($saveAsExcel){
               
           $borrower=$db_p2p->getOne('db_p2p.tb_config','v',['k'=>'borrower']);
           
           $borrowers=json_decode($borrower,true);
           
           foreach ($borrowers as $k=>$v){
           
               $uids.='"'.$k.'"'.',';
           }
           $uids= rtrim($uids,',');
            
           $where = $this->_request->get('where');
           
           var_log($where,'>>>>>>>>>>');
           
		    $sql1='SELECT sum(amount) as withdrawAmount,finishYmd  from db_p2prpt.tb_recharges_final as a '.
		        'LEFT JOIN db_p2prpt.tb_user_final as b '.'on a.userId=b.userId '.
		        'where a.orderStatus=39 '.'and a.amount<=0 '.' and b.userId not in '.'('.$uids.')'.' and a.finishYmd>='.$where['ymd'].' and a.finishYmd<='.$where['ymd[']
		        .' and b.copartnerId='.$where['copartnerId='].' GROUP BY a.finishYmd';
		    $sql=$db_rpt->execCustom(['sql'=>$sql1]);
		    $sql=$db_rpt->fetchAssocThenFree($sql);
		  
		    
		    $pager->init(Count($sql), $pageid);
		    $rsform=$pager->rsFrom();
		
		    $sql2='SELECT sum(amount) as withdrawAmount,finishYmd  from db_p2prpt.tb_recharges_final as a '.
		        'LEFT JOIN db_p2prpt.tb_user_final as b '.'on a.userId=b.userId '.
		        'where a.orderStatus=39 '.'and a.amount<=0 '.' and b.userId not in '.'('.$uids.')'.' and a.finishYmd>='.$where['ymd'].' and a.finishYmd<='.$where['ymd[']
		        .' and b.copartnerId='.$where['copartnerId='].' GROUP BY a.finishYmd '.' limit '.$rsform.','.$pagesize;
		    $sql3=$db_rpt->execCustom(['sql'=>$sql2]);
		    $sql3=$db_rpt->fetchAssocThenFree($sql3);
	
		    
		    if($where['copartnerId=']==0){
		        $copartnername='自然量';
		    }else{
		        $copartnername=$db_p2p->getOne('db_p2p.tb_copartner_0','copartnerName',['copartnerId'=>$where['copartnerId=']]);
		        if($copartnername==''){
		            $copartnername='渠道管理中找不到此渠道号';
		        }
		    }
		   // $copartnername=$db_p2p->getOne('db_p2p.tb_copartner_0','copartnerName',['copartnerId'=>$where['copartnerId=']]);
		    
		    foreach ($sql3 as $u){
		   
		    $rs[]=[
		        'ymd'=>$u['finishYmd'],
		        'copartnerId'=>$where['copartnerId='],
		        'copartnerName'=>$copartnername,
		        'withdrawAmount'=>abs($u['withdrawAmount']/100),
		        '_pkey_'=>$where['copartnerId='],
		    ];
		    }
           }else{
               
               $borrower=$db_p2p->getOne('db_p2p.tb_config','v',['k'=>'borrower']);
                
               $borrowers=json_decode($borrower,true);
                
               foreach ($borrowers as $k=>$v){
                    
                   $uids.='"'.$k.'"'.',';
               }
               $uids= rtrim($uids,',');
               var_log($uids,'uids>>>>>>>>>>');
               var_log($where,'>>>>>>>>>>');
               
               $sql1='SELECT sum(amount) as withdrawAmount,finishYmd  from db_p2prpt.tb_recharges_final as a '.
                   'LEFT JOIN db_p2prpt.tb_user_final as b '.'on a.userId=b.userId '.
                   'where a.orderStatus=39 '.'and a.amount<=0 '.' and b.userId not in '.'('.$uids.')'.' and a.finishYmd>='.$where['ymd]'].' and finishYmd<='.$where['ymd[']
                   .' and b.copartnerId='.$where['copartnerId='].' GROUP BY finishYmd';
               $sql=$db_rpt->execCustom(['sql'=>$sql1]);
               $sql=$db_rpt->fetchAssocThenFree($sql);
               
               
               $pager->init(Count($sql), $pageid);
               $rsform=$pager->rsFrom();
               
               $sql2='SELECT sum(amount) as withdrawAmount,finishYmd  from db_p2prpt.tb_recharges_final as a '.
                   'LEFT JOIN db_p2prpt.tb_user_final as b '.'on a.userId=b.userId '.
                   'where a.orderStatus=39 '.'and a.amount<=0 '.' and b.userId not in '.'('.$uids.')'.' and a.finishYmd>='.$where['ymd]'].' and finishYmd<='.$where['ymd[']
                   .' and b.copartnerId='.$where['copartnerId='].' GROUP BY finishYmd '.' limit '.$rsform.','.$pagesize;
               $sql3=$db_rpt->execCustom(['sql'=>$sql2]);
               $sql3=$db_rpt->fetchAssocThenFree($sql3);
               
               
               if($where['copartnerId=']==0){
                   $copartnername='自然量';
               }else{
                   $copartnername=$db_p2p->getOne('db_p2p.tb_copartner_0','copartnerName',['copartnerId'=>$where['copartnerId=']]);
                   if($copartnername==''){
                       $copartnername='渠道管理中找不到此渠道号';
                   }
               }
               // $copartnername=$db_p2p->getOne('db_p2p.tb_copartner_0','copartnerName',['copartnerId'=>$where['copartnerId=']]);
               
               foreach ($sql3 as $u){
                    
                   $rs[]=[
                       'ymd'=>$u['finishYmd'],
                       'copartnerId'=>$where['copartnerId='],
                       'copartnerName'=>$copartnername,
                       'withdrawAmount'=>$u['withdrawAmount'],
                       '_pkey_'=>$where['copartnerId='],
                   ];
               }
               
               
           }
		
           if($saveAsExcel){
               // var_log($records,'records>>>>>>>>');
               return $this->downExcel($rs, array_keys($headers));
           }
            
           
		$this->_view->assign('records', $rs);
		$this->_view->assign('headers', $headers);
		$this->_view->assign('pager', $pager);
		$this->_view->assign('where', $where);

	}

	public function copartneruserAction () {
		$saveAsExcel = $this->_request->get('__EXCEL__');
		$pageid = $this->_request->get('pageId',1)-0;
		$pagesize = $this->_request->get('pageSize',current($this->pageSizeEnum))-0;
		$pager = new \Sooh\DB\Pager($pagesize,$this->pageSizeEnum,false);
		$dt_instance = \Sooh\Base\Time::getInstance();
		$ymdFrom = date('Y-m-d', $dt_instance->timestamp(-30));
		$ymdTo = $dt_instance->yesterday('Y-m-d');
		$copartnerId = $this->_request->get('_pkey_');
		$ymdOne=$dt_instance->yesterday('Ymd');
		//$ymdOne='20160321';
		
		$form = \Sooh\Base\Form\Broker::getCopy('default')
				->init(\Sooh\Base\Tools::uri(),'get',\Sooh\Base\Form\Broker::type_s);
		$form->addItem('_userId_eq', form_def::factory('用户Id', '', form_def::text))
		       ->addItem('_copartnerId_eq', form_def::factory('渠道Id', $copartnerId, form_def::text))
		        ->addItem('_ymdForm_g2', form_def::factory('日期从', $ymdFrom, form_def::datepicker))
				->addItem('_ymdTo_l2', form_def::factory('到', $ymdTo, form_def::datepicker))
				->addItem('pageid', $pageid)
				->addItem('pagesize', $pager->page_size);
		$form->fillValues();
		$where = $form->getWhere();
		if($where['ymdForm]']) {
			$where['ymdForm]'] = date('Ymd', strtotime($where['ymdForm]']));
		}
		if($where['ymdTo[']){
			$where['ymdTo['] = date('Ymd', strtotime($where['ymdTo[']));
		}
		
		var_log($where,'>>>>>>>>>>>>>>');
		var_log($copartnerId,'>>>>>>>>>>>>>>');
		$db_p2p=\Sooh\DB\Broker::getInstance();
		$db_rpt= \Sooh\DB\Broker::getInstance('dbForRpt');
		
		$fielsdMap=[
		    'copartnerId'=>['渠道号',null],
		    'copartnerName'=>['渠道名称',null],
		    'realname'=>['姓名', null],
		    'withdrawAmount'=>['提现金额',null],
		
		];
		
		$headers = [];
		foreach($fielsdMap as $r) {
		    $headers[$r[0]] = $r[1];
		}
		
		
		if($saveAsExcel){
		    
		    $borrower=$db_p2p->getOne('db_p2p.tb_config','v',['k'=>'borrower']);
		     
		    $borrowers=json_decode($borrower,true);
		     
		    foreach ($borrowers as $k=>$v){
		         
		        $uids.='"'.$k.'"'.',';
		    }
		    $uids= rtrim($uids,',');
		    
		    var_log($uids,'uid>>>>>>>>>>');
		    
		    $where = $this->_request->get('where');
		    
		    var_log($where,'where>>>>>>>>>>>>');
		    
	    if(empty($where['userId=']) && $where['userId=']!==Null){
	       
	        //var_log('111111111');
	        
	       $sql1='SELECT b.realname,a.amount from db_p2prpt.tb_recharges_final as a '.
	           'LEFT JOIN db_p2prpt.tb_user_final as b '.'on a.userId=b.userId '.
	           'where a.orderStatus=39 '.'and a.amount<=0 '.' and b.userId not in '.'('.$uids.')'.' and b.userId not in '.'('.$uids.')'.'and a.finishYmd='.$ymdOne
	           .' and b.copartnerId='.$where['copartnerId='].' ORDER BY a.amount';
	     
	       $sql=$db_rpt->execCustom(['sql'=>$sql1]);
	       $sql=$db_rpt->fetchAssocThenFree($sql);
	       
	       $pager->init(Count($sql), $pageid);
	       $rsform=$pager->rsFrom();
// 	       var_log($pager,'page>>>>>>>>>');
	       
	       $sql4='SELECT b.realname,a.amount from db_p2prpt.tb_recharges_final as a '.
	           'LEFT JOIN db_p2prpt.tb_user_final as b '.'on a.userId=b.userId '.
	           'where a.orderStatus=39 '.'and a.amount<=0 '.' and b.userId not in '.'('.$uids.')'.'and a.finishYmd='.$ymdOne
	           .' and b.copartnerId='.$where['copartnerId='].' ORDER BY a.amount'.' limit '.$rsform.','.$pagesize;
	       
	       $sql3=$db_rpt->execCustom(['sql'=>$sql4]);
	       $sql3=$db_rpt->fetchAssocThenFree($sql3);
	       
	       if($where['copartnerId=']==0){
	           $copartnername='自然量';
	       }else{
	           $copartnername=$db_p2p->getOne('db_p2p.tb_copartner_0','copartnerName',['copartnerId'=>$where['copartnerId=']]);
	           if($copartnername==''){
	               $copartnername='渠道管理中找不到此渠道号';
	           }
	       }
	       
	       //$copartnername=$db_p2p->getOne('db_p2p.tb_copartner_0','copartnerName',['copartnerId'=>$where['copartnerId=']]);
	       foreach ($sql3 as $u){
	       $rs[]=[
	           'copartnerId'=>$where['copartnerId='],
	           'copartnerName'=>$copartnername,
	           'realname'=>substr_replace($u['realname'],'*',3,3),
	           'withdrawAmount'=>abs($u['amount']/100),
	           '_pkey_'=>$where['copartnerId='],
	       ];
	       }
	   }
	   elseif($where['userId=']!='' && $where['copartnerId=']==''){
	   
	       //var_log('22222222222');
	   
	       $sql2='SELECT b.realname,a.amount,b.copartnerId  from db_p2prpt.tb_recharges_final as a '.
	           'LEFT JOIN db_p2prpt.tb_user_final as b '.'on a.userId=b.userId '.
	           'where a.orderStatus=39 '.' and a.amount<=0 '.' and b.userId= '.$where['userId='].' and b.userId not in '.'('.$uids.')'.' and a.finishYmd>='.$where['ymdForm'].' and a.finishYmd<='.$where['ymdTo[']
	           .' ORDER BY a.amount';
	        
	       $sql=$db_rpt->execCustom(['sql'=>$sql2]);
	       $sql=$db_rpt->fetchAssocThenFree($sql);
	       $pager->init(Count($sql), $pageid);
	   
	       $rsform=$pager->rsFrom();
	       //$rsto=$rsform+$pagesize;
	        
	   
	       $sql3='SELECT b.realname,a.amount,b.copartnerId from db_p2prpt.tb_recharges_final as a '.
	           'LEFT JOIN db_p2prpt.tb_user_final as b '.'on a.userId=b.userId '.
	           'where a.orderStatus=39 '.' and a.amount<=0 '.' and b.userId= '.$where['userId='].' and b.userId not in '.'('.$uids.')'.' and a.finishYmd>='.$where['ymdForm'].' and a.finishYmd<='.$where['ymdTo[']
	           .' ORDER BY a.amount'.' limit '.$rsform.','.$pagesize;
	       $sql6=$db_rpt->execCustom(['sql'=>$sql3]);
	       $sql6=$db_rpt->fetchAssocThenFree($sql6);
	   
	      // var_log($sql6,'666666666666>>>>>>>>>>>>>>');
	        
	       foreach ($sql6 as $u){
	   
	   
	           if($u['copartnerId']==0){
	               $copartnername='自然量';
	           }else{
	               $copartnername=$db_p2p->getOne('db_p2p.tb_copartner_0','copartnerName',['copartnerId'=>$u['copartnerId']]);
	               if($copartnername==''){
	                   $copartnername='渠道管理中找不到此渠道号';
	               }
	           }
	   
	           $rs[]=[
	               'copartnerId'=>$u['copartnerId'],
	               'copartnerName'=>$copartnername,
	               'realname'=>substr_replace($u['realname'],'*',3,3),
	               'withdrawAmount'=>abs($u['amount']/100),
	               '_pkey_'=>$u['copartnerId'],
	           ];
	       }
	   }
	   
	   elseif($where['userId=']!='' && $where['copartnerId=']!=''){
	   
	      // var_log('333333333333');
	       
	       $sql2='SELECT b.realname,a.amount,b.copartnerId  from db_p2prpt.tb_recharges_final as a '.
	           'LEFT JOIN db_p2prpt.tb_user_final as b '.'on a.userId=b.userId '.
	           'where a.orderStatus=39 '.' and a.amount<=0 '.' and b.userId= '.$where['userId=']
	           .' and b.copartnerId= '.$where['copartnerId='].' and b.userId not in '.'('.$uids.')'.' and a.finishYmd>='.$where['ymdForm'].' and a.finishYmd<='.$where['ymdTo[']
	           .' ORDER BY a.amount';
	        
	       $sql=$db_rpt->execCustom(['sql'=>$sql2]);
	       $sql=$db_rpt->fetchAssocThenFree($sql);
	       $pager->init(Count($sql), $pageid);
	     
	       $rsform=$pager->rsFrom();
	       //$rsto=$rsform+$pagesize;
	       var_log($sql,'666666666666>>>>>>>>>>>>>>');
	   
	       $sql3='SELECT b.realname,a.amount,b.copartnerId from db_p2prpt.tb_recharges_final as a '.
	           'LEFT JOIN db_p2prpt.tb_user_final as b '.'on a.userId=b.userId '.
	           'where a.orderStatus=39 '.' and a.amount<=0 '.' and b.userId= '.$where['userId=']
	           .' and b.copartnerId= '.$where['copartnerId='].' and b.userId not in '.'('.$uids.')'.' and a.finishYmd>='.$where['ymdForm'].' and a.finishYmd<='.$where['ymdTo[']
	           .' ORDER BY a.amount'.' limit '.$rsform.','.$pagesize;
	       $sql6=$db_rpt->execCustom(['sql'=>$sql3]);
	       $sql6=$db_rpt->fetchAssocThenFree($sql6);
	   
	      // var_log($sql6,'666666666666>>>>>>>>>>>>>>');
	        
	       foreach ($sql6 as $u){
	   
	   
	           if($u['copartnerId']==0){
	               $copartnername='自然量';
	           }else{
	               $copartnername=$db_p2p->getOne('db_p2p.tb_copartner_0','copartnerName',['copartnerId'=>$u['copartnerId']]);
	               if($copartnername==''){
	                   $copartnername='渠道管理中找不到此渠道号';
	               }
	           }
	   
	           $rs[]=[
	               'copartnerId'=>$u['copartnerId'],
	               'copartnerName'=>$copartnername,
	               'realname'=>substr_replace($u['realname'],'*',3,3),
	               'withdrawAmount'=>abs($u['amount']/100),
	               '_pkey_'=>$u['copartnerId'],
	           ];
	       }
	   }
	   elseif(empty($where['ymdForm']) || empty($where['ymdTo['])){
	      
	   }
	   elseif($where['userId=']=='' && $where['copartnerId=']!=''){
	       
	         //var_log('4444444444444');
	         
	          $sql2='SELECT b.realname,a.amount from db_p2prpt.tb_recharges_final as a '.
	           'LEFT JOIN db_p2prpt.tb_user_final as b '.'on a.userId=b.userId '.
	           'where a.orderStatus=39 '.'and a.amount<=0 '.' and b.userId not in '.'('.$uids.')'.'and a.finishYmd>='.$where['ymdForm'].' and a.finishYmd<='.$where['ymdTo[']
	           .' and b.copartnerId='.$where['copartnerId='].' GROUP BY finishYmd '.' ORDER BY a.amount';
	           
	        $sql=$db_rpt->execCustom(['sql'=>$sql2]);
		    $sql=$db_rpt->fetchAssocThenFree($sql);
		    $pager->init(Count($sql), $pageid);
		    $rsform=$pager->rsFrom();
		    
		    
		    $sql6='SELECT b.realname,a.amount from db_p2prpt.tb_recharges_final as a '.
		        'LEFT JOIN db_p2prpt.tb_user_final as b '.'on a.userId=b.userId '.
		        'where a.orderStatus=39 '.'and a.amount<=0 '.' and b.userId not in '.'('.$uids.')'.'and a.finishYmd>='.$where['ymdForm'].' and a.finishYmd<='.$where['ymdTo[']
		        .' and b.copartnerId='.$where['copartnerId='].' GROUP BY finishYmd '.' ORDER BY a.amount'.' limit '.$rsform.','.$pagesize;;
		    
		    $sql5=$db_rpt->execCustom(['sql'=>$sql6]);
		    $sql5=$db_rpt->fetchAssocThenFree($sql5);
		    
		    if($where['copartnerId=']==0){
		        $copartnername='自然量';
		    }else{
		        $copartnername=$db_p2p->getOne('db_p2p.tb_copartner_0','copartnerName',['copartnerId'=>$where['copartnerId=']]);
		        if($copartnername==''){
		            $copartnername='渠道管理中找不到此渠道号';
		        }
		    }
		    
	        //$copartnername=$db_p2p->getOne('db_p2p.tb_copartner_0','copartnerName',['copartnerId'=>$where['copartnerId=']]);
	         foreach ($sql5 as $u){
	          $rs[]=[
	           'copartnerId'=>$where['copartnerId='],
	           'copartnerName'=>$copartnername,
	           'realname'=>substr_replace($u['realname'],'*',3,3),
	           'withdrawAmount'=>abs($u['amount']/100),
	           '_pkey_'=>$where['copartnerId='],
	       ];
	         }
	       
	   }else{
	       
	       $sql2='SELECT b.realname,a.amount,b.copartnerId  from db_p2prpt.tb_recharges_final as a '.
	           'LEFT JOIN db_p2prpt.tb_user_final as b '.'on a.userId=b.userId '.
	           'where a.orderStatus=39 '.'and a.amount<=0 '.' and b.userId not in '.'('.$uids.')'.'and a.finishYmd>='.$where['ymdForm]'].' and a.finishYmd<='.$where['ymdTo[']
	           .' ORDER BY a.amount';
	        
	       $sql=$db_rpt->execCustom(['sql'=>$sql2]);
	       $sql=$db_rpt->fetchAssocThenFree($sql);
	       $pager->init(Count($sql), $pageid);
	       
	       $rsform=$pager->rsFrom();
	       //$rsto=$rsform+$pagesize;
	        
	       
	       $sql3='SELECT b.realname,a.amount,b.copartnerId from db_p2prpt.tb_recharges_final as a '.
	           'LEFT JOIN db_p2prpt.tb_user_final as b '.'on a.userId=b.userId '.
	           'where a.orderStatus=39 '.'and a.amount<=0 '.' and b.userId not in '.'('.$uids.')'.'and a.finishYmd>='.$where['ymdForm]'].' and a.finishYmd<='.$where['ymdTo[']
	           .' ORDER BY a.amount'.' limit '.$rsform.','.$pagesize;
	       $sql6=$db_rpt->execCustom(['sql'=>$sql3]);
	       $sql6=$db_rpt->fetchAssocThenFree($sql6);
	       
	       //var_log($sql6,'666666666666>>>>>>>>>>>>>>');
	        
	       foreach ($sql6 as $u){
	       
	       
	           if($u['copartnerId']==0){
	               $copartnername='自然量';
	           }else{
	               $copartnername=$db_p2p->getOne('db_p2p.tb_copartner_0','copartnerName',['copartnerId'=>$u['copartnerId']]);
	               if($copartnername==''){
	                   $copartnername='渠道管理中找不到此渠道号';
	               }
	           }
	       
	           $rs[]=[
	               'copartnerId'=>$u['copartnerId'],
	               'copartnerName'=>$copartnername,
	               'realname'=>substr_replace($u['realname'],'*',3,3),
	               'withdrawAmount'=>abs($u['amount']/100),
	               '_pkey_'=>$u['copartnerId'],
	           ];
	       }
	       
	   }
	   
	   
		}else{
		    //页面展示
		    //var_log($where,'6666666666666666');
		    
		    $borrower=$db_p2p->getOne('db_p2p.tb_config','v',['k'=>'borrower']);
		     
		    $borrowers=json_decode($borrower,true);
		     
		    foreach ($borrowers as $k=>$v){
		         
		        $uids.='"'.$k.'"'.',';
		    }
		    $uids= rtrim($uids,',');
		    var_log($uids,'uid>>>>>>>>>>');
		    
		    if(empty($where['userId=']) && $where['userId=']!==Null){
		    
		        $sql1='SELECT b.realname,a.amount from db_p2prpt.tb_recharges_final as a '.
		            'LEFT JOIN db_p2prpt.tb_user_final as b '.'on a.userId=b.userId '.
		            'where a.orderStatus=39 '.'and a.amount<=0 '.' and b.userId not in '.'('.$uids.')'.'and a.finishYmd='.$ymdOne
		            .' and b.copartnerId='.$where['copartnerId='].' ORDER BY a.amount';
		    
		        $sql=$db_rpt->execCustom(['sql'=>$sql1]);
		        $sql=$db_rpt->fetchAssocThenFree($sql);
		    
		        $pager->init(Count($sql), $pageid);
		        $rsform=$pager->rsFrom();
		        // 	       var_log($pager,'page>>>>>>>>>');
		    
		        $sql4='SELECT b.realname,a.amount from db_p2prpt.tb_recharges_final as a '.
		            'LEFT JOIN db_p2prpt.tb_user_final as b '.'on a.userId=b.userId '.
		            'where a.orderStatus=39 '.'and a.amount<=0 '.' and b.userId not in '.'('.$uids.')'.'and a.finishYmd='.$ymdOne
		            .' and b.copartnerId='.$where['copartnerId='].' ORDER BY a.amount'.' limit '.$rsform.','.$pagesize;
		    
		        $sql3=$db_rpt->execCustom(['sql'=>$sql4]);
		        $sql3=$db_rpt->fetchAssocThenFree($sql3);
		    
		        if($where['copartnerId=']==0){
		            $copartnername='自然量';
		        }else{
		            $copartnername=$db_p2p->getOne('db_p2p.tb_copartner_0','copartnerName',['copartnerId'=>$where['copartnerId=']]);
		            if($copartnername==''){
		                $copartnername='渠道管理中找不到此渠道号';
		            }
		        }
		    
		        //$copartnername=$db_p2p->getOne('db_p2p.tb_copartner_0','copartnerName',['copartnerId'=>$where['copartnerId=']]);
		        foreach ($sql3 as $u){
		            $rs[]=[
		                'copartnerId'=>$where['copartnerId='],
		                'copartnerName'=>$copartnername,
		                'realname'=>substr_replace($u['realname'],'*',3,3),
		                'withdrawAmount'=>$u['amount'],
		                '_pkey_'=>$where['copartnerId='],
		            ];
		        }
		    }
		    elseif($where['userId=']!='' && $where['copartnerId=']==''){
		    
		    
		        var_log('4444');
		        
		        $sql2='SELECT b.realname,a.amount,b.copartnerId  from db_p2prpt.tb_recharges_final as a '.
		            'LEFT JOIN db_p2prpt.tb_user_final as b '.'on a.userId=b.userId '.
		            'where a.orderStatus=39 '.' and a.amount<=0 '.' and b.userId= '.$where['userId='].' and b.userId not in '.'('.$uids.')'.' and a.finishYmd>='.$where['ymdForm]'].' and a.finishYmd<='.$where['ymdTo[']
		            .' ORDER BY a.amount';
		         
		        $sql=$db_rpt->execCustom(['sql'=>$sql2]);
		        $sql=$db_rpt->fetchAssocThenFree($sql);
		        $pager->init(Count($sql), $pageid);
		    
		        $rsform=$pager->rsFrom();
		        //$rsto=$rsform+$pagesize;
		         
		    
		        $sql3='SELECT b.realname,a.amount,b.copartnerId from db_p2prpt.tb_recharges_final as a '.
		            'LEFT JOIN db_p2prpt.tb_user_final as b '.'on a.userId=b.userId '.
		            'where a.orderStatus=39 '.' and a.amount<=0 '.' and b.userId= '.$where['userId='].' and b.userId not in '.'('.$uids.')'.' and a.finishYmd>='.$where['ymdForm]'].' and a.finishYmd<='.$where['ymdTo[']
		            .' ORDER BY a.amount'.' limit '.$rsform.','.$pagesize;
		        $sql6=$db_rpt->execCustom(['sql'=>$sql3]);
		        $sql6=$db_rpt->fetchAssocThenFree($sql6);
		    
		        // var_log($sql6,'666666666666>>>>>>>>>>>>>>');
		         
		        foreach ($sql6 as $u){
		    
		    
		            if($u['copartnerId']==0){
		                $copartnername='自然量';
		            }else{
		                $copartnername=$db_p2p->getOne('db_p2p.tb_copartner_0','copartnerName',['copartnerId'=>$u['copartnerId']]);
		                if($copartnername==''){
		                    $copartnername='渠道管理中找不到此渠道号';
		                }
		            }
		    
		            $rs[]=[
		                'copartnerId'=>$u['copartnerId'],
		                'copartnerName'=>$copartnername,
		                'realname'=>substr_replace($u['realname'],'*',3,3),
		                'withdrawAmount'=>$u['amount'],
		                '_pkey_'=>$u['copartnerId'],
		            ];
		        }
		    }
		    
		    elseif($where['userId=']!='' && $where['copartnerId=']!=''){
		       
		        var_log('333333333');
		        
		        $sql2='SELECT b.realname,a.amount,b.copartnerId  from db_p2prpt.tb_recharges_final as a '.
		            'LEFT JOIN db_p2prpt.tb_user_final as b '.'on a.userId=b.userId '.
		            'where a.orderStatus=39 '.' and a.amount<=0 '.' and b.userId= '.$where['userId=']
		            .' and b.copartnerId= '.$where['copartnerId='].' and b.userId not in '.'('.$uids.')'.' and a.finishYmd>='.$where['ymdForm]'].' and a.finishYmd<='.$where['ymdTo[']
		            .' ORDER BY a.amount';
		         
		        $sql=$db_rpt->execCustom(['sql'=>$sql2]);
		        $sql=$db_rpt->fetchAssocThenFree($sql);
		        $pager->init(Count($sql), $pageid);
		    
		        $rsform=$pager->rsFrom();
		        //$rsto=$rsform+$pagesize;
		        var_log($sql,'666666666666>>>>>>>>>>>>>>');
		    
		        $sql3='SELECT b.realname,a.amount,b.copartnerId from db_p2prpt.tb_recharges_final as a '.
		            'LEFT JOIN db_p2prpt.tb_user_final as b '.'on a.userId=b.userId '.
		            'where a.orderStatus=39 '.' and a.amount<=0 '.' and b.userId= '.$where['userId=']
		            .' and b.copartnerId= '.$where['copartnerId='].' and b.userId not in '.'('.$uids.')'.' and a.finishYmd>='.$where['ymdForm]'].' and a.finishYmd<='.$where['ymdTo[']
		            .' ORDER BY a.amount'.' limit '.$rsform.','.$pagesize;
		        $sql6=$db_rpt->execCustom(['sql'=>$sql3]);
		        $sql6=$db_rpt->fetchAssocThenFree($sql6);
		    
		        // var_log($sql6,'666666666666>>>>>>>>>>>>>>');
		         
		        foreach ($sql6 as $u){
		    
		    
		            if($u['copartnerId']==0){
		                $copartnername='自然量';
		            }else{
		                $copartnername=$db_p2p->getOne('db_p2p.tb_copartner_0','copartnerName',['copartnerId'=>$u['copartnerId']]);
		                if($copartnername==''){
		                    $copartnername='渠道管理中找不到此渠道号';
		                }
		            }
		    
		            $rs[]=[
		                'copartnerId'=>$u['copartnerId'],
		                'copartnerName'=>$copartnername,
		                'realname'=>substr_replace($u['realname'],'*',3,3),
		                'withdrawAmount'=>$u['amount'],
		                '_pkey_'=>$u['copartnerId'],
		            ];
		        }
		    }
		    elseif(empty($where['ymdForm]']) || empty($where['ymdTo['])){
		    
		    }
		    elseif($where['copartnerId=']!='' && $where['userId=']==''){
		    
		        var_log('22222222');
		        var_log($where,'33333333');
		        $sql2='SELECT b.realname,a.amount from db_p2prpt.tb_recharges_final as a '.
		            'LEFT JOIN db_p2prpt.tb_user_final as b '.'on a.userId=b.userId '.
		            'where a.orderStatus=39 '.'and a.amount<=0 '.' and b.userId not in '.'('.$uids.')'.'and a.finishYmd>='.$where['ymdForm]'].' and a.finishYmd<='.$where['ymdTo[']
		            .' and b.copartnerId='.$where['copartnerId='].' ORDER BY a.amount';
		    
		        $sql=$db_rpt->execCustom(['sql'=>$sql2]);
		        $sql=$db_rpt->fetchAssocThenFree($sql);
		        $pager->init(Count($sql), $pageid);
		        $rsform=$pager->rsFrom();
		    
		    
		        $sql6='SELECT b.realname,a.amount from db_p2prpt.tb_recharges_final as a '.
		            'LEFT JOIN db_p2prpt.tb_user_final as b '.'on a.userId=b.userId '.
		            'where a.orderStatus=39 '.'and a.amount<=0 '.' and b.userId not in '.'('.$uids.')'.'and a.finishYmd>='.$where['ymdForm]'].' and a.finishYmd<='.$where['ymdTo[']
		            .' and b.copartnerId='.$where['copartnerId='] .' ORDER BY a.amount'.' limit '.$rsform.','.$pagesize;;
		    
		        $sql5=$db_rpt->execCustom(['sql'=>$sql6]);
		        $sql5=$db_rpt->fetchAssocThenFree($sql5);
		    
		        if($where['copartnerId=']==0){
		            $copartnername='自然量';
		        }else{
		            $copartnername=$db_p2p->getOne('db_p2p.tb_copartner_0','copartnerName',['copartnerId'=>$where['copartnerId=']]);
		            if($copartnername==''){
		                $copartnername='渠道管理中找不到此渠道号';
		            }
		        }
		    
		        //$copartnername=$db_p2p->getOne('db_p2p.tb_copartner_0','copartnerName',['copartnerId'=>$where['copartnerId=']]);
		        foreach ($sql5 as $u){
		            $rs[]=[
		                'copartnerId'=>$where['copartnerId='],
		                'copartnerName'=>$copartnername,
		                'realname'=>substr_replace($u['realname'],'*',3,3),
		                'withdrawAmount'=>$u['amount'],
		                '_pkey_'=>$where['copartnerId='],
		            ];
		        }
		    
		    }else{
		  
		        var_log('666');
		        
		        $sql2='SELECT b.realname,a.amount,b.copartnerId  from db_p2prpt.tb_recharges_final as a '.
		            'LEFT JOIN db_p2prpt.tb_user_final as b '.'on a.userId=b.userId '.
		            'where a.orderStatus=39 '.'and a.amount<=0 '.' and b.userId not in '.'('.$uids.')'.'and a.finishYmd>='.$where['ymdForm]'].' and a.finishYmd<='.$where['ymdTo[']
		            .' ORDER BY a.amount';
		         
		        $sql=$db_rpt->execCustom(['sql'=>$sql2]);
		        $sql=$db_rpt->fetchAssocThenFree($sql);
		        $pager->init(Count($sql), $pageid);
		        
		        $rsform=$pager->rsFrom();
		        //$rsto=$rsform+$pagesize;
		         
		        
		        $sql3='SELECT b.realname,a.amount,b.copartnerId from db_p2prpt.tb_recharges_final as a '.
		            'LEFT JOIN db_p2prpt.tb_user_final as b '.'on a.userId=b.userId '.
		            'where a.orderStatus=39 '.'and a.amount<=0 '.' and b.userId not in '.'('.$uids.')'.'and a.finishYmd>='.$where['ymdForm]'].' and a.finishYmd<='.$where['ymdTo[']
		            .' ORDER BY a.amount'.' limit '.$rsform.','.$pagesize;
		        $sql6=$db_rpt->execCustom(['sql'=>$sql3]);
		        $sql6=$db_rpt->fetchAssocThenFree($sql6);
		        
		        //var_log($sql6,'666666666666>>>>>>>>>>>>>>');
		         
		        foreach ($sql6 as $u){
		        
		        
		            if($u['copartnerId']==0){
		                $copartnername='自然量';
		            }else{
		                $copartnername=$db_p2p->getOne('db_p2p.tb_copartner_0','copartnerName',['copartnerId'=>$u['copartnerId']]);
		                if($copartnername==''){
		                    $copartnername='渠道管理中找不到此渠道号';
		                }
		            }
		        
		            $rs[]=[
		                'copartnerId'=>$u['copartnerId'],
		                'copartnerName'=>$copartnername,
		                'realname'=>substr_replace($u['realname'],'*',3,3),
		                'withdrawAmount'=>$u['amount'],
		                '_pkey_'=>$u['copartnerId'],
		            ];
		        }
		        
		    }
		    
		}
		
		if($saveAsExcel){
		    // var_log($records,'records>>>>>>>>');
		    return $this->downExcel($rs, array_keys($headers));
		}
		
	
 		$this->_view->assign('headers', $headers);
 		$this->_view->assign('records', $rs);
 		$this->_view->assign('pager', $pager);
 		$this->_view->assign('where', $where);
	}
	
	
	
}