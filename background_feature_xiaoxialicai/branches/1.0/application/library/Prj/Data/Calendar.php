<?php
namespace Prj\Data;
/**
 * Description of Carlendar
 * @author Hand
 */
class Calendar extends \Sooh\DB\Base\KVObj
{
    public static function add($ymd = 0, $workday = null, $planTotalWithdraw = null, $perWithdraw = null)
    {
        $ymd    = $ymd ? $ymd : (\Sooh\Base\Time::getInstance()->YmdFull);
        $dayNum = date('w',strtotime($ymd));
        if ($workday === null) {
            if($dayNum == '6' || $dayNum == '0')
            {
                $workday = false;
            }
            else
            {
                $workday = true;
            }
        }
        if(!$workday)
        {
            $planTotalWithdraw = $planTotalWithdraw?$planTotalWithdraw:0;
            $perWithdraw       = $planTotalWithdraw?$planTotalWithdraw:0;
        }
        else
        {
            $planTotalWithdraw = $planTotalWithdraw?$planTotalWithdraw:100000000;
            $perWithdraw       = $planTotalWithdraw?$planTotalWithdraw:10000000;
        }

        $fields            = [
            'workday'           => $workday,
            'planTotalWithdraw' => $planTotalWithdraw,
            'perWithdraw'       => $perWithdraw,
        ];
        $o                 = self::getCopy($ymd);
        foreach ($fields as $k => $v) {
            $o->setField($k, $v);
        }
        try {
            $o->update();
            return $o;
        } catch (\ErrorException $e) {
            return null;
        }
    }

    public static function addYear()
    {
        $arr = [];
        for ($i = 0; $i < 365; $i++) {
            $ymd = date('Ymd', strtotime('+' . $i . ' days'));
            self::add($ymd);
            $arr[] = $ymd;
        }
        return $arr;
    }

    /**
     * 获取提现到账日期
     * 可能会抛异常
     * By Hand
     */
    public static function getWithdrawDate($userId,$ymd,$amount)
    {
        $cal = self::getCopy($ymd);
        $cal->load();
        $realPerWithdraw = \Prj\Data\Recharges::getAmountWithdrawingByYmd($userId,$ymd);
        if(!self::checkWithdrawDate($cal,$amount,$realPerWithdraw))
        {
            $ymd = date('Ymd',strtotime('+1 days',strtotime($ymd)));
            $newYmd = self::getWithdrawDate($userId,$ymd,$amount);
        }
        return $newYmd?$newYmd:$ymd;
    }

    public static function checkWithdrawDate($cal,$amount,$realPerWithdraw)
    {
        return $cal->getField('workday') && $cal->getField('realTotalWithdraw')+$amount<=$cal->getField('planTotalWithdraw')
        && $realPerWithdraw+$amount<=$cal->getField('perWithdraw');
    }

    public static function getCopy($ymd)
    {
        return parent::getCopy(['Ymd' => $ymd]);
    }


    //针对缓存，非缓存情况下具体的表的名字
    protected static function splitedTbName($n, $isCache)
    {
//		if($isCache)return 'tb_test_cache_'.($n % static::numToSplit());
//		else 
        return 'tb_calendar';
    }

//指定使用什么id串定位数据库配置
    protected static function idFor_dbByObj_InConf($isCache)
    {
        return 'default';
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
