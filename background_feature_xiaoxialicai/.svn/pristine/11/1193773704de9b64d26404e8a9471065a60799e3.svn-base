<?php
namespace Prj\Check;
/**
 * Created by PhpStorm.
 * User: tang.gaohang
 * Date: 2016/2/23
 * Time: 10:49
 * 存钱罐收益
 */
class DayInterest extends Base{

    protected static $cmd = 'dayInterest';
    protected static $tbname = 'tb_dayInterest_0';
    protected static $fieldsMap = [
        'sn'=>'SN',
        'file'=>'path',
    ];

    /**
     * 网关的数据插入数据库
     */
    protected function insertToDB(){
        $class = get_called_class();
        $db = \Sooh\DB\Broker::getInstance();
        if(!empty($this->gData)){
            foreach($this->gData as $v){
                $data = $this->fieldsSwift($v);
                unset($data['sn']);
                $data['updateTime'] = date('YmdHis');
                $data['diff'] = 'local_miss';
                $data['havePay'] = 1;
                $record = $db->getRecord($class::$tbname,'*',['userId'=>$data['userId'],'ymd'=>$data['ymd']]);
                if(!empty($record)){
                    //todo 比对
                    $data['diff'] = $this->fieldsCheck($data,$record,0);
                    try{
                        unset($data['amount']); //不更新钱
                        $ret = $db->updRecords($class::$tbname,$data,['userId'=>$data['userId'],'ymd'=>$data['ymd']]);
                    }catch (\ErrorException $e){
                        var_log("[error]".$e->getMessage());
                    }
                }else{
                    $data['sn'] = $data['ymd'].rand(1000,9999).substr($data['userId'],-4);
                    try{
                        $ret = $db->addRecord($class::$tbname,$data);
                    }catch (\ErrorException $e){
                        if(\Sooh\DB\Broker::errorIs($e, \Sooh\DB\Error::duplicateKey)){
                            continue;
                        }else{
                            throw $e;
                        }
                    }
                }
            }
        }
        return $ret;
    }
}
