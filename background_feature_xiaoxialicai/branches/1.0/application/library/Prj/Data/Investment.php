<?php
namespace Prj\Data;
/**
 * 投资订单
 * 订单号19位长度，尾数（4位）同waresId的尾数
 * xx----------------- 订单分类
 * --xxxxxxxxxx------- 时间戳（秒）
 * ------------xxx---- 时间 毫秒
 * ---------------xxxx 分表id,同waresId的尾数
 * @author simon.wang <hillstill_simon@163.com>
 */
class Investment extends \Sooh\DB\Base\KVObj{
	/**
	 * 创建一个购买指定waresId的订单
	 * @param string $waresId
	 * @param string $userId
	 * @param int $amount 金额
	 * @return \Prj\Data\Investment or null on failed
	 */
	public static function addOrders($waresId,$userId,$amount,$more=array())
	{
		$dt = \Sooh\Base\Time::getInstance();
		$ordersIdBase = substr($userId,-4);
		for($retry=0;$retry<10;$retry++){
			list($sec,$ms) = explode('.', microtime(true));
			$ordersId = \Prj\Consts\OrderType::investment.$sec.substr($ms,0,3).$ordersIdBase;
			$tmp = self::getCopy($ordersId);
			$tmp->load();
			if(!$tmp->exists()){
				$tmp->setField('waresId', $waresId);
				$tmp->setField('userId', $userId);
				$tmp->setField('amount', $amount);
//				$tmp->setField('interest', 0);
//				$tmp->setField('amountExt', 0);
//				$tmp->setField('amountFake', 0);
//				$tmp->setField('interestExt', 0);
//				$tmp->setField('extDesc', '');
//				$tmp->setField('vouchers', '');
				foreach($more as $k=>$v){
					$tmp->setField($k, $v);
				}

				$tmp->setField('descCreate', \Prj\Misc\OrdersVar::$introForUser);
				$tmp->setField('codeCreate', \Prj\Misc\OrdersVar::$introForCoder);
				$tmp->setField('orderTime', $dt->ymdhis());
				$tmp->setField('orderStatus', \Prj\Consts\OrderStatus::abandon);

				try{
					//$tmp->update();
					return $tmp;
				} catch (\Exception $ex) {
					$err = $ex->getMessage();
				}
			}
			self::freeAll($tmp->getPKey());
		}
		\Sooh\Base\Log\Data::getInstance()->error('create investment orders failed:'.$err);
		return null;
	}
    //查询[用户]订单列表
    public static function pager($userId, $pager,$ymdStart,$ymdEnd,$where = array(),$order = null ,$lastPage = [])
    {
        if(!empty($userId))$where['userId'] = $userId;
        if(!empty($ymdStart))$where['orderTime]'] = $ymdStart;
        if(!empty($ymdEnd))$where['orderTime['] = $ymdEnd;
        if(empty($order))
        {
            $order = " rsort orderTime ";
        }
        if(!empty($userId))
        {
            $obj = self::getCopy($userId);
            $db = $obj->db();
            $tb = $obj->tbname();
            $pager->init($db->getRecordCount($tb,$where), -1);
            $rs = $db->getRecords($tb,'*',$where,$order,$pager->page_size,$pager->rsFrom());
        }
        else
        {
            $pager->init(self::loopGetRecordsCount($where), -1);
            //$rs = self::loopFindRecords($where);
            var_log($lastPage,'>>>>>>>>>>>>>');
            if($pager->pageid()!=1){
                $where = $lastPage;
            }else{
                $where = ['where'=>$where];
            }
            $rs = self::loopGetRecordsPage(['orderTime'=>'rsort'],$where,$pager);
        }

        return $rs;
    }
    //查询所有订单列表
    public static function loopAll($where)
    {
        $obj = self::getCopy('');
        $rs = $obj->loopFindRecords($where);
        return $rs;
    }
    //查询订单详情
    public static function findMine($ordersId,$userId)
    {
        $obj = self::getCopy($ordersId);
        $obj->load();
        //var_log($obj);
        if(!$obj->exists())
        {
            error_log('error>>>'.__FILE__.'不存在的订单！');
            return null;
        }
        if($obj->getField('userId',true)!=$userId)
        {
            error_log('error>>>'.__FILE__.'别人的订单！');
            return null;
        }
        return $obj->dump();
    }
    //查询xx标的的订单列表
    public static function pagerByWaresId($waresId,$where = array())
    {
        $obj = self::getCopy('');
        $where['waresId'] = $waresId;
        $rs = $obj->loopFindRecords($where);
        return $rs;
    }

