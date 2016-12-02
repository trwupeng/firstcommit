<?php
namespace Sooh\DB\Cases;
/**
 * 日期报表缓存
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class YmdReportCache {
	protected $tbName;
	public function __construct($tb) {
		$this->tbName=$tb;
	}
	/**
	 * 记录报表数据
	 * | mainType | subType | ymd | rptdata | flg1 | flg2 | flg3 |
	 * @param \Sooh\DB\Interfaces\All $db
	 * @param int $ymd yyyymmdd
	 * @param array $rptData
	 * @param string $mainType
	 * @param string $subType
	 * @param array $flgs 标志位字段，不参与任何统计
	 * @param array $conditionCanOverwrite 满足条件的才可以覆盖，null表示始终不能覆盖，空数组表示始终可以覆盖
	 * @return boolean
	 */
	public function save($db,$ymd,$rptData, $mainType,$subType, $flgs=array(),$conditionCanOverwrite=array())
	{
		$pkey = array('mainType'=>$mainType,'subType'=>$subType,'ymd'=>$ymd,);
		$fields = $flgs;
		if(!isset($fields['flg1']))$fields['flg1']=0;
		if(!isset($fields['flg2']))$fields['flg2']=0;
		if(!isset($fields['flg3']))$fields['flg3']=0;
		$fields['rptdata']=  json_encode($rptData);
		$exists = $db->getOne($this->tbName, 'ymd',$pkey);
		if($exists){
			if(is_array($conditionCanOverwrite)){
				if(empty($conditionCanOverwrite)){
					foreach($pkey as $k=>$v)$conditionCanOverwrite[$k] = $v;
					$exists = $db->getOne($this->tbName, 'ymd',$conditionCanOverwrite);
					if(!$exists)return false;
				}
				$db->updRecords($this->tbName, $fields,$pkey);
			}else return false;
		}else{
			try{
				\Sooh\DB\Broker::errorMarkSkip($v);
				foreach($pkey as $k=>$v)$fields[$k]=$v;
				$db->addRecord($this->tbName, $fields);
			} catch (ErrorException $e) {
				if(\Sooh\DB\Broker::errorIs($e)){
					$this->save($db, $ymd, $mainType, $subType, $rptData);
				}else{
					error_log($e->getMessage().'#'.\Sooh\DB\Broker::lastCmd()."\n".$e->getTraceAsString());
					return false;
				}
			}
		}
	}
	/**
	 * 合并数据
	 * @param \Sooh\DB\Interfaces\All $db
	 * @param type $ym
	 * @param type $mainType
	 * @param type $subType
	 * @param type $flgs
	 */
	public function mergeMonth($db,$ym,$mainType,$subType,$flgs)
	{
		$arr= array();
		if($ym>10020101)$ym = ($ym-$ym%100);//20150101
		else $ym=$ym*100;//201501
		$rs = $db->getPair($this->tbName,'ymd','rptdata',array('mainType'=>$mainType,'subType'=>$subType,'ymd>'=>$ym,'ymd<'=>$ym+100,));
		foreach($rs as $ymd=>$data){
			$this->onMerge($arr, $ymd, json_decode($data,true));
		}
		$this->save($db, $ym,$arr,     $mainType, $subType, $flgs,array());
	}
	/**
	 * 将$rptDataLoaded数据并入$arr;
	 * @param array $arr
	 * @param array $rptDataLoaded
	 */
	protected function onMerge(&$arr,$ymd,$rptDataLoaded)
	{
		foreach($rptDataLoaded as $k=>$ymd)
			$arr[$k]+=$ymd;
	}
	/**
	 * 加载数据(单条), 日期为yyyymm00得到的就是yyyymm对应月的数据
	 * @param \Sooh\DB\Interfaces\All  $db
	 * @param int $ymd
	 * @param string $mainType
	 * @param string $subType
	 * @return array ['rptdata'=>array(),flg1=>0,flg2=>0,flg3=>3]
	 */
	public function loadOne($db,$ymd,$mainType,$subType)
	{
		$r = $db->getRecord($this->tbName, 'rptdata,flg1,flg2,flg3',array('mainType'=>$mainType,'subType'=>$subType,'ymd'=>$ymd,));
		$r['rptdata'] = json_decode($r['rptdata'],true);
		return $r;
	}
	/**
	 * 加载数据(多条，暂不支持分页), 限制日期范围，mainType范围，subType范围
	 * @param \Sooh\DB\Interfaces\All  $db
	 * @param int $ymd
	 * @param string $mainType
	 * @param string $subType
	 * @return array ['rptdata'=>array(),flg1=>0,flg2=>0,flg3=>3]
	 */
	public function loadMore($db,$ymd,$mainType,$subType)
	{
		$rs = $db->getRecords($this->tbName, 'mainType,subType,rptdata,flg1,flg2,flg3',array('mainType'=>$mainType,'subType'=>$subType,'ymd'=>$ymd,));
		foreach ($rs as $i=>$r)
			$rs[$i]['rptdata'] = json_decode($r['rptdata'],true);
		return $rs;
	}	
	/**
	 * 获取指定日所有的maintype
	 * @param \Sooh\DB\Interfaces\All  $db
	 * @param int $ymd null表示不限制ymd
	 */
	public function getMainTypes($db,$ymd){
		if($ymd===null)$where=null;
		else $where=array('ymd'=>$ymd);
		return $db->getCol($this->tbName, 'mainType',$where);
	}
	/**
	 * 获取指定日指定maintype的subType
	 * @param type $db
	 * @param type $ymd      null表示不限制ymd
	 * @param type $mainType null表示不限制mainType
	 */
	public function getSubTypes($db,$ymd,$mainType){
		$where=array();
		if($ymd!==null)$where['ymd']=$ymd;
		if($mainType!==null)$where['mainType']=$mainType;
		if(empty($where))$where=null;
		return $db->getCol($this->tbName, 'subType',$where);
	}
	
	/**
	 * 加载数据(某月)
	 * @param \Sooh\DB\Interfaces\All  $db
	 * @param int $ymd
	 * @param string $mainType
	 * @param string $subType
	 * @return array [ymd=>[rptdata,flg1,flg2,flg3],ymd=>[rptdata,flg1,flg2,flg3]....]
	 */
	public function loadMonth($db,$ymd,$mainType,$subType)
	{
		$ret=array();
		if($ymd>10020101)$ymd = ($ymd-$ymd%100);//20150101
		else $ymd=$ymd*100;//201501
		$rs = $db->getRecords($this->tbName, 'ymd,rptdata,flg1,flg2,flg3',array('mainType'=>$mainType,'subType'=>$subType,'ymd>'=>$ymd,'ymd<'=>$ymd+100,));
		
		foreach($rs as $r){
			$r['rptdata']=json_decode($r['rptdata'],true);
			$ymd = $r['ymd'];
			unset($r['ymd']);
			$ret[$ymd]=$r;
		}
		ksort($ret);
		return $ret;
	}
}
