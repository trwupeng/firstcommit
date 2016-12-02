<?php
/**
 * Created by PhpStorm.
 * User: LTM <605415184@qq.com>
 * Date: 2016/3/11
 * Time: 10:59
 */

namespace Prj\Data;

class WechatBindPhone extends \Sooh\DB\Base\KVObj
{
	public static function getInfoByOpenid($openId)
	{
		$db = self::getCopy($openId);
		$db->load();
		if ($db->exists()) {
			return $db;
		}
		return false;
	}

	/**
	 * 根据userId获取信息
	 * @param $userId
	 * @return array
	 */
	public static function getInfoByUserid($userId)
	{
		$map = [
			'userId' => $userId,
		];
		$ret = self::loopFindRecords($map);
		if (!empty($ret)) {
			return $ret[0];
		}
		return [];
	}

	public static function saveInfo($openId, $phone)
	{
		//get userId


		$db = self::getCopy($openId);
		$db->load();
		if ($db->exists()) {
			//update

		} else {
			//insert
		}
	}

	/**
	 * 更改、保存wx绑定的手机号，存在UserId时同时修改snsWechat
	 * @param string $openid openid
	 * @param integer $phone phone
	 * @param string $userid userId
	 * @return bool
	 * @throws \ErrorException
	 * @throws \Exception
	 */
	public static function bandUser($openid, $phone, $userid = '')
	{
		$sys = self::getCopy($openid);
		$sys->load();
		if ($sys->exists()) {
			$sys->setField('updTime', \Sooh\Base\Time::getInstance()->timestamp());
		} else {
			$sys->setField('createTime', \Sooh\Base\Time::getInstance()->timestamp());
		}
		$sys->setField('phone', $phone);
		if (!empty($userid)) {
			$sys->setField('userId', $userid);
		}
		$sys->update();
		return true;
	}

	public static function getCopy($openId)
	{
		return parent::getCopy(['openId' => $openId]);
	}

	protected static function idFor_dbByObj_InConf($isCache)
	{
		return 'wechatBindPhone';
	}

	protected static function splitedTbName($n, $isCache)
	{
		return 'tb_wechat_bind_phone_' . ($n % static::numToSplit());
	}
}