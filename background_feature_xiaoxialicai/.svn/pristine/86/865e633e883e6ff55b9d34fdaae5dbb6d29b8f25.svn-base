<?php
/**
 * Created by PhpStorm.
 * User: LTM <605415184@qq.com>
 * Date: 2015/10/26
 * Time: 11:25
 */

namespace Lib\Services;

/**
 * Class CheckinBook
 * @package Lib\Services
 */
class CheckinBook
{
    const errAccountNotExist = '帐号错误';//帐号不存在
    const errCheckinClosed   = '签到功能已经关闭';//签到功能已经关闭
    const errTodayDone       = '今天已经签到过了';//今天已经签到过了
    const errUnknown         = '位置错误';
    const msgCheckinDone     = '签到成功';
    const mMax               = 7;
    const checkSwitch        = 'true';//是否开启签到

    protected $bonusTypeRule = ['RedPacketForCheckin' => 1000];

    /**
     * @var \Sooh\Base\Rpc\Broker
     */
    protected $rpc = null;

    protected static $_instance = null;

    protected static $bonusPlan  = [];
    protected static $amountLast = [];
    protected        $bonusError = '';

    /**
     * @var int 当前签到次数
     */
    protected $currentCheckNum = 1;

    /**
     * User表中checkinBook字段
     * @var array $r
     */
    private $r;

    protected $today = 19770101;

    public static function getInstance($rpcOnNew = null)
    {
        if (self::$_instance == null) {
            self::$_instance      = new CheckinBook();
            self::$_instance->rpc = $rpcOnNew;
        }
        return self::$_instance;
    }

    /**
     * 签到
     * @param int    $userId    accountId
     * @param string $withBonus 1/0,是否显示签到数据
     * @return array
     * @throws \ErrorException
     * @throws \Sooh\Base\ErrException
     */
    public function doCheckin($userId, $withBonus = null)
    {
        if ($this->rpc != null) {
            return $this->rpc->initArgs(['userId' => $userId, 'withBonus' => $withBonus])->send(__FUNCTION__);
        } else {
            if (self::checkSwitch != 'true') {
                throw new \Sooh\Base\ErrException(self::errCheckinClosed);
            }

            $user = \Prj\Data\User::getCopy($userId);
            $user->load();
            if ($user->exists() === false) {
                throw new \Sooh\Base\ErrException(self::errAccountNotExist);
            }

            $dbCheckin = \Prj\Data\Checkin::getCopy($userId);
            $dbCheckin->load();
            if ($dbCheckin->exists()) {
                throw new \Sooh\Base\ErrException(\Prj\Data\Checkin::errTodayDone);
            }

            $this->decode($user->getField('checkinBook', true), true);

            if ($this->r['ymd'] == $this->today) {
                throw new \Sooh\Base\ErrException(self::errTodayDone);
            }

            $errOnUpdate = $this->giveBonus($user, $withBonus);
            if (!empty($this->bonusError)) {
                \Sooh\Base\Log\Data::getInstance()->error('[GiveItem to user ' . $user->userId . ' Failed] on checkin with error:' . $errOnUpdate);
                \Sooh\Base\Log\Data::getInstance()->ret = "give item failed with error:" . $errOnUpdate;
                foreach ($errOnUpdate as $_ignore_) {
                    $_ignore_->give_rollback($user);
                }
                $user->unlock();
                throw new \Sooh\Base\ErrException(\Sooh\Base\ErrException::msgServerBusy, 500);
            } else {
                try {
                    if (count($this->r['bonusGot']) >= self::mMax) {
                        $this->r['bonusGot'] = [];
                    }
                    $bonusList                         = $this->analysisBonus();
                    $this->r['bonusGot'][$this->today] = $bonusList;

                    \Prj\Data\Checkin::addRecord($userId, $bonusList, count($this->r['bonusGot']));

                    $this->r['ymd'] = $this->today;
                    $user->setField('checkinBook', $this->r);
                    //周常任务
                    try {
                        $weekActiveBonus = \Prj\ActivePoints\Checkin::getCopy($userId)->addNum(1)->updUser();
                        $user->update();
                    } catch (\Exception $e) {
                        var_log($e->getMessage(), 'weekActiveBonus for ChechinBook error');
                    }
                    //勋章签到
                    try {
                        $medalCheckin = new \Lib\Medal\MedalCheckin();
                        $medalCheckin->setUserId($userId)->logic(); //签到勋章任务
                    } catch (Exception $ex) {
                        var_log($ex->getMessage(), 'medal for ChechinBook error');
                    }
                    if (!empty($weekActiveBonus)) {
                        \Lib\Services\Push::getInstance()->push('all', $userId, null,
                            json_encode($weekActiveBonus));
                    }
                    $i = 0;
                    foreach (self::$bonusPlan as $key => $val) {
                        for ($z = 0; $z < $val; $z++) {
                            if (strpos($key, 'RedPacket') !== false) {
                                //send msg
                                //								try {
                                //									\Prj\ReadConf::run(
                                //										['event' => 'red_packet', 'num_packet' => '1', 'num_money' => $errOnUpdate[$i]->getAmountLast()[0] / 100, 'num_deadline' => 30],
                                //										['phone' => $user->getField('phone'), 'userId' => $userId, 'msgTitle' => $errOnUpdate[$i]::$voucherTitle, ]
                                //									);
                                //								} catch(\Exception $e) {
                                //									var_log($e->getMessage(), 'Send CheckinBook Message Error');
                                //								}
                            }
                        }
                        $i++;
                    }


                    foreach ($errOnUpdate as $_ignore_) {
                        $_ignore_->give_confirm($user);
                    }

                    \Sooh\Base\Log\Data::getInstance()->ret = "checkin_success";
                    return $this->allDone(self::msgCheckinDone, $withBonus, $userId);
                } catch (\Exception $e) {
                    foreach ($errOnUpdate as $_ignore_) {
                        $_ignore_->give_rollback($user);
                    }
                    $user->unlock();
                    \Sooh\Base\Log\Data::getInstance()->error('[GiveItemFailed] on checkin uid:' . $user->userId . ' ' . $e->getMessage());
                    \Sooh\Base\Log\Data::getInstance()->ret = "give item failed:" . $e->getMessage();
                    throw new \Sooh\Base\ErrException($e->getMessage(), $e->getCode());
                }
            }
        }
    }

