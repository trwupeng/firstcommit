<?php
namespace Prj\Check;
/**
 * Created by PhpStorm.
 * User: tang.gaohang
 * Date: 2016/2/23
 * Time: 10:35
 */

class Base {

    protected static $cmd = '';
    protected static $class = '';
    protected $ymd = '19990101';
    protected $errors = [];
    protected $map = [];
    protected $gData = [];
    protected static $fieldsMap = [];
    protected $fields;

    public function __construct($ymd){
        $this->ymd = $ymd;
    }

    /**
     * 数据库实例
     */
    protected function DB(){
        return \Sooh\DB\Broker::getInstance();
    }

    protected function getDataFromPayGW(){
        //调用网关
        $class = get_called_class();
        $rpc = \Sooh\Base\Ini::getInstance()->get('noGW')?self::getRpcDefault('PayCK'):\Sooh\Base\Rpc\Broker::factory('PayCK');
        //$rpc = \Sooh\Base\Rpc\Broker::factory('PayCK');
        $sys = \Lib\Services\PayCK::getInstance($rpc);
        $data = [
            $this->ymd
        ];
        var_log($data,$class.'发送给网关的参数>>>>>>>>>>>>>>>>>>>>');
        $newApi = 1;
        try{
            $ret = $newApi?\Lib\Services\PayGWCmd::sendToPayGWCmd($class::$newCmd,$data):call_user_func_array([$sys,$class::$cmd],$data);
        }catch (\Sooh\Base\ErrException $e) {
            usleep(500000);
            error_log('#歇半秒重试#');
            try{
                $ret = $newApi?\Lib\Services\PayGWCmd::sendToPayGWCmd('funding_userIncome',$data):call_user_func_array([$sys,$class::$cmd],$data);
            }catch (\Sooh\Base\ErrException $e) {
                $code = $e->getCode();
                if ($code == 400 || $code == 500) {
                    $this->errors[] = $e->getMessage();
                }else{
                    $this->errors[] = 'gw_error';
                }
            }
        }

        if($newApi){
            if($ret['code'] == 400){
                $this->errors[] = $ret['msg'];
            }
            $arr = $ret['data'];
            if(!empty($arr)){
                if(!is_array($arr)){
                    $arr = json_decode($arr,true);
                }
                $this->gData = $arr;
                var_log($arr,get_called_class().' 从网关获取的数据>>>');
            }
            return $arr;
        }else{
            if(!empty($ret)){
                if(!is_array($ret)){
                    $ret = json_decode($ret,true);
                }
                $this->gData = $ret;
                var_log($ret,get_called_class().' 从网关获取的数据>>>');
            }
            return $ret;
        }
    }


    protected static function getRpcDefault($serviceName)
    {
        if($serviceName==='Rpcservices' ||$serviceName==='SessionStorage'){
            return null;
        }
        $flg = \Sooh\Base\Ini::getInstance()->get('RpcConfig.force');
        if($flg!==null){
            if($flg){
                error_log('force rpc for '.$serviceName);
                return \Sooh\Base\Rpc\Broker::factory($serviceName);
            }else{
                error_log('no rpc for '.$serviceName);
                return null;
            }
        }else{
            error_log('try rpc for '.$serviceName);
            return \Sooh\Base\Rpc\Broker::factory($serviceName);
        }
    }

    /**
     * 从网关拉取对账数据存入数据库
     */
    public static function saveData($ymd){
        self::$class = get_called_class();
        $day = new self::$class($ymd);
        $day->gData = $day->getDataFromPayGW();
        $day->insertToDB();
        return $day;
    }

    /**
     * 从本地拉取数据比对入库
     * @param $ymd
     * @return mixed
     */
    public static function check($ymd){
        self::$class = get_called_class();
        $class = self::$class;
        $day = new $class($ymd);
        //$day = new self($ymd);//测试

        $data = $day->getLocalData();
        //var_log($data,'从本地获取的数据>>>');
        //插入数据库
        foreach($data as $local){
            if($day->snField){
                $sn = $local[$day->snField];
            }else{
                $sn = $local['sn']?$local['sn']:current($local);
            }
            $rs = $day->DB()->getRecord($class::$tbname,'*',['sn'=>$sn]);
            $local['sn'] = $sn;
            $local['ymd'] = $ymd;
            $local['haveLocal'] = 1;

            if(empty($rs)){
                $ret = $day->addData($local);
            }else{
                $diff = '';
                if(empty($rs['paycorp'])){
                    $diff = 'pay_miss';
                }else{
                    $diff = $day->fieldsCheck($local,$rs);
                }
                $local['diff'] = $diff;
                $local['sn'] = $sn;
                //var_log($local,'$local >>>');
                $ret = $day->updateData($local);
            }
        }
        return $day;
    }

