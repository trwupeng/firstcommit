<?php
namespace Rpt\DataDig;
class VouchersData {
    
    protected $dbMysql;
    public function __construct(){
        $this->dbMysql = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_p2prpt);
    }
    
    public function exportData ($where) {
        $ymdFrom = $where['ymd]'];
        $ymdTo = $where['ymd['];
        $voucherType = $where['voucherType='];
        $records = [];
        while ($ymdTo >= $ymdFrom) {



            if (!empty($voucherType)) {
                $where = $this->dbMysql->newWhereBuilder();
                $tmp = $this->dbMysql->newWhereBuilder();
                $tmp->init('OR');
                $tmp->append(['ymdCreate'=>$ymdTo,'ymdUsed'=>$ymdTo]);
                $where->init('AND');
                $where->append(null,$tmp);
                $where->append('voucherType',$voucherType);
            }else {
				$where = $this->dbMysql->newWhereBuilder();
				$where->init('OR');
				$where->append('ymdCreate',$ymdTo);
				$where->append('ymdUsed',$ymdTo);
            }
            $conditions = $this->dbMysql->getRecords(\Rpt\Tbname::tb_vouchers_final, ['voucherType','amount','waresId'], 
                        $where, 'groupby voucherType groupby amount groupby waresId');
            if (empty($conditions)) {
                $ymdTo = date('Ymd', strtotime($ymdTo) - 86400);
                continue;
            }
// var_log($conditions, 'conditions>>>>>>>>>>>>>>>>>>>>>');
            foreach ($conditions as $con) {
// var_log($con, 'con>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>.');                
                $tmp = [];
                // 日期
                $tmp['ymd'] = date('Y-m-d', strtotime($ymdTo));
                // 红包类型
                $tmp['voucherType'] = \Prj\Consts\Voucher::$voucherTypeArr[$con['voucherType']];
                // 红包面值
                if ($con['voucherType'] == \Prj\Consts\Voucher::type_yield) {
                    $tmp['amount'] = $con['amount'].'%';
                }else {
                    $tmp['amount'] = $con['amount'];
                }
                
                // 红包使用产品
                $tmp['waresName'] = $this->dbMysql->getOne(\Rpt\Tbname::tb_vouchers_final, 'waresName', array_merge($con, ['ymdUsed'=>$ymdTo]));
                // 发放红包数量
                $tmp['vouchersNum'] = $this->dbMysql->getOne(\Rpt\Tbname::tb_vouchers_final, 'count(voucherId)', array_merge($con, ['ymdCreate'=>$ymdTo]));
                // 发放红包金额
                if ($con['voucherType'] == \Prj\Consts\Voucher::type_yield) {
                    $tmp['vouchersAmount'] = '';
                }else {
                    $tmp['vouchersAmount'] = $this->dbMysql->getOne(\Rpt\Tbname::tb_vouchers_final, 'sum(amount)', array_merge($con, ['ymdCreate'=>$ymdTo])) / 100;
                }
                // 当日红包使用人数
                $tmp['userNumOfTodayUse'] = $this->dbMysql->getOne(\Rpt\Tbname::tb_vouchers_final, 'count(distinct(userId))', array_merge($con, ['ymdUsed'=>$ymdTo, 'ymdCreate'=>$ymdTo]));
                // 当日红包使用数量
                $tmp['vouchersNumOfTodayUse'] = $this->dbMysql->getOne(\Rpt\Tbname::tb_vouchers_final, 'count(voucherId)', array_merge($con, ['ymdUsed'=>$ymdTo, 'ymdCreate'=>$ymdTo]));
                // 当日红包使用金额
                if ($con['voucherType'] == \Prj\Consts\Voucher::type_yield) {
                    $tmp['vouchersAmountOfTodayUse'] = '';
                } else {
                    $tmp['vouchersAmountOfTodayUse'] = $tmp['amount'] * $tmp['vouchersNumOfTodayUse'] / 100;
                }
                // 当日红包使用订单数
                $tmp['orderNumOfTodayUse'] = $this->dbMysql->getOne(\Rpt\Tbname::tb_vouchers_final, 'count(distinct(orderId))', array_merge($con, ['ymdUsed'=>$ymdTo, 'ymdCreate'=>$ymdTo]));
                // 当日红包购买金额
                $tmp['orderAmountOfTodayUse'] = $this->dbMysql->getOne(\Rpt\Tbname::tb_vouchers_final, 'sum(boughtAmount)', array_merge($con, ['ymdUsed'=>$ymdTo, 'ymdCreate'=>$ymdTo])) / 100;
                // 使用红包人数
                $tmp['userNumOfUse'] = $this->dbMysql->getOne(\Rpt\Tbname::tb_vouchers_final, 'count(distinct(userId))', array_merge($con, ['ymdUsed'=>$ymdTo]));
                // 使用红包数量
                $tmp['vouchersNumOfUse'] = $this->dbMysql->getOne(\Rpt\Tbname::tb_vouchers_final, 'count(voucherId)', array_merge($con, ['ymdUsed'=>$ymdTo]));
                // 使用红包金额
                if ($con['voucherType'] == \Prj\Consts\Voucher::type_yield) {
                    $tmp['vouchersAmountOfUse'] = '';
                }else {
                    $tmp['vouchersAmountOfUse'] = $this->dbMysql->getOne(\Rpt\Tbname::tb_vouchers_final, 'sum(amount)', array_merge($con, ['ymdUsed'=>$ymdTo])) / 100;
                }
                // 使用红包订单数
                $tmp['orderNumOfUse'] = $this->dbMysql->getOne(\Rpt\Tbname::tb_vouchers_final, 'count(distinct(orderId))', array_merge($con, ['ymdUsed'=>$ymdTo]));
                // 使用红包购买金额
                $tmp['orderAmountOfUse'] = $this->dbMysql->getOne(\Rpt\Tbname::tb_vouchers_final, 'sum(boughtAmount)', array_merge($con, ['ymdUsed'=>$ymdTo])) / 100;
                
                $records[] =$tmp;
            }            

            $ymdTo = date('Ymd', strtotime($ymdTo) - 86400);
        }
// var_log($records, 'records>>>>>>>>>>>');
        return $records;
    }
    
    protected $header = [
        '日          期',
        '券类型',
        '券面值(分)',
        '券的使用产品',
        '发放券数量',
        '发放券金额(元)',
        '当日券使用人数',
        '当日券使用数量',
        '当日券使用金额(元)',
        '当日券使用订单数',
        '当日券定单金额(元)',
        '使用券人数',
        '使用券数量',
        '使用券金额(元)',
        '使用券订单数',
        '使用券购买金额(元)',
    ];
    
    public function getHeaders () {
        $headers = [];
        foreach ($this->header as $name) {
            $headers[$name] = strlen($name) * 6;
        }
        return $headers;
    }
}