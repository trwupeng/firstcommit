<?php
namespace Prj\Data;
/**
 * 充值订单
 * 订单号19位长度，尾数（4位）同waresId的尾数
 * xx----------------- 订单分类
 * --xxxxxxxxxx------- 时间戳（秒）
 * ------------xxx---- 时间 毫秒
 * ---------------xxxx 分表id,同waresId的尾数
 * @author simon.wang <hillstill_simon@163.com>
 */
class Vouchers extends \Sooh\DB\Base\KVObj{

	/*
	 * @param \Sooh\DB\Pager $pager
	 * @param array where
	 * @param string $order
	 */
//	public static function paged($pager, $where = [], $order = null, $fields = '*') {
//		$rs = self::loopGetRecordsPage(array('timeCreate'=>'rsort','subkey'=>'voucherId'),array('where'=>$where,), $pager);
//
//        if(!empty($where['userId']))
//        {
//            $sys = self::getCopy($where['userId']);
//            $db = $sys->db();
//            $tb = $sys->tbname();
//
//            $maps = [
////			'voucherType' => [\Prj\Consts\Voucher::type_fake, \Prj\Consts\Voucher::type_yield],
////			'statusCode]' => 0
//            ];
//            $maps = array_merge($maps, $where);
//            $pager->init($db->getRecordCount($tb, $maps), -1);
//            if (empty($order)) {
//                $order = 'sort statusCode rsort timeCreate';
//            } else {
//                $order = str_replace('_', ' ', $order);
//            }
//
//            $rs = $db->getRecords($tb, $fields, $maps, $order, $pager->page_size, $pager->rsFrom());
//        }
//        else
//        {
//            $pager->init(self::loopGetRecordsCount($where), -1);
//            $rs = self::loopFindRecords($where);
//        }
//
//		return $rs;
//	}

	/**
	 * 全库分页
	 * @param array  $where
	 * @param int    $pageId
	 * @param array $lastPage
	 * @param int    $pageSize
	 * @param string  $order
	 * @return array
	 */
	public static function pagerByLoop($where, $pageId = 1, $lastPage = [], $pageSize = 10, $order = '') {
		if (!empty($lastPage) && is_string($lastPage)) {
			$lastPage = json_decode($lastPage, true);
		}
		$map = $lastPage ? : $where;
		$pager = new \Sooh\DB\Pager($pageSize);
		$count = self::loopGetRecordsCount($where);
		$totalPages = ceil($count / $pageSize);
		$pager->init($count, $pageId);

		$records = self::loopGetRecordsPage($order, $map, $pager);
		return $records;
	}

	/**
	 * 
	 * @param string $userId 
	 * @param int $flgUsed -1不限,0未使用,1已使用
	 * @return type
	 */
	public function counts($userId,$flgUsed)
	{
		$where=['userId'=>$userId];
		switch($flgUsed) {
			case 1:$where['statusCode']=\Prj\Consts\Voucher::status_used;break;
			case 0:$where['statusCode']=\Prj\Consts\Voucher::status_unuse;break;
		}
		return $this->db()->getRecordCount($this->tbname(), $where);
	}
    /*
    public static function getVouchers($pager,$where = array(),$orderBy = '')
    {
        $obj = self::getCopy('');
        $db = $obj->db();
        $tb = $obj->tbname();
        if(empty($orderBy))
        {
            $orderBy = " rsort dtExpired ";
        }
        $pager->init($db->getRecordCount($tb,$where),-1);
        $rs = $db->getRecords($tb,'*',$where,$orderBy,$pager->page_size,$pager->rsFrom());
        return $rs;
    }
    */
	/**
	 * 返回该券是否能在购买指定wares的时候使用(类型，类型，标签限制，限额)
	 * @param \Prj\Data\Wares $wares
	 * @param int $amount
	 * @return bool
	 */
	public function isUsableFor($wares,$amount,$days=0)
	{
        if(is_scalar($wares)){
            $wares = \Prj\Data\Wares::getCopy($wares);
            $wares->load();
        }
		if($this->getField('statusCode')-0!==\Prj\Consts\Voucher::status_unuse){
            error_log('error:error statusCode');
            \Prj\Consts\Voucher::$isUsableForMsg = 'voucher_status';
			return false;
		}

		$cmp = $this->getField('limitsShelf',true);
		if(!empty($cmp)){
			if(strpos($cmp, $wares->getField('shelfId'))===false){
                var_log('error:voucher_limit_shelf');
                \Prj\Consts\Voucher::$isUsableForMsg = 'voucher_limit_shelf';
				return false;
			}
		}
		
		$cmp = $this->getField('limitsType',true);
		if(!empty($cmp)){
			if(strpos($cmp, $wares->getField('mainType'))===false){
                var_log('error:voucher_limit_type');
                \Prj\Consts\Voucher::$isUsableForMsg = 'voucher_limit_type';
				return false;
			}
		}

		$cmp1 = $this->getField('limitsTag',true);
		if(!empty($cmp1)){
			$cmp2 = $wares->getField('tags');
			if(empty($cmp2)){
				return false;
			}else{
				$cmp1 = explode(',',$cmp1);
				$cmp2 = explode(',',$cmp2);
				$chk = array_intersect($cmp1, $cmp2);
				if(empty($chk)){
                    var_log('error:voucher_limit_tag');
                    \Prj\Consts\Voucher::$isUsableForMsg = 'voucher_limit_tag';
					return false;
				}
			}
		}
        //限制期限
        $limitDays = $this->getField('limitsDeadline');
        if(!empty($days) && !empty($limitDays))
        {
            if($days<$limitDays)
            {
                var_log('error:voucher_limit_deadline');
                \Prj\Consts\Voucher::$isUsableForMsg = 'voucher_limit_deadline';
                return false;
            }
        }
        //限制金额
        $limitAmount = $this->getField('limitsAmount');
        if(!empty($amount) && !empty($limitAmount))
        {
            if($limitAmount>$amount){
                var_log('error:voucher_limit_amount#'.$amount);
                \Prj\Consts\Voucher::$isUsableForMsg = 'voucher_limit_amount';
                return false;
            }
        }
		return true;
	}

