<?php

namespace PrjCronds;

use Lib\Medal\MedalFriendsReg;

/**
 * php /var/www/licai_php/run/crond.php "task=MedalReport.CrondMedalH5"
 * 这个脚本为勋章H5页面的补丁脚本 统计字2016-07-12 21:00:00后通过H5注册的人
 * @author wu.chen
 */
class CrondMedalH5 extends \Sooh\Base\Crond\Task {

    public $medalObj;

    public function init() {
        parent::init();
        //$this->_isManual = true;
        $this->_iissStartAfter = 3000;
        $this->toBeContinue = true;
        $this->db = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_p2p);
        $this->medalObj = new \Lib\Medal\Medal();
    }

    public function free() {
        parent::free();
    }

    protected function onRun($dt) {
        var_log('###[warning]勋章系统H5注册补丁,当前时间:' . $dt->YmdFull . '###');
        if ($this->_isManual) {   //只有手动的时候才能运行           
            for ($i = 0; $i < 2; $i++) {
                /* 邀请好友的任务 */
                $sql = "SELECT COUNT(*) as c, `inviteByUser`, ymdReg, hisReg FROM db_p2p.tb_user_{$i} WHERE `inviteByUser` != 0 AND ymdReg >= 20160712 AND clientType = 903 GROUP BY `inviteByUser`";
                $friendsRes = $this->db->execCustom(['sql' => $sql]);
                $friendsRes = $this->db->fetchAssocThenFree($friendsRes);
                $medalFri = new MedalFriendsReg();
                foreach ($friendsRes as $key => $friends) {
                    $his = $friends['hisReg'];                    
                    if (strlen($his) < 6) {
                        for ($k=0; $k < 6-strlen($his); $k++) {
                            $his = '0'.$his;
                        }
                    }                    
                    $ymdhis = $friends['ymdReg'] . $his;
                    $ymdhis = strtotime($ymdhis);
                    if ($ymdhis >= strtotime('20160712210000') && $ymdhis <= strtotime('20160713120000')) {
                        $medalFri->setUserId($friends['inviteByUser'])->setNum($friends['c'])->logic();
                    }
                }
            }
        }
        return true;
    }

}
