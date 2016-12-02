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
    /**
     * 更改、保存wx绑定的手机号，存在UserId时同时修改snsWechat
     * @param string  $openid openid
     * @param integer $phone  phone
     * @param string  $userid userId
     * @return bool
     * @throws \ErrorException
     * @throws \Exception
     */
    public static function bandUser($openid, $phone, $userid)
    {
        $create_time = \Sooh\Base\Time::getInstance()->timestamp();
        $sys         = self::getCopy($userid, $create_time);
        $sys->load();
        $sys->setField('openId', $openid);
        $sys->setField('updTime', $create_time);
        $sys->setField('phone', $phone);
        $sys->update();
        return true;
    }

    /**
     * 根据openId获取最后（倒数第二）绑定的用户ID
     * @param string $openId    微信的用户唯一标识
     * @param bool   $getSecond 是否获取倒数第二
     * @return array [phone, userId, isToday]
     */
    public static function getLastUser($openId, $getSecond = false)
    {
        $map = [
            'openId' => $openId
        ];

        $ret = self::loopFindRecords($map);

        if (empty($ret)) {
            return ['', '', false];
        } elseif (count($ret) == 1 && $getSecond) {
            return ['', '', false];
        } elseif (count($ret) == 1 && !$getSecond) {
            $isToday = floor(\Sooh\Base\Time::getInstance()->timestamp() / 86400) == floor($ret[0]['updTime'] / 86400) ? true : false;
            return [$ret[0]['phone'], $ret[0]['userId'], $isToday];
        }

        //构造排序数组
        $arr_sort = [];
        foreach ($ret as $k => $v) {
            $arr_sort[$v['updTime']] = $k;
        }
        krsort($arr_sort);

        //从高到低构造新的数组
        $arr_sort_list = [];
        foreach ($arr_sort as $k => $v) {
            $arr_sort_list[] = $ret[$v];
        }

        if ($getSecond) {
            $isToday = floor(\Sooh\Base\Time::getInstance()->timestamp() / 86400) == floor($arr_sort_list[1]['updTime'] / 86400) ? true : false;
            return [$arr_sort_list[1]['phone'], $arr_sort_list[1]['userId'], $isToday];
        } else {
            $isToday = floor(\Sooh\Base\Time::getInstance()->timestamp() / 86400) == floor($arr_sort_list[0]['updTime'] / 86400) ? true : false;
            return [$arr_sort_list[0]['phone'], $arr_sort_list[0]['userId'], $isToday];
        }
    }

    /**
     * 根据UserId获取最后绑定的微信ID
     * @param string $userId userId
     * @return array [phone, openId, isToday]
     */
    public static function getLastOpenId($userId)
    {
        $map = [
            'userId' => $userId
        ];

        $ret = self::loopFindRecords($map);

        if (empty($ret)) {
            return ['', '', false];
        } elseif (count($ret) == 1) {
            $isToday = floor(\Sooh\Base\Time::getInstance()->timestamp() / 86400) == floor($ret[0]['updTime'] / 86400) ? true : false;
            return [$ret[0]['phone'], $ret[0]['openId'], $isToday];
        }

        //构造排序数组
        $arr_sort = [];
        foreach ($ret as $k => $v) {
            $arr_sort[$v['updTime']] = $k;
        }
        krsort($arr_sort);

        //从高到低构造新的数组
        $arr_sort_list = [];
        foreach ($arr_sort as $k => $v) {
            $arr_sort_list[] = $ret[$v];
        }

        $isToday = floor(\Sooh\Base\Time::getInstance()->timestamp() / 86400) == floor($arr_sort_list[0]['updTime'] / 86400) ? true : false;
        return [$arr_sort_list[0]['phone'], $arr_sort_list[0]['openId'], $isToday];
    }

    public static function getCopy($userId, $createTime)
    {
        return parent::getCopy(['userId' => $userId, 'createTime' => $createTime]);
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