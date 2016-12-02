<?php
namespace Prj\Data;
/**
 * Created by PhpStorm.
 * User: tang.gaohang
 * Date: 2016/5/13
 * Time: 14:27
 */

class ReturnPlan extends \Sooh\DB\Base\KVObj{

    public static function paged($pager,$where=[],$order=''){
        $tmp = self::getCopy('');
        $db = $tmp->db();
        $tb = $tmp->tbname();
        $pager->init($db->getRecordCount($tb, $where), -1);
        return $db->getRecords($tb,'*',$where,$order,$pager->page_size, $pager->rsFrom());
    }

    protected static $fields = [
        'ordersId', 'periods',
        'userId', 'waresId',
        'waresName', 'interestStatic',
        'interestAdd', 'interestExt',
        'interestFloat', 'interestSub',
        'amount', 'amountExt',
        'realPayAmount', 'realPayInterest',
        'realPayinterestSub', 'exp',
        'status', 'isPay',
        'sn', 'planDateYmd',
        'realDateYmd', 'ahead',
        'updateTime', 'iRecordVerID',
        'sLockData', 'days',
    ];

    public static function add($fields,$userId){
        if(empty($fields))throw new \ErrorException('数据为空');
        $tmp = self::getCopy($fields['ordersId'],$fields['periods']);
        $tmp->load();
        if($tmp->exists()){
            if($userId == $tmp->getField('userId') && $tmp->getField('status') == 1){
                return $tmp;
            }
            throw new \ErrorException('冲突的记录');
        }
        foreach($fields as $k=>$v){
            if($v!==null && in_array($k,self::$fields))$tmp->setField($k,$v);
        }
        $tmp->setField('updateTime',date('YmdHis'));
        $tmp->setField('userId',$userId);
        $num = 0;
        $retry = 1;
        while($num<=5 && $retry==1){
            $num++;
            $sn = time().rand(1000,9999).substr($userId,-4);
            error_log(__CLASS__.'#sn:'.$sn);
            try{
                $tmp->setField('sn',$sn);
                $tmp->update();
                $retry = 0;
            }catch (\ErrorException $e){
                error_log(__METHOD__.'#'.$fields['ordersId'].'#error#'.$e->getMessage());
                if($num==5)throw $e;
            }
        }
        return $tmp;
    }

    protected $init = [];

    public static function updateFields($ordersId,$periods,$status,$isPay = null,$exp = ''){
        $tmp = self::getCopy($ordersId,$periods);
        $tmp->load();
        if(!$tmp->exists())throw new \ErrorException('查不到数据'.$ordersId.'#'.$periods);
        $tmp->init['status'] = $tmp->getField('status');
        $tmp->init['isPay'] = $tmp->getField('isPay');
        $tmp->init['exp'] = $tmp->getField('exp');
        $tmp->setField('status',$status);
        if($isPay!==null)$tmp->setField('isPay',$isPay);
        $tmp->setField('exp',$exp);
        $tmp->update();
        return $tmp;
    }

    public function updateFields_rollBack(){
        if($this->init){
            foreach($this->init as $k=>$v){
                $this->setField($k,$v);
            }
            $this->update();
        }
    }

    public static function getCopy($ordersId,$periods = 1) {
        return parent::getCopy(['ordersId'=>$ordersId,'periods'=>$periods]);
    }


    //针对缓存，非缓存情况下具体的表的名字
    protected static function splitedTbName($n,$isCache)
    {
        return 'tb_returnplan_'.($n % static::numToSplit());
    }
    //指定使用什么id串定位数据库配置
    protected static function idFor_dbByObj_InConf($isCache) {
        return 'default'.($isCache?'Cache':'');
    }

}