<?php

namespace Lib\Medal;

/**
 * Description of Medal
 *
 * @author wu.chen
 */
class Medal extends \Lib\Medal\MedalBase {

    protected $userId;
    protected $num;

    public function setNum($num) {
        $this->num = $num;
        return $this;
    }

    public function __construct() {
        parent::__construct();
        $this->tbname = 'db_p2p.tb_medal';
        $this->where = ['status' => 1];
    }

    public function setUserId($userId) {
        $this->userId = $userId;
        return $this;
    }

    public function getAllBySql() {
        $sql = "SELECT * FROM {$this->tbname} WHERE {$this->where}";
        $result = $this->db->execCustom(['sql' => $sql]);
        $result = $this->db->fetchAssocThenFree($result);
        return $result;
    }

    /**
     * 获得勋章和奖励
     * @param string $userId 用户id
     * @param string $increment 当前进度增量
     * @param string $type 勋章key
     */
    protected function setMedal($userId, $increment, $type) {
        if ($userId) {
            $this->tbname = 'db_p2p.tb_medal';
            $medals = $this->setWhere(['`key`' => $type])->getRecords();
            $this->tbname = 'db_p2p.tb_user_medal';
            $user = $this->setWhere(['userId' => $userId])->getRecord();
            $userMedals = json_decode($user['medals'], TRUE);
            foreach ($medals as $key => $medal) {
                if (isset($userMedals[$medal['key']])) {
                    $userMedals[$medal['key']]['progress']+=$increment;
                } else {
                    $userMedals[$medal['key']]['id'] = $medal['id'];
                    $userMedals[$medal['key']]['key'] = $medal['key'];
                    $userMedals[$medal['key']]['progress'] = $increment;
                }
                $excludeLevel = $uMedals = [];
                /* 取得已获得勋章等级 */
                if (isset($userMedals[$medal['key']]['getLevel']) && !empty($userMedals[$medal['key']]['getLevel'])) {
                    $excludeLevel = array_keys($userMedals[$medal['key']]['getLevel']);
                    $uMedals = $userMedals[$medal['key']]['getLevel'];
                }
                $userMedals[$medal['key']]['getLevel'] = $this->compare($medal, $userId, $userMedals[$medal['key']]['progress'], $excludeLevel, $uMedals);
            }
            if (!empty($user)) {
                $this->setWhere(['userId' => $userId])->updateRec(['medals' => json_encode($userMedals)]);
            } else {
                $this->save(['userId' => $userId, 'coverMedal' => '', 'medals' => json_encode($userMedals)]);
            }
        }
    }

    /**
     * 判断是否满足勋章任务等级
     * @param array $medal 勋章数组
     * @param string $userId 用户id
     * @param int $progress 任务进度
     * @param array $excludeLevel 排除的任务等级
     * @param array $userMedals 已获得的勋章
     */
    protected function compare($medal, $userId, $progress, $excludeLevel, $userMedals) {
        $taskLevels = explode(',', $medal['taskLevel']);
        if (!empty($taskLevels)) {
            foreach ($taskLevels as $k => $taskLevel) {
                $tLevel = explode(':', $taskLevel);
                if (!in_array($tLevel[0], $excludeLevel)) {      //判断当前的等级是否已领取
                    if ($progress >= $tLevel[1]) {       //判断当前进度是否大于等于任务要求
                        $userMedals[$tLevel[0]]['getTime'] = date('YmdHis');
                        $reg = '/' . $tLevel[0] . ':\d+\_\d+/';
                        preg_match($reg, $medal['reward'], $r);
                        $reward = isset($r[0]) && $r[0] ? $r[0] : FALSE;
                        $r = explode(':', $reward);
                        $reward = isset($r[1]) && $r[1] ? $r[1] : FALSE;
                        if ($reward) {
                            $userMedals[$tLevel[0]]['getReward'] = $this->reward($medal, $userId, $reward, $r[0], $tLevel[1]);
                        }
                    }
                }
            }
        }
        return $userMedals;
    }

    /**
     * 发放奖励
     * @param array $medal 勋章数组
     * @param int $userId 用户ID
     * @param string $reward 发放的奖励数据 1_500表示发放1个5块的红包
     * @param int @level 勋章等级
     */
    protected function reward($medal, $userId, $reward, $level, $taskLevel) {
        try {
            $rewardName = $medal['rewardName'];
            $reward = explode('_', $reward);
            if (!empty($reward) && !empty($reward[0]) && isset($reward[1]) && !empty($reward[1])) {
                if ($rewardName == MedalConfig::REWARD_REDPACKET) {
                    //发放奖励红包
                    $amount = $reward[1];
                    $time = time();
                    for ($i = 0; $i < $reward[0]; $i++) {
                        \Prj\Misc\OrdersVar::$introForUser = "勋章任务奖励红包";
                        \Prj\Misc\OrdersVar::$introForCoder = 'medal_' . $medal['key'] . '_' . $level;
                        $task = $medal['task'] . $this->getTask($taskLevel, $medal['taskUnit']);
                        $desc = "完成勋章“{$medal['name']}”{$level}级任务--{$task}获得的奖励";
                        //发放红包
                        if (\Prj\Data\Vouchers::addVoucher($userId, $amount, \Prj\Consts\Voucher::type_real, ['end' => ($time + 86400 * 2)], $desc)) {
                            \Prj\Message\Message::run([
                                'event' => 'medal_get',
                                'medal_name' => $medal['name'],
                                'num_packet' => 1,
                                'medal_level' => $level,
                                'medal_task' => $task,
                                'medal_money' => sprintf('%.2f', $amount / 100),
                                    ], ['userId' => $userId]
                            );      //通知/站内信
                        }
                    }
                    return date('YmdHis', $time);
                }
            }
            return FALSE;
        } catch (\Exception $e) {
            return $this->returnError($e->getMessage(), $e->getCode());
        }
    }

    /**
     * 判断是否满足勋章任务等级
     * @param array $medal 勋章数组
     * @param string $userId 用户id
     * @param int $progress 任务进度
     * @param array $excludeLevel 排除的任务等级
     * @param array $userMedals 已获得的勋章
     */
    public function comparePro($medal, $userId, $progress, $userMedals) {
        $taskLevels = explode(',', $medal['taskLevel']);
        if (!empty($taskLevels)) {
            foreach ($taskLevels as $k => $taskLevel) {
                $tLevel = explode(':', $taskLevel);
                if ($progress >= $tLevel[1]) {       //判断当前进度是否大于等于任务要求
                    $userMedals[$tLevel[0]]['getTime'] = date('YmdHis');
                    $reg = '/' . $tLevel[0] . ':\d+\_\d+/';
                    preg_match($reg, $medal['reward'], $r);
                    $reward = isset($r[0]) && $r[0] ? $r[0] : FALSE;
                    $r = explode(':', $reward);
                    $reward = isset($r[1]) && $r[1] ? $r[1] : FALSE;
                    if ($reward) {
                        $userMedals[$tLevel[0]]['getReward'] = $this->reward($medal, $userId, $reward, $r[0], $tLevel[1]);
                    }
                }
            }
        }
        return $userMedals;
    }

    protected function getTask($num, $unit) {
        if ($unit == 'xiaoshuyuan') {
            $num = sprintf("%.2f", $num / 100);
        }
        $unit = MedalConfig::getUnit($unit);
        return $num . $unit;
    }

}