    /**
     * 数据入库前对比
     * fromLocal = 1 数据来源于本地
     * formLocal = 0 数据来源于支付网关
     * @param $data
     * @param $dbData
     * @param int $fromLocal
     * @return string
     */
    protected function fieldsCheck($data,$dbData,$fromLocal = 1){
        $diff = '';
        if($dbData['ymd']!=$this->ymd)$diff.='error_ymd,';
        if($fromLocal){
            if(empty($dbData['havePay']))return 'pay_miss';
            if($data['waresId'] > 0 && $dbData['waresId']!=$data['waresId'])$diff.='error_waresId,';
            if($dbData['amountExtra']!=$data['amountExt'])$diff.='error_amountExt,';
            if($data['userId'] > 0 && $dbData['userId']!=$data['userId'])$diff.='error_userId,';
            if($data['interest'] > 0 && $dbData['interest']!=$data['interest'])$diff.='error_interest,';
            if(isset($data['amountAbs'])){
                if($dbData['amount']!=$data['amountAbs'])
                $diff.='error_amount,';
            }elseif(isset($data['amount'])){
                if($dbData['amount']!=$data['amount'])
                $diff.='error_amount,';
            }
            if($dbData['borrowerId']!=$data['borrowerId'])$diff.='error_borrowerId,';
        }else{
            if(empty($dbData['haveLocal']))return 'local_miss';
            if($data['waresId'] > 0 && $dbData['waresIdLocal']!=$data['waresId'])$diff.='error_waresId,';
            if($dbData['amountExtraLocal']!=$data['amountExt'])$diff.='error_waresId,';
            if($data['userId'] > 0 && $dbData['userIdLocal']!=$data['userId'])$diff.='error_userId,';
            if($data['interest']>0 && $dbData['interestLocal']!=$data['interest'])$diff.='error_interest,';
            if($dbData['amountLocal']!=$data['amountAbs'] && isset($data['amountAbs'])){
                $diff.='error_amount,';
            }elseif($dbData['amountLocal']!=$data['amount'] && isset($data['amount'])){
                $diff.='error_amount,';
            }
            if($dbData['borrowerId']!=$data['borrowerId'])$diff.='error_borrowerId,';
        }
        var_log($diff,' diff '.$data['sn'].'>>> ');
        return $diff;
    }

    /**
     * 网关的数据插入数据库
     */
    protected function insertToDB(){
        $class = get_called_class();
        $db = \Sooh\DB\Broker::getInstance();
        if(!empty($this->gData)){
            foreach($this->gData as $v){
                $data = $this->fieldsSwift($v);
                if(empty($data['sn']))continue;
                $data['updateTime'] = date('YmdHis');
                $data['diff'] = 'local_miss';
                $data['havePay'] = 1;
                $record = $db->getRecord($class::$tbname,'*',['sn'=>$data['sn']]);
                if(!empty($record)){
                    //todo 比对
                    $data['diff'] = $this->fieldsCheck($data,$record,0);
                    try{
                        $ret = $db->updRecords($class::$tbname,$data,['sn'=>$data['sn']]);
                    }catch (\ErrorException $e){
                        var_log("[error]".$e->getMessage());
                    }
                }else{
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

    /**
     * 字段匹配
     */
    protected function fieldsSwift($data){
        $class = get_called_class();
        if($this->fields){
            foreach($data as $k=>$v){
                if(!in_array($k,$this->fields)){
                    unset($data[$k]);
                }
            }
        }

        foreach($class::$fieldsMap as $k=>$v){
            $data[$k] = $data[$v];
            unset($data[$v]);
        }
        return $data;
    }

    /**
     * 对账审核
     */
    public static function doCheck($sn,$check,$exp = ''){
        $class = get_called_class();
        $tbname = $class::$tbname;
        $db = \Sooh\DB\Broker::getInstance();
        $ret = $db->updRecords($tbname,['checkk'=>$check,'exp'=>$exp],['sn'=>$sn]);
        return $ret;
    }

    /**
     *
     */
    public static function getDataFromDB($where = []){
        $class = get_called_class();
        $tbname = $class::$tbname;
        $db = \Sooh\DB\Broker::getInstance();
        return $db->getRecords($tbname,'*',$where);
    }
}
