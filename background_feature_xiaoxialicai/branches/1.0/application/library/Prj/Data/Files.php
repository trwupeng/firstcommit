<?php
namespace Prj\Data;

class Files {
    /**
     * @return \Sooh\DB\Interfaces\All
     * @throws \ErrorException
     */
	protected static function getDB() {
		return \Sooh\DB\Broker::getInstance();
	}

    public static function createNew($fileData, $prefix ,$date = 0)
    {
        $date = $date?$date:date('YmdHis');
        $stop = false;
        $num = 0;
        while($stop==false)
        {
            $num++;
            try{
                $fileId = $prefix.time().rand(1000,9999);
                $ret = self::getDB()->addRecord('tb_files',array('fileId'=>$fileId,'fileData'=>$fileData,'ymd'=>$date));
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

    public static function getDataById($fileId)
    {
        $db = self::getDB();
        $list = $db->getRecord('tb_files','*',array('fileId'=>$fileId));
        return $list['fileData'];
    }

}
