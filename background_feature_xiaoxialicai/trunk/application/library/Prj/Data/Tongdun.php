<?php
/**
 * Created by PhpStorm.
 * User: LTM <605415184@qq.com>
 * Date: 2016/6/7
 * Time: 13:36
 */

namespace Prj\Data;

/**
 * 同盾结果
 * Class Tongdun
 * @package Prj\Data
 */
class Tongdun extends \Sooh\DB\Base\KVObj
{
    /**
     * 将一条同盾结果计入数据库
     * @param string $loginName  登录名
     * @param string $loginType  登录名类型
     * @param mixed  $tongdunRet 同盾返回结果
     * @return null|\Sooh\DB\Base\KVObj
     * @throws \ErrorException
     * @throws \Exception
     */
    public static function newAdd($loginName, $loginType, $tongdunRet)
    {
        var_log(func_get_args());
        if (!is_array($tongdunRet)) {
            if (!json_decode($tongdunRet, true)) {
                return null;
            } else {
                $tongdunRet = json_decode($tongdunRet, true);
            }
        }

        $dt        = \Sooh\Base\Time::getInstance();
        $dbTongdun = self::getCopy($loginName, $loginType);
        $dbTongdun->load();
        if ($dbTongdun->exists()) {
            $oldJson       = $dbTongdun->getField('json');
            $oldTimeCreate = $dbTongdun->getField('timeCreate');
            $extJson       = $dbTongdun->getField('extJson');
            if (!empty($extJson)) {
                if (!is_array($extJson)) {
                    if (json_decode($extJson, true)) {
                        $extJson = json_decode($extJson, true);
                    } else {
                        $extJson = [];
                    }
                }
            } else {
                $extJson = [];
            }
            $extJson[$oldTimeCreate] = $oldJson;
        }

        if (isset($extJson) && count($extJson) >= 4) {
            unset($extJson[key($extJson)]);
        }

        $dbTongdun->setField('timeCreate', $dt->ymdhis());
        $dbTongdun->setField('json', json_encode($tongdunRet));
        $dbTongdun->setField('final_decision', isset($tongdunRet['final_decision']) ? $tongdunRet['final_decision'] : '');
        $dbTongdun->setField('final_score', isset($tongdunRet['final_score']) ? $tongdunRet['final_score'] : -1);
        $dbTongdun->setField('seq_id', isset($tongdunRet['seq_id']) ? $tongdunRet['seq_id'] : 0);
        $dbTongdun->setField('remarks', '');
        $dbTongdun->setField('extJson', isset($extJson) ? json_encode($extJson) : '');
        $dbTongdun->update();
        return $dbTongdun;
    }

    /**
     * @param string $orderId
     * @return \Sooh\DB\Base\KVObj
     */
    public static function getCopy($loginName, $loginType)
    {
        return parent::getCopy(['loginName' => $loginName, 'loginType' => $loginType]);
    }

    protected static function splitedTbName($n, $isCache)
    {
        return 'tb_tongdun_' . ($n % static::numToSplit());
    }

    protected static function idFor_dbByObj_InConf($isCache)
    {
        return 'tongdun' . ($isCache ? 'Cache' : '');
    }
}