<?php

namespace PrjCronds;

/**
 * php /var/www/licai_php/run/crond.php "__=crond/run&task=RptWeekAct.CrondWeekActivity&ymdh=2342"
 * 周赏金任务数据脚本
 *
 * @author wu.chen
 */
class CrondWeekActivity extends \Sooh\Base\Crond\Task {

    private $time;

    public function init() {
        parent::init();
        $this->_isManual = true;
        $this->_iissStartAfter = 100;
        $this->toBeContinue = true;
        $this->ret = new \Sooh\Base\Crond\Ret();
        $this->db = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_p2p);
    }

    public function free() {
        parent::free();
    }

    protected function onRun($dt) {
        var_log('###[warning]周常活动脚本运行,当前时间:' . $dt->hour . '###');
        if ($dt->hour == 23) {
            $this->time = date('Ymd', strtotime($dt->YmdFull));
            var_log('###[warning]赏金报表日期：' . $this->time . '###');
            $week = \Lib\Misc\ActivePoints::weekIndex(intval($this->time));
            $count = isset($GLOBALS['CONF']['dbByObj']['user'][0]) ? $GLOBALS['CONF']['dbByObj']['user'][0] : 2;
            for ($i = 0; $i < $count; $i++) {
                $sql = "SELECT COUNT(*) as cou FROM db_p2p.tb_user_{$i}";
                error_log('###[warning]赏金报表日期sql：' . $sql . '###');
                $result = $this->db->execCustom(['sql' => $sql]);
                $userCountRes = $this->db->fetchAssocThenFree($result);
                if (isset($userCountRes[0]['cou']) && $userCountRes[0]['cou']) {
                    $userCountRes = $userCountRes[0]['cou'];
                    error_log('###[warning]数量：' . $userCountRes . '###');
                    $k = 0;
                    while ($k <= $userCountRes) {
                        $kk = 1000;
                        $sql = "SELECT userId, ymdReg, dtLast, checkinBook, inviteByUser, ap_UsedShareVoucher, ap_Checkin, exchangecodegrp, ap_fetched, ap_BuyAmount, ap_BuyTimes, ap_Invited, ap_InvitedInvest FROM db_p2p.tb_user_{$i} LIMIT {$k}, {$kk}";
                        var_log('###[warning]赏金报表日期sql：' . $sql . '###');
                        $result = $this->db->execCustom(['sql' => $sql]);
                        $result = $this->db->fetchAssocThenFree($result);
                        $k+=$kk;
                        $this->saveInterim($result, $week, $i);
                        \Prj\Data\User::freeAll();
                    }
                }
            }
            var_log('####赏金任务脚本完成####');
            $this->toBeContinue = FALSE;
        }
        return true;
    }

    private function saveInterim($userRes, $week, $i) {
        foreach ($userRes as $key => $u) {
            $userId = $u['userId'];
            /* $user = \Prj\Data\User::getCopy($userId);
              $user->load(); */
            //$fetched = \Prj\ActivePoints\APFetched::getByUser($userId);
            //$items = \Prj\ActivePoints\APFetched::getAllClasses();
            //$done = [];
            /* foreach ($items as $item) {
              $done[$item] = \Lib\Misc\ActivePoints::getCopy($userId, '\\Prj\\ActivePoints\\' . $item)->getNum();
              } */
            $fetchedThisWeek = [];
            $tmp = $this->getFetched($u['ap_fetched'], $week); //$fetched->getFetched();
            foreach ($tmp as $score => $items) {
                $itemname = key($items);
                $itemname = explode("\\", $itemname);
                $fetchedThisWeek[] = [$score, array_pop($itemname), current($items)];
            }
            $score = $this->getScores($u); //$fetched->getTotalScore();

            if (isset($score['totalScore']) && $score['totalScore']) {
                $times = $rpt = [];
                $shareVoucherLast = ($score['UsedShareVoucher'] ? $this->time : null);
                $investmentLast = ($score['BuyAmount'] ? $this->time : null);
                $inviteLast = ($score['Invited'] ? $this->time : null);
                $friendsInvestmentLast = ($score['InvitedInvest'] ? $this->time : null);
                $rpt['userId'] = $u['userId'];
                $rpt['taskNumber'] = $week;
                $rpt['investmentScore'] = $score['BuyAmount'];
                $rpt['investmentLast'] = $investmentLast;
                $rpt['shareVoucherScore'] = $score['UsedShareVoucher'];
                $rpt['shareVoucherLast'] = $shareVoucherLast;
                $rpt['checkinScore'] = $score['Checkin'];
                $checkin = null;
                if ($score['Checkin']) {
                    $checkin = $this->db->getOne("db_p2p.tb_checkin_{$i}", 'MAX(ymd)', ['userId' => $rpt['userId']]);
                }
                $rpt['checkinLast'] = $checkin;
                $rpt['inviteScore'] = $score['Invited'];
                $rpt['inviteLast'] = $inviteLast;
                $rpt['friendsInvestmentScore'] = $score['InvitedInvest'];
                $rpt['friendsInvestmentLast'] = $friendsInvestmentLast;
                $rpt['rewards'] = json_encode($fetchedThisWeek);
                $rpt['totalScore'] = $score['totalScore'];
                if ($investmentLast) {
                    $times[] = $investmentLast;
                }
                if ($shareVoucherLast) {
                    $times[] = $shareVoucherLast;
                }
                if ($checkin) {
                    $times[] = $checkin;
                }
                if ($inviteLast) {
                    $times[] = $inviteLast;
                }
                if ($friendsInvestmentLast) {
                    $times[] = $friendsInvestmentLast;
                }
                sort($times);
                $rpt['ymdFirst'] = (isset($times[0]) && $times[0] ? $times[0] : $this->time);
                $rs = $this->db->getOne('db_p2prpt.tb_weekactivity_final', 'ymdFirst', ['userId' => $rpt['userId'], 'taskNumber' => $rpt['taskNumber']]);
                if ($rs) {
                    unset($rpt['ymdFirst']);
                    $this->db->updRecords('db_p2prpt.tb_weekactivity_final', $rpt, ['userId' => $rpt['userId'], 'taskNumber' => $rpt['taskNumber']]);
                } else {
                    $this->db->addRecord('db_p2prpt.tb_weekactivity_final', $rpt);
                }
                unset($score);
                unset($rpt);
                unset($times);
                unset($checkin);
            }
        }
    }

    private function getFetched($apFetched, $week) {
        $arr = json_decode($apFetched, TRUE);
        if (isset($arr['_']) && $arr['_'] == $week) {
            return $arr['got'];
        }
        return [];
    }
    
    private function getScores($user) {
        $list = \Prj\ActivePoints\APFetched::getAllClasses();
        $activePoints = new \Lib\Misc\ActivePoints();
        $apFetched = new \Prj\ActivePoints\APFetched();
        $arr = [];
        foreach($list as $evt) {
            $activePoints->parseField($user["ap_{$evt}"]);
            $arr[$evt] = $apFetched->getScore($evt, $activePoints->getNum());
            $arr['totalScore'] += $arr[$evt];
        }
        return $arr;
    }

}
