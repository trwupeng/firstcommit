<?php
namespace Prj\Data;
/**
 * 标的
 * 标的ID 18位长度，尾数（4位）同shelfId的尾数
 *
 * @author simon.wang <hillstill_simon@163.com>
 */
class Wares extends \Sooh\DB\Base\KVObj{

    protected static function _creatID()
    {
        return time() . rand(100000, 999999);
    }

    protected $initSort = 'rsort sortval rsort vipLevel rsort timeStartPlan ';

	public static function createWare($fields)
	{
        var_log($fields,'fields>>>>>>>>>>');
		do{
            $waresId = self::_creatID();
            $ware = self::getCopy($waresId);
            $ware->load();
        }while($ware->exists());
        foreach($fields as $k=>$v){
            //var_log($ware);
            $ware->setField($k,$v);
        }
        if(empty($fields['shelfId']))$ware->setField('shelfId',\Prj\Consts\Wares::shelf_static);
        if(empty($fields['priceStep']))$ware->setField('priceStep',1);
        if(empty($fields['priceStart']))$ware->setField('priceStart',100);
        if(empty($fields['returnType']))$ware->setField('returnType',\Prj\Consts\ReturnType::single);
        if(empty($fields['interestStartType']))$ware->setField('interestStartType',\Prj\Consts\InterestStart::afterBuy);
        if(empty($fields['yieldStatic']))$ware->setField('yieldStatic',0.01);
        if(empty($fields['viewTPL']))$ware->setField('viewTPL','Std01');
        if(empty($fields['returnTPL']))$ware->setField('returnTPL','Std01');
        if(empty($fields['item']))$ware->setField('item','中华基金');
        if(empty($fields['introDisplay'])){
            $ware->setField('introDisplay',['a'=>'','b'=>[]]);
        }
        $ware->setField('statusCode',\Prj\Consts\Wares::status_new);
        $ware->setField('remain',$fields['amount']);
        return $ware;
	}

    public static function getWaresIdsByName($waresName){
        $records = self::loopFindRecords(['waresName*'=>'%'.$waresName.'%']);
        $ids = [];
        if($records){
            array_walk($records,function($v) use (&$ids){
                $ids[] = $v['waresId'];
            });
        }
        var_log($ids);
        return $ids;
    }
	/**
	 * 获取指定类型的标的列表
	 * @param int $shelfId
	 * @param \Sooh\DB\Pager $pager
	 */
	public static function paged($shelfId,\Sooh\DB\Pager $pager,$order = null,$where = [])
	{
		$sys = self::getCopy($shelfId);
		$db = $sys->db();
		$tb = $sys->tbname();
        //排序sort
        if(empty($order))
        {
            $order = $sys->initSort;
        }
        $where['shelfId'] = empty($where['shelfId'])?$shelfId:$where['shelfId'];
        if(empty($where['shelfId']))unset($where['shelfId']);
        $where['statusCode]'] = 10;

        $pager->init($db->getRecordCount($tb,$where), -1);

        if($sys->cacheWhenVerIDIs && $pager->pageid()==1){
            $tbCache = null;
            $dbCache = \Prj\Data\Wares::getDBAndTbName($tbCache, $sys->pkey,true);
            $rs = $dbCache->getRecords($tbCache,'*',$where,$order,$pager->page_size,$pager->rsFrom());
        }else{
            $rs = $db->getRecords($tb, '*', $where, $order, $pager->page_size,$pager->rsFrom());
        }
		return $rs;
	}

    /**
     * 检查数据的合法性
     */
    public static function check($fields){
        if($fields['ymdPayPlan']<strtotime($fields['timeStartPlan'])){
            throw new \ErrorException('error_date');
        }
        if($fields['priceStep']<0.01){
            throw  new \ErrorException('error_priceStep');
        }
        if($fields['returnType']==\Prj\Consts\ReturnType::byMonth){
            if($fields['dlUnit']!='月'){
                throw new \ErrorException('error_dlUnit');
            }else{
                if($fields['deadLine']!=1){
                    $planDay = strtotime('+'.($fields['deadLine']-1).'months',strtotime($fields['timeStartPlan']));
                    var_log(date('Ymd',$planDay),'>>>>>>>>>>>>>>>>');
                    if($fields['ymdPayPlan']<$planDay){
                        throw new \ErrorException('error_days');
                    }
                   
                }
            }
        }
    }

	public function fullname()
	{
		$i = $this->getField('waresSN');
		if($i>0){
			return $this->getField('waresName').'['.$i.']';
		}else{
			return $this->getField('waresName');
		}
	}
	/**
	 * 
	 * @param string $waresId
	 * @return \Prj\Data\Wares
	 */
	public static function getCopy($waresId) {
		return parent::getCopy(['waresId'=>$waresId]);
	}

