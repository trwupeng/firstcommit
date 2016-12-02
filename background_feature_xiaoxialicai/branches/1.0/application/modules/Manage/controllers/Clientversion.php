<?php
use Sooh\Base\Form\Item as form_def;
use Sooh\Base\Form\Options as options_def;
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/12/10 0010
 * Time: 8:05
 */

class ClientversionController extends \Prj\ManagerCtrl {
    public function init() {
        parent::init();
        $this->clientType = array('901'=>'IOS','902'=>'Android','903'=>'M');
        $this->dbMysql = \Sooh\DB\Broker::getInstance();
        
        $obj = Prj\Data\Copartner::getCopy();
        $this->copartners = $obj->getAllCopartnerNameById();
        foreach($this->copartners as $k => $v) {
            $this->copartners[$k] = $v.'['.$k.']';
        }

        $rs = \Prj\Data\Contract::getContractName();
        if (!empty($rs)) {
            foreach ($rs as $k=> $v) {
                $this->arr_contract_id[$k] = $v.'['.$k.']';
            }
        }
        $this->arr_contract_id[0]  = '自然量[0]';
        $this->arr_contract_id[-1] = '对应端类型所有渠道[-1]';


    }
    protected $arr_contract_id;
    protected $clientType;
    protected $dbMysql;
    protected $pageSizeEnum = [30, 50, 100];
    public function indexAction () {
        $pageId = $this->_request->get('pageId',1)-0;
        $pageSize = $this->_request->get('pageSize',current($this->pageSizeEnum))-0;
        $isDownloadExcel = $this->_request->get('__EXCEL__');

        $pager = new \Sooh\DB\Pager($pageSize,$this->pageSizeEnum,false);
        $formObj = \Sooh\Base\Form\Broker::getCopy('default')
            ->init(\Sooh\Base\Tools::uri(),'get',\Sooh\Base\Form\Broker::type_s);
        $formObj->addItem('_ymd_g2',form_def::factory('版本日期从',date('Y-m-d',time()-100*86400),form_def::datepicker))
                ->addItem('_ymd_l2',form_def::factory('到',date('Y-m-d'),form_def::datepicker))
                ->addItem('_clientType_eq',form_def::factory('客户端类型：','',form_def::select));
        $formObj->items['_clientType_eq']->options = new options_def($this->clientType, '全部');
        $formObj->fillValues();
        if ($formObj->flgIsThisForm){
            $where = $formObj->getWhere();
            $where['ymd]'] = str_replace('-', '', $where['ymd]']);
            $where['ymd['] = str_replace('-', '', $where['ymd[']);
        }else {
            $where['ymd]'] = date('Ymd',time()-15*86400);
            $where['ymd['] = date('Ymd');
        }
        $headers = $this->getHeader();
        if ($isDownloadExcel==1) {
            $keysStr = $this->_request->get('ids');
            if (!empty($keysStr)) {
                $where = $this->dbMysql->newWhereBuilder();
                $where->init('OR');

                foreach($keysStr as $k => $v) {
                    $and = \Prj\Misc\View::decodePkey($v);
                    $tmp = $this->dbMysql->newWhereBuilder();
                    $tmp->init('AND');
                    $tmp->append($and);
                    $where->append(null,$tmp);
                    $tmp=null;
                }
//var_log($where,'isDownloadExcel>>>>>>where>>>>>>>>>>>');
            }
            $records = $this->getRecords($where,null,0,false);
//var_log($records, 'isDownloadExcel>>>>>>records>>>>>>>>>>>>>>>>');
            return $this->downExcel($records,array_keys($headers),null,false);
        }

        $pager->init($this->getRecordsCount($where), $pageId);
        $records = $this->getRecords($where,$pageSize,$pager->rsFrom());
        $this->_view->assign('records',$records);
        $this->_view->assign('headers',$headers);
        $this->_view->assign('pager',$pager);
    }

