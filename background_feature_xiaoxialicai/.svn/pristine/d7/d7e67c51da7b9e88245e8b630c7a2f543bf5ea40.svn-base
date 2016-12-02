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
        $this->_iissStartAfter = 3000;
        $this->toBeContinue = true;
        $this->ret = new \Sooh\Base\Crond\Ret();
        $this->db = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_p2p);
    }

    public function free() {
        parent::free();
    }

    protected function onRun($dt) {
        error_log('###[warning]周常活动脚本运行,当前时间:'.$dt->hour.'###');
        if ($dt->hour == 23) {
            error_log('###[warning]周常活动脚本运行,符合23点要求###');
            $this->time = date('Ymd', strtotime($dt->YmdFull));
            $week = \Lib\Misc\ActivePoints::weekIndex(intval($this->time));
            for ($i = 0; $i < 2; $i++) {
                $sql = "SELECT COUNT(*) as cou FROM db_p2p.tb_user_{$i}";
                $result = $this->db->execCustom(['sql' => $sql]);
                $userCountRes = $this->db->fetchAssocThenFree($result);
                if (isset($userCountRes[0]['cou']) && $userCountRes[0]['cou']) {
                    $userCountRes = $userCountRes[0]['cou'];
                    error_log('###[warning]数量：'.$userCountRes.'###');
                    $k = 0;
                    while ($k <= $userCountRes) {
                        $kk = 1000;
                        $sql = "SELECT userId, ymdReg, dtLast, checkinBook, inviteByUser, ap_UsedShareVoucher, ap_Checkin, exchangecodegrp, ap_fetched, ap_BuyAmount, ap_BuyTimes, ap_Invited, ap_InvitedInvest FROM db_p2p.tb_user_{$i} LIMIT {$k}, {$kk}";
                        $result = $this->db->execCustom(['sql' => $sql]);
                        $userRes = $this->db->fetchAssocThenFree($result);
                        $k+=$kk;
                        $this->saveInterim($userRes, $week);
                    }
                }
            }
            $this->toBeContinue = FALSE;
        }
        return true;
    }

    private function saveInterim($userRes, $week) {
        foreach ($userRes as $key => $u) {
            $userId = $u['userId'];
            $user = \Prj\Data\User::getCopy($userId);
            $user->load();
            $fetched = \Prj\ActivePoints\APFetched::getByUser($userId);
            $items = \Prj\ActivePoints\APFetched::getAllClasses();
            $done = [];
            foreach ($items as $item) {
                $done[$item] = \Lib\Misc\ActivePoints::getCopy($userId, '\\Prj\\ActivePoints\\' . $item)->getNum();
            }
            $fetchedThisWeek = [];
            $tmp = $fetched->getFetched();
            foreach ($tmp as $score => $items) {
                $itemname = key($items);
                $itemname = explode("\\", $itemname);
                $fetchedThisWeek[] = [$score, array_pop($itemname), current($items)];
            }
            $totalScore = $fetched->getTotalScore();
            
            if ($totalScore) {
                $times = [substr($u['ap_BuyAmount'], 0, 8), substr($u['ap_UsedShareVoucher'], 0, 8), substr($u['ap_Checkin'], 0, 8), substr($u['ap_Invited'], 0, 8), substr($u['ap_InvitedInvest'], 0, 8)];
                sort($times);
                $rpt['userId'] = $u['userId'];
                $rpt['taskNumber'] = $week;
                $rpt['ymdFirst'] = (isset($times[0]) && $times[0] ? $times[0] : $this->time);
                $rpt['investmentScore'] = $fetched->eachScore['BuyAmount'];
                $rpt['investmentLast'] = substr($u['ap_BuyAmount'], 0, 8);
                $rpt['shareVoucherScore'] = $fetched->eachScore['UsedShareVoucher'];
                $rpt['shareVoucherLast'] = substr($u['ap_UsedShareVoucher'], 0, 8);
                $rpt['checkinScore'] = $fetched->eachScore['Checkin'];
                $rpt['checkinLast'] = substr($u['ap_Checkin'], 0, 8);
                $rpt['inviteScore'] = $fetched->eachScore['Invited'];
                $rpt['inviteLast'] = substr($u['ap_Invited'], 0, 8);
                $rpt['friendsInvestmentScore'] = $fetched->eachScore['InvitedInvest'];
                $rpt['friendsInvestmentLast'] = substr($u['ap_InvitedInvest'], 0, 8);
                $rpt['rewards'] = json_encode($fetchedThisWeek);
                $rpt['totalScore'] = $totalScore;
                $rs = $this->db->getOne('db_p2prpt.tb_weekactivity_final', 'ymdFirst', ['userId' => $rpt['userId'], 'taskNumber' => $rpt['taskNumber']]);
                error_log('###[warning]赏金报表数据：'.$rs.'###');
                if ($rs) {
                    unset($rpt['ymdFirst']);
                    $this->db->updRecords('db_p2prpt.tb_weekactivity_final', $rpt, ['userId' => $rpt['userId'], 'taskNumber' => $rpt['taskNumber']]);
                } else {
                    $this->db->addRecord('db_p2prpt.tb_weekactivity_final', $rpt);
                }
            }
        }
    }

}