    public function createReturnPlan(){
        $this->load();
        $waresId = $this->getField('waresId');
        $rp = $this->getField('returnPlan');
        if(!empty($rp))
        {
            var_log($rp,'rp>>>>>>>>>>>>');
            return '已经存在回款计划';
        }
        else
        {
            try{
                $returnPlan = \Prj\ReturnPlan\All01\ReturnPlan::calendar($waresId);
            }catch (\ErrorException $e){
                return $e->getMessage();
            }
            try{
                $this->setField('returnPlan',$returnPlan->decode());
                $this->update();
            }catch (\ErrorException $e){
                return $e->getMessage();
            }
            return null;
        }
    }

    /**
     * 获取所有的字段
     */
    public function getAllFields(){
        $db = $this->db();
        $tbname = $this->tbname();
        return $db->getRecord($tbname,'*',['waresId'=>$this->pkey['waresId']]);
    }

    /**
     * 更新
     * @param null $arr
     * @return int
     */
    public function updateFromArr($arr){
        $db = $this->db();
        $tbname = $this->tbname();
        return $db->updRecords($tbname,$arr,['waresId'=>$this->pkey['waresId']]);
    }
	/*
	//针对缓存，非缓存情况下具体的表的名字
	protected static function splitedTbName($n,$isCache)
	{
//		if($isCache)return 'tb_test_cache_'.($n % static::numToSplit());
//		else 
		return 'tb_wares_'.($n % static::numToSplit());
	}
	*/
//指定使用什么id串定位数据库配置
	protected static function idFor_dbByObj_InConf($isCache)
	{
		return 'default';
	}
	//针对缓存，非缓存情况下具体的表的名字


    public function getCount($where) //tgh
    {
        return static::loopGetRecordsCount($where);
    }
//	/**
//	 * 是否启用cache机制
//	 * cacheSetting=0：不启用
//	 * cacheSetting=1：优先从cache表读，每次更新都先更新硬盘表，然后更新cache表
//	 * cacheSetting>1：优先从cache表读，每次更新先更新cache表，如果达到一定次数，才更新硬盘表
//	 */
//	protected function initConstruct($cacheSetting=0,$fieldVer='iRecordVerID')
//	{
//		return parent::initConstruct($cacheSetting,$fieldVer);
//	}

    protected function initConstruct($cacheSetting=0,$fieldVer='iRecordVerID')
    {
        return parent::initConstruct($cacheSetting, $fieldVer);
    }

    protected static function splitedTbName($n,$isCache)
    {
        if($isCache){
            return 'tb_wares_0_ram';
        }else{
            return 'tb_wares_0';
        }
    }
    //复制unlock 加一句 $this->update() 刷新缓存表
    public function unlock(){
        $where = $this->pkey;
        $where[$this->fieldName_verid] = $this->r[$this->fieldName_verid];
        $ret = $this->db()->updRecords($this->tbname(), array($this->fieldName_verid=>$this->r[$this->fieldName_verid]+1,$this->fieldName_lockmsg=>''), $where);
        if($ret===1){
            $this->r[$this->fieldName_verid]++;
            $this->r[$this->fieldName_lockmsg]='';
            $this->update(); //刷新缓存表
            return true;
        }else{
            return false;
        }
    }
    //重写update 10分之
    public function update($callback=null){
        $this->setField('sortval',$this->getSort());
        parent::update($callback);
        $this->refreshCache();
    }

    /**
     * 刷新缓存表
     * @throws \ErrorException
     */
    public function refreshCache(){
        if($this->cacheWhenVerIDIs){
            $num = rand(0,19);
            if($num==0){
                $tbCache = null;
                $dbCache = static::getDBAndTbName($tbCache, $this->pkey,true);
                $rs1 = $this->db()->getRecords($this->tbname(),'*',['statusCode]'=>10,'shelfId'=>\Prj\Consts\Wares::shelf_static],$this->initSort,15);
                $rs2 = $this->db()->getRecords($this->tbname(),'*',['statusCode]'=>10,'shelfId'=>\Prj\Consts\Wares::shelf_static_float],$this->initSort,15);
                $all = array_merge($rs1,$rs2);
                $waresIds = [];
                foreach($all as $v){
                    $waresIds[] = $v['waresId'];
                }
                $delRet = $dbCache->delRecords($tbCache,['waresId!'=>$waresIds]);
                var_log('[warning]标的缓存表删除了'.$delRet.'条记录!');
            }
        }
    }

    protected function error($str,$code=400){
        throw new \ErrorException($str,$code);
    }

