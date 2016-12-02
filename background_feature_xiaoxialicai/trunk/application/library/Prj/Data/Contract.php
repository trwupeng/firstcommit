<?php
namespace Prj\Data;
/**
 * 渠道描述
 */
class  Contract extends \Sooh\DB\Base\KVObj{
    public $cameFrom;
    /**
     * 
     * @param string $pkey
     * @return \Prj\Data\Contract
     */
    public static function getCopy($pkey=null){
        return parent::getCopy($pkey);
    }
    //针对缓存，非缓存情况下具体的表的名字
    protected static function splitedTbName ($n, $isCache)
    {
        return 'tb_contract_'.($n % static::numToSplit());
    }
    
    //针对缓存，非缓存情况下具体的表的名字
    protected static function idFor_dbByObj_InConf ($isCache)
    {
        return 'contract';
    }
    
    public function getAccountNum($where)
    {
        return static::loopGetRecordsCount($where);
    }
    
    public static function getDefaultFor($copartnerAbs)
	{
		$tmp = self::getCopy(['contractId'=>0]);
		$ret = $tmp->db()->getOne($tmp->tbname(), 
				'contractId',['copartnerAbs'=>$copartnerAbs]);
		if($ret===null){
			return '0';
		}else{
			return $ret;
		}
	}

	public static $promotionWay = ['cpc'=>'cpc','cpa'=>'cpa','cpm'=>'cpm','cps'=>'cps','market'=>'market'];
	
	

    /**
     * 无规则限制
     */
    const unlimitrule = 0;
    /**
     * 注册规则限制
     */
    const regrule = 1;
    /**
     * 认证规则限制
     */
    const bindrule = 2;
    /**
     * 购买规则限制
     */
    const buyrule = 3;
    public static $displayRule = [
        self::unlimitrule => '无',
        self::regrule => '按照注册',
        self::bindrule => '按照绑卡',
        self::buyrule => '按照购买',
    ];

    public static function getContractName ($contractId=null) {
        if($contractId === null) {
            $records = static::loopFindRecordsByFields(['contractId]'=>0], null, 'contractId,remarks');
            $tmp=[];
            if(!empty($records)) {
                foreach($records as $r) {
                    $tmp[$r['contractId']] = $r['remarks'];
                }
            }
            $tmp[0] = '自然量';
            return $tmp;
        }elseif($contractId == 0){
            return '自然量';
        }else{
            $o = self::getCopy(['contractId'=>$contractId]);
            $o->load('remarks');
            if(!$o->exists()){
                return null;
            }
            $tmp = $o->getField('remarks');
            $o->free();
            return $tmp;
        }
    }
}