    public static function checkLimit($wares,$arr = array())
    {
        if(is_scalar($wares)){
            $wares = \Prj\Data\Wares::getCopy($wares);
            $wares->load();
        }
        $cmp = $arr['limitsShelf'];
        if(!empty($cmp)){
            if(strpos($cmp, $wares->getField('shelfId'))===false){
                return false;
            }
        }

        $cmp = $arr['limitsType'];
        if(!empty($cmp)){
            if(strpos($cmp, $wares->getField('mainType'))===false){
                return false;
            }
        }

        $cmp1 = $arr['limitsTag'];
        if(!empty($cmp1)){
            $cmp2 = $wares->getField('tags');
            if(empty($cmp2)){
                return false;
            }else{
                $cmp1 = explode(',',$cmp1);
                $cmp2 = explode(',',$cmp2);
                $chk = array_intersect($cmp1, $cmp2);
                if(empty($chk)){
                    return false;
                }
            }
        }
        //限制期限
        $limitDays = $arr['limitsDeadline'];
        $days = $wares->getField('deadLine')*( ( strpos($wares->getField('dlUnit'),'月')===false )?1:30 );
        if(!empty($days) && !empty($limitDays))
        {
            if($days<$limitDays)
            {
                return false;
            }
        }
        return true;
    }

	/**
	 * 创建一个发放给指定userId的券
	 * @param string $userId
	 * @param string $type
	 * @param int $amount 金额
	 * @param int $expired 有效期（天）
	 * @return \Prj\Data\Vouchers or null on failed
	 */
	public static function newForUser($userId,$type,$amount,$expired=30,$limit = [])
	{
//		error_log('Data\Voucher::newForUser');
		$ordersIdBase = substr($userId,-4);
		$dt = \Sooh\Base\Time::getInstance();
		if(is_array($expired)){
			$expired = date('YmdHis',$expired['end']);
		}else{
			$expired =date('Ymd',$dt->timestamp($expired)).'235959';
		}
		for($retry=0;$retry<10;$retry++){
			list($sec,$ms) = explode('.', microtime(true));
			$ordersId = \Prj\Consts\OrderType::vouchers.$sec.substr($ms,0,3).$ordersIdBase;
			$tmp = self::getCopy($ordersId);
			$tmp->load();
			if(!$tmp->exists()){
                $tmp->setField('userId', $userId);
				$tmp->setField('voucherType', $type);
				$tmp->setField('amount', $amount);
				//$tmp->setField('limits', '');
				$tmp->setField('timeCreate', $dt->ymdhis());
				
				$tmp->setField('dtExpired', $expired);
				$tmp->setField('dtUsed', 0);
				$tmp->setField('orderId', 0);
				$tmp->setField('descCreate', \Prj\Misc\OrdersVar::$introForUser);
				$tmp->setField('codeCreate', \Prj\Misc\OrdersVar::$introForCoder);//poinst
				$tmp->setField('exp1', \Prj\Misc\OrdersVar::$explain1);
				$tmp->setField('exp2', \Prj\Misc\OrdersVar::$explain2);
				$tmp->setField('statusCode', \Prj\Consts\Voucher::status_abandon);  //发一张废弃的券
				$tmp->setField('createYmd', $dt->YmdFull);
                if(empty($limit['limitsAmount']))$tmp->setField('limitsAmount',10000); //发红包默认满100可用
                $tmp->update();
				if ($type == \Prj\Consts\Voucher::type_real) {
					\Prj\Data\User::updateExpiredRedpacket($userId, $expired);
				}
				return $tmp;
			}
			self::freeAll($tmp->getPKey());
		}
		return null;
	}

