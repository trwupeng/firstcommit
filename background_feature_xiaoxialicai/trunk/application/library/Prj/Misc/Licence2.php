<?php

namespace Prj\Misc;

/**
 * 各类许可协议相关接口
 *
 * @author simon.wang
 */
class Licence2 {

    public static function version($name = 'invest') {
        $obj = \Prj\Data\Agreement2::getCopy(['verName' => $name, 'verId' => 0]);
        $r = $obj->db()->getOne($obj->tbname(), 'max(verId)', ['verName' => $name, 'status' => 1]) - 0;
        return $r;
    }

    public static function register($ver = null) {
        return $tmp = self::getTpl(__FUNCTION__, $ver);
    }

    public static function diya($ver = null) {
        return $tmp = self::getTpl(__FUNCTION__, $ver);
    }

    public static function jiekuan($ver = null) {
        return $tmp = self::getTpl(__FUNCTION__, $ver);
    }

    public static function zhaiquan($ver = null) {
        return $tmp = self::getTpl(__FUNCTION__, $ver);
    }

    public static function binding($ver = null) {
        return $tmp = self::getTpl(__FUNCTION__, $ver);
        ;
    }

    public static function invest($arr, $ver = null) {
        $find = [];
        $rep = [];
        foreach ($arr as $k => $v) {
            $find[] = '{$' . $k . '}';
            $rep[] = $v;
        }
        $tmp = self::getTpl(__FUNCTION__, $ver);
        return str_replace($find, $rep, $tmp);
    }

    public static function recharges($arr, $ver = null) {

        $find = [];
        $rep = [];
        foreach ($arr as $k => $v) {
            $find[] = '{$' . $k . '}';
            $rep[] = $v;
        }
        $tmp = self::getTpl(__FUNCTION__, $ver);
        return str_replace($find, $rep, $tmp);
    }

    protected static function getTpl($name, $ver) {
        if ($ver == 1) {
            $obj = \Prj\Data\Agreement2::getCopy(['verName' => $name, 'verId' => $ver]);
            $r = $obj->db()->getOne($obj->tbname(), 'content', ['verName' => $name, 'status' => 1, 'verId' => $ver], 'rsort verId');
        } else {
            $obj = \Prj\Data\Agreement2::getCopy(['verName' => $name, 'verId' => 0]);
            $r = $obj->db()->getOne($obj->tbname(), 'content', ['verName' => $name, 'status' => 1], 'rsort verId');
        }
        return $r;
    }

