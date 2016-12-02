<?php
/**
 * Created by PhpStorm.
 * User: tang.gaohang
 * Date: 2016/4/24
 * Time: 19:23
 */

namespace Prj\Data;

class Trans extends \Sooh\DB\Base\KVObj{

    public static function paged($pager,$where=[],$order=''){
        $tmp = self::getCopy('');
        $db = $tmp->db();
        $tb = $tmp->tbname();
        $pager->init($db->getRecordCount($tb, $where), -1);
        return $db->getRecords($tb,'*',$where,$order,$pager->page_size, $pager->rsFrom());
    }

    public static function add($fields,$nickname = ''){
        $time = \Sooh\Base\Time::getInstance();
        do{
            $id = time().rand(1000,9999).substr($fields['fromUid'],-4);
            $tmp = self::getCopy($id);
            $tmp->load();
        }while($tmp->exists());
        foreach($fields as $k=>$v){
            $tmp->setField($k,$v);
        }
        $tmp->setField('updateYmd',$time->ymdhis());
        $tmp->setField('updateUser',$nickname);
        return $tmp;
    }

    public static function getCopy($id) {
        return parent::getCopy(['transId'=>$id]);
    }


    //针对缓存，非缓存情况下具体的表的名字
    protected static function splitedTbName($n,$isCache)
    {
        return 'tb_trans_0';
    }
    //指定使用什么id串定位数据库配置
    protected static function idFor_dbByObj_InConf($isCache) {
        return 'default'.($isCache?'Cache':'');
    }

}
