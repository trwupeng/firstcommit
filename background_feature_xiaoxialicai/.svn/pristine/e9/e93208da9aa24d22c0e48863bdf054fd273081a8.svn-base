<?php
namespace Prj\Check;
/**
 * Created by PhpStorm.
 * User: tang.gaohang
 * Date: 2016/2/23
 * Time: 10:49
 */
class DayRecharges extends Base{

    protected static $cmd = 'dayRecharges';
    protected static $tbname = 'tb_dayRecharges';
    protected static $fieldsMap = [ //'表字段'=>'接口字段'
        'sn'=>'SN',
        'file'=>'path',
    ];

    public function getLocalData(){
        //取本地数据
        $where = [
            'amountFlg'=>\Prj\Consts\OrderType::recharges,
            'LEFT(orderTime,8)'=>$this->ymd,
            'orderStatus'=>\Prj\Consts\OrderStatus::done,
        ];
        return \Prj\Data\Recharges::loopAll($where);
    }

    protected function updateData($data){
        $tmp = [
            'userIdLocal'=>$data['userId'],
            'amountLocal'=>$data['amountAbs'],
            'diff'=>$data['diff'],
            'updateTime'=>date('YmdHis'),
            'haveLocal'=>1,
        ];
       return $this->DB()->updRecords(self::$tbname,$tmp,['sn'=>$data['sn']]);
    }

    protected function addData($data){
        $tmp = [
            'sn'=>$data['sn'],
            'ymd'=>$data['ymd'],
            'userIdLocal'=>$data['userId'],
            'amountLocal'=>$data['amountAbs'],
            'diff'=>'pay_miss',
            'updateTime'=>date('YmdHis'),
            'haveLocal'=>1,
        ];
        if(empty($tmp['sn']))return;
        return $this->DB()->addRecord(self::$tbname,$tmp);
    }

    public static function addRecords($data){
        $tmp = new self(date('Ymd'));
        $newData = [
            'sn'=>$data['sn'],
            'ymd'=>$tmp->ymd,
            'userIdLocal'=>$data['userId'],
            'amountLocal'=>$data['amountAbs'],
            'diff'=>'pay_miss',
            'updateTime'=>date('YmdHis'),
            'haveLocal'=>1,
        ];
        if(empty($newData['sn']))return;
        return $tmp->DB()->addRecord(self::$tbname,$newData);
    }
}
