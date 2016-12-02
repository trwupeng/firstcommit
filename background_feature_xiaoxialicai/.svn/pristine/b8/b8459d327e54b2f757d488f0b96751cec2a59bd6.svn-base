<?php
namespace Prj\Data;
/**
 * 充值订单
 * 订单号19位长度，尾数（4位）同userId的尾数
 * xx----------------- 订单分类
 * --xxxxxxxxxx------- 时间戳（秒）
 * ------------xxx---- 时间 毫秒
 * ---------------xxxx 分表id,同userId的尾数
 * @author simon.wang <hillstill_simon@163.com>
 */
class Recharges extends \Sooh\DB\Base\KVObj{
	/**
	 * 创建一个指定userId的充值或提现订单
	 * @param string $userId 
	 * @param string $amount 金额
	 * @param string $bankAbs  银行代码 ICBC
	 * @param string $bankCard 银行卡
	 * @param string $payway 支付通道，默认0
	 * @return \Prj\Data\Recharges or null on failed
	 */
	public static function addOrders($userId,$amount,$bankAbs,$bankCard,$amountFlg = \Prj\Consts\OrderType::recharges,$payway=0)
	{
		$dt = \Sooh\Base\Time::getInstance();
		$ordersIdBase = substr($userId,-4);
		for($retry=0;$retry<10;$retry++){
			list($sec,$ms) = explode('.', microtime(true));
			$ordersId = \Prj\Consts\OrderType::recharges.$sec.substr($ms,0,3).$ordersIdBase;
			$tmp = self::getCopy($ordersId);
			$tmp->load();
			if(!$tmp->exists()){
				$tmp->setField('userId', $userId);
				$tmp->setField('amount', $amount);
				$tmp->setField('amountAbs', abs($amount));
				$tmp->setField('amountFlg', $amountFlg);
				$tmp->setField('orderTime', $dt->ymdhis());
				$tmp->setField('payTime', 0);
				$tmp->setField('orderStatus', \Prj\Consts\OrderStatus::created);
				$tmp->setField('payCorp', $payway);
				$tmp->setField('bankAbs', $bankAbs);
				$tmp->setField('bankCard', $bankCard);
				return $tmp;
			}
			self::freeAll($tmp->getPKey());
		}
		return null;
	}

    /**
     * 查询所有资料
     * By Hand
     */
    public static function loopAll($where)
    {
        $o = self::getCopy('');
        $rs = $o->loopFindRecords($where);
        return $rs;
    }

    /**
     * 分页查询
     */
    public static function paged($userId,\Sooh\DB\Pager $pager,$ymdStart,$ymdEnd,$orderBy = ' rsort orderTime ')
    {
        $where['userId'] = $userId;
        $where['orderTime]'] = $ymdStart;
        $where['orderTime['] = $ymdEnd;
        $o = self::getCopy($userId);
        $db = $o->db();
        $tb = $o->tbname();
        $total = $db->getRecordCount($tb,$where);
        $pager->init($total);
        $rs = $db->getRecords($tb,'*',$where,$orderBy,$pager->page_size,$pager->rsFrom());
        return $rs;
    }
	/**
	 * 
	 * @param type $status
	 * @return \Prj\Data\Recharges
	 */
	public function updStatus($status){
		$this->setField('orderStatus', $status);
		return $this;
	}

    /**
     * 当月提现次数
     * By Hand
     */
    public static function getTimesWithdrawingByUserId($userId)
    {
        $where = [
            'userId'=>$userId,
            'amountFlg'=>\Prj\Consts\OrderType::withdraw,
            'orderStatus'=>[
                \Prj\Consts\OrderStatus::waiting,
                \Prj\Consts\OrderStatus::done,
                \Prj\Consts\OrderStatus::waitingGW,
            ],
            'left(orderTime,6)'=>date('Ym'),
        ];
        $o = self::getCopy('');
        $db = $o->db();
        $tb = $o->tbname();
        $times = $db->getRecordCount($tb,$where);
        return $times;
    }

    /**
     * 提现
     * By Hand
     * 指定日期已提 及 在提的金额总和
     * 用于比对用户的当天提现限额
     * 按到账日期计算
     */
    public static function getAmountWithdrawingByYmd($userId,$ymd)
    {
        $total = 0;
        $where = [
            'userId'=>$userId,
            'amountFlg'=>\Prj\Consts\OrderType::withdraw,
            'orderStatus'=>[
                \Prj\Consts\OrderStatus::waiting,
                \Prj\Consts\OrderStatus::done,
            ],
            'withdrawYmd'=>$ymd,
        ];
        $o = self::getCopy($userId);
        $db = $o->db();
        $tb = $o->tbname();
        $rs = $db->getRecords($tb,'userId,amountAbs,withdrawYmd',$where);
        if(empty($rs))
        {
            return $total;
        }
        else
        {
            foreach($rs as $v)
            {
                $total+=$v['amountAbs'];
            }
            return $total;
        }
    }

    /**
     * 按申请日期计算总额
     * @param $userId
     * @param $ymd
     * @return int
     */
    public static function getAmountWithdrawingByOrderTime($userId,$ymd){
        $total = 0;
        $where = [
            'userId'=>$userId,
            'amountFlg'=>\Prj\Consts\OrderType::withdraw,
            'orderStatus'=>[
                \Prj\Consts\OrderStatus::waiting,
                \Prj\Consts\OrderStatus::waitingGW,
                \Prj\Consts\OrderStatus::done,
                \Prj\Consts\OrderStatus::payed,
                \Prj\Consts\OrderStatus::failed,
            ],
            'LEFT(orderTime,8)'=>$ymd,
        ];
        $o = self::getCopy($userId);
        $db = $o->db();
        $tb = $o->tbname();
        $rs = $db->getRecords($tb,'userId,amountAbs,withdrawYmd',$where);
        if(empty($rs))
        {
            return $total;
        }
        else
        {
            foreach($rs as $v)
            {
                $total+=$v['amountAbs'];
            }
            return $total;
        }
    }

    /**
     * 获得在提的金额总和
     */
    public static function getAmountWithdrawingByUserId($userId)
    {
        $where = [
            'userId'=>$userId,
            'amountFlg'=>0,
            'orderStatus'=>\Prj\Consts\OrderStatus::waiting,
        ];
        $o = self::getCopy($userId);
        $db = $o->db();
        $tb = $o->tbname();
        $rs = $db->getRecords($tb,'userId,amountAbs',$where);
        $total = 0;
        if(empty($rs))
        {
            return $total;
        }
        else
        {
            foreach($rs as $v)
            {
                $total+=$v['amountAbs'];
            }
            return $total;
        }
    }

	/**
	 * 
	 * @param string $account
	 * @param string $camefrom
	 * @return \Prj\Data\Recharges
	 */
	public static function getCopy($ordersId) {
		return parent::getCopy(['ordersId'=>$ordersId]);
	}

	
	//针对缓存，非缓存情况下具体的表的名字
	protected static function splitedTbName($n,$isCache)
	{
//		if($isCache)return 'tb_test_cache_'.($n % static::numToSplit());
//		else 
		return 'tb_recharges_'.($n % static::numToSplit());
	}
//指定使用什么id串定位数据库配置
	protected static function idFor_dbByObj_InConf($isCache)
	{
		return 'recharges'.($isCache?'Cache':'');
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
