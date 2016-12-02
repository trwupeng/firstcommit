<?php
namespace Prj\Check;
/**
 * Created by PhpStorm.
 * User: tang.gaohang
 * Date: 2016/2/23
 * Time: 10:49
 */
class DayManage extends Base{

    protected static $cmd = 'dayManage';
    protected static $tbname = 'tb_dayManage';
    protected static $fieldsMap = [
        'sn'=>'SN',
        'file'=>'path',
    ];

    public function getLocalData(){
        //取本地数据
        $where = [
            'type'=>[\Prj\Consts\PayGW::tally_managementTrans,\Prj\Consts\PayGW::tally_managementConfirm],
            'statusCode'=>\Prj\Consts\Systally::pay_status,
            'LEFT(payYmd,8)'=>$this->ymd,
        ];
        $ret = \Prj\Data\Systally::loopFindRecords($where);
        if($ret){
            foreach($ret as $k=>$v){
                $ret[$k]['userId'] = 0;
            }
        }
        return $ret;
    }

    protected function updateData($data){
        $tmp = [
            'waresIdLocal'=>$data['waresId'],
            'amountLocal'=>$data['amount'],
            'type'=>$data['type'],
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
            'waresIdLocal'=>$data['waresId'],
            'amountLocal'=>$data['amount'],
            'type'=>$data['type'],
            'diff'=>'pay_miss',
            'updateTime'=>date('YmdHis'),
            'haveLocal'=>1,
        ];
        if(empty($tmp['sn']))return;
        return $this->DB()->addRecord(self::$tbname,$tmp);
    }
}
