<?php
use Sooh\Base\Form\Item as form_def;
use Sooh\Base\Form\Options as options_def;
use \Rpt\DataDig\CopartnerWorthDig as CopartnerWorthDig;

class CopartnerworthController extends \Prj\ManagerCtrl{
	
	public function init() {
		parent::init();
	}
	protected $copartners;
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
		$o = new CopartnerWorthDig();
		$pager->init($o->recordsCount($where), $pageid);

		$headers = $o->headerOfDayData();
		if($saveAsExcel) {
			$where = $this->_request->get('where');
			$keys = $this->_request->get('ids');
			if(!empty($keys)) {
				$where['contractId'] = $keys;
			}
			$records = $o->fetchRecords($where);
		}else {
			$records = $o->fetchRecords($where, $pager->page_size, $pager->rsFrom(), true);
		}
		if($saveAsExcel){
			return $this->downExcel($records, array_keys($headers));
		}
		$this->_view->assign('records', $records);
		$this->_view->assign('pager', $pager);
		$this->_view->assign('headers', $headers);
		$this->_view->assign('where', $where);

	}


	public function contractdailyAction () {
		$saveAsExcel = $this->_request->get('__EXCEL__');
		$pageid = $this->_request->get('pageId',1)-0;
		$pagesize = $this->_request->get('pageSize',current($this->pageSizeEnum))-0;
		$pager = new \Sooh\DB\Pager($pagesize,$this->pageSizeEnum,false);
		$dt_instance = \Sooh\Base\Time::getInstance();
		$ymdFrom = date('Y-m-d', $dt_instance->timestamp(-21));
		$ymdTo = $dt_instance->yesterday('Y-m-d');
		$contractId = $this->_request->get('_pkey_');
		$form = \Sooh\Base\Form\Broker::getCopy('default')
				->init(\Sooh\Base\Tools::uri(),'get',\Sooh\Base\Form\Broker::type_s);
		$form->addItem('_ymd_g2', form_def::factory('日期从', $ymdFrom, form_def::datepicker))
				->addItem('_ymd_l2', form_def::factory('到', $ymdTo, form_def::datepicker))
				->addItem('_contractId_eq', form_def::factory('协议', $contractId, form_def::hidden))
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
		$o = new CopartnerWorthDig();
		$pager->init($o->recordsCount($where), $pageid);
		$headers = $o->headerOfDayData();
		if($saveAsExcel) {
			$where = \Prj\Misc\View::decodePkey($this->_request->get('where'));
			$records = $o->fetchRecords($where);
		}else {
			$records = $o->fetchRecords($where,$pager->page_size, $pager->rsFrom(), true);
		}
		if($saveAsExcel){
			return $this->downExcel($records, array_keys($headers));
		}
		$this->_view->assign('records', $records);

		$this->_view->assign('pager', $pager);
		$this->_view->assign('headers', $headers);
		$this->_view->assign('where', \Prj\Misc\View::encodePkey($where));

	}

	public function userAction () {
		$saveAsExcel = $this->_request->get('__EXCEL__');
		$pageid = $this->_request->get('pageId',1)-0;
		$pagesize = $this->_request->get('pageSize',current($this->pageSizeEnum))-0;
		$pager = new \Sooh\DB\Pager($pagesize,$this->pageSizeEnum,false);
		$dt_instance = \Sooh\Base\Time::getInstance();
		$ymdFrom = date('Y-m-d', $dt_instance->timestamp(-21));
		$ymdTo = $dt_instance->yesterday('Y-m-d');
		$contractId = $this->_request->get('_pkey_');
		$form = \Sooh\Base\Form\Broker::getCopy('default')
				->init(\Sooh\Base\Tools::uri(),'get',\Sooh\Base\Form\Broker::type_s);
		$form->addItem('_ymdReg_g2', form_def::factory('日期从', $ymdFrom, form_def::datepicker))
				->addItem('_ymdReg_l2', form_def::factory('到', $ymdTo, form_def::datepicker))
				->addItem('_contractId_eq', form_def::factory('协议', $contractId, form_def::hidden))
				->addItem('pageid', $pageid)
				->addItem('pagesize', $pager->page_size);
		$form->fillValues();
		$where = $form->getWhere();
		if($where['ymdReg]']) {
			$where['ymdReg]'] = date('Ymd', strtotime($where['ymdReg]']));
		}
		if($where['ymdReg[']){
			$where['ymdReg['] = date('Ymd', strtotime($where['ymdReg[']));
		}

		$o = new CopartnerWorthDig();
		$pager->init($o->userRecordsCount($where), $pageid);
		$headers = $o->getUserDataHeader();

		if($saveAsExcel) {
			$where = \Prj\Misc\View::decodePkey($this->_request->get('where'));
			$records = $o->userRecords($where);
			return $this->downExcel($records, array_keys($headers));
		}else{
			$records = $o->userRecords($where, $pager->page_size, $pager->rsFrom());
		}
		$this->_view->assign('headers', $headers);
		$this->_view->assign('records', $records);
		$this->_view->assign('pager', $pager);
		$this->_view->assign('where', \Prj\Misc\View::encodePkey($where));
	}
}