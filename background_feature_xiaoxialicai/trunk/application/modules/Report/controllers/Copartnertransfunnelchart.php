<?php
/**
 * 渠道转化漏斗图
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/5/20 0020
 * Time: 下午 3:53
 */

class CopartnertransfunnelchartController extends \Prj\ManagerCtrl {

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
        $contractId = $this->_request->get('contractid');
        if($this->checkDate($ymdRegFrom) && $this->checkDate($ymdRegTo) && $ymdRegFrom<=$ymdRegTo){
            // where 条件转换
            if($copartnerId != '' && $contractId != '') {
                $where = [
                    'ymdReg]' => date('Ymd', strtotime($ymdRegFrom)),
                    'ymdReg[' => date('Ymd', strtotime($ymdRegTo)),
                ];
                if($contractId == 'all') {
                    $where['copartnerId']= $copartnerId;
                }else {
                    $where['contractId'] = $contractId;
                }
                $ret = [];

                // benzhou
                $dataDig = \Rpt\DataDig\CopartnertransfunnelchartDataDig::createFunnelData($where);
                $serias_data = json_encode($dataDig['serias_data']);
                $data = json_encode($dataDig['data']);
                $elementId = 'benzhou';
                $show = \Rpt\Misc\BaiduEcharts\Funnel::show($elementId, '渠道转化', $ymdRegFrom.' 至 '.$ymdRegTo.'注册的用户', $data, '', $serias_data);
                $ret[$elementId] = $show;

                // 同比
                $days = floor((strtotime($ymdRegTo)-strtotime($ymdRegFrom))/86400);
                $tongbi_timestamp_to = strtotime($ymdRegFrom) - 86400;
                $tongbi_timestamp_from = $tongbi_timestamp_to - $days*86400;
                $where['ymdReg]'] = date('Ymd', $tongbi_timestamp_from);
                $where['ymdReg['] = date('Ymd', $tongbi_timestamp_to);
//var_log($where, 'where>>>>>>');
                $dataDig = \Rpt\DataDig\CopartnertransfunnelchartDataDig::createFunnelData($where);
                $serias_data = json_encode($dataDig['serias_data']);
                $data = json_encode($dataDig['data']);
                $elementId = 'tongbi';
                $show = \Rpt\Misc\BaiduEcharts\Funnel::show($elementId, '环比-渠道转化', date('Y-m-d', $tongbi_timestamp_from).' 至 '.date('Y-m-d', $tongbi_timestamp_to).'注册的用户', $data, '', $serias_data);
                $ret[$elementId] = $show;

                // 环比
                $huanbi_timestamp_from = strtotime($ymdRegFrom.' -1 year');
                $huanbi_timestamp_to = strtotime($ymdRegTo.' -1 year');
                $where['ymdReg]'] = date('Ymd', $huanbi_timestamp_from);
                $where['ymdReg['] = date('Ymd', $huanbi_timestamp_to);
                $dataDig = \Rpt\DataDig\CopartnertransfunnelchartDataDig::createFunnelData($where);
                $serias_data = json_encode($dataDig['serias_data']);
                $data = json_encode($dataDig['data']);
                $elementId = 'huanbi';
                $show = \Rpt\Misc\BaiduEcharts\Funnel::show($elementId, '同比-渠道转化', date('Y-m-d', $huanbi_timestamp_from).' 至 '.date('Y-m-d', $huanbi_timestamp_to).'注册的用户', $data, '', $serias_data);
                $ret[$elementId] = $show;


                $this->_view->assign('echarts', $ret);
            }

        }


        $this->_view->assign('ymdRegFrom', $ymdRegFrom);
        $this->_view->assign('ymdRegTo', $ymdRegTo);
        $this->_view->assign('copartnerIdSelected', $copartnerId);
        $this->_view->assign('contractIdSelected', $contractId);
        $this->_view->assign('copartners', $this->copartners);
        $this->_view->assign('contracts', $this->getContractsByCopartnerId($copartnerId));
    }

    /**
     *
     *  渠道下拉列表联动协议号下拉列表，获取渠道对应的所有协议号
     */
    public function getcontractsAction() {

        \Sooh\Base\Ini::getInstance()->viewRenderType('echo');
        $copartnerId = $this->_request->get('copartnerid');
        $ret = $this->getContractsByCopartnerId($copartnerId);
        echo json_encode($ret);
    }

    protected function getContractsByCopartnerId($copartnerId) {
        $ret = [];
        if($copartnerId === 0) {
            $contracts = [0=>'自然量'];
        }elseif(empty($copartnerId)){
            $contracts = [];
        }else {
            $copartner_obj = \Prj\Data\Copartner::getCopy();
            $copartnerAbs = $copartner_obj->db()->getOne($copartner_obj->tbname(), 'copartnerAbs', ['copartnerId'=>$copartnerId]);
            $copartner_obj->free();

            if(empty($copartnerAbs)) {
                $contracts = [];
            }else {
                $contract_obj = \Prj\Data\Contract::getCopy();
                $contracts = $contract_obj->db()->getPair($contract_obj->tbname(), 'contractId', 'remarks', ['copartnerAbs'=>$copartnerAbs]);
                $contract_obj->free();
            }
        }

        if(!empty($contracts)) {
            $ret = [['all', '-- 本渠道所有协议 --']];
            foreach($contracts as $contractid => $contractRemarks) {
                $contracts[$contractid] = [ $contractid,  $contractRemarks.'['.$contractid.']'];
            }
            $ret = array_merge($ret, $contracts);
        }
        return $ret;
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