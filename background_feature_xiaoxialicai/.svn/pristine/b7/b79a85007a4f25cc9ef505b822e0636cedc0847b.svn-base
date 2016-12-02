<?php
use Sooh\Base\Form\Item as form_def;
include_once __DIR__.'/Paygwcmds.php';

/**
 * 支付网关命令执行
 */

class PaygwcmdslogController extends \PaygwcmdsController {
    protected $pageSizeEnum =[30, 50, 100];
    protected $grpIdWithBatchId;

	
    public function logsAction (){
		$pageid = $this->_request->get('pageId', 1)-0;
        $pagesize = $this->_request->get('pageSize', current($this->pageSizeEnum)) - 0;
        $pager = new \Sooh\DB\Pager($pagesize,$this->pageSizeEnum, false);
      
        $frm = \Sooh\Base\Form\Broker::getCopy('default')
        ->init(\Sooh\Base\Tools::uri(), 'get', \Sooh\Base\Form\Broker::type_s);
        $frm->addItem('_evt_eq', form_def::factory('类型', '', form_def::select)->initMore(new \Sooh\Base\Form\Options($this->cmds,'不限')))
            ->addItem('pageId', $pageid)
           ->addItem('pageSize',$pagesize);
        $frm->fillValues();
 
        if ($frm->flgIsThisForm) {
           $where = $frm->getWhere();
        }else{
			$where = [];
		}
		if(!isset($where['evt']) && !isset($where['evt='])){
			$where['evt*']='Manage/Paygwcmds/%';
		}
		$headers = array('时间'=>70,'事件'=>90,'目标'=>180,'执行结果'=>180,'管理员'=>70);
		$db = \Sooh\DB\Broker::getInstance('crondForLog');
		$pager->init($db->getRecordCount('db_logs.tblog__a_0', $where), $pageid);
		$rs = $db->getRecords('db_logs.tblog__a_0', '*',$where,'rsort ymd rsort hhiiss',$pager->page_size,$pager->rsFrom());
		$records=array();
		foreach($rs as $r){
			$records[]=[
				\Prj\Misc\View::fmtYmd($r['ymd'].  sprintf('%06d',$r['hhiiss'])),
				isset($this->cmds[$r['evt']])?$this->cmds[$r['evt']]:$r['evt'],
				$r['target'],
				$r['ret'],
				$r['userId'],
				'_pkey_val_'=>'todo:'.$r['logGuid'],
			];
		}
		
	    $this->_view->assign('records', $records);
        $this->_view->assign('headers', $headers);
        $this->_view->assign('pager', $pager);
          
    }

}