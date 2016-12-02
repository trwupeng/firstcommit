<?php
namespace Prj\Data;
/**
 * Description of BankCard
 * @author simon.wang
 */
class BankCard extends \Sooh\DB\Base\KVObj
{
    /**
     * 添加一张新卡
     * @param string $userId
     * @param string $bankId
     * @param string $bankCard
     * @param int    $payCorp
     * @param string $realName
     * @param string $phone
     * @param int    $idCardType
     * @param string $idCardSn
     * @param int    $status
     * @return \Prj\Data\BankCard
     */
    public static function addCard($userId, $bankId, $bankCard, $payCorp,
                                   $realname, $phone, $idCardType, $idCardSn, $status = \Prj\Consts\BankCard::checking)
    {
        $baseid     = substr($userId, -4);
        $dt         = \Sooh\Base\Time::getInstance();
        $sec        = $dt->timestamp();
        $fields     = [
            'userId'     => $userId,
            'payCorp'    => $payCorp,
            'bankId'     => $bankId,
            'bankCard'   => $bankCard,
            'isDefault'  => 0,
            'statusCode' => $status,
            'realName'   => $realname,
            'phone'      => $phone,
            'idCardType' => $idCardType,
            'idCardSn'   => $idCardSn,
            'timeCreate' => $dt->ymdhis(),
            'resultMsg'  => '',
            'resultTime' => 0,
        ];
        $flgChecked = false;
        for ($retry = 0; $retry < 10; $retry++) {
            $ordersId = $sec . rand(10000, 99999) . $baseid;
            $tmp      = self::getCopy($ordersId);
            $tmp->load();
            if ($flgChecked == false && $status == \Prj\Consts\BankCard::enabled) {
                $exists = $tmp->db()->getRecordCount($tmp->tbname(), ['userId' => $userId, 'statusCode' => \Prj\Consts\BankCard::enabled]);
                if (!$exists) {
                    $fields['isDefault'] = 1;
                }
                $flgChecked = true;
            }
            if (!$tmp->exists()) {
                foreach ($fields as $k => $v) {
                    $tmp->setField($k, $v);
                }
                try {
                    $tmp->update();
                    break;
                } catch (\ErrorException $ex) {
                    $tmp = null;
                }
            } else {
                $tmp = null;
            }
        }

        /*
        if ($tmp != null) {
            $user = \Prj\Data\User::getCopy($userId);
            $user->load();
            if ($user->getField('ymdBindcard') == 0) {
                $user->setField('ymdBindcard', $dt->YmdFull);
                try {
                    $user->update();
                } catch (\ErrorException $e) {
                    \Sooh\Base\Log\Data::getInstance()->error('error: ' . $e->getMessage() . ' on update_user_bind:' . $userId);
                }
            }
        }
        */
        return $tmp;
    }
    /**
     * 是否已经存在
     */
    public static function repeatCheck($userId,$bankId,$bankCard)
    {
        $o = self::getCopy('');
        $db = $o->db();
        $tb = $o->tbname();
        $where = array(
            'userId'=>$userId,
            'bankId'=>$bankId,
            'bankCard'=>$bankCard,
            'statusCode'=>\Prj\Consts\BankCard::enabled,
        );
        $rs = $db->getRecord($tb,'orderId,statusCode',$where);
        return $rs;
    }

    /**
     * 更新记录的状态
     * @param int $status
     * @return \Prj\Data\BankCard
     */
    public function updStatus($status)
    {
        $this->setField('statusCode', $status);
        return $this;
    }

    /**
     * 获取用户的银行卡列表
     * @param string $userId
     * @return array
     */
    public static function getList($userId,$where = [])
    {
        $tmp = self::getCopy($userId);
        $where['userId'] = $userId;
        return $tmp->db()->getRecords($tmp->tbname(), '*', $where);
    }

    /**
     * 获取所有的银行卡列表
     */
    public static function loopAll($where = array())
    {
        $o  = self::getCopy('');
        $rs = $o->loopFindRecords($where);
        return $rs;
    }

    /**
     * @param string $account
     * @param string $camefrom
     * @return \Prj\Data\Investment
     */
    public static function getCopy($ordersId)
    {
        return parent::getCopy(['orderId' => $ordersId]);
    }


    //针对缓存，非缓存情况下具体的表的名字
    protected static function splitedTbName($n, $isCache)
    {
//		if($isCache)return 'tb_test_cache_'.($n % static::numToSplit());
//		else 
        return 'tb_user_bankcard_' . ($n % static::numToSplit());
    }

//指定使用什么id串定位数据库配置
    protected static function idFor_dbByObj_InConf($isCache)
    {
        return 'userbank'.($isCache?'Cache':'');
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
