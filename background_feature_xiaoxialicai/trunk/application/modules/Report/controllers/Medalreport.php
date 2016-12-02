<?php

use Sooh\Base\Form\Item as form_def;
use Rpt\Medal\MedalFinal;

/**
 * Description of Medalreport
 *
 * @author wu.chen
 */
class MedalreportController extends \Prj\ManagerCtrl {

    protected $pageSizeEnum = [30, 50, 100];

    public function indexAction() {
        $pageid = $this->_request->get('pageId', 1);
        $pagesize = $this->_request->get('pageSize', current($this->pageSizeEnum));
        $pager = new \Sooh\DB\Pager($pagesize, $this->pageSizeEnum);
        $frm = \Sooh\Base\Form\Broker::getCopy('default')
                ->init(\Sooh\Base\Tools::uri(), 'get', \Sooh\Base\Form\Broker::type_s);
        $frm->addItem('pageid', $pageid)
                ->addItem('pagesize', $pager->page_size);

        $frm->fillValues();

        $medalFinal = new MedalFinal();
        $pager->total = $medalFinal->getRecordsCount();    //获得总数
        $records = FALSE;
        if ($pager->total) {
            $pageid -= 1;
            $pageid *= $pagesize;
            $records = $medalFinal->getRecords($pageid, $pagesize);   //获得列表
        }
        $buyNUsrAll = \Prj\Data\User::getAllBuyUser();        
        $headers = ['勋章名称' => 70, '勋章英文名称' => 70, '勋章统计' => 100];
        $this->_view->assign('records', $records);
        $this->_view->assign('buyNUsrAll', $buyNUsrAll);
        $this->_view->assign('headers', $headers);
        $this->_view->assign('pager', $pager);
    }

}