    public function addnewAction () {
        $enForce = array(0=>'否',1=>'是');
        $full = array(0=>'否',1=>'是');

        $formObj = \Sooh\Base\Form\Broker::getCopy('default')
            ->init(\Sooh\Base\Tools::uri(), 'get', \Sooh\Base\Form\Broker::type_c);
        $current = key($this->clientType);
        $current_copartnerId = key($this->arr_contract_id);
        $formObj->addItem('ymd', form_def::factory('版本日期', date('Y-m-d'), form_def::datepicker))
            ->addItem('contractId', form_def::factory('协议号', $current_copartnerId, form_def::select,$this->arr_contract_id))
            ->addItem('clientType', form_def::factory('客户端类型', $current, form_def::select,$this->clientType)/*->verifyInteger(1, 100000000000000000, 999999999999999999)*/)
            ->addItem('ver', form_def::factory('版本号', '', form_def::text)/*->initMore(new options_def($this->copartners), form_def::constval)*/)
            ->addItem('enforce', form_def::factory('是否强制更新', 0, form_def::select,$enForce))
            ->addItem('full',form_def::factory('是否整包更新',0,form_def::select,$full))
            ->addItem('info', form_def::factory('版本描述', '', form_def::mulit))
            ->addItem('url', form_def::factory('链接地址','', form_def::text));

        $formObj->fillValues();
        if ($formObj->flgIsThisForm){
            try {
                $fields = $formObj->getFields();
                $fields['ymd'] = str_replace('-', '', $fields['ymd']);
// var_log($fields, 'fields>>>>>>>>>>>>>>>>>>');
                if (empty($fields['ver'])){
                    //$this->returnError('添加失败：版本号未填写');
                   $this->returnError(\Prj\Lang\Broker::getMsg('clientversion.ver_add_not_filled'));
                    return;
                }
                if(empty($fields['contractId'])) {
                   // $this->returnError('添加失败：协议号未填写');
                    $this->returnError('协议号未填写');
                }


                $tmp_ver = explode('.', $fields['ver']);
                $fields['ver1'] = $tmp_ver[0];
                $fields['ver2'] = $tmp_ver[1]>0 ? $tmp_ver[1] : 0;
                $fields['ver3'] = $tmp_ver[2]>0 ? $tmp_ver[2] : 0;
                $fields['ver4'] = $tmp_ver[3]>0 ? $tmp_ver[3] : 0;

                $where = [
                    'clientType'=>$fields['clientType'],
                    'copartnerId'=>substr($fields['contractId'], 0, 4),
                    'contractId'=>$fields['contractId'],
                    'ver1' => $fields['ver1'],
                    'ver2' => $fields['ver2'],
                    'ver3' => $fields['ver3'],
                    'ver4' => $fields['ver4'],
                ];
                $tmp = $this->dbMysql->getRecordCount('db_p2p.tb_clientPatch', $where);
                if ($tmp>0) {
                    //$this->returnError('添加失败：版本号已经存在');
                    $this->returnError(\Prj\Lang\Broker::getMsg('clientversion.var_already_existing'));
                    return;
                }
//                 $tmpArr = explode('.', $fields['ver']);
//                 $fields['ymd'] = date('Y').substr($tmpArr[2],2,4);


                $fields['copartnerId'] = substr($fields['contractId'], 0, 4);
//var_log($fields, 'fields>>>>>');
                $this->dbMysql->addRecord('db_p2p.tb_clientPatch',$fields);
                $this->closeAndReloadPage($this->tabname('index'));
                $this->returnOk('添加成功');
            }catch (\ErrorException $e) {
                $this->returnError('添加失败',$e->getMessage());
            }
        }
    }

    public function updAction () {
        $where = \Prj\Misc\View::decodePkey($_REQUEST['_pkey_val_']);

        $enForce = array(0=>'否',1=>'是');
        $full = array(0=>'否',1=>'是');

        $formObj = \Sooh\Base\Form\Broker::getCopy('default')
            ->init(\Sooh\Base\Tools::uri(), 'get', \Sooh\Base\Form\Broker::type_u);
        $current = key($this->clientType);
        $current_copartnerId = key($this->arr_contract_id);
        $formObj->addItem('autoid', form_def::factory('版本ID', $where['autoid'], form_def::constval))
            ->addItem('copartnerId', form_def::factory('渠道名称', '', form_def::constval))
          ->addItem('contractId', form_def::factory('协议号', $current_copartnerId, form_def::select,$this->arr_contract_id))
            ->addItem('clientType', form_def::factory('客户端类型', $current, form_def::select,$this->clientType)/*->verifyInteger(1, 100000000000000000, 999999999999999999)*/)
            ->addItem('ver', form_def::factory('版本号', '', form_def::text)/*->initMore(new options_def($this->copartners), form_def::constval)*/)
            ->addItem('enforce', form_def::factory('是否强制更新', 0, form_def::select,$enForce))
            ->addItem('full',form_def::factory('是否整包更新',0,form_def::select,$full))
            ->addItem('info', form_def::factory('版本描述', '', form_def::mulit))
            ->addItem('url', form_def::factory('链接地址','', form_def::text));

        $formObj->fillValues();

        if ($formObj->flgIsThisForm){
            try {
                $fields = $formObj->getFields();

                if (empty($fields['ver'])){
                   // $this->returnError('更新失败：版本号未填写');
                    $this->returnError(\Prj\Lang\Broker::getMsg('clientversion.ver_upd_not_filled'));
                   
                    return;
                }
                $tmp = $this->dbMysql->getRecordCount('db_p2p.tb_clientPatch', ['autoid'=>$fields['autoid']]);
                if ($tmp<1) {
                    //$this->returnError('更新失败：无相关记录');
                    $this->returnError(\Prj\Lang\Broker::getMsg('clientversion.upd_not_record'));
                    return;
                }

                $tmp_ver = explode('.', $fields['ver']);
                $fields['ver1'] = $tmp_ver[0];
                $fields['ver2'] = $tmp_ver[1]>0 ? $tmp_ver[1] : 0;
                $fields['ver3'] = $tmp_ver[2]>0 ? $tmp_ver[2] : 0;
                $fields['ver4'] = $tmp_ver[3]>0 ? $tmp_ver[3] : 0;

                $where = [
                    'autoid!'=>$fields['autoid'],
                    'ver1' => $fields['ver1'],
                    'ver2' => $fields['ver2'],
                    'ver3' => $fields['ver3'],
                    'ver4' => $fields['ver4'],
                ];

                $tmp = $this->dbMysql->getRecordCount('db_p2p.tb_clientPatch', $where);
                if($tmp > 0 ) {
                    $this->returnError('版本冲突，已经存在此版本号');
                    return;
                }

                $where = ['autoid'=>$fields['autoid']];
                unset ($fields['autoid']);

                $this->dbMysql->updRecords('db_p2p.tb_clientPatch',$fields,$where);
// error_log('lastCmd>>>>>>>'."\n".\Sooh\DB\Broker::lastCmd());
                $this->closeAndReloadPage($this->tabname('index'));
//                 $this->closeAndReloadPage($this->tabname('index'));
                $this->returnOk('更新成功');
            }catch (\ErrorException $e) {
                $this->returnError('更新失败',$e->getMessage());
            }
        }else {
            $fields = 'clientType,ver,enforce,info,url,full,contractId,copartnerId';
            $record = $this->dbMysql->getRecord('db_p2p.tb_clientPatch',$fields,$where);
            $record = array_merge($record,$where);
            if (empty($record)) {
               // $this->returnError('记录找不到');
                $this->returnError(\Prj\Lang\Broker::getMsg('clientversion.record_unfound'));
            }else {
                $ks = array_keys($formObj->items);
                foreach ($ks as $k){
                    if (isset($record[$k])){
                        if($k == 'copartnerId') {
                            $formObj->item($k)->value=(isset($this->copartners[$record[$k]])?$this->copartners[$record[$k]]:'未知渠道['.$record[$k].']');
                        }else {
                            $formObj->item($k)->value = $record[$k];
                        }
                    }
                }
            }
        }
    }

