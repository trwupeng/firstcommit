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
    /**
     * 根据openId获取微信用户信息
     * @param string $openId openId
     * @return bool|\Sooh\DB\Base\KVObj
     */
    public static function getInfoByOpenId($openId)
    {
        $db = self::getCopy($openId);
        $db->load();
        if ($db->exists()) {
            return $db;
        }
        return false;
    }

    /**
     * 根据userId获取微信用户信息
     * @param string $userId 用户ID
     * @return bool|\Sooh\DB\Base\KVObj
     * @throws \ErrorException
     */
    public static function getInfoByUserId($userId)
    {
        $openId = \Prj\Data\WechatBindPhone::getLastOpenId($userId);
        $dbWechatInfo = self::getCopy($openId);
        $dbWechatInfo->load();
        if ($dbWechatInfo->exists()) {
            return $dbWechatInfo;
        }
        return false;
    }

    public static function getCopy($openId)
    {
        return parent::getCopy(['openid' => $openId]);
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