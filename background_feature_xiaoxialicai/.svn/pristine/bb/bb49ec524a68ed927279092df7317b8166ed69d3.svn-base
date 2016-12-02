<?php
use Sooh\Base\Form\Item as form_def;
use Sooh\Base\Form\Options as options_def;

class RedpacketreportController extends \Prj\ManagerCtrl {
    protected $pageSizeEnum = [50,100, 150, 300, 500, 1000];

    public function indexAction() {
        $saveAsExcel = $this->_request->get('__EXCEL__');
        $pageid = $this->_request->get('pageId',1)-0;
        $pagesize = $this->_request->get('pageSize',current($this->pageSizeEnum))-0;
        $pager = new \Sooh\DB\Pager($pagesize,$this->pageSizeEnum,false);

        $dt_instance = \Sooh\Base\Time::getInstance();
        $ymdFrom = date('Y-m-d', $dt_instance->timestamp(-20));
        $ymdTo = date('Y-m-d', $dt_instance->timestamp());
        $form = \Sooh\Base\Form\Broker::getCopy('default')
            ->init(\Sooh\Base\Tools::uri(),'get',\Sooh\Base\Form\Broker::type_s);
        $form->addItem('_ymdCreate_g2', form_def::factory('日期从', $ymdFrom, form_def::datepicker))
            ->addItem('_ymdCreate_l2', form_def::factory('到', $ymdTo, form_def::datepicker))
            ->addItem('pageid', $pageid)
            ->addItem('pagesize', $pager->page_size);

        $form->fillValues();
        $where = $form->getWhere();
        $ymdFrom = date('Ymd', strtotime($where['ymdCreate]']));
        $ymdTo = date('Ymd', strtotime($where['ymdCreate[']));
        $o = new \Rpt\DataDig\RedpacketreportDataDig();
        $headers = $o->getHeadersDailyData();

        if($saveAsExcel){
            $records = $o->getDailyDataRecords($ymdFrom, $ymdTo);
            return $this->downExcel($records, array_keys($headers));
        }else{
            $o->getDailyDataRecords($ymdFrom, $ymdTo, true);
            $pager->init($o->redpacketDailyDataCount(), $pageid);
            $records = $o->getDailyDataRecordsByPage($pager->page_size, $pager->rsFrom());
            $this->_view->assign('records', $records);
        }
        $this->_view->assign('pager', $pager);
        $this->_view->assign('headers', $headers);
        $this->_view->assign('where', $where);

    }

    public function detailOfCategoryAction() {
        $saveAsExcel = $this->_request->get('__EXCEL__');
        $pageid = $this->_request->get('pageId',1)-0;
        $pagesize = $this->_request->get('pageSize',current($this->pageSizeEnum))-0;
        $pager = new \Sooh\DB\Pager($pagesize,$this->pageSizeEnum,false);
        $pkey = $this->_request->get('_pkey_');

        $form = \Sooh\Base\Form\Broker::getCopy('default')
            ->init(\Sooh\Base\Tools::uri(),'get',\Sooh\Base\Form\Broker::type_s);
        $form->addItem('_ymd_eq', form_def::factory('日期', date('Y-m-d', strtotime($pkey)), form_def::datepicker))
            ->addItem('pageid', $pageid)
            ->addItem('pagesize', $pager->page_size);

        $form->fillValues();
        $where = $form->getWhere();
        $ymd = date('Ymd', strtotime($where['ymd=']));
        $o = new \Rpt\DataDig\RedpacketreportDataDig();
        $records = $o->recordsByCategory($ymd);
        $headers = $o->headerOfCategory;

        if($saveAsExcel){
            $where = $this->_request->get('where');
            $ymd = date('Ymd', strtotime($where['ymd=']));
            $records = $o->recordsByCategory($ymd);
            return $this->downExcel($records, array_keys($headers));
        }
        $this->_view->assign('headers', $headers);
        $this->_view->assign('records', $records);
        $this->_view->assign('pager', $pager);
        $this->_view->assign('where', $where);
    }

