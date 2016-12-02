<?php
/**
 * Created by PhpStorm.
 * User: LTM <605415184@qq.com>
 * Date: 2016/2/29
 * Time: 11:00
 */
namespace Prj\Data;

class VouchersInterim extends \Sooh\DB\Base\KVObj
{
	const DT_CHILD_EXPIRED = 5;//有效期为5天
	/**
	 * 母红包专用，将子红包写入临时表
	 * @param array $data [['pid' => '12', 'amount' => '123'],['pid' => '12', 'amount' => '123']]
	 * @return boolean true|false
	 */
	public static function insertForParent($data)
	{
		if (is_array($data)) {
			$dt = \Sooh\Base\Time::getInstance();
			$result = [];
			foreach($data as $key => $value) {
				for($retry = 0; $retry < 10; $retry++) {
					list($sec, $ms) = explode('.', microtime(true));
					$ordersId = \Prj\Consts\OrderType::vouchers . $sec . substr($ms, 0, 3) . mt_rand(1000, 9999);
					$tmp = self::getCopy($ordersId);
					$tmp->load();
					if(!$tmp->exists()) {
						$dtExpired = $dt->timestamp(self::DT_CHILD_EXPIRED);
						$tmp->setField('pid', $value['pid']);
						$tmp->setField('amount', $value['amount']);
						$tmp->setField('timeCreate', $dt->ymdhis());
						$tmp->setField('dtExpired', $dtExpired);
						$tmp->setField('isUsed', 1);
						$tmp->setField('isUsed', 1);
						$tmp->setField('isLock', 2);
						$tmp->setField('status', 1);

						$tmp->update();
						$result[$ordersId] = $tmp;
						break;
					}
				}
			}

			if (count($result) != count($data)) {
				foreach ($result as $k => $v) {
					$v->delete();
					return false;
				}
			} else {
				foreach ($result as $k => $v) {
					$v->setField('isLock', 1);
					$v->update();
				}
				return true;
			}
		}
	}

	/**
	 * 获取一条记录
	 * @param $voucherId
	 * @return bool
	 */
	public static function findOne($voucherId)
	{
		$dt = \Sooh\Base\Time::getInstance();
		$map = [
			'pid' => $voucherId,
		    'isUsed' => 1,
		    'isLock' => 1,
		    'status' => 1,
		];
		$rs = self::loopFindRecords($map);

		if ($rs[0]['dtExpired'] > $dt->timestamp()) {
			return $rs[0];
		} elseif (empty($rs)) {
			return false;//领完了
		} elseif ($rs[0]['dtExpired'] < $dt->timestamp()) {
			return 0; //红包过期
		}
		return false; //领完了
	}

	/**
	 * 将一张子红包标记为已使用
	 * @param string $voucherId 券ID
	 * @param int $isLock 是否已经被锁定
	 * @return bool
	 */
	public static function useOne($voucherId, $isLock = 1)
	{
		$map = [
			'voucherId' => $voucherId,
		    'isLock' => ($isLock ? 2 : 1),
		    'isUsed' => 1,
		    'status' => 1,
		    'dtExpired]' => \Sooh\Base\Time::getInstance()->timestamp(),
		];

		$rs = self::loopFindRecords($map);

		if ($rs[0]) {
			$tmp = self::getCopy($voucherId);
			$tmp->load();
			$tmp->setField('isUsed', 2);
			$retUsed = $tmp->update();
			if ($retUsed == 1) {
				$tmp->setField('status', 2);
				$retLock = $tmp->update();
				if ($retLock == 1) {
					return $rs[0];
				}
			}
		}

		return false;
	}

	public static function getCopy($key)
	{
		return parent::getCopy(['voucherId' => $key]);
	}

	protected static function idFor_dbByObj_InConf($isCache)
	{
		return 'vouchersInterim';
	}

	protected static function splitedTbName($n, $isCache)
	{
		return 'tb_vouchers_interim_' . ($n % static::numToSplit());
	}
}