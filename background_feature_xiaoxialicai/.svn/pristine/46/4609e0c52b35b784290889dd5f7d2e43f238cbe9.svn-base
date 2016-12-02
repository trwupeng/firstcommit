<?php
namespace Rpt\DataDig;

class CopartnerWorthDig {

    protected $ymd;
    public function __construct(){
        $this->dbMysql = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_p2prpt);
    }
    protected $dbMysql;

    public $str=[];

	public function importData ($ymd)
	{
//        先删除这一天的记录
        $this->dbMysql->delRecords(\Rpt\Tbname::tb_copartner_worth, ['ymd'=>$ymd]);

//        新增注册
        $sql = 'select contractId, count(*) as n from db_p2prpt.tb_user_final where ymdReg='.$ymd.' group by contractId';
        $this->writeData($ymd, 'newRegNum', $sql);

//        新增认证绑卡
        $sql = 'select contractId, count(*) as n from db_p2prpt.tb_user_final where ymdRealnameAuth='.$ymd.' group by contractId';
        $this->writeData($ymd, 'newBindNum', $sql);

//        新增投资人数
        $sql = 'select contractId, count(*) as n from db_p2prpt.tb_user_final where ymdFirstBuy='.$ymd.' group by contractId';
        $this->writeData($ymd, 'newBoughtNum', $sql);

//        新增投资用户首笔投资金额
        $sql = 'select contractId, sum(amountFirstBuy) as n from db_p2prpt.tb_user_final where ymdFirstBuy='.$ymd.' group by contractId';
        $this->writeData($ymd, 'newBoughtAmount', $sql);

//        车贷投资人数
        $sql = 'select contractId, count(DISTINCT(tb_orders_final.userId)) as n from db_p2prpt.tb_user_final'
            .' right join db_p2prpt.tb_orders_final'
            .' on tb_user_final.userId = tb_orders_final.userId'
            .' where ymd='.$ymd
            .' and shelfId='.\Prj\Consts\Wares::shelf_static_float
            .' and orderStatus in (2, 3, 8, 10, 21, 20, 38, 39)'
            .' group by contractId';
        $this->writeData($ymd, 'carLoanBoughtNum', $sql);

//        车贷投资金额
        $sql = 'select contractId, sum(amount) as n from db_p2prpt.tb_user_final'
            .' right join db_p2prpt.tb_orders_final'
            .' on tb_user_final.userId = tb_orders_final.userId'
            .' where ymd='.$ymd
            .' and shelfId='.\Prj\Consts\Wares::shelf_static_float
            .' and orderStatus in (2, 3, 8, 10, 21, 20, 38, 39)'
            .' group by contractId';
        $this->writeData($ymd, 'carLoanBoughtAmount', $sql);

//        房贷投资人数
        $sql = 'select contractId, count(DISTINCT(tb_orders_final.userId)) as n from db_p2prpt.tb_user_final'
            .' right join db_p2prpt.tb_orders_final'
            .' on tb_user_final.userId = tb_orders_final.userId'
            .' where ymd='.$ymd
            .' and shelfId='.\Prj\Consts\Wares::shelf_static
            .' and orderStatus in (2, 3, 8, 10, 21, 20, 38, 39)'
            .' group by contractId';
        $this->writeData($ymd, 'houseLoanBoughtNum', $sql);

//        房贷投资金额
        $sql = 'select contractId, sum(amount) as n from db_p2prpt.tb_user_final'
            .' right join db_p2prpt.tb_orders_final'
            .' on tb_user_final.userId = tb_orders_final.userId'
            .' where ymd='.$ymd
            .' and shelfId='.\Prj\Consts\Wares::shelf_static
            .' and orderStatus in (2, 3, 8, 10, 21, 20, 38, 39)'
            .' group by contractId';
        $this->writeData($ymd, 'houseLoanBoughtAmount', $sql);

//        当日购买人数
        $sql = 'select contractId, count(DISTINCT(tb_orders_final.userId)) as n from db_p2prpt.tb_user_final'
            .' right join db_p2prpt.tb_orders_final'
            .' on tb_user_final.userId = tb_orders_final.userId'
            .' where ymd='.$ymd
            .' and orderStatus in (2, 3, 8, 10, 21, 20, 38, 39)'
            .' group by contractId';
        $this->writeData($ymd, 'boughtNum', $sql);

//        当日购买金额
        $sql = 'select contractId, sum(amount) as n from db_p2prpt.tb_user_final'
            .' right join db_p2prpt.tb_orders_final'
            .' on tb_user_final.userId = tb_orders_final.userId'
            .' where ymd='.$ymd
            .' and orderStatus in (2, 3, 8, 10, 21, 20, 38, 39)'
            .' group by contractId';
        $this->writeData($ymd, 'boughtAmount', $sql);

//        当日在投金额
        // 当日未还款产品
        $sql = 'select waresId from db_p2prpt.tb_products_final'
            .' where ymdStartReal<='.$ymd
            .' and (ymdPayReal>='.$ymd.' or ymdPayReal=0)'
            .' and statusCode >= 11';

        $prdts = $this->execSql($sql);
        if(!empty($prdts)){
            $prdts_str =[];
            foreach($prdts as $prdt) {
                $prdts_str[] = $prdt['waresId'];
            }
            $prdts_str = '\''.implode('\',\'', $prdts_str).'\'';
            $sql= 'select contractId, sum(amount) as n from db_p2prpt.tb_user_final'
                .' right join db_p2prpt.tb_orders_final'
                .' on tb_user_final.userId = tb_orders_final.userId'
                .' where waresId in ('.$prdts_str.')'
                .' and ymd<='.$ymd
                .' and orderStatus in(2, 3, 8, 10, 21, 20, 38, 39)'
                .' group by contractId';
            $this->writeData($ymd, 'unexpiredAmount', $sql);
        }
	}


    protected function writeData($ymd, $fieldname, $sql){
        $rs = $this->execSql($sql);
        if(!empty($rs)) {
            foreach($rs as $r) {
                $keys = ['ymd'=>$ymd, 'contractId'=>$r['contractId']];
                $record = [$fieldname=>$r['n'], 'week'=>date('w', strtotime($ymd))];
                $upd_keys = array_keys($record);
                $record = array_merge($record, $keys);
                $this->dbMysql->ensureRecord(\Rpt\Tbname::tb_copartner_worth,$record, $upd_keys);
            }
        }
    }

    protected function execSql ($sql) {
        $rs = $this->dbMysql->execCustom(['sql'=>$sql]);
        $rs = $this->dbMysql->fetchAssocThenFree($rs);
        return $rs;
    }



    public $fields = [
        'ymd'                           => ['日期', null],
        'contractId'                    => ['协议号', null],
        'contractId as contractName'    => ['协议名称', null],
        'newRegNum'                     => ['新增注册', null],
        'newBindNum'                    => ['新增认证绑卡', null],
        'newBoughtNum'                  => ['新增投资人数', null],
        'newBoughtAmount'               => ['新增首笔投资金额', null],
        'carLoanBoughtNum'              => ['车贷投资人数', null],
        'carLoanBoughtAmount'           => ['车贷投资金额', null],
        'houseLoanBoughtNum'            => ['房贷投资人数', null],
        'houseLoanBoughtAmount'         => ['房贷投资金额', null],
        'boughtNum'                     => ['投资人数', null],
        'boughtAmount'                  => ['投资金额', null],
        'unexpiredAmount'               => ['在投金额', null],
    ];


    // 日数据需要的字段
    public function fieldsOfDayData() {
        return array_keys($this->fields);
    }


    // 日数据表格标题
    public function headerOfDayData (){
        $tmp = $this->fields;
        $headers = [];
        foreach($tmp as $r) {
            $headers[$r[0]] = $r[1];
        }
        return $headers;
    }


    public function fetchRecords($where,$pagesize=null, $pagefrom=null, $pkey=false){
        $fields = $this->fieldsOfDayData();
        $records = $this->dbMysql->getRecords(\Rpt\Tbname::tb_copartner_worth, $fields, $where, null, $pagesize, $pagefrom);
        if(!empty($records)) {
            foreach($records as $k=> $r) {
                $records[$k]['ymd']						= date('Y-m-d', strtotime($r['ymd']));
                $records[$k]['contractId']				= $r['contractId'];
                $records[$k]['contractName']			=  \Prj\Data\Contract::getContractName($r['contractName']);
                $records[$k]['newRegNum']				= empty($r['newRegNum'])?'':$r['newRegNum'];
                $records[$k]['newBindNum']				= empty($r['newBindNum'])?'':$r['newBindNum'];
                $records[$k]['newBoughtNum']			= empty($r['newBoughtNum'])?'':$r['newBoughtNum'];
                $records[$k]['newBoughtAmount']			= empty($r['newBoughtAmount'])?'':number_format($r['newBoughtAmount']/100, 2);
                $records[$k]['carLoanBoughtNum']		= empty($r['carLoanBoughtNum'])?'':$r['carLoanBoughtNum'];
                $records[$k]['carLoanBoughtAmount']		= empty($r['carLoanBoughtAmount'])?'':number_format($r['carLoanBoughtAmount']/100, 2);
                $records[$k]['houseLoanBoughtNum']		= empty($r['houseLoanBoughtNum'])?'':$r['houseLoanBoughtNum'];
                $records[$k]['houseLoanBoughtAmount']	= empty($r['houseLoanBoughtAmount'])?'':number_format($r['houseLoanBoughtAmount']/100,2);
                $records[$k]['boughtNum']				= empty($r['boughtNum'])?'':$r['boughtNum'];
                $records[$k]['boughtAmount']			= empty($r['boughtAmount'])?'':number_format($r['boughtAmount']/100, 2);
                $records[$k]['unexpiredAmount']			= empty($r['unexpiredAmount'])?'':number_format($r['unexpiredAmount']/100, 2);

                if($pkey){
                    $records[$k]['_pkey_'] = $r['contractId'];
                }

                if(empty($records[$k]['contractName'])) {
                    $records[$k]['contractName'] = '协议管理中找不到此协议号';
                }
            }
        }
        return $records;
    }

    public function recordsCount($where) {
        return $this->dbMysql->getRecordCount(\Rpt\Tbname::tb_copartner_worth, $where);
    }


    /**
     *
     * 用户分布相关
     */

    public function userRecordsCount($where) {
        return $this->dbMysql->getRecordCount(\Rpt\Tbname::tb_user_final, $where);
    }

    public $userDataHeader = [
        '协议'=>null,
        '协议名称'=>null,
        '手机号码'=>null,
        '注册日期'=>null,
        '姓名'=>null,
        '实名认证'=>null,
        '首投时间'=>null,
        '首投金额'=>null,
        '首投类型'=>null,
        '房贷投资金额'=>null,
        '车贷投资金额'=>null,
    ];
    public function getUserDataHeader () {
        return $this->userDataHeader;
    }

    protected $rs;
    public function userRecords ($where,$pagesize=null, $pagefrom=null) {
        $fields= 'userId,contractId, contractId as contractName,phone,ymdReg,realname,length(idCard) as realnameOauth,ymdFirstBuy,amountFirstBuy,shelfIdFirstBuy';
        $this->rs = $this->dbMysql->getRecords(\Rpt\Tbname::tb_user_final, $fields,$where, 'rsort ymdReg', $pagesize, $pagefrom);
        $tmp = array();
        foreach($this->rs as $i=>$r){
            $contractName = \Prj\Data\Contract::getContractName($r['contractName']);
            $this->rs[$i]['contractName'] = $contractName ? $contractName:'协议管理中找不到此协议';
            $this->rs[$i]['phone']=substr_replace($this->rs[ $i ]['phone'], '****', 3, 4);
            if(!empty($this->rs[ $i ]['realname']))	{
                $this->rs[$i]['realname'] = substr_replace($this->rs[ $i ]['realname'], '*', 3,3);
            }else {
                $this->rs[$i]['realname'] = '';
            }
            $this->rs[$i]['ymdReg'] = date('Y-m-d', strtotime($r['ymdReg']));
            $this->rs[$i]['phone'] = empty($r['phone'])?"":substr($r['phone'],0,4).'***'.substr($r['phone'],-4);
            $this->rs[$i][realnameOauth] = ($r['realnameOauth']? '已认证':'未认证');

            if(!empty($r['ymdFirstBuy'])){
                $this->rs[$i]['ymdFirstBuy'] = date('Y-m-d', strtotime($r['ymdFirstBuy']));
                $this->rs[$i]['amountFirstBuy']=number_format( $r['amountFirstBuy']/100,2);
                $this->rs[$i]['shelfIdFirstBuy'] =(in_array($r['shelfIdFirstBuy'], [2000, 3000])?'定期':'活期');
            }else {
                $this->rs[$i]['ymdFirstBuy'] = '';
                $this->rs[$i]['amountFirstBuy']='';
                $this->rs[$i]['shelfIdFirstBuy'] ='';
            }

            $this->rs[$i]['amountHouseLoan']='';
            $this->rs[$i]['amountCarLoan']='';

            $tmp[$i]=$r['userId'];
            unset($this->rs[$i]['userId']);
            if(sizeof($tmp)>=50){
                $this->fillOrder($tmp);
                $tmp=[];
            }
        }
        $this->fillOrder($tmp);
        return $this->rs;

    }

    protected function fillOrder($users) {
        if(empty($users)){
            return;
        }
        $rs = $this->dbMysql->getRecords(\Rpt\Tbname::tb_orders_final, 'userId,sum(amount) as n, shelfId',
            ['userId'=>$users, 'orderStatus'=>\Prj\Consts\OrderStatus::$running],
            'groupby userId groupby shelfId'
        );
        $users = array_combine($users, array_keys($users));
        foreach($rs as $r){
            if($r['n'] > 0) {
                $u = $r['userId'];
                $i = $users[$u];

                if($r['shelfId' == \Prj\Consts\Wares::shelf_static]) {
                    $this->rs[$i]['amountHouseLoan'] =number_format( $r['n']/100,2);
                }elseif($r['shelfId'] == \Prj\Consts\Wares::shelf_static_float){
                    $this->rs[$i]['amountCarLoan'] = number_format( $r['n']/100,2);
                }
            }

        }
    }
}