    /**
     * 获取当日情况： [是否已经签到：checked=>array(1,3,4,9,), todaychked=0|1,ymd=>20150501,
     * bonusList=>array(array(item1=>num1),...30=>array(item1=>num1),)] 注意日期列表都是下标0开始的数组
     * @param boolean        $withBonus       返回里是否带奖励物品列表
     * @param \Prj\Data\User $userOrAccountId accountId 或 \Prj\Data\User
     * @return array
     */
    public function doGetTodayStatus($withBonus, $userOrAccountId)
    {
        if ($this->rpc !== null) {
            return $this->rpc->initArgs(array(
                'withBonus'       => $withBonus,
                'userOrAccountId' => $userOrAccountId
            ))->send(__FUNCTION__);
        } else {
            if (is_scalar($userOrAccountId)) {
                $userOrAccountId = \Prj\Data\User::getCopy($userOrAccountId);
            }
            $userOrAccountId->load();
            $this->decode($userOrAccountId->getField('checkinBook', true), false);

            return $this->allDone('', $withBonus);
        }
    }

    /**
     * 发放奖励
     * @param \Prj\Data\User $user      用户类
     * @param string         $withBonus 是否回传奖励
     * @return array|string
     * @throws \Sooh\Base\ErrException
     */
    protected function giveBonus($user, $withBonus)
    {
        $bonusType       = $this->getBonusType();
        $bonusPlan       = [$bonusType => 1, 'ShopPointForCheckin' => 1];
        self::$bonusPlan = $bonusPlan;
        if (\Sooh\Base\Tests\Bomb::blowup('CheckinBook_GiveBinus_Return_BonusPlan', false)) {
            $this->_view->assign('data', $bonusPlan);
            return $this->returnOK();
        }

        //各类检查通过，锁定用户
        \Prj\Misc\OrdersVar::$introForUser  = "签到奖励";
        \Prj\Misc\OrdersVar::$introForCoder = 'actives.checkin';
        if (false === $user->lock('chkinBonus:' . http_build_query($bonusPlan))) {
            \Sooh\Base\Log\Data::getInstance()->ret = "lock user for checkin failed";
            \Sooh\Base\Log\Data::getInstance()->error('[LockFailed user:' . $user->userId . '] user-table on checkin' . \Sooh\DB\Broker::lastCmd());
            throw new \Sooh\Base\ErrException(\Sooh\Base\ErrException::msgServerBusy, 500);
        }
        $gived = [];
        foreach ($bonusPlan as $key => $value) {
            $itemClass = '\\Prj\\Items\\' . $key;
            if (!class_exists($itemClass)) {
                throw new \Sooh\Base\ErrException(self::errUnknown, 500);
            }

            /**
             * @var \Prj\Items\RedPacketForCheckin
             */
            $_ignore_               = new $itemClass;
            $errmsg                 = $_ignore_->give_prepare($user, 1, $this->currentCheckNum);
            self::$amountLast[$key] = $_ignore_->getAmountLast();

            $gived[] = $_ignore_;
            if (!empty($errmsg)) {
                $this->bonusError = $errmsg;
                break;
            }
        }
        return $gived;
    }

