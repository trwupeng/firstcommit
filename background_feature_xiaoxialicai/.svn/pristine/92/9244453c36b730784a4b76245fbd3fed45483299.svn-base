<?php
namespace Prj\Data;
/**
 * 配置获取和设置
 *
 * @author gh.tang
 */
class TbConfig {

    protected static function DB(){
        return \Sooh\DB\Broker::getInstance();
    }

    public static function get($key){
		error_log("use \Prj\Data\Config::get()  instead.......................................................................................");
        $arr = self::DB()->getRecord('tb_config','v',['k'=>$key]) or $arr = [];
        return json_decode($arr['v'],true)?json_decode($arr['v'],true):$arr['v'];
    }
}
