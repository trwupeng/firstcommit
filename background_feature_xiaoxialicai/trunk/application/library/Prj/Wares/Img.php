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

    public static function uploadImg($fileArr , $pre = ''){
        $data = file_get_contents($fileArr['tmp_name']);
        $tail = strrchr($fileArr['name'],'.');
        if($_FILES['file']['size']/1024 > 1024)return self::returnError('图片大小请控制在1M以内');
        $fileId = $pre.time().rand(1000,9999);
        $file = \Prj\Data\Files::getCopy($fileId);
        $file->load();
        if($file->exists())return self::returnError('系统正忙,请稍候重试');
        $file->setField('ymd',date('YmdHis'));
        var_log($_FILES,'>>>');

        //todo 图片传到CDN
        $fileName = $fileId.$tail;
        $ret = \Prj\Wares\Img::saveImgToCdn($fileName,'wares',$data);
        if($ret['code'] == 200){
            if($ret['fileName']){
                $file->setField('urlCdn',$ret['fileName']);
                $file->setField('cdn',1);
            }
        }else{
            return self::returnError($ret['msg']);
        }

        //todo 图片存到硬盘
        $dir = APP_PATH.'/public/upload/wares';
        var_log($dir,'>>>');
        if(is_dir($dir)){
            error_log('###save to ying pan');
            if(!in_array($tail,['.jpg','.png','.gif'])){
                error_log($tail , 'tail >>> ');
                return self::returnError('不支持的文件格式');
            }
            error_log('###img save to db');
            $filePath = APP_PATH.'/public/upload/wares/'.$fileName;
            file_put_contents($filePath,$data);
            error_log('###img save to dir');
            $file->setField('url','/upload/wares/'.$fileName);
            try{
                $file->update();
            }catch (\ErrorException $e){
                return self::returnError($e->getMessage());
            }
            return [
                'fileId' => $fileId,
                'url' => '/upload/wares/'.$fileName
            ];
        }else{
            return self::returnError('上传目录不存在');
        }
    }

    protected static function returnError($msg){
        throw new \ErrorException($msg);
    }
}