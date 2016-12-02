<?php

namespace PrjCronds;

use Lib\Medal\MedalFriendsReg;
use Lib\Medal\MedalCheckin;
use Lib\Medal\MedalInvestment;
use Lib\Medal\MedalRedPacket;

/**
 * php /var/www/licai_php/run/crond.php "task=MedalReport.CrondMedalFirst&ymdh=2016062823"
 * 这个脚本为勋章系统第一次统计用户历史行为是否符合勋章要求，理论上只需要在勋章系统上线的时候执行一次
 *
 * @author wu.chen
 */
class CrondMedalFirst extends \Sooh\Base\Crond\Task {

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
        var_log('###[warning]勋章系统第一次任务统计脚本运行,当前时间:' . $dt->YmdFull . '###');
        if ($this->_isManual) {   //只有手动的时候才能运行
            $sql = "SELECT * FROM db_p2p.tb_medal";
            $medals = $this->db->execCustom(['sql' => $sql]);
            $medals = $this->db->fetchAssocThenFree($medals);
            $medal_arr = [];
            foreach ($medals as $k => $m) {
                $medal_arr[$m['key']] = $m;
            }
            unset($medals);
            for ($i = 0; $i < 2; $i++) {
                $sql = "SELECT SUM(`amount`) as amount, `userId` FROM db_p2p.tb_investment_{$i} WHERE `orderStatus` != 4 AND `orderStatus` != -4 AND `orderStatus` != -1 GROUP BY `userId`";
                $result = $this->db->execCustom(['sql' => $sql]);
                $result = $this->db->fetchAssocThenFree($result);
                $userIds = $userAmount = [];
                $medalInv = new MedalInvestment();
                /* 本人投资的任务 */
                foreach ($result as $key => $res) {
                    $this->_logic($res['userId'], $res['amount'], $medal_arr[\Lib\Medal\MedalConfig::TASK_INVESTMENT]);
                    //$medalInv->setUserId($res['userId'])->setAmount($res['amount'])->setType(\Lib\Medal\MedalConfig::TASK_INVESTMENT)->logic();
                    $userIds[] = $res['userId'];
                    $userAmount[$res['userId']] = $res['amount'];
                }
                /* 好友投资的任务 */
                $where = implode(',', $userIds);
                $sql = "SELECT `userId`, `inviteByUser` FROM db_p2p.tb_user_{$i} WHERE `inviteByUser` != 0 AND `userId` IN ($where)";
                $userRes = $this->db->execCustom(['sql' => $sql]);
                $userRes = $this->db->fetchAssocThenFree($userRes);
                foreach ($userRes as $key => $user) {
                    if (isset($userAmount[$user['userId']])) {
                        //$this->_logic($user['inviteByUser'], $userAmount[$user['userId']], $medal_arr[\Lib\Medal\MedalConfig::TASK_FRIENDS_INV]);
                        $medalInv->setUserId($user['inviteByUser'])->setAmount($userAmount[$user['userId']])->setType(\Lib\Medal\MedalConfig::TASK_FRIENDS_INV)->logic();
                    }
                }
                /* 邀请好友的任务 */
                $sql = "SELECT COUNT(*) as c, `inviteByUser` FROM db_p2p.tb_user_{$i} WHERE `inviteByUser` != 0 GROUP BY `inviteByUser`";
                $friendsRes = $this->db->execCustom(['sql' => $sql]);
                $friendsRes = $this->db->fetchAssocThenFree($friendsRes);
                $medalFri = new MedalFriendsReg();
                foreach ($friendsRes as $key => $friends) {
                    //$this->_logic($friends['inviteByUser'], $friends['c'], $medal_arr[\Lib\Medal\MedalConfig::TASK_FRIENDS_REG]);
                    $medalFri->setUserId($friends['inviteByUser'])->setNum($friends['c'])->logic();
                }
                /* 红包使用金额的任务 */
                $sql = "SELECT SUM(`amount`) as amount, `userId` FROM db_p2p.tb_vouchers_{$i} WHERE `statusCode` = 1 GROUP BY `userId`";
                $redpacketRes = $this->db->execCustom(['sql' => $sql]);
                $redpacketRes = $this->db->fetchAssocThenFree($redpacketRes);
                //$medalRp = new MedalRedPacket();
                foreach ($redpacketRes as $key => $red) {
                    $this->_logic($red['userId'], $red['amount'], $medal_arr[\Lib\Medal\MedalConfig::TASK_USER_REDPACKET]);
                    //$medalRp->setUserId($red['userId'])->setNum($red['amount'])->logicPro(\Lib\Medal\MedalConfig::TASK_USER_REDPACKET);
                }
            }
            /* 分享出去的红包使用个数的任务 */
            $pids = $shareRedpackets = [];
            $sql = "SELECT count(*) as c, `pid` FROM db_p2p.tb_vouchers_0 WHERE `statusCode` = 1 AND `pid` != 0 GROUP BY `pid`";
            $redpacketRes = $this->db->execCustom(['sql' => $sql]);
            $redpacketRes = $this->db->fetchAssocThenFree($redpacketRes);
            foreach ($redpacketRes as $key => $red) {
                $pids[] = $red['pid'];
                $shareRedpackets[$red['pid']] += $red['c'];
            }
            $sql = "SELECT count(*) as c, `pid` FROM db_p2p.tb_vouchers_1 WHERE `statusCode` = 1 AND `pid` != 0 GROUP BY `pid`";
            $redpacketRes = $this->db->execCustom(['sql' => $sql]);
            $redpacketRes = $this->db->fetchAssocThenFree($redpacketRes);
            foreach ($redpacketRes as $key => $red) {
                $pids[] = $red['pid'];
                $shareRedpackets[$red['pid']] += $red['c'];
            }
            $where = implode(',', $pids);
            $pids = [];
            for ($i = 0; $i < 2; $i++) {
                $sql = "SELECT `userId`, `voucherId` FROM db_p2p.tb_vouchers_{$i} WHERE `voucherId` IN ({$where})";
                $redpacketRes = $this->db->execCustom(['sql' => $sql]);
                $redpacketRes = $this->db->fetchAssocThenFree($redpacketRes);
                foreach ($redpacketRes as $key => $red) {
                    if (isset($shareRedpackets[$red['voucherId']])) {
                        $pids[$red['userId']] += $shareRedpackets[$red['voucherId']];
                    }
                }
            }
            if ($pids) {
                foreach ($pids as $key => $p) {
                    $this->_logic($key, $p, $medal_arr[\Lib\Medal\MedalConfig::TASK_SHARE_REDPACKET]);
                }
            }
            /* 签到的任务 */
            $sql = "SELECT MAX(total) as total, userId FROM db_p2p.tb_checkin_0 GROUP BY `userId`";
            $checkinRes = $this->db->execCustom(['sql' => $sql]);
            $checkinRes = $this->db->fetchAssocThenFree($checkinRes);
            $checkin = [];
            foreach ($checkinRes as $key => $ck) {
                $checkin[$ck['userId']] = $ck['total'];
            }
            $sql = "SELECT MAX(total) as total, userId FROM db_p2p.tb_checkin_1 GROUP BY `userId`";
            $checkinRes = $this->db->execCustom(['sql' => $sql]);
            $checkinRes = $this->db->fetchAssocThenFree($checkinRes);
            foreach ($checkinRes as $key => $ck) {
                if (isset($checkin[$ck['userId']])) {
                    if ($ck['total'] < $checkin[$ck['userId']]) {
                        $this->_logic($ck['userId'], $checkin[$ck['userId']], $medal_arr[\Lib\Medal\MedalConfig::TASK_CHECKIN]);                        
                    } else {
                        $this->_logic($ck['userId'], $ck['total'], $medal_arr[\Lib\Medal\MedalConfig::TASK_CHECKIN]);
                    }
                    unset($checkin[$ck['userId']]);
                } else {
                    $this->_logic($ck['userId'], $ck['total'], $medal_arr[\Lib\Medal\MedalConfig::TASK_CHECKIN]);
                }
            }
            if (!empty($checkin)) {
                foreach ($checkin as $key => $ck) {
                    $this->_logic($key, $ck, $medal_arr[\Lib\Medal\MedalConfig::TASK_CHECKIN]);
                }
            }
        }
        return true;
    }

    private function _logic($userId, $num, $medal) {
        /* $sql = "SELECT * FROM db_p2p.tb_user_medal WHERE `userId` = {$userId}";
          $user = $this->db->execCustom(['sql' => $sql]);
          $user = $this->db->fetchAssocThenFree($user); */
        $userMedal = new \Lib\Medal\UserMedal();
        $user = $userMedal->setWhere(['userId' => $userId])->getRecord();
        $userMedals = json_decode($user['medals'], TRUE);
        $userMedals[$medal['key']]['id'] = $medal['id'];
        $userMedals[$medal['key']]['key'] = $medal['key'];
        $userMedals[$medal['key']]['progress'] = $num;
        $userMedals[$medal['key']]['getLevel'] = $this->medalObj->comparePro($medal, $userId, $userMedals[$medal['key']]['progress'], $userMedals[$medal['key']]['getLevel']);
        if (!empty($user)) {
            $userMedal->setWhere(['userId' => $userId])->updateRec(['medals' => json_encode($userMedals)]);
        } else {
            $userMedal->save(['userId' => $userId, 'coverMedal' => '', 'medals' => json_encode($userMedals)]);
        }
    }

}
