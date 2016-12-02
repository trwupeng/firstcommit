<?php
/**
 *
 * php /var/www/licai_php/run/crond.php "__=crond/rungrab&task=Standalone.CrondOverdueVouchersGrab&ymdh=20150819"
 * 凌晨5点的时候抓取明天即将过期的红包
 *
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/5/9 0009
 * Time: 下午 2:13
 */
namespace PrjCronds;

class CrondOverdueVouchersGrab extends \Sooh\Base\Crond\Task{

    protected $db_rpt;
    public function init() {
        parent::init();

        $this->toBeContinue=true;
        $this->_iissStartAfter=1955;
        $this->ret = new \Sooh\Base\Crond\Ret();
        $this->db_rpt = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_p2prpt);
        $this->excludeUser = \Rpt\Funcs::getexcludedUser();
    }
    protected $excludeUser;
    public function free() {
        parent::free();
        $this->db_rpt = null;
    }

    public function onRun ($dt) {
        if(!$this->_isManual && $dt->hour!=5) {
            return true;
        }
        error_log('###'.__CLASS__.'### start');
        $overdure = \Rpt\Configs\VoucherOverdue::$overdue;
        $grabVoucherType = array_keys(\Rpt\Configs\VoucherOverdue::$grabVoucherType);
        $grabVoucherDesc = [];
        foreach(\Rpt\Configs\VoucherOverdue::$grabVoucherType as $v) {
            $grabVoucherDesc = array_merge($grabVoucherDesc, $v);
        }

        if(empty($grabVoucherDesc)) {
            error_log('###'.__CLASS__.'### 没有要抓取的券类型');
            return true;
        }

        $ymdTomorrow = date('Ymd', $dt->timestamp(1));

        foreach($overdure as $hisRemind => $timeRange) {
            $where = [
                'statusCode'    =>0,
                'descCreate'    =>$grabVoucherDesc,
                'voucherType'   => $grabVoucherType,
                'dtExpired]'    =>$ymdTomorrow.sprintf('%06d', $timeRange[0]),
                'dtExpired['    =>$ymdTomorrow.sprintf('%06d', $timeRange[1]),
            ];
            error_log('###'.__CLASS__.'#grab time range###. '.$where['dtExpired]'].', '.$where['dtExpired<'].']');
            /**
             * 分页获取过期记录
             */
//            $sort = ['dtExpired'=>'sort'];
//            $recordsCount = \Prj\Data\Vouchers::loopGetRecordsCount($where);
//            $pager = new \Sooh\DB\Pager(self::GRAB_PAGESIZE);
//            $pager->init($recordsCount);
//            for ($i=1; $i<=$pager->page_count; $i++ ) {
//                if($i == 1){
//                    $ret = \Prj\Data\Vouchers::loopGetRecordsPage($sort, ['where'=>$where], $pager->init($recordsCount, $i));
//                }else {
//                    $ret = \Prj\Data\Vouchers::loopGetRecordsPage($sort, $lastPage, $pager->init($recordsCount, $i));
//                }
//                $lastPage = $ret['lastPage'];
//
//
//                $records = $ret['records'];
//                foreach($records as $r) {
//                    error_log($r['voucherId'].' '.$r['userId'].' '.$r['dtExpired']);
//                }
//
//            }
            $users = \Prj\Data\Vouchers::loopFindRecordsByFields($where, null, 'distinct(userId)', 'getCol');
//error_log('lastCmd>>>>'.\Sooh\DB\Broker::lastCmd());
            if(empty($users)) {
                continue;
            }

            foreach($users as $u) {
                if(in_array($u, $this->excludeUser)) {
                    continue;
                }
//error_log($u.' '.$hisRemind);
                try {
                    \Sooh\DB\Broker::errorMarkSkip();
                    $record = [
                        'userId'=>$u,
                        'ymdRemind' => $dt->YmdFull,
                        'hisRemind' => $hisRemind,
                        'ymdExpired' => $ymdTomorrow,
                    ];
                    $this->db_rpt->addRecord(\Rpt\Tbname::tb_vouchers_overdue, $record);
                    $this->ret->newadd++;
                }catch(\ErrorException $e) {
                        error_log('Trace add voucher overdue userID:'.$u.' '.$e->getMessage());
                }
            }
//            error_log('lastCmd>>>>>'.\Sooh\DB\Broker::lastCmd());
        }

        $this->lastMsg = $this->ret->toString();
        error_log('###'.__CLASS__.'#lastMsg###'.$this->lastMsg);
        $this->toBeContinue = false;
        return true;
    }

}
