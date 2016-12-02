<?php
namespace Lib\Api\Notify;
/**
 *
 *
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/7/1 0001
 * Time: 上午 11:43
 */

class Base {

    private static $instnace=[];

    public static function getByCopartnerAbsOrId($absOrId) {
        if(is_numeric($absOrId)) {
            $copartnerId = substr($absOrId, 0, 4);
            $oCopartner= \Prj\Data\Copartner::getCopy(['contractId'=>$copartnerId]);
            $copartnerAbs = $oCopartner->db()->getOne($oCopartner->tbname(), 'copartnerAbs', ['copartnerId'=>$copartnerId]);
        }else{
            $copartnerAbs = $absOrId;
        }
        if(empty($copartnerAbs)) {
            error_log($absOrId.'渠道简称未找到');
            return false;
        }
        $copartnerAbs = ucfirst($copartnerAbs);
//error_log('协议号########'.$absOrId.'渠道简称########'.$copartnerAbs);
        if(!empty(self::$instnace[$copartnerAbs])) {
            return self::$instnace[$copartnerAbs];
        }else {
            $filename = dirname(__FILE__).'/'.$copartnerAbs.'.php';
            if(file_exists($filename)) {
                $classname = '\\Lib\Api\\Notify\\'.$copartnerAbs;
                self::$instnace[$copartnerAbs] = new $classname();
                return self::$instnace[$copartnerAbs];
            }
            return false;
        }

    }


    /**
     * app安装通知
     */

    public function onInstalled ($args) {

    }

    /**
     * app 启动时通知
     *
     */
    public function onStartUp ($args) {
        $db_rpt = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_p2prpt);
        $copartnerId = $db_rpt->getOne(\Rpt\Tbname::tb_copartner_notify, 'copartnerId', ['deviceId'=>$args['deviceId']]);
        if(empty($copartnerId)) {
            error_log('tb_copartner_notify表中未找到设备号'.$args['deviceId']);
            return;
        }

        $obj_copartner = self::getByCopartnerAbsOrId($copartnerId);
        if($obj_copartner){
            $obj_copartner->onStartUpToCallBack($args);
        }

    }


    protected function onStartUpToCallBack($args){
        return false;
    }


    public static  function getCopartnerIdByAbs ($copartnerAbs) {
        $rs = \Prj\Data\Copartner::loopFindRecordsByFields(['copartnerAbs'=>$copartnerAbs], null, 'copartnerId', 'getCol');
        return $rs[0];
    }

}