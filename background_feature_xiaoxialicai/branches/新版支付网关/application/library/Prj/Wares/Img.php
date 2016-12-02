<?php
namespace Prj\Wares;
/**
 * Created by PhpStorm.
 * User: tang.gaohang
 * Date: 2016/4/11
 * Time: 14:55
 */

class Img {


    public static function getImgUrl($id){
        $http = \Sooh\Base\Ini::getInstance()->get('http');
        $http = $http?$http:'http';
        $data = \Prj\Data\Files::getDataById($id);
        $cdnSever = \Prj\Data\Config::get('IMG_CDN_SERVER');
        if($cdnSever && $data['urlCdn']){
            return $cdnSever.'/uploadfile/app/wares/'.$data['urlCdn'];
        }else{
            return $http.'://'.$_SERVER['HTTP_HOST'].'/index.php?__=public/getImage&fileId='.$id;
        }
    }

    public static function saveImgToCdn($fileName, $dir , $data){
        $cmsServer = \Prj\Data\Config::get('cms_server');
        if($cmsServer){
            $url  = $cmsServer . '/api.php?op=cdn_wares';
            error_log('###send to cdn ...url:'.$url);
            $base = base64_encode($data);
            $ret  = \Prj\Tool\Func::curl_post($url, ['data' => $base, 'fileName' => $fileName, 'dir' => $dir, 'key' => md5(\Sooh\Base\Ini::getInstance()->get('TestKey'))], 5);
            error_log('###cdn return###' . $ret);
            $ret = json_decode($ret, true);
            if(empty($ret)){
                return ['code'=>400,'msg'=>'连接CDN失败'];
            }else{
                if($ret['code'] == 400){
                   return $ret;
                }
            }
            return ['code'=>200,'fileName'=>$fileName];
        }else{
            return ['code'=>200];
        }
    }
}