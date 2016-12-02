<?php
/**
 * Created by PhpStorm.
 * User: tang.gaohang
 * Date: 2016/3/3
 * Time: 19:15
 */
namespace Prj\Data;

class Systally extends \Sooh\DB\Base\KVObj{

    public static function paged($pager,$where=[],$order=''){
        $tmp = self::getCopy('');
        $db = $tmp->db();
        $tb = $tmp->tbname();
        $pager->init($db->getRecordCount($tb, $where), -1);
        return $db->getRecords($tb,'*',$where,$order,$pager->page_size, $pager->rsFrom());
    }

    public static function addTally($sn,$amount,$userId,$waresId,$statusCode = \Prj\Consts\Systally::cancel_status){
        $time = \Sooh\Base\Time::getInstance();
        $tmp = self::getCopy($sn);
        $tmp->load();
        if($tmp->exists())return null;
        $tmp->setField('amount',$amount);
        $tmp->setField('userId',$userId);
        $tmp->setField('statusCode',$statusCode);
        if($waresId)$tmp->setField('waresId',$waresId);
        $tmp->setField('tallyYmd',$time->ymdhis());
        return $tmp;
    }

    public static function getWaitDelayAmount($waresId)
    {
        $list = self::loopFindRecords(['waresId'=>$waresId,'type'=>\Prj\Consts\PayGW::tally_delayConfirm,'statusCode'=>\Prj\Consts\Systally::wait_status]);
        $total = 0;
        if($list){
            var_log($list,'list >>> ');
            array_walk($list,function($v) use (&$total){
                $total+=$v['amount'];
            });
        }
        return $total;
    }

    public static function getCopy($id) {
        return parent::getCopy(['sn'=>$id]);
    }


    //针对缓存，非缓存情况下具体的表的名字
    protected static function splitedTbName($n,$isCache)
    {
        return 'tb_systally_'.($n % static::numToSplit());
    }
    //指定使用什么id串定位数据库配置
    protected static function idFor_dbByObj_InConf($isCache) {
        return 'dbForRpt'.($isCache?'Cache':'');
    }

}
