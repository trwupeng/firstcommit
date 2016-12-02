<?php
/**
 * Created by PhpStorm.
 * User: LTM <605415184@qq.com>
 * Date: 2016/3/7
 * Time: 16:48
 */

namespace Prj\Data;

class WechatUserinfo extends \Sooh\DB\Base\KVObj
{
	public static function getInfoByOpenid($openid)
	{
		$db = self::getCopy($openid);
		$db->load();
		if ($db->exists()) {
			return $db;
		}
		return false;
	}

	public static function getInfoByPhone($phone)
	{

	}

	// //*
	public static function getInfoByUserid($userid)
	{
		$wechatBindPhone = \Prj\Data\WechatBindPhone::getInfoByUserid($userid);
		if (!empty($wechatBindPhone)) {
			$openid = $wechatBindPhone['openId'];
			$wxInfo = self::getCopy($openid);
			$wxInfo->load();
			if ($wxInfo->exists()) {
				return $wxInfo;
			}
		}
		return false;
	}

	public static function getCopy($openid)
	{
		return parent::getCopy(['openid' => $openid]);
	}

	protected static function idFor_dbByObj_InConf($isCache)
	{
		return 'wechatUserInfo';
	}

	protected static function splitedTbName($n, $isCache)
	{
		return 'tb_wechat_userinfo_' . ($n % static::numToSplit());
	}
}