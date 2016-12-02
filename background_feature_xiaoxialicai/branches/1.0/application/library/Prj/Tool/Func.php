<?php
namespace Prj\Tool;

class Func
{
    /**
     * 发post请求
     */
    public static function curl_post($url,$post=[],$time=5)
    {
        $ch = curl_init();
        curl_setopt ( $ch, CURLOPT_URL, $url );
        curl_setopt ( $ch, CURLOPT_POST, 1 );
        curl_setopt ( $ch, CURLOPT_HEADER, 0 );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt ( $ch, CURLOPT_TIMEOUT, $time );
        curl_setopt ( $ch, CURLOPT_POSTFIELDS, $post );
        $return = curl_exec ( $ch );
        curl_close ( $ch );
        return $return;
    }

    public static function debug_log(){
        ini_set('error_log','/var/www/logs/tgh_log.txt');
    }

    public static function paged($arr,$orderBy = null,\Sooh\DB\Pager $pager){
        if($orderBy){

        }
        $pager->total = count($arr);
        var_log($pager->page_size);
        var_log($pager->rsFrom());
        return array_slice($arr,$pager->rsFrom(),$pager->page_size);
    }
}