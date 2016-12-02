<?php
namespace Prj\Data;

class WithdrawNum extends \Sooh\DB\Base\KVObj{

    public static function paged($pager,$where=[],$order=''){
        $tmp = self::getCopy('');
        $db = $tmp->db();
        $tb = $tmp->tbname();
        $pager->init($db->getRecordCount($tb, $where), -1);
        return $db->getRecords($tb,'*',$where,$order,$pager->page_size, $pager->rsFrom());
    }

    public static function add($userId,$num,$month,$exp='',$nickname){
        $time = \Sooh\Base\Time::getInstance();
        do{
            $id = time().rand(1000,9999).substr($userId,-4);
            $tmp = self::getCopy($id);
            $tmp->load();
        }while($tmp->exists());
        $tmp->setField('userId',$userId);
        $tmp->setField('num',$num);
        $tmp->setField('month',$month);
        $tmp->setField('exp',$exp);
        $tmp->setField('statusCode',\Prj\Consts\Tally::status_new);
        $tmp->setField('updateUser',$nickname);
        $tmp->setField('updateTime',$time->ymdhis());
        return $tmp;
    }

    public static function getCopy($id) {
        return parent::getCopy(['numId'=>$id]);
    }


    //针对缓存，非缓存情况下具体的表的名字
    protected static function splitedTbName($n,$isCache)
    {
        return 'tb_withdraw_num_'.($n % static::numToSplit());
    }
    //指定使用什么id串定位数据库配置
    protected static function idFor_dbByObj_InConf($isCache) {
        return 'default'.($isCache?'Cache':'');
    }

}
