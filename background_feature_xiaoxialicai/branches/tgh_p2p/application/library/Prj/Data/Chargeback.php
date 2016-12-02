<?php
/**
 * Created by PhpStorm.
 * User: LTM <605415184@qq.com>
 * Date: 2016/5/24
 * Time: 14:15
 */

namespace Prj\Data;

/**
 * 退款记录
 * @package Prj\Data
 */
class Chargeback extends \Sooh\DB\Base\KVObj
{
    /**
     * 创建一个新的退款记录
     * @param string $orderId 订单编号
     * @param string $userId  用户编号
     * @param string $reason  退款原因
     * @return bool
     * @throws \ErrorException
     * @throws \Exception
     */
    public static function createNew($orderId, $userId, $reason)
    {
        $dbInvestment = \Prj\Data\Investment::getCopy($orderId);
        $dbInvestment->load();
        if ($dbInvestment->exists() == false) {
            return '订单不存在';
        }
        $db = self::getCopy($orderId);
        $db->load();
        if ($db->exists()) {
            return '退款请求已存在';
        }
        if ($userId != $dbInvestment->getField('userId')) {
            return '用户ID不合法';
        }
//        if ($amount > $dbInvestment->getField('amount')) {
//            return '退款金额不合法';
//        }

        $time = \Sooh\Base\Time::getInstance();
        $db->setField('userId', $userId);
        $db->setField('amount', 0);
        $db->setField('reason', $reason);
        $db->setField('sn', '');
        $db->setField('serviceRet', '');
        $db->setField('serviceCode', '');
        $db->setField('retryMsg', '{}');
        $db->setField('createTime', $time->ymdhis());
        $db->setField('status', 1);
        $db->update();

        //调用网关
        $retry = 2;//重试次数
        $rpc = \Sooh\Base\Ini::getInstance()->get('noGW') ? self::getRpcDefault('PayGWCmd') : \Sooh\Base\Rpc\Broker::factory('PayGWCmd');
        $sys = \Lib\Services\PayGWCmd::getInstance($rpc);
        while ($retry > 0) {
            $ret = call_user_func_array([$sys, 'refund'], [$orderId, $userId, $reason]);

            if ($ret['code'] == 200) {
                $db->setField('status', 4);
                $db->setField('sn', $ret['data']['serialNo']);
                $db->setField('serviceRet', '网关接收成功');
                $db->update();
                $dbInvestment->setField('chargeBackStatus', 1);
                $dbInvestment->update();
                return '';
            } else {
                $db->setField('serviceRet', $ret['msg']);
                $db->setField('status', 4);

                //记录错误结果
                $retryMsg = $db->getField('retryMsg');
                if (is_string($retryMsg)) {
                    $retryMsg = json_decode($retryMsg, true);
                }
                if (count($retryMsg) >= 5) {
                    $retryMsg = [];
                }
                $retryMsg[$time->timestamp() . 'try' . $retry] = empty($ret) ? '' : $ret;
                var_log($retryMsg, 'retryMsg');
                $db->setField('retryMsg', json_encode($retryMsg));
                $db->update();

                $retry--;
                sleep(1);
            }
        }
        return json_encode($ret ? : '调用网关异常，请联系开发人员调试');
//        return '调用网关异常，请联系开发人员调试';
    }

    public static function loopAll($where)
    {
        $db = self::getCopy('');
        $rs = $db->loopFindRecords($where);
        return $rs;
    }

    public static function getRpcDefault($serviceName)
    {
        if ($serviceName === 'Rpcservices' || $serviceName === 'SessionStorage') {
            return null;
        }
        $flg = \Sooh\Base\Ini::getInstance()->get('RpcConfig.force');
        if ($flg !== null) {
            if ($flg) {
                error_log('force rpc for ' . $serviceName);
                return \Sooh\Base\Rpc\Broker::factory($serviceName);
            } else {
                error_log('no rpc for ' . $serviceName);
                return null;
            }
        } else {
            error_log('try rpc for ' . $serviceName);
            return \Sooh\Base\Rpc\Broker::factory($serviceName);
        }
    }

    /**
     * @param string $orderId
     * @return \Sooh\DB\Base\KVObj
     */
    public static function getCopy($orderId)
    {
        return parent::getCopy(['orderId' => $orderId]);
    }

    protected static function splitedTbName($n, $isCache)
    {
        return 'tb_chargeback_' . ($n % static::numToSplit());
    }

    protected static function idFor_dbByObj_InConf($isCache)
    {
        return 'chargeback' . ($isCache ? 'Cache' : '');
    }
}