    public function trans(){
        $statusCode = $this->getField('statusCode');
        if($this->getField('payStatus') == \Prj\Consts\PayGW::accept){
            return $this->error('重复的请求');
        }
        if($statusCode!=\Prj\Consts\Wares::status_go){
            $this->error('error_status');
        }else if($this->getField('payStatus')==\Prj\Consts\PayGW::success){
            $this->error('have_success');
        }else{
            $fields = $this->dump();
            //调用网关
            $rpc = \Sooh\Base\Ini::getInstance()->get('noGW')?self::getRpcDefault('PayGWCmd'):\Sooh\Base\Rpc\Broker::factory('PayGWCmd');
            $sys = \Lib\Services\PayGWCmd::getInstance($rpc);
            $sn = time() . rand(100000, 999999);

            $this->setField('payGift',$fields['amount']-$fields['realRaise']);
            $this->setField('paySn',$sn);

            try {
                $data = [
                    $sn,
                    $fields['waresId'],
                    $fields['realRaise'],
                    $fields['amount']-$fields['realRaise'],
                    $fields['amount'],
                    $fields['managementTrans'],
                    $fields['borrowerId'],
                    $fields['borrowerId'],
                ];
                //var_log($data,'发给网关的参数>>>>>>>>>>>>>>>>>>>>>');
                //$ret = call_user_func_array([$sys,'trans'],$data);
                $ret = \Lib\Services\PayGWCmd::sendToPayGWCmd('trade_trans',$data); //新版接口  注意不要更新到正式服
            } catch (\Sooh\Base\ErrException $e) {
                $this->setField('exp',"网关错误:".$e->getMessage());
                try{
                    $this->update();
                }catch (\ErrorException $e){
                    var_log("[error]满标转账网关错误#sn:".$sn." waresId:".$fields['waresId']." error:".$e->getMessage());
                }
                $code = $e->getCode();
                if ($code == 400) {
                    $this->error($e->getMessage());
                } elseif ($code == 500) {
                    $this->error($e->getMessage());
                }
                // return $this->returnError('gw_error');
                $this->error(\Prj\Lang\Broker::getMsg('system.gw_error'));
            }

            try{
                $this->setField('retryUrl',\Prj\Misc\JavaService::$lastUrl);
                $this->setField('retryBtnShow',1);
            }catch (\ErrorException $e){
                error_log('error#'.$fields['waresId'].'#'.$e->getMessage());
            }

            if($ret['code']== 200)
            {
                $this->setField('payStatus',\Prj\Consts\PayGW::accept);
                $this->setField('payYmd',date('YmdHis'));
            }else{
                $this->setField('payStatus',\Prj\Consts\PayGW::failed);
                $this->setField('exp',$ret['reason']);
            }

            try{
                $this->update();
            }catch (\ErrorException $k){
                $this->error($k->getMessage());
            }

            return $ret;
        }
    }

    public static function getRpcDefault($serviceName)
    {
        return null;
    }

    public function getSort(){
        $statusCode = $this->getField('statusCode');
        $tags = $this->getField('tags');
        switch($statusCode){
            case \Prj\Consts\Wares::status_open :$first = 9;break; //未满标的
            case \Prj\Consts\Wares::status_ready :$first = 8;break; //等待上架
            case \Prj\Consts\Wares::status_go :$first = 7;break; //已满
            case \Prj\Consts\Wares::status_return :$first = 6;break; //还款中
            case \Prj\Consts\Wares::status_ahead :$first = 5;break; //已还清 提前还款
            case \Prj\Consts\Wares::status_close :$first = 5;break; //已还清
            default : $first = 1;
        }
        if($statusCode == \Prj\Consts\Wares::status_open){
            switch (true){
                case (strpos($tags,'新手')!==false && strpos($tags,'活动')!==false) : $second = 9;break;
                case strpos($tags,'活动')!==false : $second = 8;break;
                case strpos($tags,'新手')!==false : $second = 7;break;
                default : $second = 6;
            }
            $time = $this->getField('timeStartReal');
        }else{
            $second = 0;
            switch($statusCode){
                case \Prj\Consts\Wares::status_ready :$time = $this->getField('timeStartPlan');break; //等待上架
                case \Prj\Consts\Wares::status_go :$time = $this->getField('timeEndReal');break; //已满
                case \Prj\Consts\Wares::status_return :$time = $this->getField('payYmd');break; //还款中
                case \Prj\Consts\Wares::status_ahead :$time = date('YmdHis',strtotime($this->getField('lastPaybackYmd')));break; //已还清 提前还款
                case \Prj\Consts\Wares::status_close :$time = date('YmdHis',strtotime($this->getField('lastPaybackYmd')));break; //已还清
                default : $time = $this->getField('timeStartPlan');
            }
        }

        return $first.$second.$time;
    }


    public static function getNewSN($simName){
        $ware = self::getCopy('');
        $ware->load();
        $db = $ware->db();
        $tb = $ware->tbname();
        $length = mb_strlen($simName,'utf-8');
        $rs = $db->getRecord($tb,"waresSN",['waresNameSim'=>$simName,'statusCode]'=>-1],'rsort waresSN');
        //var_log(\Sooh\DB\Broker::lastCmd(),'last cmd >>>');
        return $rs['waresSN']+1;
    }
}
