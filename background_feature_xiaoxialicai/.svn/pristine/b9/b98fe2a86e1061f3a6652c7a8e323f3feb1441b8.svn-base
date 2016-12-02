<?php
namespace Rpt\DataDig;

class RedpacketreportDataDig {

    protected $ymd;
    public function __construct(){
        $this->dbMysql = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_p2prpt);
    }
    protected $dbMysql;
    protected $voucherAllStatus = [
        \Prj\Consts\Voucher::status_freeze,
        \Prj\Consts\Voucher::status_wait,
        \Prj\Consts\Voucher::status_used,
        \Prj\Consts\Voucher::status_unuse,
    ];

    protected $voucherUnusedStatus = [
        \Prj\Consts\Voucher::status_freeze,
        \Prj\Consts\Voucher::status_wait,
        \Prj\Consts\Voucher::status_unuse,
    ];



    protected $dailyDataRecords=[];

    public function redpacketDailyDataCount () {


        return sizeof($this->dailyDataRecords);

    }

    public function getDailyDataRecords ($ymdFrom, $ymdTo, $addPkey = false) {

        // 红包产出
        $where = ['voucherType'=>\Prj\Consts\Voucher::type_real, 'statusCode'=>$this->voucherAllStatus, 'ymdCreate]'=>$ymdFrom, 'ymdCreate['=>$ymdTo];
        $produced = $this->dbMysql->getPair(\Rpt\Tbname::tb_vouchers_final, 'ymdCreate', 'sum(amount)/100 as n', $where, 'groupby ymdCreate');
        // 红包消耗
        $where = ['voucherType'=>\Prj\Consts\Voucher::type_real, 'statusCode'=>\Prj\Consts\Voucher::status_used, 'ymdUsed]'=>$ymdFrom, 'ymdUsed['=>$ymdTo];
        $used = $this->dbMysql->getPair(\Rpt\Tbname::tb_vouchers_final, 'ymdUsed', 'sum(amount)/100 as n', $where, 'groupby ymdUsed');

        // 红包失效
        $where = ['voucherType'=>\Prj\Consts\Voucher::type_real, 'statusCode'=>-99, 'dtExpired]'=>$ymdFrom, 'dtExpired['=>$ymdTo];
        $overdue = $this->dbMysql->getPair(\Rpt\Tbname::tb_vouchers_final, 'dtExpired', 'sum(amount)/100 as n', $where, 'groupby dtExpired');


        while ($ymdTo >= $ymdFrom) {
            $r = [
                'ymd'=>date('Y-m-d', strtotime($ymdTo)),
                'produce'=>isset($produced[$ymdTo])?number_format($produced[$ymdTo], 2):'',
                'used'=> isset($used[$ymdTo])?number_format($used[$ymdTo], 2):'',
                'overdue'=>isset($overdue[$ymdTo])?number_format($overdue[$ymdTo], 2):'',
            ];
            $tmp = $r;
            unset($tmp['ymd']);
            if(array_sum($tmp) == 0) {
                $ymdTo = date('Ymd', strtotime($ymdTo)-86400);
                continue;
            }

            if($addPkey) {
                $r['_pkey_'] = $ymdTo;
            }
            $this->dailyDataRecords[]= $r;
            $ymdTo = date('Ymd', strtotime($ymdTo)-86400);
        }
        return $this->dailyDataRecords;
    }

    public function getDailyDataRecordsByPage($pagesize, $pagefrom) {
        return  array_slice($this->dailyDataRecords, $pagefrom, $pagesize);

    }

    public $headersDailyData = [
        'ymd'           =>['日期', null],
        'produced'       =>['红包产出', null],
        'used'          =>['红包消耗', null],
        'overdue'       =>['红包失效', null],
    ];

    public function getHeadersDailyData() {
        $headers = [];
        foreach($this->headersDailyData as $v) {
            $headers[$v[0]] = $v[1];
        }
        return $headers;
    }

    /**
     *
     * 红包分类详情
     */


    public function getRedpacketCate() {
//        $db = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_p2p);
        $records = \Prj\Data\Vouchers::loopFindRecordsByFields(['voucherType'=>\Prj\Consts\Voucher::type_real], null, 'codeCreate, descCreate');
        $tmp = [];
        if(!empty($records)) {
            foreach($records as $r) {
                $tmp[$r['codeCreate']] = $r['descCreate'];
            }
        }
        return $tmp;
    }
    public function recordsByCategory($ymd){
        $redpacketCate = $this->getRedpacketCate();
        // 红包产出
        $where = ['voucherType'=>\Prj\Consts\Voucher::type_real, 'ymdCreate'=>$ymd, 'statusCode'=>$this->voucherAllStatus];
        $produced = $this->dbMysql->getPair(\Rpt\Tbname::tb_vouchers_final, 'codeCreate', 'sum(amount)/100 as n', $where, 'groupby codeCreate');

        // 红包消耗
        $where = ['voucherType'=>\Prj\Consts\Voucher::type_real, 'ymdUsed'=>$ymd, 'statusCode'=>\Prj\Consts\Voucher::status_used];
        $used = $this->dbMysql->getPair(\Rpt\Tbname::tb_vouchers_final, 'codeCreate', 'sum(amount)/100 as n', $where, 'groupby codeCreate');

        // 红包失效
        $where = ['voucherType'=>\Prj\Consts\Voucher::type_real, 'statusCode'=>-99, 'dtExpired'=>$ymd];
        $overdue = $this->dbMysql->getPair(\Rpt\Tbname::tb_vouchers_final, 'codeCreate', 'sum(amount)/100 as n', $where, 'groupby codeCreate');


        $cates = array_merge(array_keys($produced), array_keys($used), array_keys($overdue));
        $cates = array_unique($cates);

        $records = [];
        if(!empty($cates)) {
            foreach($cates as $catid) {
                $tmp = [
                    'produced' => isset($produced[$catid])?number_format($produced[$catid], 2):'',
                    'used' => isset($used[$catid])?number_format($used[$catid], 2):'',
                    'overdue'=>isset($overdue[$catid])?number_format($overdue[$catid], 2):'',
                ];

                if(array_sum($tmp) == 0) {
                    continue;
                }
                $tmp = ['catid'=>$redpacketCate[$catid]] + $tmp;
                $records[] = $tmp;
            }
        }
        return $records;

    }

    public $headerOfCategory = [
        '红包类别'=>null,
        '红包产出'=>null,
        '红包消耗'=>null,
        '红包失效'=>null,
    ];


    /**
     *
     * 红包排行
     */

