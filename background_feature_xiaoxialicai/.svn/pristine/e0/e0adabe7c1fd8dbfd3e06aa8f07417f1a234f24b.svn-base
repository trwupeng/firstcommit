<?php

namespace PrjCronds;

/**
 * php /var/www/licai_php/run/crond.php "__=crond/run&task=MedalReport.CrondMedalReport&ymdh=2016062823"
 * 勋章系统数据统计脚本
 *
 * @author wu.chen
 */
class CrondMedalReport extends \Sooh\Base\Crond\Task {

    public function init() {
        parent::init();
        $this->_isManual = true;
        $this->_iissStartAfter = 3000;
        $this->toBeContinue = true;
        $this->ret = new \Sooh\Base\Crond\Ret();
    }

    public function free() {
        parent::free();
    }

    protected function onRun($dt) {
        var_log('###[warning]勋章统计脚本运行,当前时间:' . $dt->hour . '###');
        if ($dt->hour == 23) {
            $userMedal = new \Lib\Medal\UserMedal();
            $res = $userMedal->getAllRecords();
            if ($res) {
                $medal = new \Lib\Medal\Medal();
                $medals = $medal->getAllRecords();
                $report = [];
                foreach ($res as $key => $value) {
                    $m = json_decode($value['medals'], TRUE);
                    if (!empty($m)) {
                        foreach ($m as $k => $v) {
                            if (!empty($v['getLevel'])) {
                                foreach ($v['getLevel'] as $i => $l) {
                                    $report[$v['key']][$i] += 1;
                                }
                            }
                        }
                    }
                }
                if (!empty($report)) {
                    $medalFinal = new \Rpt\Medal\MedalFinal();
                    foreach ($medals as $value) {
                        if ($report[$value['key']]) {
                            $data = ['medalKey' => $value['key'], 'medalName' => $value['name'], 'medalReport' => json_encode($report[$value['key']])];
                            $medalFinal->replaceRec($data);
                        }
                    }
                    $this->toBeContinue = FALSE;
                }
            }
        }
        return true;
    }

}
