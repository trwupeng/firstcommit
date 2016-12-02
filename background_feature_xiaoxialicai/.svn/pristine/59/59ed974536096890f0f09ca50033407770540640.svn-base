<?php
namespace Prj\Data;
/**
 *
 */
class Asset extends \Sooh\DB\Base\KVObj{
	/**
	 *
	 */
    public static function add($fields){
        if(!empty($fields)){
            do{
                $assetId = date('YmdHis').rand(10000,99999);
                $asset = self::getCopy($assetId);
                $asset->load();
            }while($asset->exists());
            foreach($fields as $k=>$v){
                $asset->setField($k,$v);
            }
            var_log(\Sooh\Base\Time::getInstance()->ymdhis(),'\Sooh\Base\Time::getInstance()->ymdhis()>>>>>>>>');
            $asset->setField('createTime',\Sooh\Base\Time::getInstance()->ymdhis());
            $asset->setField('status',\Prj\Consts\Asset::status_new);
            return $asset;
        }else{
            return null;
        }
    }

    public static function pager(\Sooh\DB\Pager $pager,$where=[],$order=''){
        $asset = self::getCopy('');
        $db = $asset->db();
        $tb = $asset->tbname;
        $pager->init($db->getRecordCount($tb,$where),-1);
        return $db->getRecords($tb,'*',$where,$order,$pager->page_size,$pager->rsFrom());
    }



	/**
	 * 
	 * @param string $account
	 * @param string $camefrom
	 * @return \Prj\Data\Investment
	 */
	public static function getCopy($ordersId) {
		return parent::getCopy(['assetId'=>$ordersId]);
	}

	
	//针对缓存，非缓存情况下具体的表的名字
	protected static function splitedTbName($n,$isCache)
	{
//		if($isCache)return 'tb_test_cache_'.($n % static::numToSplit());
//		else 
		return 'tb_asset_'.($n % static::numToSplit());
	}
//指定使用什么id串定位数据库配置
	protected static function idFor_dbByObj_InConf($isCache) {
		return 'asset'.($isCache?'Cache':'');
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