    /**
     * 解析奖励
     * @return array ['bonus1' => 123, 'bonus2' => 555]
     */
    protected function analysisBonus()
    {
        $i      = 0;
        $result = [];
        foreach (self::$bonusPlan as $key => $val) {
            for ($z = 0; $z < $val; $z++) {
                if (strpos($key, 'RedPacket') !== false) {
                    //					$result[$key] = sprintf('%.2f', self::$amountLast[$key][$z] / 100);
                    $result[$key] = self::$amountLast[$key][$z];
                } else {
                    $result[$key] = self::$amountLast[$key][$z];
                }
                $i++;
            }
        }
        return $result;
    }

    /**
     * 设置签到记录基础数据
     * @param mixed $var     数据库中的签到记录
     * @param bool  $checkin checkin
     *                       $param $checkin 是否签到
     */
    protected function decode($var, $checkin = false)
    {
        if (is_string($var)) {
            $this->r = json_decode($var, true);
        } elseif (is_array($var)) {
            $this->r = $var;
        } else {
            $this->r = array();
        }

        $this->today = \Sooh\Base\Time::getInstance()->YmdFull;
        $ym          = floor($this->today / 100);

        $this->currentCheckNum = count($this->r['bonusGot']) + 1;

        //达到本轮最大签到次数，则重置 $this->r
        if (count($this->r['bonusGot']) >= self::mMax && $checkin) {
            $this->r['ymd']        = $ym * 100;
            $this->r['bonusGot']   = array();
            $this->currentCheckNum = 1;
        }
    }

    /**
     * 签到成功执行此函数
     * @param string $msg       文字描述
     * @param string $withBonus 奖励列表
     * @return array
     */
    protected function allDone($msg, $withBonus, $userId = '')
    {
        $ret = array('ymd' => $this->today);
        $i   = 0;

        if (empty($this->r['bonusGot'])) {
            $ret['checked'] = [];
        } else {
            foreach ($this->r['bonusGot'] as $key => $val) {
                $ret['checked'][$i]['ymd']     = $key;
                $ret['checked'][$i++]['bonus'] = $val;
            }
        }

        $ret['todaychked'] = $this->r['ymd'] == $this->today ? 1 : 0;
        if ($withBonus) {
            for ($i = 0; $i < 6; $i++) {
                $ret['bonusList'][] = ['VoucherSmall' => 1];
            }
            $ret['bonusList'][] = ['VoucherBig' => 1];
        }

        if ($userId) {
            $ret['checkFew'] = $this->getCheckFew($userId);
        }

        $r = array('data' => $ret);

        $r['code'] = 200;
        $r['msg']  = $msg;

        return $r;
    }

    /**
     * 获取连续签到次数
     * @param $userId
     * @return int
     */
    private function getCheckFew($userId) {
        $fews = \Prj\Data\Checkin::loopFindRecords(['userId' => $userId]);
        $count = 1;
        if ($fews) {
            $ret = [];
            foreach ($fews as $v) {
                $ret[] = '20' . $v['ymd'];//补足完整年份写法2016
            }

            rsort($ret);

            for ($i = 0; $i < count($ret); $i++) {
                if (isset($ret[$i + 1]) && $ret[$i] == date('Ymd', strtotime('+1 day', strtotime($ret[$i + 1])))) {
                    $count++;
                } else {
                    break;
                }
            }
        }
        return $count;
    }

    /**
     * 返回奖励种类
     * @return mixed
     */
    private function getBonusType()
    {
        return ucfirst($this->getRand($this->bonusTypeRule));
    }

    /**
     * 获取一个随机数
     * @param array $rule 随机规则:['1_100'=>99,'100_900'=>891,'900_1000'=>10]
     * @return int
     */
    private function getRand($rule)
    {
        $result  = '';
        $ruleSum = array_sum($rule);

        //概率数组循环
        foreach ($rule as $key => $val) {
            $randNum = mt_rand(1, $ruleSum);
            if ($randNum <= $val) {
                $result = $key;
                break;
            } else {
                $ruleSum -= $val;
            }
        }
        unset ($rule);

        $loc = strpos($result, '_');
        if ($loc) {
            $result = mt_rand(substr($result, 0, $loc), substr($result, $loc + 1));
        }
        return $result;
    }
}