//红包产出排行','红包消耗排行','红包失效排行'
    public function rankRecords($ymd, $rankBase, $pkey=false) {
        $records = [];
        switch($rankBase) {
            case 0:
                $where = ['ymdCreate'=>$ymd, 'voucherType'=>\Prj\Consts\Voucher::type_real, 'statusCode!'=>-1];
                break;
            case 1:
                $where = ['ymdUsed'=>$ymd, 'voucherType'=>\Prj\Consts\Voucher::type_real];

                break;
            case 2:
                $where = ['dtExpired'=>$ymd, 'statusCode'=>-99, 'voucherType'=>\Prj\Consts\Voucher::type_real];
                break;
        }
        $rs = $this->dbMysql->getPair(\Rpt\Tbname::tb_vouchers_final, 'userId', 'sum(amount)/100 as n', $where,
            'groupby userId rsort n', 10);
        $users = array_keys($rs);
        if(empty($users)){
            return $records;
        }

        $user_info = $this->dbMysql->getAssoc(\Rpt\Tbname::tb_user_final, 'userId', 'phone,realname', ['userId'=>$users]);
        $produces = $this->getProduceByUser($ymd, $users);
        $used = $this->getUsedByUser($ymd, $users);
        $overdure = $this->getOverdureByUser($ymd, $users);

        foreach($users as $uid){
            $tmp = [
                'userId'=>$user_info[$uid]['userId'],
                'phone'=>$user_info[$uid]['phone'],
                'realname'=>$user_info[$uid]['realname'],
                'ymd'=> date('Y-m-d', strtotime($ymd)),
                'produce'=>empty($produces[$uid])?'':number_format($produces[$uid], 2),
                'used'=>empty($used[$uid])?'':number_format($used[$uid], 2),
                'overdue'=>empty($overdure[$uid])?'':number_format($overdure[$uid], 2),
            ];
            if($pkey) {
                $tmp['_pkey_'] = \Prj\Misc\View::encodePkey(['ymd'=>$ymd, 'userId'=>$uid]);
            }
            $records[] = $tmp;
        }


        return $records;
    }


    protected function getProduceByUser($ymd, $users) {
        $where =['userId'=>$users, 'ymdCreate'=>$ymd, 'voucherType'=>\Prj\Consts\Voucher::type_real, 'statusCode!'=>-1];
        return $this->dbMysql->getPair(\Rpt\Tbname::tb_vouchers_final, 'userId', 'sum(amount)/100 as n', $where, 'groupby userId rsort n');
    }

    protected function getUsedByUser($ymd, $users) {
        $where = ['userId'=>$users, 'ymdUsed'=>$ymd, 'voucherType'=>\Prj\Consts\Voucher::type_real];
        return $this->dbMysql->getPair(\Rpt\Tbname::tb_vouchers_final, 'userId', 'sum(amount)/100 as n', $where, 'groupby userId rsort n');
    }
    protected function getOverdureByUser($ymd, $users) {
        $where = ['userId'=>$users, 'dtExpired'=>$ymd, 'statusCode'=>-99, 'voucherType'=>\Prj\Consts\Voucher::type_real];
        return $this->dbMysql->getPair(\Rpt\Tbname::tb_vouchers_final, 'userId', 'sum(amount)/100 as n', $where, 'groupby userId rsort n');
    }

    public $rankheaders = [
        '用户id'      => null,
        '手机号'       => null,
        '姓名'        => null,
        '日期'        => null,
        '红包产出'      => null,
        '红包消耗'      => null,
        '红包失效'      => null,
    ];

    protected function execSql ($sql) {
        $rs = $this->dbMysql->execCustom(['sql'=>$sql]);
        $rs = $this->dbMysql->fetchAssocThenFree($rs);
        return $rs;
    }


    /**
     *
     * 用户红包每日统计
     */

    protected $userRedPacketRecords;
    public function getUserRedPacketRecords($ymdFrom, $ymdTo, $userId) {
        $records = [];
        $userInfo = $this->dbMysql->getRecord(\Rpt\Tbname::tb_user_final, 'phone,realname', ['userId'=>$userId]);
        while ($ymdTo >= $ymdFrom) {
            $produce = $this->getProduceByUser($ymdTo, $userId);
            $used = $this->getUsedByUser($ymdTo, $userId);
            $overdure = $this->getOverdureByUser($ymdTo, $userId);

            $tmp = [
                'produce'=>empty($produce[$userId])?'':number_format($produce[$userId], 2),
                'used'=>empty($used[$userId])?'':number_format($used[$userId], 2),
                'overdure'=>empty($overdure[$userId])?'':number_format($overdure[$userId], 2),
            ];
            if(array_sum($tmp) == 0) {
                $ymdTo = date('Ymd', strtotime($ymdTo)-86400);
                continue;
            }
            $records[] = ['userId'=>$userId, 'phone'=>$userInfo['phone'], 'realname'=>$userInfo['realname'], 'ymd'=>date('Y-m-d', strtotime($ymdTo))] + $tmp;
            $ymdTo = date('Ymd', strtotime($ymdTo)-86400);
        }
        $this->userRedPacketRecords = $records;
        return $records;
    }

    public function getUserRedPacketRecordsCount() {
        return sizeof($this->userRedPacketRecords);
    }

    public function getUserRedPacketRecordsByPage($pagesize, $pagefrom) {
        return  array_slice($this->userRedPacketRecords, $pagefrom, $pagesize);
    }


}