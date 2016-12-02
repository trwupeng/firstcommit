<?php
/**
 * 记录身份证的表
 * Created by PhpStorm.
 * User: tang.gaohang
 * Date: 2016/3/30
 * Time: 17:18
 */
namespace Prj\Data;

class IdCard extends \Sooh\DB\Base\KVObj{

    public static function getCopy($id) {
        return parent::getCopy(['id'=>$id]);
    }

    /**
     * 检查身份证是否可用
     * @param $id
     * @param $userId
     * @return null|\Sooh\DB\Base\KVObj
     * @throws \ErrorException
     */
    public static function check($id,$userId){
        $idCard = self::getCopy($id);
        $idCard->load();
        var_log($idCard,'idCard >>> ');
        if(!$idCard->exists()){
            $idCard->setField('userId',$userId);
            return $idCard;
        }else{
            if($idCard->getField('userId') == $userId ){
                return $idCard;
            }else{
                error_log('[error]id:'.$id.' have been used by others');
                return null;
            }
        }
    }



    //针对缓存，非缓存情况下具体的表的名字
    protected static function splitedTbName($n,$isCache)
    {
        return 'tb_user_idcard_'.($n % static::numToSplit());
    }
    //指定使用什么id串定位数据库配置
    protected static function idFor_dbByObj_InConf($isCache) {
        return 'default'.($isCache?'Cache':'');
    }

}
