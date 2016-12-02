<?php
namespace Prj\Data;
/**
 * 兑换码
 *
 */
class ExchangeCode extends \Sooh\DB\Base\KVObj {
	const tbGrp = 'db_p2p.tb_exchangecodes_grp';
		
	/**
	 * 
	 * @param string $excode
	 * @return \Prj\Data\ExchangeCode
	 */
	public static function getCopy($excode) {
		return parent::getCopy(array('excode'=>$excode));
	}
	
	protected static function idFor_dbByObj_InConf($isCache)
	{
		return 'exchangecodes'.($isCache?'Cache':'');
	}	
	
	/**
	 * 使用哪个表
	 */
	protected static function splitedTbName($n,$isCache)
	{
		return 'tb_exchangecodes_'.($n%static::numToSplit());
	}
	public static  $grp_info = [
			'grpId'=> ['分组', 30],
			'batchId'=> ['批号', 30],
			'dtExpire'=> ['过期日期', 100],
			'intro'=> ['说明', 100],
			'batchNum' => ['计划生成兑换码数量', 80],
			'realNum' => ['实际生成兑换码数量', 80],
			'useNum'=> ['已兑换数量', 80],
			'bonusini'=> ['奖励明细', 100],
	         'exp'=>['红包类',60],
	];


	/**
	 *
	 * 兑换码的发放情况
	 * @param null $grpId
	 * @param null $batchId
	 * @return array
	 * 分组id（主排序）	批次号（次排序）	过期日期	说明	创建数量	已兑换数量	奖励明细	操作
	 */

	public static function usageByBachId($where, $pageSize=null, $pageFrom=null) {
		$obj = self::getCopy();
		$db = $obj->db();
		$grp_fields = '*';
		$basic = $db->getRecords(self::tbGrp, $grp_fields, $where, 'rsort dtAddGrp rsort dtAddBatch', $pageSize, $pageFrom);
		if(!empty($basic)) {
			foreach($basic as $k => $r){
				$tmp=[];
				foreach(self::$grp_info as $column => $row) {
					if($column == 'realNum') {
						$tmp[$column] = $obj->loopGetRecordsCount(['grpId'=>$r['grpId'], 'batchId'=>$r['batchId']]);
					}elseif($column == 'useNum'){
						$tmp[$column] = $obj->loopGetRecordsCount(['grpId'=>$r['grpId'], 'batchId'=>$r['batchId'], 'dtFetch>'=>0]);
					}else{
						$tmp[$column] = $r[$column];
					}
				}
				$basic[$k] = $tmp;
			}
		}
		return $basic;
	}

	public static function totalBatch($where) {
		$obj = self::getCopy();
		$db = $obj->db();
		return $db->getRecordCount(self::tbGrp, $where);
	}

	/**
	 * 获取分组 极其对应的批次
	 * @param null $grpId
	 */
	public static function grpIdWithBatchId ($grpId=null) {
		$obj = self::getCopy('');
		$db = $obj->db();
		if(!empty($grpId)) {
			return $db->getCol(self::tbGrp, 'batchId', ['grpId'=>$grpId]);
		}

		$tmp = [];
		$records = $db->getRecords(self::tbGrp, 'grpId,batchId', null, 'rsort dtAddGrp rsort dtAddBatch');
		if(!empty($records)) {
			foreach($records as $k => $r) {
				$tmp[$r['grpId']][] = $r['batchId'];
			}
		}

		return $tmp;
	}


	/**
	 * @param $grpId
	 * @param $batchId
	 * @return mixed
	 *
	 * 获取一个批次的信息
	 */
	public static function batchInfo($grpId, $batchId) {
		$obj = self::getCopy();
		$db = $obj->db();
		return $db->getRecord(self::tbGrp, 'grpId, batchId,intro,bonusini,dtExpire', ['grpId'=>$grpId, 'batchId'=>$batchId]);
	}

	/**
	 * @param $grpId
	 * @param $batchId
	 * 获取一个批次的兑换码
	 */
	public static function getExchangeCode ($grpId, $batchId, $isExchange=null){
		$where =['grpId'=>$grpId, 'batchId'=>$batchId];
		if($isExchange === null) {
			;
		}elseif($isExchange===true) {
			$where += ['userId>'=>0];
		}elseif($isExchange===false){
			$where += ['userId'=>0];
		}

		return self::loopFindRecordsByFields($where, null, 'excode,dtExpire,dtFetch');
	}




































}
