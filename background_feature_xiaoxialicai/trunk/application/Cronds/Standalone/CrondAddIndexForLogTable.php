<?php
namespace PrjCronds;
/**
 *
 * php /var/www/licai_php/run/crond.php "__=crond/run&task=Standalone.CrondAddIndexForLogTable&ymdh=20150819"
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/7/7 0007
 * Time: 上午 9:31
 */

class CrondAddIndexForLogTable extends \Sooh\Base\Crond\Task{

    public function init() {
        $this->_iissStartAfter = 500;
    }

    public function onRun($dt)
    {
        if($this->_isManual){
            $ymd= $dt->YmdFull;
           $this->addIndex($ymd);
        }else {
            $hour = $dt->hour-0;
            if($hour == 5) {
                $ymd= $dt->YmdFull;
                $this->addIndex($ymd);
            }
        }
        $this->toBeContinue=false;
        return true;
    }

    protected function addIndex($ymd) {
        \Sooh\DB\Cases\LogStorage::$__YMD = $ymd;
        \Sooh\DB\Cases\LogStorage::$__id_in_dbByObj='dbgrpForLog';
        \Sooh\DB\Cases\LogStorage::$__type = 'a';
        \Sooh\DB\Cases\LogStorage::$__nSplitedBy = $GLOBALS['CONF']['dbByObj']['dbgrpForLog'][0];
        \Sooh\DB\Cases\LogStorage::loop(__CLASS__.'::callback_addIndex');
    }

    public static function callback_addIndex ($db, $tbname) {
        try {
            \Sooh\DB\Broker::errorMarkSkip(\Sooh\DB\Error::tableNotExists);

            $sql = ' alter table '.$tbname.' add index index_hhiiss(`hhiiss`)';
            $result = $db->execCustom(['sql'=>$sql]);
            if(!$result){
                error_log('ErrorOnCrondAddIndexForLogTGable###'.'add index for `hhiiss` failed');
            }
        }catch(\ErrorException $e) {
            if(\Sooh\DB\Broker::errorIs($e,\Sooh\DB\Error::tableNotExists)){
                error_log('WaringOnCrondAddIndexForLogTGable### table '.$tbname.' not exists');
            }else {
                error_log('ErrorOnCrondAddIndexForLogTGable###'.$e->getMessage()."\n".\Sooh\DB\Broker::lastCmd()."\n".$e->getTraceAsString());
            }
        }


    }
}