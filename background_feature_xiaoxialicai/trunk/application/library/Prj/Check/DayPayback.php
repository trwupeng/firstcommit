<?php
namespace Prj\Check;
/**
 * Created by PhpStorm.
 * User: tang.gaohang
 * Date: 2016/2/23
 * Time: 10:49
 */
class DayPayback extends Base{

    protected static $cmd = 'dayPayback';
    protected static $tbname = 'tb_dayPayback';
    protected static $fieldsMap = [
        'sn'=>'SN',
        'file'=>'path',
    ];

    public function getLocalData(){
        //取本地数据
        $where = [
            'lastPaybackYmd'=>$this->ymd,
        ];
        $wares = \Prj\Data\Wares::getCopy('')->loopFindRecords($where);
        $records = [];
        if(!empty($wares)){
            foreach($wares as $v){
                $plans = \Prj\ReturnPlan\All01\ReturnPlan::createPlan($v['returnPlan']);
                $planArr = $plans->getPlan(['realDateYmd'=>$this->ymd]);
                if($planArr){
                    foreach($planArr as $plan){
                        $tmp = [
                            'sn'=>$plan['sn'],
                            'ymd'=>$this->ymd,
                            'waresId'=>$v['waresId'],
                            'borrowerId'=>$v['borrowerId'],
                            'amount'=>$plan['realPay'],
                        ];
                        $records[$plan['sn']] = $tmp;
                    }
                }

            }
        }
        return $records;
    }

    protected function updateData($data){
        $tmp = [
            'waresIdLocal'=>$data['waresId'],
            'borrowerIdLocal'=>$data['borrowerId'],
            'amountLocal'=>$data['amount'],
            'diff'=>$data['diff'],
            'updateTime'=>date('YmdHis'),
            'haveLocal'=>1,
        ];
        return $this->DB()->updRecords(self::$tbname,$tmp,['sn'=>$data['sn']]);
    }

    protected function addData($data){
        $borrowerIdLocal = empty($data['borrowerId'])?0:$data['borrowerId'];
        $tmp = [
            'sn'=>$data['sn'],
            'ymd'=>$data['ymd'],
            'waresIdLocal'=>$data['waresId'],
            'amountLocal'=>$data['amount'],
            'borrowerIdLocal'=>$borrowerIdLocal,
            'diff'=>'pay_miss',
            'updateTime'=>date('YmdHis'),
            'haveLocal'=>1,
        ];
        var_log($tmp,'tmp >>> ');
        if(empty($tmp['sn']))return;
        return $this->DB()->addRecord(self::$tbname,$tmp);
    }

    public static function addRecords($data){
        $tmp = new self(date('Ymd'));
        $data['ymd'] = $tmp->ymd;
        return $tmp->addData($data);
    }
}
