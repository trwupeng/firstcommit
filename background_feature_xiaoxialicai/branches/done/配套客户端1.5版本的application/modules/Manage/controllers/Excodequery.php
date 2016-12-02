<?php
use Sooh\Base\Form\Item as form_def;
use Sooh\Base\Form\Options as options_def;
include_once __DIR__.'/Excode.php';

/**
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/3/22 0022
 * Time: 下午 4:55
 */

class ExcodequeryController extends \Prj\ManagerCtrl {

    protected $pageSizeEnum =[30, 50, 100];
    protected $grpIdWithBatchId;
    public function init() {
        parent::init();
        $this->grpIdWithBatchId = \Prj\Data\ExchangeCode::grpIdWithBatchId();
    }

    public function queryexcodeAction (){

        $head_fields = [
            'grpId'=>['分组', 30],
            'batchId'=>['批次', 30],
            'excode'=>['兑换码', 30],
            'exname'=>['兑换者', 30],
            'userId'=>['兑换者ID', 50],
            'dtFetch'=>['兑换时间', 100],
            'ordersId'=>['领取的订单ID', 50],
            'intro'=>['说明', 80],
        ];


        $pageid = $this->_request->get('pageId', 1) - 0;
        $pagesize = $this->_request->get('pageSize', current($this->pageSizeEnum)) - 0;
        $pager = new \Sooh\DB\Pager($pagesize, $this->pageSizeEnum, false);
        if(empty($this->grpIdWithBatchId)) {
            $grpId = '';
            $arr_grp_id = [''=>'无数据'];
        }else {
            $arr_grp_id = array_keys($this->grpIdWithBatchId);
            $arr_grp_id = array_combine($arr_grp_id, $arr_grp_id);
            $grpId = key($arr_grp_id);
        }

        $form = \Sooh\Base\Form\Broker::getCopy('default')
            ->init(\Sooh\Base\Tools::uri(), 'get', \Sooh\Base\Form\Broker::type_s);
        $form -> addItem('_grpId_eq', form_def::factory('分组', $grpId, form_def::select)->initMore(new options_def($arr_grp_id)))
            ->addItem('_batchId_eq', form_def::factory('批次', '', form_def::text))
            ->addItem('_excode_eq', form_def::factory('兑换码', '', form_def::text))
            ->addItem('pageid', $pageid)
            ->addItem('pagesize', $pager->page_size);
        $form->fillValues();
        if($form->flgIsThisForm){
            $where = $form->getWhere();
            $records = [];
            $keys = array_keys($head_fields);
            if(!empty($where['excode='])) {
                $obj = \Prj\Data\ExchangeCode::getCopy($where['excode=']);
                $obj->load();
                if($obj->exists()) {
                    $record = $obj->dump();
                    $tmp = $this->trans($record, $keys);
                    $records[] = $tmp;
                }
                $obj->free();
            }elseif(!empty($this->grpIdWithBatchId)){
                $fields = 'grpId,batchId,excode,userId,dtFetch,ordersId';
                $records = \Prj\Data\ExchangeCode::loopFindRecordsByFields($where, null, $fields, 'getRecords');
                if(!empty($records)) {
                    foreach($records as $k => $record) {
                        $tmp = $this->trans($record, $keys);
                        $records[$k] = $tmp;
                    }

                }
            }
        }
        $this->_view->assign('records', $records);
        $this->_view->assign('pager', $pager);

        foreach($head_fields as $v) {
            $headers[$v[0]] = $v[1];
        }
        $this->_view->assign('headers', $headers);
    }

    protected function trans($record, $keys) {
        $obj = \Prj\Data\ExchangeCode::getCopy($record['excode']);
        $db_excode = $obj->db();
        $obj->free();
        $tmp = [];
        foreach($keys as $v ) {
            if($v == 'userId') {
                $tmp[$v] = (empty($record['userId'])? '' : $record['userId']);
            }elseif($v == 'exname') {
                if(!empty($record['userId'])) {
                    $user = \Prj\Data\User::getCopy($record['userId']);
                    $db = $user->db();
                    $tbname = $user->tbname();
                    $tmp['exname'] = $db->getOne($tbname, 'nickname', ['userId'=>$record['userId']]);
                    $user->free();
                    $db = null;
                }else {
                    $tmp['exname'] = '';
                }
            }elseif($v == 'dtFetch'){
                if( 0 < $record['dtFetch']){
                    $tmp['dtFetch'] = date('Y-m-d H:i:s', $record['dtFetch']);
                }else {
                    $tmp['dtFetch'] ='';
                }
            } elseif($v == 'intro') {
                $tmp['intro'] = $db_excode->getOne(\Prj\Data\ExchangeCode::tbGrp, 'intro', ['grpId' => $record['grpId'], 'batchId' => $record['batchId']]);
            }elseif($v == 'ordersId'){ // 暂时不填
                $tmp[$v] = '';
            }else {
                $tmp[$v] = $record[$v];
            }
        }
        return $tmp;
    }

}