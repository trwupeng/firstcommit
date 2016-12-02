<?php

use Sooh\Base\Form\Item as form_def;
use Prj\Data\WeekActivity;
use Prj\Tool\ExcelTool;

/**
 * Description of Weekactivity
 *
 * @author wu.chen
 */
class WeekactivityController extends \Prj\ManagerCtrl {

    protected $pageSizeEnum = [30, 50, 100];

    public function init() {
        define('SOOH_USE_REWRITE', 1);  //url rewrite
        parent::init();
    }

    public function indexAction() {
        $pageid = $this->_request->get('pageId', 1);
        $pagesize = $this->_request->get('pageSize', current($this->pageSizeEnum));
        $pager = new \Sooh\DB\Pager($pagesize, $this->pageSizeEnum);
        $frm = \Sooh\Base\Form\Broker::getCopy('default')
                ->init(\Sooh\Base\Tools::uri(), 'get', \Sooh\Base\Form\Broker::type_s);
        $frm->addItem('_userid_lk', form_def::factory('用户id', '', form_def::text))
                ->addItem('_tasknumber_eq', form_def::factory('期数', '', form_def::text))
                ->addItem('pageid', $pageid)
                ->addItem('pagesize', $pager->page_size);

        $frm->fillValues();
        $where = [];
        if ($frm->flgIsThisForm) {
            $where = $frm->getWhere();
        }
        $userId = $this->_request->get('_userid_lk');
        $tasknumber = $this->_request->get('_tasknumber_lk');
        $weekActivity = new WeekActivity();
        $pager->total = $weekActivity->getWeekActivityByUserIdAndTaskNumber($userId, $tasknumber, TRUE);    //获得总数
        $records = FALSE;
        if ($pager->total) {
            $pageid -= 1;
            $records = $weekActivity->getWeekActivityByUserIdAndTaskNumber($userId, $tasknumber, FALSE, $pagesize, $pageid);   //获得列表
        }
        $headers = ['用户id' => 70, '期数' => 20, '参与时间' => 50, '投资积分' => 30, '分享红包积分' => 30, '签到积分' => 30, '邀请好友积分' => 30, '好友投资积分' => 30, '总积分' => 50, '奖励' => 100];
        $this->_view->assign('where', $where);
        $this->_view->assign('records', $records);
        $this->_view->assign('headers', $headers);
        $this->_view->assign('pager', $pager);
    }

    public function downExcelAction() {
        $ids = $this->_request->get('idp');
        if ($ids) {
            $ids = "userId IN (" . implode(',', $ids) . ")";    //where条件，是否选中
        }
        $weekActivity = new WeekActivity();
        $records = $weekActivity->getWeekActivityAll($ids);
        /**导出excel**/
        ExcelTool::outExcel($records, ['用户ID', '期数', '参与时间', '投资积分', '最后投资时间', '分享红包积分', '最后分享红包时间', '签到积分', '最后签到时间', '邀请好友积分', '最后邀请好友时间', '好友投资积分', '最后好友投资时间', '奖励', '总积分']);
    }

}
