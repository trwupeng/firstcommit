<?php
/**
 * 渠道转化漏斗图
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/5/20 0020
 * Time: 下午 3:53
 */

class ContractcompareController extends \Prj\ManagerCtrl {

    public function init() {
        parent::init();
        $copartner_obj = \Prj\Data\Copartner::getCopy();
        $this->copartners = $copartner_obj->getAllCopartnerNameById();
        $this->db_rpt = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_p2p_slave);
        $copartner_obj->free();
    }
    public function free() {
        parent::free();
        $this->db_rpt->free();
    }

    protected $copartners;
    protected $db_rpt;

    public function indexAction () {
//var_log($_GET, 'get>>>>');
        $ymdRegFrom = $this->_request->get('ymdRegFrom');
        $ymdRegTo = $this->_request->get('ymdRegTo');
        $copartnerId = $this->_request->get('copartnerid');
        if($this->checkDate($ymdRegFrom) && $this->checkDate($ymdRegTo) && $ymdRegFrom<=$ymdRegTo && $copartnerId!==''){
            $where = [
                'ymdReg]' => date('Ymd', strtotime($ymdRegFrom)),
                'ymdReg[' => date('Ymd', strtotime($ymdRegTo)),
                'copartnerId' => $copartnerId,
            ];


            $obj = new \Rpt\DataDig\ContractcompareDataDig();
            $ret = $obj->dig($where);
            $this->_view->assign('header', $ret['header']);
            $this->_view->assign('data', $ret['data']);
        }

        $this->_view->assign('ymdRegFrom', $ymdRegFrom);
        $this->_view->assign('ymdRegTo', $ymdRegTo);
        $this->_view->assign('copartnerIdSelected', $copartnerId);
        $this->_view->assign('copartners', $this->copartners);
    }


    /**
     * 规定的日期格式 XXXX-XX-XX
     *
     * @param $inputDate
     * @return bool
     */
    protected function checkDate ($inputDate) {
        $date_arr = explode('-', $inputDate);
        if(sizeof($date_arr) !=3) {
            return false;
        }

        $date_str = implode( '', $date_arr );
        if( strlen( $date_str ) != 8 ) {
            return false;
        }

        if( !checkdate( $date_arr[1], $date_arr[2], $date_arr[0] ) ) {
            return false;
        }

        return true;


    }

}