    public static function getInvest($type, $amount, $ordersId, $waresId) {
        $arr = ['ymdft' => '', 'ymdsd' => '', 'ymdtd' => '', 'userName' => '', 'userPhone' => '', 'userIdCard' => '', 'userId' => '', 'borrowerId' => '',
            'borrowerIdCard' => '', 'borrowerName' => '', 'amount' => $amount, 'amount1' => '', 'amount2' => '', 'waresName' => '', 'waresId' => '', 'yieldStatic' => '', 'timeDur' => ''];
        if ($waresId) {
            $wares = \Prj\Data\Wares::getCopy($waresId);
            $wares->load();
            if ($wares->exists()) {
                $borrowerId = $wares->getField('borrowerId');
                if ($borrowerId) {
                    $user = \Prj\Data\User::getCopy($borrowerId);
                    $user->load();
                    if ($user->exists()) {
                        $arr['borrowerName'] = $user->getField('nickname');
                    }
                }
                $arr['timeDur'] = $wares->getField('deadLine') . $wares->getField('dlUnit');
                $introDisplay = $wares->getField('introDisplay');
                $arr['borrowerIdCard'] = $introDisplay['b']['idCard'];
                $arr['waresName'] = $wares->getField('waresName');
                $arr['borrowerId'] = $borrowerId;
                $arr['yieldStatic'] = $wares->getField('yieldStatic');
                $arr['yieldStatic']*=100;
                $arr['timeDur'] = $wares->getField('deadLine') . $wares->getField('dlUnit');
                $amount/=100;

                $interestTotal1 = \Prj\Tool\Func::num_to_rmb($amount);
                $arr['amount1'] = $interestTotal1;
                $arr['amount2'] = $amount;


                $returnNext = $wares->getField('payYmd');
                if (!empty($returnNext)) {
                    $time = time();
                    $returnNext = strtotime($returnNext);
                    if ($time < $returnNext) {
                        $arr['ymdft'] = '合同签署之日';
                    } else {
                        $arr['ymdft'] = date('Y-m-d', $returnNext);
                    }
                } else {
                    $arr['ymdft'] = '合同签署之日';
                }
            }
        }
        if ($ordersId) {
            $investment = \Prj\Data\Investment::getCopy($ordersId);
            $investment->load();
            if ($investment->exists()) {
                $orderTime = $investment->getField('orderTime');
                $orderTime = \Prj\Misc\View::fmtYmd($orderTime);
                $arr['ymdsd'] = $orderTime;
                $arr['ymdtd'] = $orderTime;
                $licence = $investment->getField('licence');
                $ver = $licence[0]['ver'];

                $userId = $investment->getField('userId');
                if (!empty($userId)) {
                    $user = \Prj\Data\User::getCopy($userId);
                    $user->load();
                    if ($user->exists()) {
                        $arr['userId'] = $userId;
                        $arr['userName'] = $user->getField('nickname');
                        $arr['userPhone'] = $user->getField('phone');
                        $arr['userIdCard'] = $user->getField('idCard');
                    }
                }
            }
        } else {
            $uid = \Sooh\Base\Session\Data::getInstance()->get('accountId');
            if (!empty($uid)) {
                $user = \Prj\Data\User::getCopy($uid);
                $user->load();
                if ($user->exists()) {
                    $arr['userId'] = $uid;
                    $arr['userName'] = $user->getField('nickname');
                    $arr['userPhone'] = $user->getField('phone');
                    $arr['userIdCard'] = $user->getField('idCard');
                }
            }
        }

        if ($type == 1) {
            $arr['borrowerName'] = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
            $arr['borrowerIdCard'] = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
            $arr['borrowerId'] = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
            $arr['userId'] = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
            $arr['userName'] = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
            $arr['userIdCard'] = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
            $arr['amount1'] = '&nbsp;&nbsp;&nbsp;&nbsp;';
            $arr['amount2'] = '&nbsp;&nbsp;&nbsp;&nbsp;';
            $arr['yieldStatic'] = '&nbsp;&nbsp;&nbsp;&nbsp;';
            $arr['timeDur'] = '&nbsp;&nbsp;&nbsp;&nbsp;';
            $arr['ymdft'] = '';
            $arr['ymdsd'] = '';
            $arr['ymdtd'] = '';
        } elseif ($type == 2) {
            $arr['borrowerName'] = $arr['borrowerIdCard'] = $arr['borrowerId'] = '***投资成功才可查看';
            $arr['ymdft'] = '合同签署之日';
            $arr['ymdsd'] = date('Y-m-d', time());
            $arr['ymdtd'] = date('Y-m-d', time());
        }
        return self::invest($arr, $ver);
    }

    public static function getCharges($type, $amount, $bankCard, $uid) {
        $arr = ['ymd' => date('Y-m-d'), 'amount' => $amount / 100, 'userName' => '', 'userPhone' => '', 'userIdCard' => '',
            'userId' => '', 'bankId' => '', 'bankCard' => ''];
        if ($uid) {
            $user = \Prj\Data\User::getCopy($uid);
            $user->load();
            if ($user->exists()) {
                $arr['userName'] = $user->getField('nickname');
                $arr['userPhone'] = $user->getField('phone');
                $arr['userIdCard'] = $user->getField('idCard');
                $arr['userId'] = $uid;

                $bank = \Prj\Data\BankCard::getList($uid, ['statusCode' => \Prj\Consts\BankCard::enabled]);
                if (!empty($bank)) {
                    if ($bankCard) {
                        foreach ($bank as $r) {
                            if ($r['bankCard'] == $bankCard) {
                                $arr['bankId'] = \Prj\Consts\Banks::$enums[$r['bankId']][0];
                                $arr['bankCard'] = $r['bankCard'];
                            }
                        }
                        /*
                          $r = current($bank);
                          $arr['bankId'] = \Prj\Consts\Banks::$enums[$r['bankId']];
                          $arr['bankCard'] = $r['bankCard'];
                         */
                    }
                }
            }
            //var_log($arr);
        }

        if ($type == 1) {
            $arr['userId'] = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
 	            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
            $arr['userName'] = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
 	            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
            $arr['userIdCard'] = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
 	            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
            $arr['bankCard'] = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
 	            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
            $arr['bankId'] = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
 	            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
            $arr['ymd'] = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
 	            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
            $arr['amount'] = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
        }
        return self::recharges($arr, $ver = 1);
    }

}
