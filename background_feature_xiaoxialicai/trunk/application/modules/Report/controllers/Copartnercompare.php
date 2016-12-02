<?php

/**
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/5/26 0026
 * Time: 上午 11:58
 */
class CopartnercompareController extends \Prj\ManagerCtrl
{

    protected $promotionWay;

    public function init() {
        parent::init();
        $this->promotionWay = \Prj\Data\Contract::$promotionWay;

    }
    public function indexAction () {
        $ymdRegFrom = $this->_request->get('ymdregfrom');
        $ymdRegTo = $this->_request->get('ymdregto');
        $promotionWaySelected = $this->_request->get('promotionway');
        $where['ymdReg]'] = date('Ymd', strtotime($ymdRegFrom));
        $where['ymdReg['] = date('Ymd', strtotime($ymdRegTo));
        $where['promotionWay'] = $promotionWaySelected;



        if($where['ymdReg]'] <= $where['ymdReg['] && !empty($where['promotionWay'])) {
            $obj = new \Rpt\DataDig\CopartnercompareDataDig();
            $ret = $obj->dig($where);

            $this->_view->assign('header', $ret['header']);
            $this->_view->assign('data', $ret['data']);
        }

//var_log($where, 'where>>>>>>>>>>>>>');
//var_log($selectedPromotionWay, 'selectedPromotionWay>>>>>>>>');
        $this->_view->assign('ymdRegFrom', $ymdRegFrom);
        $this->_view->assign('ymdRegTo', $ymdRegTo);
        $this->_view->assign('promotionWaySelected', $promotionWaySelected);
        $this->_view->assign('promotionWays', $this->promotionWay);
    }

}