    public function delAction () {
        $where = \Prj\Misc\View::decodePkey($_REQUEST['_pkey_val_']);
        if (empty($where)) {
            $this->returnError('删除失败');
          //  $this->returnError(\Prj\Lang\Broker::getMsg('clientversion.delete_error'));
        }
        $n = $this->dbMysql->getRecordCount('db_p2p.tb_clientPatch', $where);
        if (empty($n)) {
           // $this->returnError('删除失败：未找到此记录！');
            $this->returnError(\Prj\Lang\Broker::getMsg('clientversion.delete_not_record'));
        }
        $n = $this->dbMysql->delRecords('db_p2p.tb_clientPatch', $where);
        if ($n) {
//             $this->closeAndReloadPage($this->tabname('index'));
            $this->returnOK('删除成功');
        }else {
            $this->returnError('删除失败');
        }
    }

    protected $copartners;

    protected function getRecords($where,$pageSize=null,$rsFrom=0, $pkeyFlg = true) {
        $fields = 'autoid,ymd,copartnerId, contractId,clientType,ver,enforce,info,url,full';
        $records = $this->dbMysql->getRecords('db_p2p.tb_clientPatch',$fields,$where,'rsort autoid',$pageSize,$rsFrom);

        if (!empty($records)) {
            foreach($records as $k => $v) {
                if ($pkeyFlg){
                    $records[$k]['_pkey_val_'] = \Prj\Misc\View::encodePkey(array('autoid'=>$v['autoid']));
                }
                unset($records[$k]['autoid']);
                $records[$k]['copartnerId'] = $this->copartners[$v['copartnerId']];
                if(!isset($this->copartners[$v['copartnerId']])) {
                    $records[$k]['copartnerId'] = '未知渠道['.$v['copartnerId'].']';
                }

                $records[$k]['contractId'] = $this->arr_contract_id[$v['contractId']];
                if(!isset($this->arr_contract_id[$v['contractId']])){
                    $records[$k]['contractId'] = '未知协议['.$v['contractId'].']';
                }

                if($v['copartnerId']!=0 && $v['contractId'] ==0) {
                    $records[$k]['contractId'] = '';
                }

                $records[$k]['clientType'] = $this->clientType[$v['clientType']];
                $records[$k]['enforce'] = $v['enforce']?'是':'否';
                $records[$k]['full'] = $v['full']?'是':'否';
            }
        }
        return $records;
    }

    protected function getRecordsCount($where) {
        return $this->dbMysql->getRecordCount('db_p2p.tb_clientPatch',$where);
    }

    protected function getHeader() {
        return array(
            '版本日期'=>50,
            '渠道-渠道号'=>90,
            '协议-协议号'=>130,
            '客户端类型'=>70,
            '版本号'=>90,
            '是否强制更新'=>90,
            '版本描述'=>90,
            '下载地址'=>90,
            '是否整包更新'=>100
        );
    }
}