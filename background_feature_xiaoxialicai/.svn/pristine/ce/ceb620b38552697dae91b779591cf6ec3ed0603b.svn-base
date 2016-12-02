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
        $tmp = new self();
        return $http.'://'.$tmp->getServer().$id;
    }

    protected function getServer(){
        $server = \Prj\Data\Config::get('IMG_CDN_SERVER');
        $localServer = $_SERVER['HTTP_HOST'].'/index.php?__=public/getImage&fileId=';
        if(empty($server)){
            return $localServer;
        }else{
            return $this->useCdn()?$server : $localServer;
        }
    }

    protected function useCdn(){
        return true;
    }
}