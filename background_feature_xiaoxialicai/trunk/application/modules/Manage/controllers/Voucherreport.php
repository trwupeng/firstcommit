<?php
use Sooh\Base\Form\Item as form_def;
use Sooh\Base\Form\Options as options_def;

class VoucherreportController extends \Prj\ManagerCtrl {
    public function indexAction() {
        $pageId             = $this->_request->get('pageId', 1) - 0;
        $pageSize           = $this->_request->get('pageSize', 10);
        $isDownloadExcel    = $this->_request->get('__EXCEL__') == 1;
        $pager = new \Sooh\DB\Pager($pageSize, $this->pageSizeEnum, false);
        $formObj = \Sooh\Base\Form\Broker::getCopy('default')->init(\Sooh\Base\Tools::uri(),'get',
                    \Sooh\Base\Form\Broker::type_s);
        $formObj->addItem('_ymd_g2', form_def::factory('日期从', date('Y-m-d', time()-15*86400), form_def::datepicker))
                ->addItem('_ymd_l2', form_def::factory('到', date('Y-m-d'), form_def::datepicker))
                ->addItem('_voucherType_eq', form_def::factory('券类型', '', form_def::select)->initMore(new
			         \Sooh\Base\Form\Options(\Prj\Consts\Voucher::$voucherTypeArr, '不限')))
			    ->addItem('pageId', $pageId)
                ->addItem('pageSize', $pageSize);
        $formObj->fillValues();
        if ($formObj->flgIsThisForm) {
            $where = $formObj->getWhere();
            $where['ymd]'] = str_replace('-', '', $where['ymd]']);
            $where['ymd['] = str_replace('-', '', $where['ymd[']);
        }else {
            $where['ymd]'] = date('Ymd', time()-15*86400);
            $where['ymd['] = date('Ymd');
        }
        
        
        $o = new \Rpt\DataDig\VouchersData ();
        $records = $o->exportData($where);
        $headers = $o->getHeaders();
        if ($isDownloadExcel) {
            return $this->downExcel($records, array_keys($headers),null, false);
        }
        
        $pager->init(sizeof($records), $pageId);
        $records = array_slice($records, ($pageId-1)*$pageSize, $pageSize);
        
        $this->_view->assign('headers', $headers);
        $this->_view->assign('records', $records);
        $this->_view->assign('pager', $pager);
    }
}