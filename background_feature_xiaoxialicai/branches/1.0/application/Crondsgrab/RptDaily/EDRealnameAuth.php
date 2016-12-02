<?php
namespace PrjCronds;
/**
 *
 * OK
 * php /var/www/licai_php/run/crond.php "__=crond/rungrab&task=RptDaily.EDRealnameAuth&ymdh=20160126"
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/1/27 0027
 * Time: 下午 3:35
 */

class EDRealnameAuth extends  \Sooh\Base\Crond\Task {
    public function init() {
        parent::init();
        $this->toBeContinue = true;
        $this->_iissStartAfter = 1055;
        $this->_secondsRunAgain=1200;
        $this->ret = new \Sooh\Base\Crond\Ret();
    }

    public function free() {
        parent::free();
    }

    protected function onRun ($dt) {
        $this->oneday($dt->YmdFull);
        if(!$this->_isManual && $dt->hour <= 6){
            $dt0 = strtotime($dt->YmdFull);
            switch ($dt->hour) {
                case 1: $ymd = date('Ymd',$dt0-86400*10);break;
                case 2: $ymd = date('Ymd',$dt0-86400*7);break;
                case 3: $ymd = date('Ymd',$dt0-86400*4);break;
                case 4: $ymd = date('Ymd',$dt0-86400*3);break;
                case 5: $ymd = date('Ymd',$dt0-86400*2);break;
                case 6: $ymd = date('Ymd',$dt0-86400*1);break;
            }
        }
        return true;
    }

    protected function oneday($ymd){
        $db_rpt = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_p2prpt);
        $realname = \Rpt\EvtDaily\RealnameAuth::getCopy('RealnameAuth');

        $rs = $db_rpt->getRecords(\Rpt\Tbname::tb_user_final, 'clientType,copartnerId,flagUser,count(*) as n',
            ['ymdRealnameAuth'=>$ymd, 'flagUser!'=>1],
            'groupby clientType groupby copartnerId groupby flagUser');
        if(!empty($rs)) {
            foreach($rs as $r) {
                $realname->add($r['n'], $r['clientType'], $r['copartnerId'],$r['flagUser'],0);
            }
        }
        $realname->save($db_rpt, $ymd);
        $realname->reset();

        $realnameNewReg = \Rpt\EvtDaily\RealnameAuthNewReg::getCopy('RealnameAuthNewReg');
        $rs = $db_rpt->getRecords(\Rpt\Tbname::tb_user_final, 'clientType, copartnerId, flagUser, count(*) as n',
            ['ymdRealnameAuth'=>$ymd, 'ymdReg'=>$ymd, 'flagUser!'=>1],
            'groupby clientType groupby copartnerId groupby flagUser');
        if(!empty($rs)) {
            foreach($rs as $r) {
                $realnameNewReg->add($r['n'], $r['clientType'], $r['copartnerId'], $r['flagUser'], 0);
            }
        }

        $realnameNewReg->save($db_rpt, $ymd);
        $realnameNewReg->reset();
        $this->lastMsg = 'Total('.$ymd.'):';//要在运行日志中记录的信息
        error_log(__CLASS__.'###########'.$this->lastMsg);
    }

}