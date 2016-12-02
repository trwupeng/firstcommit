<?php
namespace Prj\Data;
/**
 * 积分流水日志
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class ShopPoints extends \Sooh\DB\Base\KVObj {
	/**
	 * 添加积分流水
	 * @param string $userId
	 * @param number $old
	 * @param number $add
	 * @param string $orderId
	 * @param int $type
	 * @return \Prj\Data\ShopPoints
	 */
	public static function addTally($userId,$old,$add,$orderId,$type)
	{
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
				$tmp->setField('statusCode', \Prj\Consts\Tally::status_new);
				$tmp->setField('timeCreate',  \Sooh\Base\Time::getInstance()->ymdhis());
				$tmp->setField('descCreate', \Prj\Misc\OrdersVar::$introForUser);
				$tmp->setField('codeCreate', \Prj\Misc\OrdersVar::$introForCoder);//poinst
				$tmp->setField('nOld', $old);
				$tmp->setField('nAdd', $add);
				$tmp->setField('nNew', $old+$add);
				return $tmp;
			}
			self::freeAll($tmp->getPKey());
		}
		return null;
	}


	/*
	 * @param \Sooh\DB\Pager $pager
	 * @param array where
	 * @param string $order
	 * @param string $fields
	 */
	public static function paged($pager, $where = [], $order = null, $fields = '*') {
		$sys = self::getCopy('');
		$db = $sys->db();
		$tb = $sys->tbname();

		$maps = [
//			'statusCode]' => 0
		];
		$maps = array_merge($maps, $where);
		$pager->init($db->getRecordCount($tb, $maps), -1);

		if (empty($order)) {
			$order = 'rsort timeCreate';
		} else {
			$order = str_replace('_', ' ', $order);
		}

		$rs = $db->getRecords($tb, $fields, $maps, $order, $pager->page_size, $pager->rsFrom());
		return $rs;
	}
	
	/**
	 * 
	 * @param string $tallyId
	 * @return \Prj\Data\ShopPointLog
	 */
	public static function getCopy($tallyId) {
		return parent::getCopy(array('tallyId'=>$tallyId));
	}
	protected static function idFor_dbByObj_InConf($isCache)
	{
		return 'pointsTally'.($isCache?'Cache':'');
	}	
	
	/**
	 * 使用哪个表
	 */
	protected static function splitedTbName($n,$isCache)
	{
		return 'tb_pointstally_'.($n%static::numToSplit());
	}

}
