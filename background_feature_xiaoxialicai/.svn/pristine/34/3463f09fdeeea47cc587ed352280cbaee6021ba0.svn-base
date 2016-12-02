<?php
/**
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/4/27 0027
 * Time: 下午 4:56
 */

class BindfailController extends \Prj\ManagerCtrl{

    public function init() {
        parent::init();
        $this->db_rpt = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_p2prpt);
    }

    protected $db_rpt;
    protected $pageSizeEnum = [30, 50, 100];
    protected $fieldsMap = [
        'userId'                                                    => ['用户ID', null],
        'bankCard'                                                  => ['银行卡号', null],
        'realname'                                                  => ['姓名', null],
        'phone'                                                     => ['手机号码', null],
        'concat(createYmd,LPAD(createHis, 6, 0)) as createTime'     => ['绑卡时间', null],
        'resultMsg'                                                 => ['失败原因', null],

    ];

    public function indexAction () {
        $pageid = $this->_request->get('pageId', 1) - 0;
        $pagesize = $this->_request->get('pageSize', current($this->pageSizeEnum)) - 0;
        $formguid = $this->_request->get('__formguid__');
        $pager = new \Sooh\DB\Pager($pagesize, $this->pageSizeEnum, false);

        if(!empty($formguid)) {
            $createYmd = $this->_request->get('createYmd');
                $ymd = date('Ymd', strtotime($createYmd));
        }else {
            $createYmd = date('Y-m-d');
            $ymd = date('Ymd');

        }


        $success_users = $this->db_rpt->getCol(\Rpt\Tbname::tb_bankcard_final, 'distinct(userId)',['createYmd'=>$ymd, 'statusCode'=>[4,16]]);
        $where = ['createYmd'=>$ymd, 'statusCode'=>[0,-1]];
        if(!empty($success_users)) {
            $where ['userId!']=$success_users;
        }

        $recordsCount = $this->db_rpt->getRecordCount(\Rpt\Tbname::tb_bankcard_final, $where);

        $pager->init($recordsCount, $pageid);
        $records = $this->db_rpt->getRecords( \Rpt\Tbname::tb_bankcard_final, array_keys($this->fieldsMap),
            $where, 'sort createHis', $pagesize, $pager->rsFrom() );
        if(!empty($records)) {
            foreach($records as $k=> $r) {
                $records[$k]['createTime'] = date('Y-m-d H:i:s', strtotime($r['createTime']));
            }
        }

        foreach($this->fieldsMap as $k => $v){
            $headers[$v[0]] = $v[1];
        }

        $this->_view->assign('records', $records);
        $this->_view->assign('headers', $headers);
        $this->_view->assign('createYmd', $createYmd);
        $this->_view->assign('pager', $pager);
        $this->_view->assign('pageid', $pageid);
        $this->_view->assign('pagesize', $pagesize);
    }
}