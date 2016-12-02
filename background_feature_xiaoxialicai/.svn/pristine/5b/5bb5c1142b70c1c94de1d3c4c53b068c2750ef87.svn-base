<?php
namespace Rpt\DataDig;

class MonthreportData {
    protected $dbMysql;
    public function __construct() {
        $this->dbMysql = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_p2prpt);
    }
    
    public function allMonth ($where) {
        $monthFrom = $where['ymd]'];
        $monthTo = $where['ymd['];
        unset ($where['ymd]']);
        unset ($where['ymd[']);
        
        $records = [];
        while ($monthTo >= $monthFrom) {
            $where['ymd]'] = $monthTo.'01';
            $where['ymd['] = $monthTo.'31';
            // 先获取这个月有么有记录, 没有记录就直接跳过
            $recordsCount = $this->dbMysql->getRecordCount(\Rpt\Tbname::tb_copartner_worth, $where);
            if ($recordsCount == 0) {
                $monthTo = date('Ym', strtotime("-1 month", strtotime($monthTo.'01')));
                continue;
            }
            $tmp = [];
            
            $_pkey_val_ = ['ym'=>$monthTo];
            if (isset($where['copartnerId='])) {
                $_pkey_val_['copartnerId'] = $where['copartnerId='];
            }
            
            $tmp['_pkey_val_'] = \Prj\Misc\View::encodePkey($_pkey_val_);
            $tmp[] = date('Ym', strtotime($monthTo.'01'));
            $tmp[] = $this->dbMysql->getOne (\Rpt\Tbname::tb_copartner_worth, 'sum(n)', array_merge($where, ['act'=>'NewRegister']))+0;
            $tmp[] = $this->dbMysql->getOne (\Rpt\Tbname::tb_copartner_worth, 'sum(n)', array_merge($where, ['act'=>'BindOk']))+0;
            $tmp[] = $this->dbMysql->getOne(\Rpt\Tbname::tb_copartner_worth,'sum(n)',array_merge($where, ['act'=>'NewChargeCount']))+0;
            $tmp[] = $this->dbMysql->getOne(\Rpt\Tbname::tb_copartner_worth,'sum(n)',array_merge($where, ['act'=>'NewChargeAmount']))/100;
            $tmp[] = $this->dbMysql->getOne(\Rpt\Tbname::tb_copartner_worth,'sum(n)',array_merge($where, ['act'=>'NewBoughtCount']))+0;
            $tmp[] = $this->dbMysql->getOne(\Rpt\Tbname::tb_copartner_worth,'sum(n)',array_merge($where, ['act'=>'NewBoughtAmount']))/100;
            $tmp[] = $this->dbMysql->getOne(\Rpt\Tbname::tb_copartner_worth,'sum(n)',array_merge($where, ['act'=>'NewBoughtAmount','shelfId'=>\Prj\Consts\Wares::shelf_static]))/100;
            $tmp[] = $this->dbMysql->getOne(\Rpt\Tbname::tb_copartner_worth,'sum(n)',array_merge($where, ['act'=>'NewBoughtAmount','shelfId'=>\Prj\Consts\Wares::shelf_float]))/100;
            $tmp[] = $this->dbMysql->getOne(\Rpt\Tbname::tb_copartner_worth,'sum(n)',array_merge($where, ['act'=>'NewBoughtAmount','shelfId'=>\Prj\Consts\Wares::shelf_static_float]))/100;
            $tmp[] = $this->dbMysql->getOne(\Rpt\Tbname::tb_copartner_worth,'sum(n)',array_merge($where, ['act'=>'ChargeCount']))+0;
            $tmp[] = $this->dbMysql->getOne(\Rpt\Tbname::tb_copartner_worth,'sum(n)',array_merge($where, ['act'=>'ChargeAmount']))/100;
            $tmp[] = $this->dbMysql->getOne(\Rpt\Tbname::tb_copartner_worth,'sum(n)',array_merge($where, ['act'=>'BoughtCount']))+0;
            $tmp[] = $this->dbMysql->getOne(\Rpt\Tbname::tb_copartner_worth,'sum(n)',array_merge($where, ['act'=>'BoughtAmount']))/100;
            $tmp[] = $this->dbMysql->getOne(\Rpt\Tbname::tb_copartner_worth,'sum(n)',array_merge($where, ['act'=>'BoughtAmount','shelfId'=>\Prj\Consts\Wares::shelf_static]))/100;
            $tmp[] = $this->dbMysql->getOne(\Rpt\Tbname::tb_copartner_worth,'sum(n)',array_merge($where, ['act'=>'BoughtAmount','shelfId'=>\Prj\Consts\Wares::shelf_float]))/100;
            $tmp[] = $this->dbMysql->getOne(\Rpt\Tbname::tb_copartner_worth,'sum(n)',array_merge($where, ['act'=>'BoughtAmount','shelfId'=>\Prj\Consts\Wares::shelf_static_float]))/100;
            $records[] = $tmp;
            $monthTo = date('Ym', strtotime("-1 month", strtotime($monthTo.'01')));
        }
        var_log($records, 'records>>>>>>>>>>');
        return $records;
    }
    
    protected $allMonthheader = [
        '月        份',
        '新增注册人数',
        '新增绑卡人数',
        '新增充值人数',
        '新增充值金额',
        '新增购买人数',
        '新增购买金额-全部产品',
        '新增购买金额-固定收益',
        '新增购买金额-浮动收益',
        '新增购买金额-固定+浮动',
        
        '充值人数',
        '充值金额',
        '购买人数',
        '购买金额-全部产品',
        '购买金额-固定收益',
        '购买金额-浮动收益',
        '购买金额-固定+浮动',
    ];
    
    public function getAllMonthHeader () {
        $headers = [];
        foreach ($this->allMonthheader as $name){
            $headers[$name] = strlen($name)*6;
        }
        return $headers;
    }
    
    public function detailRecords ($where) {
        $ymdFrom = $where['ymd]'];
        $ymdTo = $where['ymd['];
        unset ($where['ymd]']);
        unset ($where['ymd[']);
        $records = [];
        while ($ymdTo >= $ymdFrom) {
            $where['ymd'] = $ymdTo;
            $recordsCount = $this->dbMysql->getRecordCount(\Rpt\Tbname::tb_copartner_worth, $where);
// var_log(\Sooh\DB\Broker::lastCmd(), $recordsCount.'>>>>>>>lastCmd>>>>>>>>>');            
            if ($recordsCount == 0) {
                $ymdTo = date('Ymd', strtotime($ymdTo) - 86400);
                continue;
            }
            $tmp = [];
            $tmp[] = $ymdTo;
            $tmp[] = $this->dbMysql->getOne (\Rpt\Tbname::tb_copartner_worth, 'sum(n)', array_merge($where, ['act'=>'NewRegister']))+0;
            $tmp[] = $this->dbMysql->getOne (\Rpt\Tbname::tb_copartner_worth, 'sum(n)', array_merge($where, ['act'=>'BindOk']))+0;
            $tmp[] = $this->dbMysql->getOne(\Rpt\Tbname::tb_copartner_worth,'sum(n)',array_merge($where, ['act'=>'NewChargeCount']))+0;
            $tmp[] = $this->dbMysql->getOne(\Rpt\Tbname::tb_copartner_worth,'sum(n)',array_merge($where, ['act'=>'NewChargeAmount']))/100;
            $tmp[] = $this->dbMysql->getOne(\Rpt\Tbname::tb_copartner_worth,'sum(n)',array_merge($where, ['act'=>'NewBoughtCount']))+0;
            $tmp[] = $this->dbMysql->getOne(\Rpt\Tbname::tb_copartner_worth,'sum(n)',array_merge($where, ['act'=>'NewBoughtAmount']))/100;
            $tmp[] = $this->dbMysql->getOne(\Rpt\Tbname::tb_copartner_worth,'sum(n)',array_merge($where, ['act'=>'NewBoughtAmount','shelfId'=>\Prj\Consts\Wares::shelf_static]))/100;
            $tmp[] = $this->dbMysql->getOne(\Rpt\Tbname::tb_copartner_worth,'sum(n)',array_merge($where, ['act'=>'NewBoughtAmount','shelfId'=>\Prj\Consts\Wares::shelf_float]))/100;
            $tmp[] = $this->dbMysql->getOne(\Rpt\Tbname::tb_copartner_worth,'sum(n)',array_merge($where, ['act'=>'NewBoughtAmount','shelfId'=>\Prj\Consts\Wares::shelf_static_float]))/100;
            $tmp[] = $this->dbMysql->getOne(\Rpt\Tbname::tb_copartner_worth,'sum(n)',array_merge($where, ['act'=>'ChargeCount']))+0;
            $tmp[] = $this->dbMysql->getOne(\Rpt\Tbname::tb_copartner_worth,'sum(n)',array_merge($where, ['act'=>'ChargeAmount']))/100;
            $tmp[] = $this->dbMysql->getOne(\Rpt\Tbname::tb_copartner_worth,'sum(n)',array_merge($where, ['act'=>'BoughtCount']))+0;
            $tmp[] = $this->dbMysql->getOne(\Rpt\Tbname::tb_copartner_worth,'sum(n)',array_merge($where, ['act'=>'BoughtAmount']))/100;
            $tmp[] = $this->dbMysql->getOne(\Rpt\Tbname::tb_copartner_worth,'sum(n)',array_merge($where, ['act'=>'BoughtAmount','shelfId'=>\Prj\Consts\Wares::shelf_static]))/100;
            $tmp[] = $this->dbMysql->getOne(\Rpt\Tbname::tb_copartner_worth,'sum(n)',array_merge($where, ['act'=>'BoughtAmount','shelfId'=>\Prj\Consts\Wares::shelf_float]))/100;
            $tmp[] = $this->dbMysql->getOne(\Rpt\Tbname::tb_copartner_worth,'sum(n)',array_merge($where, ['act'=>'BoughtAmount','shelfId'=>\Prj\Consts\Wares::shelf_static_float]))/100;
            
            $records[] = $tmp;
            $ymdTo = date('Ymd', strtotime($ymdTo) - 86400);
        }
        
        return $records;
    }
    
    protected $detailMonthheader = [
        '日          期',
        '新增注册人数',
        '新增绑卡人数',
        '新增充值人数',
        '新增充值金额',
        '新增购买人数',
        '新增购买金额-全部产品',
        '新增购买金额-固定收益',
        '新增购买金额-浮动收益',
        '新增购买金额-固定+浮动',
    
        '充值人数',
        '充值金额',
        '购买人数',
        '购买金额-全部产品',
        '购买金额-固定收益',
        '购买金额-浮动收益',
        '购买金额-固定+浮动',
    ];
    
    public function getDetailHeaders () {
        $headers = [];
        foreach($this->detailMonthheader as $name) {
            $headers[$name] = strlen($name) * 6;
        }
        return $headers;
    }
}