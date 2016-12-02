<?php
namespace prjCronds;

/**
 * php /var/www/licai_php/run/crond.php "__=crond/rungrab&task=Standalone.CrondVouchers&ymdh=20160321"
 * 时间段内发券情况的流水
 * 
 */
class CrondVouchers extends \Rpt\Misc\DataCrondGather
{

    public function init()
    {
        parent::init();
        $this->_iissStartAfter = 200;
        $this->_secondsRunAgain = 600;
        $this->dbMysql = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_p2prpt);
        $this->excludeUser = \Rpt\Funcs::getexcludedUser();
    }

    protected $excludeUser;
    protected $dbMysql;

    public function free()
    {
        parent::free();
        $this->dbMysql = null;
    }

    protected $unuse_status = [
        \Prj\Consts\Voucher::status_unuse,
    ];

    protected function gather()
    {
        $this->printLogOfTimeRang();
        /**
         * 抓取券的发放情况
         */
        $startTime = date('YmdHis', $this->dtFrom);
        $endTime = date('YmdHis', $this->dtTo);
        $where = array(
            'timeCreate]' => $startTime,
            'timeCreate[' => $endTime,
            'statusCode' => [\Prj\Consts\Voucher::status_unuse, \Prj\Consts\Voucher::status_used],
        );
        // var_log($where, '111111111111111111111111111111111111111');
        $arr_user_id = \Prj\Data\Vouchers::loopFindRecordsByFields($where, null, 'distinct(userId) as uid', 'getCol');
        // error_log('发放券的信息111111111111111111111：' . \Sooh\DB\Broker::lastCmd());
        // var_log($arr_user_id, '发放券 的情况：666666666666666666666666666');
        if (! empty($arr_user_id)) {
            foreach ($arr_user_id as $k => $uid) {
                if(in_array($uid, $this->excludeUser)) {
                    continue;
                }
                // 抓取发券信息
                $obj = \Prj\Data\Vouchers::getCopy($uid);
                
                $db = $obj->db();
                
                $tbname = $obj->tbname();
                // var_log($tbname,'1111111111111111111111111111111');
                $where['userId'] = $uid;
                $records = $db->getRecords($tbname, \Rpt\Fields::$tb_vouchers_produce_fields, $where);
                // error_log('发放券的信息222222222222222222222：' . \Sooh\DB\Broker::lastCmd());
                // var_log($records, $k . '用户的发券信息666666666666666');
                foreach ($records as $r) {
                    $this->ret->total ++;
                    $tmp = [
                        'voucherId' => $r['voucherId'],
                        'userId' => $r['userId'],
                        'orderId' => $r['orderId'],
                        'voucherType' => $r['voucherType'],
                        'amount' => $r['amount'],
                        'ymdCreate' => substr($r['timeCreate'], 0, 8),
                        'ymdUsed' => substr($r['dtUsed'], 0, 8),
                        'statusCode' => $r['statusCode'],
                        'dtExpired' => substr($r['dtExpired'], 0, 8),
                        'codeCreate'=>$r['codeCreate'],
                        'descCreate'=>$r['descCreate'],
                    ];

                    if(in_array($tmp['statusCode'], $this->unuse_status) && $r['dtExpired']< \Sooh\Base\Time::getInstance()->ymdhis()) {
                        var_log($tmp['statusCode'], $tmp['voucherId'].'    add statusCode>>>>>>>>>>>>>>>>');
                        $tmp['statusCode'] = -99; // 过期的券也加一个状态
                    }
                    try {
                        \Sooh\DB\Broker::errorMarkSkip();
                        $this->dbMysql->addRecord(\Rpt\Tbname::tb_vouchers_final, $tmp);
                        // error_log('发放券的信息：'.\Sooh\DB\Broker::lastCmd());
                        $this->ret->newadd ++;
                        // error_log('发放券的信息：' . \Sooh\DB\Broker::lastCmd());
                    } catch (\ErrorException $e) {
                        if (\Sooh\DB\Broker::errorIs($e)) {
                            
                            unset($tmp['voucherId']);
                            $this->dbMysql->updRecords(\Rpt\Tbname::tb_vouchers_final, $tmp, array(
                                'voucherId' => $r['voucherId']
                            ));
                            // error_log('券信息：' . \Sooh\DB\Broker::lastCmd());
                            $this->ret->newupd ++;
                        } else {
                            error_log($e->getMessage() . "\n" . $e->getTraceAsString());
                        }
                    }
                }
                $obj->free();
                $db = null;
            }
            }
            
            $arr_user_id = null;
            
         

                /**
                 * 抓取券在报表的情况
                 */
                $where = [
                    'statusCode' => $this->unuse_status
                ];
                // var_log($where, '4444444444444444444444444444444444');
                
                $arr_voucher_rpt = $this->dbMysql->getCol(\Rpt\Tbname::tb_vouchers_final, 'voucherId', $where);
                //var_log($arr_voucher_rpt,'333333333333333333333333');
                // error_log('使用券用户：' . \Sooh\DB\Broker::lastCmd());
                // var_log($arr_voucher_rpt,'4444444444444444444444444444444444');
                if (! empty($arr_voucher_rpt)) {
                    $arr_voucher_rpt = array_chunk($arr_voucher_rpt, 500);
                    foreach ($arr_voucher_rpt as $group) {
                        
                        $records = \Prj\Data\Vouchers::loopFindRecordsByFields([
                            'voucherId' => $group
                        ], null, \Rpt\Fields::$tb_vouchers_produce_fields, 'getRecords');
                        
                        // $records=$this->dbMysql->getPair(\Rpt\Tbname::tb_vouchers_final, $where);
                        //  error_log('使用券用户：' . \Sooh\DB\Broker::lastCmd());
                       //

//                        var_log($records, '5555555555555555555555555');
                        foreach ($records as $r) {
                            $tmp = [
                                'voucherId' => $r['voucherId'],
                                'userId' => $r['userId'],
                                'voucherType' => $r['voucherType'],
                                'amount' => $r['amount'],
                                'ymdCreate' => substr($r['timeCreate'], 0, 8),
                                'ymdUsed' => substr($r['dtUsed'], 0, 8),
                                'orderId' => $r['orderId'],
                                'statusCode' => $r['statusCode'],
                                'codeCreate'=>$r['codeCreate'],
                                'descCreate'=>$r['descCreate'],
                            ];
                            if (!empty($r['dtExpired'])) {
                                $tmp['dtExpired'] = substr($r['dtExpired'], 0, 8);
                            }
                            if(in_array($tmp['statusCode'], $this->unuse_status) && $r['dtExpired']< \Sooh\Base\Time::getInstance()->ymdhis()) {
                                $tmp['statusCode'] = -99; // 过期的券也加一个状态
                            }
                            unset($tmp['voucherId']);
                            $this->dbMysql->updRecords(\Rpt\Tbname::tb_vouchers_final, $tmp, array(
                                'voucherId' => $r['voucherId']
                            ));
                            $this->ret->newupd ++;
                            $this->ret->total ++;
                        }
                    }
                    // var_log($records,'5555555555555555555555555');
                }
                $this->lastMsg = $this->ret->toString();
                error_log('[ Trace ] ### ' . __CLASS__ . ' ### LastMsg:' . $this->lastMsg);
                return true;
            }
        }
    
