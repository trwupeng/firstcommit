<?php
namespace Prj\Data;
/**
 * 配置ram获取和设置
 *
 * @author gh.tang
 */
class TbConfigRam {

    protected static function DB(){
        return \Sooh\DB\Broker::getInstance();
    }

    public static function get($key){
        $arr = self::DB()->getRecord('tb_config_ram','v',['k'=>$key]) or $arr = [];
        return json_decode($arr['v'],true)?json_decode($arr['v'],true):$arr['v'];
    }
}
