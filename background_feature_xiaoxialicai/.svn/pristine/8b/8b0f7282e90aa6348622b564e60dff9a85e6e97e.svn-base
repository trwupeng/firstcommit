<?php
namespace Prj\Misc;
/**
 * Description of Banks
 * @author simon.wang
 */
class BankStop
{
    protected static $start_time;
    protected static $end_time;

    public static function checkStop($bank){
        $bank = strtolower($bank);
        var_log('check bank stop #'.$bank);
        foreach(\Prj\Consts\BankStop::$plan as $k=>$v){
            if($bank != $v[0])continue;
            $time = time();
            if($time >= strtotime($v[1]) && $time < strtotime($v[2])){
                self::$start_time = date('Y年m月d日H:i',strtotime($v[1]));
                self::$end_time = date('Y年m月d日H:i',strtotime($v[2]));
                return true;
            }
        }
        return false;
    }

    public static function getBankName($bank){
        $bank = strtolower($bank);
        return \Prj\Consts\BankStop::$banks[$bank];
    }

    public static function getNotice($bank){
        $bank = strtolower($bank);
        return self::getBankName($bank)."正在进行核心系统维护，维护时间为：".self::$start_time."-".self::$end_time."。";
    }
}
