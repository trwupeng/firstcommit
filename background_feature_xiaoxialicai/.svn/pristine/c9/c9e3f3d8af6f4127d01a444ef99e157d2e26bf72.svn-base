<?php
namespace Prj\Check;
/**
 * Created by PhpStorm.
 * User: tang.gaohang
 * Date: 2016/2/23
 * Time: 10:49
 */
class DayWithdraw extends Base{

    protected static $cmd = 'dayWithdraw';
    protected static $tbname = 'tb_dayWithdraw';
    protected static $fieldsMap = [
        'sn'=>'SN',
        'file'=>'path',
    ];

    public function getLocalData(){
        //取本地数据
        $where = [
            'amountFlg'=>\Prj\Consts\OrderType::withdraw,
            'LEFT(payTime,8)'=>$this->ymd,
            'orderStatus'=>\Prj\Consts\OrderStatus::done,
        ];
        return \Prj\Data\Withdraw::loopAll($where);
    }

    protected function updateData($data){
        $tmp = [
            'userIdLocal'=>$data['userId'],
            'amountLocal'=>$data['amountAbs'],
            'poundageLocal'=>$data['poundage'],
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
            'poundageLocal'=>$data['poundage'],
            'diff'=>'pay_miss',
            'updateTime'=>date('YmdHis'),
            'haveLocal'=>1,
        ];
        if(empty($tmp['sn']))return;
        return $this->DB()->addRecord(self::$tbname,$tmp);
    }

    public static function addRecords($data){
        $tmp = new self(date('Ymd'));
        $data['ymd'] = $tmp->ymd;
        return $tmp->addData($data);
    }
}