	/**
	 * 创建一个发放给指定用户的分享红包
	 * @param string $userId 用户ID
	 * @param string $type 类型
	 * @param integer $amount 金额
	 * @param string $vouchersId 券ID
	 * @param string $pid pid
	 * @param int $expired 有效期
	 * @return null|Vouchers
	 * @throws \ErrorException
	 * @throws \Exception
	 */
	public static function newForUserForChild($userId, $type, $amount, $vouchersId, $pid, $expired = 2)
//	public static function newForUserForChild($userId, $type, $amount, $vouchersId, $pid, $uniqueId, $expired = 2)
	{
		$dt = \Sooh\Base\Time::getInstance();
		$ordersIdBase = substr($userId, -4);

		if (is_array($expired)) {
			$expired = date('YmdHis', $expired['end']);
		} else {
			$expired = date('Ymd', $dt->timestamp($expired)) . '235959';
		}

		for ($retry = 0; $retry < 10; $retry++) {
			list($sec, $ms) = explode('.', microtime(true));
			$ordersId = \Prj\Consts\OrderType::vouchers . $sec . substr($ms, 0, 3) . $ordersIdBase;
			$tmp      = self::getCopy($ordersId);
			$tmp->load();
			if (!$tmp->exists()) {
				$tmp->setField('userId', $userId);
				$tmp->setField('voucherType', $type);
				$tmp->setField('amount', $amount);
				//$tmp->setField('limits', '');
				$tmp->setField('timeCreate', $dt->ymdhis());
				$tmp->setField('dtExpired', $expired);
				$tmp->setField('dtUsed', 0);
				$tmp->setField('orderId', 0);
				$tmp->setField('descCreate', \Prj\Misc\OrdersVar::$introForUser);
				$tmp->setField('codeCreate', \Prj\Misc\OrdersVar::$introForCoder);//poinst
				$tmp->setField('exp1', \Prj\Misc\OrdersVar::$explain1);
				$tmp->setField('exp2', \Prj\Misc\OrdersVar::$explain2);
				$tmp->setField('statusCode', \Prj\Consts\Voucher::status_unuse);  //发一张可用的券
				$tmp->setField('pid', $pid);
				$tmp->setField('limitsAmount', 10000);
				//			$tmp->setField('uniqueId', $uniqueId);
				$tmp->setField('createYmd', $dt->YmdFull);
				$tmp->update();
				if ($type == \Prj\Consts\Voucher::type_real) {
					\Prj\Data\User::updateExpiredRedpacket($userId, $expired);
				}
				return $tmp;
			}
			self::freeAll($tmp->getPKey());
		}
		return null;
	}

    public function rebateNum(){
        return 3;
    }

    public static function sendRebateRedPacket($userId,$ymd){
        $tmp = self::getCopy($userId);
        $db = $tmp->db();
        $tbname = $tmp->tbname();
        $where1 = ['codeCreate'=>'firstBuyInvite','LEFT(timeCreate,8)'=>$ymd,'statusCode'=>\Prj\Consts\Voucher::status_freeze,'userId'=>$userId];
        $rs = $db->getRecords($tbname,'*',$where1,'rsort amount rsort timeCreate',$tmp->rebateNum());
        if(empty($rs)){
            return [];
        }else{
            $voucherIds = [];
            $amountTotal = 0;
            array_map(function($value)use(&$voucherIds,&$amountTotal){
                $voucherIds[] = $value['voucherId'];
                $amountTotal+=$value['amount'];
            },$rs);
            //var_log($voucherIds);

            $ret1 = $db->updRecords($tbname,['statusCode'=>\Prj\Consts\Voucher::status_unuse],['voucherId'=>$voucherIds]);
            try{
                $ret2 = $db->updRecords($tbname,['statusCode'=>\Prj\Consts\Voucher::status_abandon],$where1);
            }catch (\ErrorException $e){
                $db->updRecords($tbname,['statusCode'=>\Prj\Consts\Voucher::status_freeze],['voucherId'=>$voucherIds]);
                throw $e;
            }

            return [
                'vouchers'=>$voucherIds,
                'amountTotal'=>$amountTotal,
            ];
        }
    }

