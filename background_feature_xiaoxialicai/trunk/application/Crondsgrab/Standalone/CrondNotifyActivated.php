<?php
namespace PrjCronds;
/**
 * php /var/www/licai_php/run/crond.php "__=crond/rungrab&task=Standalone.CrondNotifyActivated&ymdh=20150819"
 *
 * 通过日志表，客户端激活通知合作方
 * Created by PhpStorm.
 * User: li.lianqi
 */

class CrondNotifyActivated extends \Sooh\Base\Crond\Task{
    public function init() {
        parent::init();
        $this->toBeContinue = true;
        $this->_iissStartAfter = 700; // 每个小时的第7分钟执行一次
        $this->_secondsRunAgain = 600; // 10分钟跑一次
        $this->db_rpt = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_p2prpt);
        $this->db_produce_slave = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_p2p_slave);
    }
    public function free() {
        parent::free();
        $this->db_rpt=$this->db_produce_slave=null;
    }

    protected $db_rpt;
    protected $db_produce_slave;

    protected function onRun($dt) {
error_log(__CLASS__.'###########'.date('Y-m-d H:i:s', $dt->timestamp()));
        \Sooh\DB\Cases\LogStorage::$__id_in_dbByObj='dbgrpForLog';
        \Sooh\DB\Cases\LogStorage::$__type = 'a';
        \Sooh\DB\Cases\LogStorage::$__nSplitedBy = $GLOBALS['CONF']['dbByObj']['dbgrpForLog'][0];
        if($this->_isManual) {
            $ymd = date('Ymd', $dt->timestamp());
            \Sooh\DB\Cases\LogStorage::$__YMD = $ymd;
            $where = [
                'ymd'=> $ymd,
                'evt'=> 'start_up',
            ];
//var_log($where, '手动 where#####');
            $this->startUpTodo($where);
        }else {
            $dtTo = $dt->timestamp();
            $dtFrom = $dt->timestamp()-1800;  // 30分钟内的日志
            $ymdFrom = date('Ymd', $dtFrom);
            $ymdTo = date('Ymd', $dtTo);
            if($ymdFrom != $ymdTo) {
                \Sooh\DB\Cases\LogStorage::$__YMD = $ymdFrom;
                $where = [
                    'ymd'       => $ymdFrom,
                    'hhiiss]'   => date('His', $dtFrom)-0,
                    'hhiiss['   => 235959,
                    'evt'       => 'start_up',
                ];
//var_log($where, '自动where 1 ');
                $this->startUpTodo($where);
                $where = ['hhiiss]'=>0];

            }else {
                \Sooh\DB\Cases\LogStorage::$__YMD = $ymdTo;
                $where = ['hhiiss]'=> date('His', $dtFrom)-0];
            }

            \Sooh\DB\Cases\LogStorage::$__YMD = $ymdTo;
            $where['ymd']       = $ymdTo;
            $where['hhiiss[']   = date('His', $dtTo)-0;
            $where['evt']       = 'start_up';
//var_log($where, '自动where 2 ');
            $ret = $this->startUpTodo($where);
        }
        return true;
    }


    protected function startUpTodo ($where) {
        try {
            \Sooh\DB\Broker::errorMarkSkip(\Sooh\DB\Error::tableNotExists);
            $sort = ['hhiiss'=>'sort'];
            $recordsCount = \Sooh\DB\Cases\LogStorage::loopGetRecordsCount($where);
//error_log('start_up的日志记录数目：'.$recordsCount);
            $pager = new \Sooh\DB\Pager(500);
            $pager->init($recordsCount);
            for($i = 1; $i<=$pager->page_count; $i++) {
                if($i==1) {
                    $ret = \Sooh\DB\Cases\LogStorage::loopGetRecordsPage($sort, ['where'=>$where], $pager->init($recordsCount, $i));
                }else {
                    $ret = \Sooh\DB\Cases\LogStorage::loopGetRecordsPage($sort, $lastPage, $pager->init($recordsCount, $i));
                }
                $lastPage = $ret['lastPage'];
//error_log('lastCmd#########'.\Sooh\DB\Broker::lastCmd());
                $logGuids = $ret['records'];
//var_log($logGuids, '符合的日志记录:');
                foreach($logGuids as $r) {
                    if(isset($r['contractId']) && isset($r['deviceId']) && !empty($r['deviceId'])) {
                        $args=[
                            'ymd'=>$r['ymd'],
                            'hhiiss'=>$r['hhiiss'],
                        ];

                        if(substr($r['deviceId'], 0, 5)=='SESS:') {
                            $args['deviceId'] = substr($r['deviceId'], 5);
                        }else {
                            $args['deviceId'] = $r['deviceId'];
                        }
                        $o = new \Lib\Api\Notify\Base();
                        $o->onStartUp($args);
                    }
                }
            }
        }catch(\ErrorException $e) {
            if(\Sooh\DB\Broker::errorIs($e,\Sooh\DB\Error::tableNotExists)){
                error_log('WarningOnNotifyActivated'.$e->getMessage());
            }else {
                error_log("ErrorOnNotifyActivated:".$e->getMessage()."\n".\Sooh\DB\Broker::lastCmd()."\n".$e->getTraceAsString());
            }
        }

    }

}
