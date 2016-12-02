<?php
namespace Prj\Data;
/**
 * Created by PhpStorm.
 * User: LTM <605415184@qq.com>
 * Date: 2015/10/28
 * Time: 18:15
 */

class Checkin extends \Sooh\DB\Base\KVObj {
	const errTodayDone       = '今天已经签到过了';//今天已经签到过了

	public static function addRecord($userId, $bonus, $number) {
		$checkin = self::getCopy($userId);
		$checkin->load();
		if ($checkin->exists()) {
			throw new \Sooh\Base\ErrException(self::errTodayDone);
		} else {
			$total = self::getAccountNum(['userId' => $userId]);
			$checkin->setField('date', \Sooh\Base\Time::getInstance()->timestamp());
			$checkin->setField('bonus', $bonus);
			$checkin->setField('total', $total + 1);
			$checkin->setField('number', $number);
			$checkin->update();
		}
	}

	protected static function splitedTbName($n, $isCache) {
		return 'tb_checkin_' . ($n % static::numToSplit());
	}

	/**
	 * @param int  $userId
	 * @param string $ymd
	 * @return \Sooh\DB\Base\KVObj
	 */
	public static function getCopy($userId, $ymd = '') {
		if (empty($ymd)) {
			$ymd = \Sooh\Base\Time::getInstance()->ymd;
		}
		return parent::getCopy(['userId' => $userId, 'ymd' => $ymd]);
	}

	protected static function idFor_dbByObj_InConf($isCache) {
		return 'modCheckin'.($isCache?'Cache':'');
	}

	public static function getAccountNum($where)
	{
		return static::loopGetRecordsCount($where);
	}
}