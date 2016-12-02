<?php
namespace Prj\Data;

class Files extends \Sooh\DB\Base\KVObj {
    /**
     * @return \Sooh\DB\Interfaces\All
     * @throws \ErrorException
     */
	protected static function getDB() {
		return \Sooh\DB\Broker::getInstance();
	}

    public static function createNew($fileData, $prefix ,$date = 0 , $dir = '' , $tail = '')
    {
        $date = $date?$date:date('YmdHis');
        $stop = false;
        $num = 0;
        while($stop==false)
        {
            $num++;
            try{
                $fileId = $prefix.time().rand(1000,9999);
                $data = array('fileId'=>$fileId,'fileData'=>$fileData,'ymd'=>$date);
                if($dir){
                    $data['url'] = $dir.$fileId.$tail;
                    error_log('img url >>> '.$data['url']);
                }
                $ret = self::getDB()->addRecord('tb_files',$data);
                $stop = true;
            }catch (\ErrorException $e){
                if(\Sooh\DB\Broker::errorIs($e, \Sooh\DB\Error::duplicateKey)){
                    $stop = false;
                }else{
                    return false;
                }
            }
            if($num>3)break;
        }

        return $ret?$fileId:false;

    }

    public static function updateData($fileId , $data){
        $db = self::getDB();
        return $db->updRecords('tb_files',$data,['fileId'=>$fileId]);
    }

    public static function getDataById($fileId)
    {
        $db = self::getDB();
        $list = $db->getRecord('tb_files','*',array('fileId'=>$fileId));
        return ['data'=> $list['fileData'] , 'url'=>$list['url'],'urlCdn'=>$list['urlCdn']];
    }

    public static function add($fields){
        $time = \Sooh\Base\Time::getInstance();
        do{
            $id = $time->ymdhis().rand(1000,9999);
            $tmp = self::getCopy($id);
            $tmp->load();
        }while($tmp->exists());
        foreach($fields as $k=>$v){
            $tmp->setField($k,$v);
        }
        return $tmp;
    }

    public static function getCopy($id) {
        return parent::getCopy(['fileId'=>$id]);
    }


    //针对缓存，非缓存情况下具体的表的名字
    protected static function splitedTbName($n,$isCache)
    {
        return 'tb_files';
    }
    //指定使用什么id串定位数据库配置
    protected static function idFor_dbByObj_InConf($isCache) {
        return 'default'.($isCache?'Cache':'');
    }
}
