<?php
/**
 * Created by PhpStorm.
 * User: tang.gaohang
 * Date: 2016/3/21
 * Time: 15:40
 */
namespace Prj\Data;

class MarketingSecond extends \Sooh\DB\Base\KVObj{

    public static function paged($pager,$where=[],$order=''){
        $tmp = self::getCopy('');
        $db = $tmp->db();
        $tb = $tmp->tbname();
        $pager->init($db->getRecordCount($tb, $where), -1);
        return $db->getRecords($tb,'*',$where,$order,$pager->page_size, $pager->rsFrom());
    }

    public static function updateData($fields,$nickname=''){
        $updateFields = ['nickname','ymdBindcard','ymdBindcard','ymdFirstBuy','updateTime','updateUser','phone','ymdReg'];
        $updateData = [];
        $userId = $fields['userId'];
        if(empty($userId)){
            throw new \ErrorException('用户ID为空');
        }else{
            $fields['updateTime'] = date('YmdHis');
            $fields['updateUser'] = $nickname;
            $mc = self::getCopy($userId);
            $mc->load();
            $exists = $mc->exists();
            foreach($updateFields as $v){
                if(isset($fields[$v])){
                    $updateData[$v] = $fields[$v];
                }
            }

            if(!empty($updateData)){
                foreach($updateData as $k=>$v){
                    $mc->setField($k,$v);
                }
            }

            $mc->update();

            if($exists){
                $updateData['type'] = '更新';
            }else{
                $updateData['type'] = '新增';
            }
            $updateData['userId'] = $fields['userId'];
            return $updateData;
        }

    }

    public static function getBindWaver($ymdBindcard,$ymdReg){
        return $ymdBindcard?$ymdBindcard-$ymdReg+1:0;
    }

    public static function getBuyWaver($ymdFirstBuy,$ymdBindcard){
        return $ymdFirstBuy?$ymdFirstBuy-$ymdBindcard+1:0;
    }

    public static function getCopy($id) {
        return parent::getCopy(['userId'=>$id]);
    }


    //针对缓存，非缓存情况下具体的表的名字
    protected static function splitedTbName($n,$isCache)
    {
        return 'tb_marketing_second_'.($n % static::numToSplit());
    }
    //指定使用什么id串定位数据库配置
    protected static function idFor_dbByObj_InConf($isCache) {
        return 'default'.($isCache?'Cache':'');
    }

}