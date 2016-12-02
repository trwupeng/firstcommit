<?php
use Sooh\Base\Form\Item as form_def;
use Sooh\Base\Form\Options as options_def;
use \Rpt\DataDig\CopartnerWorthDig as CopartnerWorthDig;

class WithdrawuserController extends \Prj\ManagerCtrl{
	
	public function init() {
		parent::init();
	}
	
	protected $pageSizeEnum = [50,100, 150, 300, 500, 1000];

	public function indexAction() {
		$isDownloadExcel = $this->_request->get('__EXCEL__');
		
		$pageid = $this->_request->get('pageId',1)-0;
		$pagesize = $this->_request->get('pageSize',current($this->pageSizeEnum))-0;
		$pager = new \Sooh\DB\Pager($pagesize,$this->pageSizeEnum,false);
		$dt_instance = \Sooh\Base\Time::getInstance();
		$ymdFrom = date('Y-m-d', $dt_instance->timestamp(-30));
		$ymdTo = $dt_instance->yesterday('Y-m-d');
		$ymdOne=$dt_instance->yesterday('Ymd');
       // var_log($ymdOne,'one>>>>>>>>>>');
        
		$ymdDefault = \Sooh\Base\Time::getInstance()->yesterday('Y-m-d');
		$form = \Sooh\Base\Form\Broker::getCopy('default')
			->init(\Sooh\Base\Tools::uri(),'get',\Sooh\Base\Form\Broker::type_s);
		$form->addItem('_userId_eq', form_def::factory('用户Id', '', form_def::text))
		       ->addItem('_copartnerId_eq', form_def::factory('渠道Id', '', form_def::text))
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
        
	    var_log($where,'where>>>>');
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
		
		
		if($isDownloadExcel){
		    
		    $where=$this->_request->get('where');
		    
		   // var_log($where,'where>>>>>>>>>>');
		    $borrower=$db_p2p->getOne('db_p2p.tb_config','v',['k'=>'borrower']);
		     
		    $borrowers=json_decode($borrower,true);
		     
		    var_log($borrowers,'>>>>>>>>>');
		    $rename=count($borrowers);
		     
		    var_log($rename,'>>>>>>>>>>>>>');
		     
		    foreach ($borrowers as $k=>$v){
		         
		        $uids.='"'.$k.'"'.',';
		    }
		    $uids= rtrim($uids,',');
		    
		    
		    if((empty($where['userId=']) && empty($where['copartnerId=']) && $where['userId=']!==Null && $where['copartnerId=']!==Null)==true){
		    
		       // var_log('4444444444');
		    
		        $sql1='SELECT b.realname,a.amount,b.copartnerId,b.userId from db_p2prpt.tb_recharges_final as a '.
		            'LEFT JOIN db_p2prpt.tb_user_final as b '.'on a.userId=b.userId '.
		            'where a.orderStatus=39 '.'and a.amount<=0 '.' and b.userId not in '.'('.$uids.')'.'and a.finishYmd='.$ymdOne
		            . ' ORDER BY a.amount';
		    
		        $sql=$db_rpt->execCustom(['sql'=>$sql1]);
		        $sql=$db_rpt->fetchAssocThenFree($sql);
		        $pager->init(Count($sql), $pageid);
		    
		        $rsform=$pager->rsFrom();
		        //$rsto=$rsform+$pagesize;
		    
		        $sql4='SELECT b.realname,a.amount,b.copartnerId,b.userId from db_p2prpt.tb_recharges_final as a '.
		            'LEFT JOIN db_p2prpt.tb_user_final as b '.'on a.userId=b.userId '.
		            'where a.orderStatus=39 '.'and a.amount<=0 '.' and b.userId not in '.'('.$uids.')'.'and a.finishYmd='.$ymdOne
		            . ' ORDER BY a.amount'.' limit '.$rsform.','.$pagesize;
		    
		        $sql5=$db_rpt->execCustom(['sql'=>$sql4]);
		        $sql5=$db_rpt->fetchAssocThenFree($sql5);
		    
		        foreach ($sql5 as $u){
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
		    }elseif(!empty($where['userId=']) && empty($where['copartnerId='])){
		    
		       // var_log('333333333333');
		    
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
		    
		    elseif($where['copartnerId=']!='' && $where['userId=']==null){
		    
		        var_log('22222222222');
		       
		        $sql2='SELECT b.realname,a.amount,b.copartnerId  from db_p2prpt.tb_recharges_final as a '.
		            'LEFT JOIN db_p2prpt.tb_user_final as b '.'on a.userId=b.userId '.
		            'where a.orderStatus=39 '.' and a.amount<=0 '.' and b.copartnerId= '.$where['copartnerId='].' and b.userId not in '.'('.$uids.')'.' and a.finishYmd>='.$where['ymdForm'].' and a.finishYmd<='.$where['ymdTo[']
		            .' ORDER BY a.amount';
		         
		        $sql=$db_rpt->execCustom(['sql'=>$sql2]);
		        $sql=$db_rpt->fetchAssocThenFree($sql);
		        $pager->init(Count($sql), $pageid);
		    
		        $rsform=$pager->rsFrom();
		        //$rsto=$rsform+$pagesize;
		         
		    
		        $sql3='SELECT b.realname,a.amount,b.copartnerId from db_p2prpt.tb_recharges_final as a '.
		            'LEFT JOIN db_p2prpt.tb_user_final as b '.'on a.userId=b.userId '.
		            'where a.orderStatus=39 '.' and a.amount<=0 '.' and b.copartnerId= '.$where['copartnerId='].' and b.userId not in '.'('.$uids.')'.' and a.finishYmd>='.$where['ymdForm'].' and a.finishYmd<='.$where['ymdTo[']
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
		    
		    elseif(!empty($where['userId=']) && !empty($where['copartnerId='])){
		    
		     var_log('11111111111111111');
		     
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
		         
		    
		        $sql3='SELECT b.realname,a.amount,b.copartnerId from db_p2prpt.tb_recharges_final as a '.
		            'LEFT JOIN db_p2prpt.tb_user_final as b '.'on a.userId=b.userId '.
		            'where a.orderStatus=39 '.' and a.amount<=0 '.' and b.userId= '.$where['userId=']
		            .' and b.copartnerId= '.$where['copartnerId='].' and b.userId not in '.'('.$uids.')'.' and a.finishYmd>='.$where['ymdForm'].' and a.finishYmd<='.$where['ymdTo[']
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
		    
		    
		    }elseif(empty($where['ymdForm']) || empty($where['ymdTo['])){
		        $rename=0;
		    
		    }
		    else {
		         
		         var_log('232323');
		    
		        $sql2='SELECT b.realname,a.amount,b.copartnerId  from db_p2prpt.tb_recharges_final as a '.
		            'LEFT JOIN db_p2prpt.tb_user_final as b '.'on a.userId=b.userId '.
		            'where a.orderStatus=39 '.'and a.amount<=0 '.' and b.userId not in '.'('.$uids.')'.'and a.finishYmd>='.$where['ymdForm'].' and a.finishYmd<='.$where['ymdTo[']
		            .' ORDER BY a.amount';
		         
		        $sql=$db_rpt->execCustom(['sql'=>$sql2]);
		        $sql=$db_rpt->fetchAssocThenFree($sql);
		        $pager->init(Count($sql), $pageid);
		    
		        $rsform=$pager->rsFrom();
		        //$rsto=$rsform+$pagesize;
		         
		    
		        $sql3='SELECT b.realname,a.amount,b.copartnerId from db_p2prpt.tb_recharges_final as a '.
		            'LEFT JOIN db_p2prpt.tb_user_final as b '.'on a.userId=b.userId '.
		            'where a.orderStatus=39 '.'and a.amount<=0 '.' and b.userId not in '.'('.$uids.')'.'and a.finishYmd>='.$where['ymdForm'].' and a.finishYmd<='.$where['ymdTo[']
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
		    
		    
		}
		
		else{
		    //页面展示部分
		    
		    $borrower=$db_p2p->getOne('db_p2p.tb_config','v',['k'=>'borrower']);
		     
		    $borrowers=json_decode($borrower,true);
		     
		    var_log($borrowers,'>>>>>>>>>');
		    $rename=count($borrowers);
		     
		    var_log($rename,'>>>>>>>>>>>>>');
		     
		    foreach ($borrowers as $k=>$v){
		         
		        $uids.='"'.$k.'"'.',';
		    }
		    $uids= rtrim($uids,',');
		    
		    if((empty($where['userId=']) && empty($where['copartnerId=']) && $where['userId=']!==Null && $where['copartnerId=']!==Null)==true){
		    
		        var_log('11111111111');
	
		        $sql1='SELECT b.realname,a.amount,b.copartnerId,b.userId from db_p2prpt.tb_recharges_final as a '.
		            'LEFT JOIN db_p2prpt.tb_user_final as b '.'on a.userId=b.userId '.
		            'where a.orderStatus=39 '.'and a.amount<=0 '.' and b.userId not in '.'('.$uids.')'.'and a.finishYmd='.$ymdOne
		            . ' ORDER BY a.amount';
		    
		        $sql=$db_rpt->execCustom(['sql'=>$sql1]);
		        $sql=$db_rpt->fetchAssocThenFree($sql);
		        $pager->init(Count($sql), $pageid);
		    
		        $rsform=$pager->rsFrom();
		        //$rsto=$rsform+$pagesize;
		    
		        $sql4='SELECT b.realname,a.amount,b.copartnerId,b.userId from db_p2prpt.tb_recharges_final as a '.
		            'LEFT JOIN db_p2prpt.tb_user_final as b '.'on a.userId=b.userId '.
		            'where a.orderStatus=39 '.'and a.amount<=0 '.' and b.userId not in '.'('.$uids.')'.'and a.finishYmd='.$ymdOne
		            . ' ORDER BY a.amount'.' limit '.$rsform.','.$pagesize;
		    
		        $sql5=$db_rpt->execCustom(['sql'=>$sql4]);
		        $sql5=$db_rpt->fetchAssocThenFree($sql5);
		    
		        foreach ($sql5 as $u){
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
		    }elseif($where['userId=']!='' && $where['copartnerId=']==''){
		    
		       var_log('44444');
		       var_log($where,'44444444');

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
		    
		    elseif($where['copartnerId=']!='' && $where['userId=']==''){
		    
		        var_log('2366666');

		        $sql2='SELECT b.realname,a.amount,b.copartnerId  from db_p2prpt.tb_recharges_final as a '.
		            'LEFT JOIN db_p2prpt.tb_user_final as b '.'on a.userId=b.userId '.
		            'where a.orderStatus=39 '.' and a.amount<=0 '.' and b.copartnerId= '.$where['copartnerId='].' and b.userId not in '.'('.$uids.')'.' and a.finishYmd>='.$where['ymdForm]'].' and a.finishYmd<='.$where['ymdTo[']
		            .' ORDER BY a.amount';
		         
		        $sql=$db_rpt->execCustom(['sql'=>$sql2]);
		        $sql=$db_rpt->fetchAssocThenFree($sql);
		        $pager->init(Count($sql), $pageid);
		    
		        $rsform=$pager->rsFrom();
		        //$rsto=$rsform+$pagesize;
		         
		    
		        $sql3='SELECT b.realname,a.amount,b.copartnerId from db_p2prpt.tb_recharges_final as a '.
		            'LEFT JOIN db_p2prpt.tb_user_final as b '.'on a.userId=b.userId '.
		            'where a.orderStatus=39 '.' and a.amount<=0 '.' and b.copartnerId= '.$where['copartnerId='].' and b.userId not in '.'('.$uids.')'.' and a.finishYmd>='.$where['ymdForm]'].' and a.finishYmd<='.$where['ymdTo[']
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

		      var_log('2323223');

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
		    
		    
		    }elseif(empty($where['ymdForm]']) || empty($where['ymdTo['])){
		        //$rename=0;
		        $borrower=$db_p2p->getOne('db_p2p.tb_config','v',['k'=>'borrower']);
		        
		        $borrowers=json_decode($borrower,true);
		        
		        var_log($borrowers,'>>>>>>>>>');
		        $rename=count($borrowers);
		    
		    }
		    else {
		         
		    var_log('6666');

		        $sql2='SELECT b.realname,a.amount,b.copartnerId  from db_p2prpt.tb_recharges_final as a '.
		            'LEFT JOIN db_p2prpt.tb_user_final as b '.'on a.userId=b.userId '.
		            'where a.orderStatus=39 '.'and a.amount<=0 '.' and b.userId not in '.'('.$uids.')'.' and a.finishYmd>='.$where['ymdForm]'].' and a.finishYmd<='.$where['ymdTo[']
		            .' ORDER BY a.amount';
		         
		        
		        $sql=$db_rpt->execCustom(['sql'=>$sql2]);
		        $sql=$db_rpt->fetchAssocThenFree($sql);
		        $pager->init(Count($sql), $pageid);
		    
		        $rsform=$pager->rsFrom();
		        //$rsto=$rsform+$pagesize;
		         
		        var_log(\Sooh\DB\Broker::lastCmd(),'sql>>>>>>>>');
		        
		        $sql3='SELECT b.realname,a.amount,b.copartnerId from db_p2prpt.tb_recharges_final as a '.
		            'LEFT JOIN db_p2prpt.tb_user_final as b '.'on a.userId=b.userId '.
		            'where a.orderStatus=39 '.'and a.amount<=0 '.' and  b.userId not in '.'('.$uids.')'.'and a.finishYmd>='.$where['ymdForm]'].' and a.finishYmd<='.$where['ymdTo[']
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
	   
		if($isDownloadExcel){
		    return $this->downExcel($rs, array_keys($headers));
		}

 		$this->_view->assign('headers', $headers);
 		$this->_view->assign('records', $rs);
 		$this->_view->assign('pager', $pager);
 		$this->_view->assign('where', $where);
 		$this->_view->assign('realname', $rename);
	}

}