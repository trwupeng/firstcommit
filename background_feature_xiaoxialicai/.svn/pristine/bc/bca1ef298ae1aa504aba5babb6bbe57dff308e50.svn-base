<?php
/**
 * Created by PhpStorm.
 * User: tang.gaohang
 * Date: 2016/3/4
 * Time: 14:12
 */

namespace Prj\Data;

class Rebate extends \Sooh\DB\Base\KVObj{

    public static function paged($pager,$where=[],$order='', $userId = ''){
        if ($userId)
            $where['userId'] = $userId;
        if($userId){
            $tmp = self::getCopy($userId);
            $db = $tmp->db();
            $tb = $tmp->tbname();
            $ret = $db->getRecords($tb,'*',$where,$order,$pager->page_size, $pager->rsFrom());
        }else{
            $total = self::loopGetRecordsCount($where);
            $pager->total = $total;
            $lastPage = \Sooh\Base\Session\Data::getInstance()->get('rebate_lastPage');
            if($pager->pageid()!=1){
                $where = $lastPage;
            }else{
                $where = ['where'=>$where];
            }
            $rs = self::loopGetRecordsPage(['updateYmd'=>'rsort'],$where,$pager);
            \Sooh\Base\Session\Data::getInstance()->set('rebate_lastPage',$rs['lastPage']);
            $ret = $rs['records'];
        }
        return $ret;
    }

    public static function getSumAcount($userId,$childUserId){
        $tmp = self::getCopy($userId);
        $tmp->load();
        $db = $tmp->db();
        $tbname = $tmp->tbname();
        $record = $db->getRecord($tbname,'sum(amount) as total',['statusCode'=>\Prj\Consts\OrderStatus::done,'childUserId'=>$childUserId]);
        return $record['total']-0;
    }

	public static function getCount($where)
	{
		$sys = self::getCopy($where['userId']);
		$db = $sys->db();
		$tb = $sys->tbname();
		return $db->getRecordCount($tb, $where);
	}

    public static function addRebate($amount,$toUserId,$formUserId=0,$investId = 0,$waresId = 0,$statusCode = \Prj\Consts\OrderStatus::created){
        $baseid     = substr($toUserId, -4);
        $time = \Sooh\Base\Time::getInstance();
        do{
            $id = time().rand(1000,9999).$baseid;
            $tmp = self::getCopy($id);
            $tmp->load();
        }while($tmp->exists());
        $tmp->setField('investId',$investId);
        $tmp->setField('waresId',$waresId);
        $tmp->setField('amount',$amount);
        $tmp->setField('childUserId',$formUserId);
        $tmp->setField('userId',$toUserId);
        $tmp->setField('statusCode',$statusCode);

        $tmp->setField('updateYmd',$time->ymdhis());
        $tmp->setField('createYmd',$time->ymdhis());
        return $tmp;
    }

    public static function getCopy($id) {
        return parent::getCopy(['rebateId'=>$id]);
    }


    //针对缓存，非缓存情况下具体的表的名字
    protected static function splitedTbName($n,$isCache)
    {
        return 'tb_rebate_'.($n % static::numToSplit());
    }
    //指定使用什么id串定位数据库配置
    protected static function idFor_dbByObj_InConf($isCache) {
        return 'rebate'.($isCache?'Cache':'');
    }

}
