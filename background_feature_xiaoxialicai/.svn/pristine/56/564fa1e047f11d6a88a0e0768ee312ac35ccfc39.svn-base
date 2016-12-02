<?php
namespace Prj\Check;
/**
 * Created by PhpStorm.
 * User: tang.gaohang
 * Date: 2016/2/23
 * Time: 10:49
 */
class DayPaysplit extends Base{

    protected static $cmd = 'dayPaysplit';
    protected static $tbname = 'tb_dayPaysplit';
    protected static $fieldsMap = [
        'sn'=>'SN',
        'file'=>'path',
    ];
    //file：/sina/yyyymmdd/xxx, ymd:20150325, SN:1234123123, paycorp:101, userId:12314, amount:充值额(单位分), interest:利息（单位分） , waresId:123
    protected $fields = [
        'file','ymd','SN','paycorp','userId','amount','interest','waresId'
    ];

    public function getLocalData(){
        //取本地数据
        $where = [
            'lastReturnFundYmd'=>$this->ymd,
        ];
        $wares = \Prj\Data\Investment::getCopy('')->loopFindRecords($where);
        $records = [];
        if(!empty($wares)){
            foreach($wares as $v){
                $plans = \Prj\ReturnPlan\Std01\ReturnPlan::createPlan($v['returnPlan']);
                $planArr = $plans->getPlan(['realDateYmd'=>$this->ymd,'status'=>8]);
                if(empty($planArr))continue;
                foreach($planArr as $plan){
                    //var_log($plan,'plan >>> ');
                    $tmp = [
                        'sn'=>$plan['sn'],
                        'ymd'=>$this->ymd,
                        'waresId'=>$v['waresId'],
                        'userId'=>$v['userId'],
                        'amount'=>$plan['realPayAmount'],
                        'interest'=>$plan['realPayInterest'],
                        'ordersId'=>$v['ordersId']
                    ];
                    $records[$plan['sn']] = $tmp;
                }
            }
        }
        return $records;
    }

    /**
     * 本地数据更新入库
     */
    protected function updateData($data){
        $tmp = [
            'waresIdLocal'=>$data['waresId'],
            'userIdLocal'=>$data['userId'],
            'amountLocal'=>$data['amount'],
            'interestLocal'=>$data['interest'],
            'ordersIdLocal'=>$data['ordersId'],
            'diff'=>$data['diff'],
            'updateTime'=>date('YmdHis'),
            'haveLocal'=>1,
        ];
        $ret = $this->DB()->updRecords(self::$tbname,$tmp,['sn'=>$data['sn']]);
        return $ret;
    }

    protected function addData($data){
        $tmp = [
            'sn'=>$data['sn'],
            'ymd'=>$data['ymd'],
            'waresIdLocal'=>$data['waresId'],
            'userIdLocal'=>$data['userId'],
            'amountLocal'=>$data['amount'],
            'interestLocal'=>$data['interest'],
            'ordersIdLocal'=>$data['ordersId'],
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
