<?php
namespace Prj\Data;
/**
 * 标的
 *
 * @author simon.wang <hillstill_simon@163.com>
 */
class RedPacketTally extends \Sooh\DB\Base\KVObj{
	/**
	 * 
	 * @param string $tallyId
	 * @return \Prj\Data\WalletTally
	 */
	public static function getCopy($tallyId) {
		return parent::getCopy(['tallyId'=>$tallyId]);
	}

	/**
	 * 添加红包流水
	 * @param string $userId
	 * @param number $old
	 * @param number $add
	 * @param string $orderId
	 * @param int $type
	 * @return \Prj\Data\WalletTally
	 */
	public static function addTally($userId,$old,$add,$orderId=0,$type=  \Prj\Consts\WalletTally::type_default)
	{
		$dt = \Sooh\Base\Time::getInstance();
		$idBase = substr($userId,-4);
		for($retry=0;$retry<10;$retry++){
			list($sec,$ms) = explode('.', microtime(true));
			$autoId = $sec.substr($ms,0,5).$idBase;
			$tmp = self::getCopy($autoId);
			$tmp->load();
			if(!$tmp->exists()){
				$tmp->setField('userId', $userId);
				$tmp->setField('orderId', $orderId);
				$tmp->setField('tallyType', $type);
				$tmp->setField('statusCode', \Prj\Consts\Tally::status_abandon);
				$tmp->setField('timeCreate', $dt->ymdhis());
				$tmp->setField('descCreate', \Prj\Misc\OrdersVar::$introForUser);
				$tmp->setField('codeCreate', \Prj\Misc\OrdersVar::$introForCoder);
				$tmp->setField('nOld', $old);
				$tmp->setField('nAdd', $add);
				$tmp->setField('nNew', $old+$add);
				return $tmp;
			}
			self::freeAll($tmp->getPKey());
		}
		return null;
	}

    /**
     * 红包流水
     * @param $userId
     * @param $pager
     * @param $ymdStart
     * @param $ymdEnd
     */
    public static function pager($userId, $pager,$ymdStart,$ymdEnd,$order=null)
    {
        $obj = self::getCopy('');
        $db = $obj->db();
        $tb = $obj->tbname();
        $where = array(
            "userId"=>$userId,
            "timeCreate]"=>$ymdStart,
            "timeCreate["=>$ymdEnd,
        );
        if(empty($order))
        {
            $order = " rsort timeCreate ";
        }
        $pager->init($db->getRecordCount($tb,$where), -1);
        $rs = $db->getRecords($tb,'*',$where,$order,$pager->page_size,$pager->rsFrom());
        return $rs;
    }
	/**
	 * 
	 * @param type $statusCode
	 * @return \Prj\Data\WalletTally
	 */
	public function updStatus($statusCode=\Prj\Consts\Tally::status_new)
	{
		$this->setField('statusCode', $statusCode);
		return $this;
	}

	//针对缓存，非缓存情况下具体的表的名字
	protected static function splitedTbName($n,$isCache)
	{
//		if($isCache)return 'tb_test_cache_'.($n % static::numToSplit());
//		else 
		return 'tb_redpackettally_'.($n % static::numToSplit());
	}
//指定使用什么id串定位数据库配置
	protected static function idFor_dbByObj_InConf($isCache)
	{
		return 'redpacket'.($isCache?'Cache':'');
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
