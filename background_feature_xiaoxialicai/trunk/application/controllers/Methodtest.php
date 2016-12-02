<?php
/**
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/3/15 0015
 * Time: 下午 7:23
 */

function echo_log($var, $msg='') {
    echo "<pre>".$msg."<br>".var_export($var, true)."</pre>";
}


class MethodtestController extends \Prj\ManagerCtrl {
    public function indexAction (){
        for($i=1; $i <= 5; $i++) {
//            $result =  $this->sendMsg('15021075217', '这是测试短信'.rand(1000, 9999999));
//            echo var_export($result, true);
        }
    }

    protected function sendMsg ($phone, $msg) {
        try {
            \Lib\Services\SMS::getInstance()->sendNotice($phone, $msg);
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }


    public function testcodeAction () {
//        $records = \Prj\Data\ExchangeCode::usageByBachId();
//        echo_log($records);
        $grp_batch = \Prj\Data\ExchangeCode::grpIdWithBatchId('aaaa');
        echo_log($grp_batch);
    }

    public function copartnerAction (){
        $o = new \Rpt\DataDig\CopartnerWorthDig();
        $o->importData(20160328);
    }

    public function getEvtDailyNameAction() {

        $path = '/var/www/licai_php/application/library/Rpt/EvtDaily';
        $rs = scandir($path);
        foreach($rs as $filename) {
            if (in_array($filename, ['.', '..', 'Base.php'])) {
                continue;
            }
            $tmp = substr($filename, 0, -4);
            $class ='\\Rpt\\EvtDaily\\'.$tmp;
            $displayname = $class::displayname();
echo $tmp.' '.$displayname."<br>";
        }
    }

    public function sendmarketmsgAction (){
        $rs = \Lib\Services\SMS::getInstance()->sendMarket('15721101671', '网络理财首选小虾理财');
        echo_log($rs, 'rs>>>>');
    }

}