    //查询用户的购买总额
    public static function getAmountByUserId($userId)
    {
        $where = [
            'userId'=>$userId,
            'orderStatus'=>\Prj\Consts\OrderStatus::$running,
        ];
        $o = self::getCopy($userId);
        $db = $o->db();
        $tb = $o->tbname();
        $rs = $db->getRecords($tb,'userId,amount,amountExt',$where);
        if(empty($rs))
        {
            return 0;
        }
        else
        {
            $total = 0;
            foreach($rs as $v)
            {
                $total+=$v['amount'];
                $total+=$v['amountExt'];
            }
            return $total;
        }
    }

    //查询用户的持有资产
    public static function getHoldingAssetsByUserId($userId)
    {
        $arr = \Prj\Consts\OrderStatus::$running;
        unset($arr[array_search(\Prj\Consts\OrderStatus::waiting,$arr)]);
        unset($arr[array_search(\Prj\Consts\OrderStatus::done,$arr)]);
        $where = [
            'userId'=>$userId,
            'orderStatus'=>$arr,
        ];
        $o = self::getCopy($userId);
        $db = $o->db();
        $tb = $o->tbname();
        $rs = $db->getRecords($tb,'userId,amount,amountExt',$where);
        if(empty($rs))
        {
            return 0;
        }
        else
        {
            $total = 0;
            foreach($rs as $v)
            {
                $total+=$v['amount'];
                $total+=$v['amountExt'];
            }
            return $total;
        }
    }

    //返回用户投资次数
    public static function getCountByUserId($userId)
    {
        $where = [
            'userId'=>$userId,
            'orderStatus'=>\Prj\Consts\OrderStatus::$running,
        ];
        $invest = self::getCopy($userId);
        $db = $invest->db();
        $tb = $invest->tbname();
        return $db->getRecordCount($tb,$where)-0;
    }

    public function createReturnPlan()
    {
        $this->load();
        $rp = $this->getField('returnPlan');
        $ordersId = $this->getField('ordersId');
        if(!empty($rp))return "已经存在回款计划";
        try{
            $returnPlan = \Prj\ReturnPlan\Std01\ReturnPlan::calendar($ordersId);
        }catch (\ErrorException $e){
            return $e->getMessage();
        }
        $nextYmd = $returnPlan->getYmdNext();
        var_log($nextYmd,'nextYmd>>>>>>>>>>>');
        try{
            $this->setField('returnPlan',$returnPlan->decode());
            $this->setField('orderStatus',\Prj\Consts\OrderStatus::going); //正常回款中
            if($nextYmd)$this->setField('returnNext',$nextYmd);
            $this->update();
        }catch (\ErrorException $e){
            return $e->getMessage();
        }
        return null;
    }

    public static function getCountByWaresId($waresId){
        return self::loopGetRecordsCount(['waresId'=>$waresId,'orderStatus'=>\Prj\Consts\OrderStatus::$running]);
    }

	/**
	 * 
	 * @param int $statusCode
	 * @return \Prj\Data\Investment
	 */
	public function updStatus($statusCode)
	{
		$this->setField('orderStatus', $statusCode);
		return $this;
	}
	/**
	 * 
	 * @param string $account
	 * @param string $camefrom
	 * @return \Prj\Data\Investment
	 */
	public static function getCopy($ordersId) {
		return parent::getCopy(['ordersId'=>$ordersId]);
	}

	
	//针对缓存，非缓存情况下具体的表的名字
	protected static function splitedTbName($n,$isCache)
	{
//		if($isCache)return 'tb_test_cache_'.($n % static::numToSplit());
//		else 
		return 'tb_investment_'.($n % static::numToSplit());
	}
//指定使用什么id串定位数据库配置
	protected static function idFor_dbByObj_InConf($isCache) {
		return 'investment'.($isCache?'Cache':'');
	}
	//针对缓存，非缓存情况下具体的表的名字

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
}
