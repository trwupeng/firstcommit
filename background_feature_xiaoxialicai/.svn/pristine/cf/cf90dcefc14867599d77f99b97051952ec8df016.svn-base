<?php
namespace PrjCronds;
/**
 * 开标提醒
 * Class BidRemind
 * @package PrjCronds
 */
class BidRemind extends \Sooh\Base\Crond\Task
{
    public function init()
    {
        parent::init();
        $this->toBeContinue = true;
        $this->_secondsRunAgain = 240;//每分钟跑一次(必须偶数！！)
        $this->_iissStartAfter = 200;//每小时1分钟开始跑

        $this->ret = new \Sooh\Base\Crond\Ret();
    }

    public function free()
    {
        parent::free();
    }

    protected function onRun($dt)
    {
        if($this->_isManual){
            $m='manual';
        }else{
            $m='auto';
        }

        if($this->_counterCalled==1){
            error_log("[TRace]".__CLASS__.'# first by '.$m.' #'.$this->_counterCalled);
        }else{
            error_log("[TRace]".__CLASS__.'# continue by '.$m.' #'.$this->_counterCalled);
        }
        $this->lastMsg = $this->ret->toString();//要在运行日志中记录的信息
        $this->bidRemindStart();
        return true;
    }

    protected function bidRemindStart()
    {
        error_log('###[warning]扫描需要提醒的标的###');
        $dbWares = \Prj\Data\Wares::getCopy('');
        $db = $dbWares->db();
        $tb = $dbWares->tbname();
        //开标提醒时间
        $remindMinutes = \Prj\Data\Config::get('wares_remind_second') ? : 300;
        $time = \Sooh\Base\Time::getInstance();
        //需要扫描的最大计划上架时间-防止重复扫描
        $remindSecondsMax = date('YmdHis', $time->timestamp() + $remindMinutes + round($this->_secondsRunAgain / 2));
        //需要扫描的最小计划上架时间-防止重复扫描
        $remindSecondsMin = date('YmdHis', $time->timestamp() + $remindMinutes - round($this->_secondsRunAgain / 2));

        $where = [
            'statusCode' => \Prj\Consts\Wares::status_ready,
            'timeStartPlan]' => $remindSecondsMin,
            'timeStartPlan<' => $remindSecondsMax,
        ];

        $wares = $db->getRecords($tb, 'waresId,waresName,shelfId', $where);

        if (!empty($wares)) {
            $lastPage = null;
            $pagerId = 1;
            $pageSize = 1000;
            $pager = new \Sooh\DB\Pager($pageSize);
            $pager->page_size = $pageSize;

            //遍历标的ID
            foreach ($wares as $wv) {
                $_waresId = $wv['waresId'];
                $_waresName = $wv['waresName'];
                $_shelfId = $wv['shelfId'];
                $this->writeLog('foreachWaresId', 'this need remind, waresId:' . $_waresId);
                $map = [
                    'remindWares*' => '%"' . $_waresId . '":1%',
                ];

                $pageCount = \Prj\Data\User::loopGetRecordsCount($map);
                $userPagers = ceil($pageCount / $pageSize);

                while ($userPagers >= $pagerId) {
                    $pager->init(-1, $pagerId);
                    $pager->total = $pageCount;
                    if ($pagerId == 1) {
                        $ret = \Prj\Data\User::loopGetRecordsPage(['dtLast' => 'rsort'], ['where' => $map], $pager);
                    } else {
                        $ret = \Prj\Data\User::loopGetRecordsPage(['dtLast' => 'rsort'], $lastPage, $pager);
                    }
                    $lastPage = $ret['lastPage'];

                    $users = $ret['records'];

                    if (!empty($users)) {
                        foreach ($users as $user) {
                            $this->sendMsg($user['userId'], $_waresId, $_waresName, $_shelfId);
                            //更新user的remindWares为3
                            $dbUser = \Prj\Data\User::getCopy($user['userId']);
                            $dbUser->load();
                            if ($dbUser->exists()) {
                                $userRemindWares = $dbUser->getField('remindWares');
                                if (!empty($userRemindWares)) {
                                    if (is_string($userRemindWares) && json_decode($userRemindWares, true)) {
                                        $userRemindWares = json_decode($userRemindWares, true);
                                    }
                                }
                                $userRemindWares[$_waresId] = 3;
                                $dbUser->setField('remindWares', json_encode($userRemindWares));
                                $dbUser->update();
                            }
                        }
                    }

                    $pagerId++;
                }
            }
        }
        error_log('###[warning]扫描需要提醒的标的---结束###');
    }

    /**
     * 给用户推送一条zi自定义消息
     * @param string $userId 用户ID
     * @param string $waresId 标的ID
     * @param string $waresName 标的名称
     * @param string $shelfId 货架ID
     */
    protected function sendMsg($userId, $waresId, $waresName, $shelfId)
    {
        if (\Prj\ReadConf::checkPush($userId, 20)) {
            try {
                $message = \Prj\ReadConf::load(
                    [
                        'event'          => 'remind_by_push',
                        'sell_item_name' => $waresName,
                    ]
                );
                $content = $message['value'];
                $extras = [
                    'type' => 'xxremind',
                    'value' => 'bidRemind',
                    'shelfId' => $shelfId,
                    'waresId' => $waresId,
                ];
                $notify = [
                    'android'  => [$content, null, null, $extras],
                    'ios'      => [$content, null, null, null, $extras, null],
                    'winphone' => [$content, null, null, $extras]
                ];

                \Lib\Services\Message::getInstance()->push($userId, $notify);
                error_log('push startBid notify success, userId:' . $userId . '; classId:' . 20);
            } catch (\Exception $e) {
                error_log('push startBid notify error, userId:' . $userId . '; classId:' . 20);
            }
        }
    }

    /**
     * 统一打印日志
     * @param string $step 当前步骤
     * @param string $msg 附加消息
     */
    protected function writeLog($step = 'bidRemind', $msg = '')
    {
        $time = \Sooh\Base\Time::getInstance()->ymdhis();
        error_log("[LOG:{$step}:time{$time}]=>{$msg}");
    }
}