    public static function rebateRedPacketRollBack($userId,$ymd){
        var_log('[warning]返利红包回滚');
        $tmp = self::getCopy($userId);
        $db = $tmp->db();
        $tbname = $tmp->tbname();
        $where1 = ['codeCreate'=>'firstBuyInvite','LEFT(timeCreate,8)'=>$ymd,'userId'=>$userId];
        return $ret = $db->updRecords($tbname,['statusCode'=>\Prj\Consts\Voucher::status_freeze],$where1);
    }

	/**
	 * 设置状态为使用（回滚调用使用状态 \Prj\Consts\Voucher::status_unuse）
	 * @param type $orderId
	 * @param type $status
	 * @return \Prj\Data\Vouchers
	 */
	public function setUsed($orderId,$status=\Prj\Consts\Voucher::status_used)
	{
		$this->setField('statusCode', $status);
		$this->setField('orderId', $orderId);
		$this->setField('dtUsed', \Sooh\Base\Time::getInstance()->ymdhis());
		return $this;
	}

	/**
	 * 查找一条符合要求的记录
	 * @param array $map 条件数组
	 * @return bool
	 */
	public static function findOne($map)
	{
//		$sys = self::getCopy('');
//		$ret = $sys->db()->getRecord($sys->tbname(), '*', $map);
		$ret = self::loopFindRecords($map);
		if ($ret) {
			return current($ret);
		} else {
			return false;
		}
	}

	/**
	 * 获取一个母红包的领取记录
	 * @param string $pid 母红包ID
	 * @param \Sooh\DB\Pager $pager
	 */
	public static function getReceiveList($pid, $pager, $pagerId)
	{
		$map = [
			'pid' => $pid,
			'voucherType' => \Prj\Consts\Voucher::type_real,
		    'statusCode!' => \Prj\Consts\Voucher::status_abandon,
		];
//		$sys = self::getCopy('');
		$pager->init(self::loopGetRecordsCount($map), $pagerId);
//		$ret = $sys->db()->getRecords($sys->tbname(), '*', $map, 'rsort timeCreate', $pager->page_size, $pager->rsFrom());
		$ret = self::loopGetRecordsPage(['timeCreate' => 'rsort'], ['where' => $map], $pager);

		return $ret['records'];
	}

    /**
     * 券是否过期 1：是  0：否
     */
    public function isExpire()
    {
        $dtExpired = $this->getField('dtExpired');
        $nowTime = \Sooh\Base\Time::getInstance()->ymdhis();
        //error_log('error>>>>>>'.$nowTime.'/'.$dtExpired);
        return $nowTime>$dtExpired?true:false;
    }

    /**
     * 获取用户有效红包的总额
     * @param $userId
     * @return mixed
     */
    public static function getTotalByUserId($userId){
        $voucher = \Prj\Data\Vouchers::getCopy($userId);
        $voucher->load();
        $db = $voucher->db();
        $tb = $voucher->tbname();
        if($userId==90003837339748)var_log(\Sooh\Base\Time::getInstance()->ymdhis());
        $where = [
	        'userId'=>$userId,
            'voucherType'=>\Prj\Consts\Voucher::type_real,
	        'statusCode'=>\Prj\Consts\Voucher::status_unuse,
            'dtExpired>='=>\Sooh\Base\Time::getInstance()->ymdhis(),
        ];
        return $db->getRecord($tb,'sum(amount) as total',$where)['total']-0;
    }

	/**
	 * 
	 * @param string $voucherId
	 * @return \Prj\Data\Vouchers
	 */
	public static function getCopy($voucherId) {
		return parent::getCopy(['voucherId'=>$voucherId]);
	}

	
	//针对缓存，非缓存情况下具体的表的名字
	protected static function splitedTbName($n,$isCache)
	{
//		if($isCache)return 'tb_test_cache_'.($n % static::numToSplit());
//		else 
		return 'tb_vouchers_'.($n % static::numToSplit());
	}
//指定使用什么id串定位数据库配置
	protected static function idFor_dbByObj_InConf($isCache)
	{
		return 'vouchers'.($isCache?'Cache':'');
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