    public function rankAction () {
        $rankBasis = ['红包产出排行','红包消耗排行','红包失效排行'];
        $saveAsExcel = $this->_request->get('__EXCEL__');
        $pageid = $this->_request->get('pageId',1)-0;
        $pagesize = $this->_request->get('pageSize',current($this->pageSizeEnum))-0;
        $pager = new \Sooh\DB\Pager($pagesize,$this->pageSizeEnum,false);
        $pkey = $this->_request->get('_pkey_');
        $form = \Sooh\Base\Form\Broker::getCopy('default')
            ->init(\Sooh\Base\Tools::uri(),'get',\Sooh\Base\Form\Broker::type_s);
        $form->addItem('_ymd_eq', form_def::factory('日期', date('Y-m-d', strtotime($pkey)), form_def::datepicker))
            ->addItem('_rankBasis_eq', form_def::factory('按照', 0, form_def::select)->initMore(new options_def($rankBasis)))
            ->addItem('pageid', $pageid)
            ->addItem('pagesize', $pager->page_size);
        $form->fillValues();
        $where = $form->getWhere();
        $ymd = date('Ymd', strtotime($where['ymd=']));
        $rankBase = $where['rankBasis='];
        $o = new \Rpt\DataDig\RedpacketreportDataDig();
        $headers = $o->rankheaders;
        if($saveAsExcel){
            $where = $this->_request->get('where');
            $ymd = date('Ymd', strtotime($where['ymd=']));
            $rankBase = $where['rankBasis='];
            $records = $o->rankRecords($ymd, $rankBase);

            return $this->downExcel($records, array_keys($o->rankheaders));
        }else {
            $records = $o->rankRecords($ymd, $rankBase, true);
        }

        $this->_view->assign('records', $records);
        $this->_view->assign('pager', $pager);
        $this->_view->assign('headers', $headers);
        $this->_view->assign('where', $where);
    }

    public function userredpacketdailyAction() {
        $saveAsExcel = $this->_request->get('__EXCEL__');
        $pageid = $this->_request->get('pageId',1)-0;
        $pagesize = $this->_request->get('pageSize',current($this->pageSizeEnum))-0;
        $pager = new \Sooh\DB\Pager($pagesize,$this->pageSizeEnum,false);
        $pkey = $this->_request->get('_pkey_');
        if(!empty($pkey)) {
            $pkey = \Prj\Misc\View::decodePkey($pkey);
            $userId = $pkey['userId'];
            $ymdFrom = $pkey['ymd'];
            $ymdTo = $pkey['ymd'];
        }else {
            $dt = \Sooh\Base\Time::getInstance();
            $ymdFrom = date('Y-m-d', $dt->timestamp()-7*86400);
            $ymdTo = date('Y-m-d', $dt->timestamp());
            $userId = '';
        }
        $form = \Sooh\Base\Form\Broker::getCopy('default')
            ->init(\Sooh\Base\Tools::uri(),'get',\Sooh\Base\Form\Broker::type_s);
        $form->addItem('_ymd_g2', form_def::factory('日期从', date('Y-m-d', strtotime($ymdFrom)), form_def::datepicker))
            ->addItem('_ymd_l2', form_def::factory('到', date('Y-m-d', strtotime($ymdTo)), form_def::datepicker))
            ->addItem('_userId_eq', form_def::factory('用户ID', $userId, form_def::text))
            ->addItem('_phone_eq', form_def::factory('手机号', '', form_def::text))
            ->addItem('pageid', $pageid)
            ->addItem('pagesize', $pager->page_size);
        $form->fillValues();
        $where = $form->getWhere();
        $saveAsExcelWhere = $this->_request->get('where');

        if(!empty($saveAsExcelWhere)) {
            $where = \Prj\Misc\View::decodePkey($saveAsExcelWhere);
        }
        if(!empty($where['userId=']) || !empty($where['phone='])) {
            $ymdFrom = date('Ymd', strtotime($where['ymd]']));
            $ymdTo = date('Ymd', strtotime($where['ymd[']));
            if($ymdTo < $ymdFrom) {
                return $this->returnError('日期不正确');
            }

            if(!empty($where['userId=']) && !empty($where['phone='])) {
                $userId = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_p2prpt)->getOne(\Rpt\Tbname::tb_user_final, 'userId', ['phone'=>$where['phone=']]);
                if($userId != $where['userId=']){
                    $this->returnError('填写的用户号码和用户ID不一致');
                }
            }elseif(!empty($where['userId='])) {
                $userId = $where['userId='];
            }elseif(!empty($where['phone='])){
                $userId = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_p2prpt)->getOne(\Rpt\Tbname::tb_user_final, 'userId', ['phone'=>$where['phone=']]);
            }

            if(!empty($userId)) {
                $o = new \Rpt\DataDig\RedpacketreportDataDig();
                $headers = $o->rankheaders;
                $records = $o->getUserRedPacketRecords($ymdFrom, $ymdTo, $userId);
                if($saveAsExcel) {
                    return $this->downExcel($records, array_keys($headers));
                }
                $this->_view->assign('headers', $headers);
                $pager->init($o->getUserRedPacketRecordsCount(), $pageid);
                $records = $o->getUserRedPacketRecordsByPage($pager->page_size, $pager->rsFrom());
                $this->_view->assign('records', $records);
            }
        }

        $this->_view->assign('pager', $pager);
        $this->_view->assign('where', \Prj\Misc\View::encodePkey($where));
    }
}