<?php
/**
 *
 * php /var/www/licai_php/run/crond.php "__=crond/run&task=Standalone.CrondOverdueVouchersRemind&ymdh=20150819"
 * 到规定时间点发送短信和站内信
 *
 * 不支持手动跑
 *
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/5/9 0009
 * Time: 下午 2:13
 */
namespace PrjCronds;
use Rpt\Configs\VoucherOverdue as config;


class CrondOverdueVouchersRemind extends \Sooh\Base\Crond\Task{

    protected $db_rpt;
    public function init() {
        parent::init();
        $this->_secondsRunAgain=180;
        $this->toBeContinue=true;
        $this->ret = new \Sooh\Base\Crond\Ret();
        $this->db_rpt = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_p2prpt);
    }

    public function free() {
        parent::free();
        $this->db_rpt = null;
    }

    public function onRun ($dt) {
        $ymdRemind = $dt->YmdFull;

        /**
         * 手动的时候，当天要提醒的时间点都跑一遍
         */

        if($this->_isManual) {
            $timePoints = array_keys(config::$overdue);
            foreach($timePoints as $hisRemind) {
                $this->sendSMSAndMsg($ymdRemind, $hisRemind);
            }
        } else {
            $hisRemind = date('Hi', $dt->timestamp()) -0 ;
            $timeStamp = $dt->timestamp() -600;
            $ymdRemindF = date('Ymd', $timeStamp);
            $hisRemindF = date('Hi', $timeStamp) -0 ;  // 前10分钟范围
            if($ymdRemind > $ymdRemindF) {
                $hisRemindF = 0;
            }

            error_log(__CLASS__.'### 提醒时间点：'.$ymdRemind.' '.$hisRemind);
            $this->sendSMSAndMsg($ymdRemind, $hisRemind, $hisRemindF);
        }
        $this->lastMsg = $this->ret->toString();
        error_log('###'.__CLASS__.'#lastMsg###'.$this->lastMsg);
        return true;
    }

    /**
     * @param $ymdRemind 提醒日期
     * @param $hisRemind 提醒时间点
     * @param $overdure  提醒时间点配置
     */
    private function sendSMSAndMsg ($ymdRemind, $hisRemind, $hisRemindF=null) {

        $grabVoucherDesc = [];
        foreach(config::$grabVoucherType as $v) {
            $grabVoucherDesc = array_merge($grabVoucherDesc, $v);
        }
        // 判断提醒时间段内是不是有么有提醒的任务，没有的话就提醒，有的话就return
//        if(isset(config::$overdue[$hisRemind])) {
        error_log('过期红包提醒任务,自动执行日期和时间点：'.$ymdRemind.'-'.$hisRemind);

        if($this->_isManual && !isset(config::$overdue[$hisRemind])) {
            return;
        }elseif(!$this->_isManual) {
            $hisRemind = $this->db_rpt->getOne(\Rpt\Tbname::tb_vouchers_overdue, 'hisRemind',
                ['ymdRemind'=>$ymdRemind, 'hisRemind['=>$hisRemind, 'hisRemind]'=>$hisRemindF]);
//error_log('从时间段 查找提醒时间点sql：'.\Sooh\DB\Broker::lastCmd());
            if(empty($hisRemind)) {
                return;
            }
        }

        $remindedCount = $this->db_rpt->getRecordCount(\Rpt\Tbname::tb_vouchers_overdue, ['ymdRemind'=>$ymdRemind, 'hisRemind'=>$hisRemind, 'status>'=>0]);
        if($remindedCount > 0) {
            error_log('### Trace vouchers overdue remind: ### '. $ymdRemind.' '.$hisRemind.' has reminded');
            $this->toBeContinue = false;
            return;
        }

        $where = ['ymdRemind'=>$ymdRemind, 'hisRemind'=>$hisRemind];
        $userCount = $this->db_rpt->getRecordCount(\Rpt\Tbname::tb_vouchers_overdue, $where);
//error_log(\Sooh\DB\Broker::lastCmd());
//error_log('要发送的用户总数：'.$userCount);
        $pagesize =config::pagesize;
        $pagecount = ceil($userCount/$pagesize);
        for($i=0; $i<$pagecount; $i++) {
            $users = $this->db_rpt->getPair(\Rpt\Tbname::tb_vouchers_overdue, 'userId', 'ymdExpired', $where, null, $pagesize, $i*$pagesize);
//var_log($users, '第'.$i.'页的用户:');
            $users_info = \Prj\Data\User::loopFindRecordsByFields(['userId'=>array_keys($users)], null, 'userId, phone', 'getRecords');
            foreach($users_info as $k => $info) {
                $users_info[$info['userId']] = $info['phone'];
                unset($users_info[$k]);
            }
//var_log($users_info, '第'.$i.'页的用户的信息：');
            foreach($users as $u => $ymdExpired) {
                // 今天已经提醒过的就不提醒了
                $isReminded = $this->db_rpt->getRecordCount(\Rpt\Tbname::tb_vouchers_overdue, ['ymdRemind'=>$ymdRemind,'status'=>1, 'userId'=>$u]);
                $where_upd = array_merge($where, ['userId'=>$u, 'status'=>0]);
                if($isReminded>0) {
//                        error_log('更新成1');
                    $this->db_rpt->updRecords(\Rpt\Tbname::tb_vouchers_overdue, ['status'=>1], $where_upd);
                    $this->ret->newupd++;
                }else {     // 对应时间范围将要过期的红包全部使用，就不提醒。 否则提醒。
                    $where_voucher = [
                        'dtExpired]'    => $ymdExpired.sprintf('%06d',  config::$overdue[$hisRemind][0]),
                        'dtExpired<'    => $ymdExpired.sprintf('%06d',  config::$overdue[$hisRemind][1]),
                        'userId'        => $u,
                        'descCreate'    =>$grabVoucherDesc,
                        'voucherType'   =>array_keys(config::$grabVoucherType),
                        'statusCode'    =>0,
                    ];

                    $voucher_obj  = \Prj\Data\Vouchers::getCopy($u);
                    $records = $voucher_obj->db()->getRecords($voucher_obj->tbname(),
                        'voucherType,amount', $where_voucher);

                    /**
                     * 提醒点对应时间范围内将要过期的红包全部使用，就不发短信提醒。并将这个提醒点用户状态改为已经使用。
                     * 否则获取整天即将过去的券惊醒提醒。
                     */
                    if(empty($records)) {
//                            error_log('更新成2');
                        $this->db_rpt->updRecords(\Rpt\Tbname::tb_vouchers_overdue, ['status'=>2], $where_upd);
                        $this->ret->newupd++;
                    }else{
                        $where_voucher['dtExpired]'] = $ymdExpired.'000000';
                        $where_voucher['dtExpired<'] = $ymdExpired.'235959';
                        $records = $voucher_obj->db()->getRecords($voucher_obj->tbname(),
                            'userId,dtExpired,voucherType,amount/100 as amount', $where_voucher);
//error_log($u.'###############################################################');
//var_log($records, 'records>>>>>');
                        $addup = [];
                        $no_addup = [];
                        foreach($records as $r) {
                            if(in_array($r['voucherType'], config::$addup_type)){
                                $addup[$r['voucherType']]['amount']+= $r['amount'];
                                $addup[$r['voucherType']]['num'] += 1;
                            }elseif(in_array($r['voucherType'], config::$no_addup_type)){
                                $no_addup[$r['voucherType']]['amount'] += 1;
                            }
                        }

                        $str = '';
                        if(!empty($addup)) {
                            foreach($addup as $voucherType => $v) {
                                $str .= str_replace(['{num}','{amount}'], [$v['num'], $v['amount']], config::$msg[$voucherType]).'，';
                            }
                        }

                        if(!empty($no_addup)) {
                            foreach($no_addup as $voucherType => $v) {
                                foreach($v as $amount => $num) {
                                    $str .= str_replace(['{num}','{amount}'], [$num, $amount], config::$msg[$voucherType]).'，';
                                }
                            }
                        }
//var_log($addup, 'addup>>>>>>');
//var_log($no_addup, 'no_addup>>>>>');

                        /**
                         * 站内信或者短信有一个提醒成功就算提醒成功
                         */
                        $status =0;
                        $sendMsgType = config::$sendMsgType;
                        $sendMsgType = array_unique($sendMsgType);
                        foreach ($sendMsgType as $type ) {
                            switch ($type) {
                                case 1:
                                    try {
                                        $phone = $users_info[$u];
                                        $sms = str_replace('{replace}', $str, config::sms_tpl);
                                        \Lib\Services\SMS::getInstance()->sendNotice($phone, $sms);
                                        $status = 1;
                                    }catch (\ErrorException $e) {
                                        error_log('### Trace vouchers overdue remind: ### '. $ymdRemind.' '.$hisRemind.' sms　failed! errMsg:'.$e->getMessage());
                                    }
                                    break;
                                case 2:
                                    try {
                                        $msg = str_replace('{replace}', $str, config::msg_tpl);
                                        \Lib\Services\Message::getInstance()->add(0, $u, 5,  config::msg_title, $msg, null, false);
                                        $status = 1;
                                    }catch(\ErrorException $e){
                                        error_log('### Trace vouchers overdue remind: ### '. $ymdRemind.' '.$hisRemind.' msg　failed! errMsg:'.$e->getMessage());
                                    }
                                    break;
                                case 3:
                                    try {
//                                $u = 14529518699433;
                                        $push_msg = str_replace('{replace}', $str, config::push_tpl);
                                        \Lib\Services\Message::getInstance()->push($u, $push_msg);
                                        $status = 1;
                                    }catch(\ErrorException $e) {
                                        error_log('### Trace vouchers overdue remind: ### '. $ymdRemind.' '.$hisRemind.' push　failed! errMsg:'.$e->getMessage());
                                    }
                                break;


                            }
                        }

                        if($status) {
//                                error_log('更新 1');
                            $this->db_rpt->updRecords(\Rpt\Tbname::tb_vouchers_overdue, ['status'=>$status], $where_upd);
                            $this->ret->newupd++;
                        }

                    }
                }

            }
        }
        $this->toBeContinue = false;
//        }
    }

}































