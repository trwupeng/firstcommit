<?php
namespace Prj\Check;
/**
 * Created by PhpStorm.
 * User: tang.gaohang
 * Date: 2016/2/23
 * Time: 10:49
 */
class DayLoan extends Base{

    protected static $cmd = 'dayLoan';
    protected static $tbname = 'tb_dayLoan';
    protected static $fieldsMap = [ //网关的字段映射
        'sn'=>'SN',
        'file'=>'path',
    ];

    protected $snField = 'paySn';  //作为SN的字段名称


    public function getLocalData(){
        //取本地数据
        $where = [
            'LEFT(payYmd,8)'=>$this->ymd,
            'payStatus'=>\Prj\Consts\PayGW::success,
        ];
        $ret = \Prj\Data\Wares::getCopy('')->loopFindRecords($where);
        //===
        if($ret){
            foreach($ret as $k=>$v){
                $ret[$k]['sn'] = $ret[$k]['paySn'];
                $ret[$k]['amount'] = $ret[$k]['realRaise']+$ret[$k]['payGift']-$ret[$k]['managementTrans'];
            }
        }
        //===
        return $ret;
    }

    protected function updateData($data){
        $data['sn'] = $data['paySn'];
        $tmp = [
            'waresIdLocal'=>$data['waresId'],
            'amountLocal'=>$data['realRaise']+$data['payGift']-$data['managementTrans'],
            'borrowerIdLocal'=>$data['borrowerId'],
            'diff'=>$data['diff'],
            'updateTime'=>date('YmdHis'),
            'haveLocal'=>1,
        ];
        return $this->DB()->updRecords(self::$tbname,$tmp,['sn'=>$data['sn']]);
    }

    protected function addData($data){
        $data['sn'] = $data['paySn'];
        $tmp = [
            'sn'=>$data['sn'],
            'ymd'=>$data['ymd'],
            'waresIdLocal'=>$data['waresId'],
            'amountLocal'=>$data['realRaise']+$data['payGift']-$data['managementTrans'],
            'borrowerIdLocal'=>$data['borrowerId'],
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
