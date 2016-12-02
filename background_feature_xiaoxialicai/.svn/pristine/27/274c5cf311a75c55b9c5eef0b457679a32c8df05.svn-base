<?php
use Sooh\Base\Form\Item as form_def;
use Sooh\Base\Form\Options as options_def;

class CopartnerworthController extends \Prj\ManagerCtrl{
	
	public function init() {
		parent::init();
	}
	protected $copartners;
	protected $promotionWay;
	public function indexAction() {
		$clientType= \Prj\Consts\ClientType::clientTypes();
		$this->copartners = \Rpt\DataDig\CopartnerWorthData::getCopartners();
		$promotionWay = array('cpc'=>cpc,'cpa'=>cpa);
	
		$saveAsExcel = $this->_request->get('__EXCEL__');
		$pageid = $this->_request->get('pageId',1)-0;
		$pagesize = $this->_request->get('pageSize',current($this->pageSizeEnum))-0;
		$pager = new \Sooh\DB\Pager($pagesize,$this->pageSizeEnum,false);
		$formEdit = \Sooh\Base\Form\Broker::getCopy('default')
			->init(\Sooh\Base\Tools::uri(),'get',\Sooh\Base\Form\Broker::type_s);
		$formEdit->addItem('_ymd_eq',form_def::factory('日期',date('Y-m-d'), form_def::datepicker))
// 		  $formEdit->addItem('_ymd_g2',form_def::factory('日期从',date('Y-m-d',time()-7*86400), form_def::datepicker))
// 		        ->addItem('_ymd_l2',form_def::factory('到',date('Y-m-d'), form_def::datepicker))
				->addItem('_clientType_eq',form_def::factory('客户端类型','',form_def::select))
				->addItem('_copartnerId_eq',form_def::factory('渠道','',form_def::select))
				->addItem('_promotionWay_eq',form_def::factory('推广方式','',form_def::select));
		$formEdit->items['_clientType_eq']->options = new options_def($clientType, '全部');
		$formEdit->items['_copartnerId_eq']->options = new options_def($this->copartners, '全部');
		$formEdit->items['_promotionWay_eq']->options = new options_def($promotionWay,'全部');
		
		$formEdit->fillValues();
		if ($formEdit->flgIsThisForm){
			$where = $formEdit->getWhere();
			$where['ymd='] = str_replace('-', '', $where['ymd=']);
		}else{
			$where=array('ymd='=>date('Ymd'));
		}
		if ($saveAsExcel){
			$where = $this->_request->get('where');
		}
		$o = new \Rpt\DataDig\CopartnerWorthData();
		$records = $o->exportRecords($where);
		$headers = $o->getHeaders('all');
		if ($saveAsExcel==1) {
				
			foreach ($records as $k => $r) {
				array_shift($records[$k]);
			}
			return $this->downEXCEL($records, array_keys($headers), null, false);
			
		}else {
			$pager->init(sizeof($records), $pageid);
			$records = array_slice($records, ($pageid-1)*$pagesize,$pagesize);
			$this->_view->assign('headers', $headers);
			$this->_view->assign('records', $records);
			$this->_view->assign('pager', $pager);
			$this->_view->assign('where',$where);
		}
	}
	
	public function detailAction () {
		$clientType= \Prj\Consts\ClientType::clientTypes();
		$promotionWay = array('cpc'=>cpc,'cpa'=>cpa); // 需要修改，indexAction中也需要修改
		$_pkey_val_ = $this->_request->get('_pkey_val_');
		$_pkey_val_ = \Prj\Misc\View::decodePkey($_pkey_val_);
		$copartnerId = $_pkey_val_['copartnerId='];
		
		$saveAsExcel = $this->_request->get('__EXCEL__');
		$pageid = $this->_request->get('pageId',1)-0;
		$pagesize = $this->_request->get('pageSize',current($this->pageSizeEnum))-0;
		$pager = new \Sooh\DB\Pager($pagesize,$this->pageSizeEnum,false);
		$formEdit = \Sooh\Base\Form\Broker::getCopy('default')
				->init(\Sooh\Base\Tools::uri(),'get',\Sooh\Base\Form\Broker::type_s);
		$formEdit->addItem('_ymd_g2',form_def::factory('日期从',date('Y-m-d',time()-7*86400), form_def::datepicker))
				->addItem('_copartnerId_eq',form_def::factory('渠道Id', $copartnerId?$copartnerId:null, form_def::hidden))
				->addItem('_ymd_l2',form_def::factory('到',date('Y-m-d'), form_def::datepicker))
				->addItem('_clientType_eq',form_def::factory('客户端类型','',form_def::select))
				->addItem('_promotionWay_eq',form_def::factory('推广方式','',form_def::select));
		$formEdit->items['_clientType_eq']->options = new options_def($clientType, '全部');
		$formEdit->items['_promotionWay_eq']->options = new options_def($promotionWay,'全部');
	
		$formEdit->fillValues();
		if ($formEdit->flgIsThisForm){
			$where = $formEdit->getWhere();
			$where['ymd]'] = str_replace('-', '', $where['ymd]']);
			$where['ymd['] = str_replace('-', '', $where['ymd[']);
		}else {
			$where['ymd]']=date('Ymd', time()-7*86400);
			$where['ymd[']=date('Ymd');
			$where['copartnerId']=$copartnerId;
			$formEdit->items['_copartnerId_eq'] = $copartnerId;
		}
		if ($saveAsExcel){
			$where = $this->_request->get('where');
var_log($where, 'saveAsExcel>>>>>>> where >>>>>>>>');			
		}
// var_log($where, 'where DitailAction>>>>>>>>>>>>>>>>>>');		
		$o = new \Rpt\DataDig\CopartnerWorthData($where);
		$records = $o->exportDetaiData($where);
// var_log($records, 'records>>>>>>>>>>>');		
		$headers = $o->getHeaders('detail');
		if ($saveAsExcel==1) {
// 		    foreach ($records as $k => $r) {
// 		        array_shift($records[$k]);
// 		    }
		    return $this->downEXCEL($records, array_keys($headers), null, false);
		}

		$this->_view->assign('records', $records);
		$this->_view->assign('headers', $headers);
		$this->_view->assign('where', $where);
	}
	
}