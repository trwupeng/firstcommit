<?php
/**
 * Created by PhpStorm.
 * User: tang.gaohang
 * Date: 2016/6/15
 * Time: 11:28
 */
namespace Prj\Data;

class Warestpl extends \Sooh\DB\Base\KVObj{

    protected static $_pkey = 'tplId';

    public static function getPkeyName(){
        return static::$_pkey;
    }

    public static function paged($pager,$where=[],$order=''){
        $tmp = self::getCopy('');
        $db = $tmp->db();
        $tb = $tmp->tbname();
        $pager->init($db->getRecordCount($tb, $where), -1);
        return $db->getRecords($tb,'*',$where,$order,$pager->page_size, $pager->rsFrom());
    }

    public static function getTplSelect(){
        $tmp = self::getCopy('');
        $db = $tmp->db();
        $tb = $tmp->tbname();
        $rs = $db->getRecords($tb,'*',['statusCode'=>0]);
        $newRs = [];
        if($rs){
            foreach($rs as $v){
                $newRs[$v['tplId']] = $v['tplName'];
            }
        }
        return $newRs;
    }

    public static function add($fields,$nickname = ''){
        $time = \Sooh\Base\Time::getInstance();
        do{
            $id = $time->ymdhis().rand(1000,9999);
            $tmp = self::getCopy($id);
            $tmp->load();
        }while($tmp->exists());
        foreach($fields as $k=>$v){
            $tmp->setField($k,$v);
        }
        return $tmp;
    }

    public static function dataUpdate($fields,$nickname = ''){
        $tmp = self::getCopy($fields[static::$_pkey]);
        $tmp->load();
        if(!$tmp->exists())return null;
        foreach($fields as $k=>$v){
            $tmp->setField($k,$v);
        }
        return $tmp;
    }

    public static function getCopy($id) {
        return parent::getCopy([static::$_pkey=>$id]);
    }


    //针对缓存，非缓存情况下具体的表的名字
    protected static function splitedTbName($n,$isCache)
    {
        //return 'tb_asset_'.($n % static::numToSplit());
        return 'tb_wares_tpl';
    }
    //指定使用什么id串定位数据库配置
    protected static function idFor_dbByObj_InConf($isCache) {
        return 'default'.($isCache?'Cache':'');
    }

}
