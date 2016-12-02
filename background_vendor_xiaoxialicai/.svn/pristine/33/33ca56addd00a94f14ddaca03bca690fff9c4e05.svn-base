<?php
namespace Sooh\DB\Interfaces;

/**
 * 此类库的目标否是最大化灵活运用某种数据库，而是简化语句同时尽可能应付从mysql到no-sql的切换
 * 为了达成上述目的，使用时要求：
 *		时间全部用数字（时间戳）；
 *		全程使用单表查询（诸如join这种，切no-sql会有问题）；
 *		提供表名的时候带上数据库名
 * 
 * 如果只是用于Mysql，可以把obj写成 a left join b on a.id=b.id 来进行多表操作
 * 
 * $db->getPair('test.table1','iAutoid','sName',array('sAccountId!'=>'0'),'rsort field1 groupby field2');
 * 
 * new where() -> init('AND','dbname.tablename')->append()->append()
 * 
 * @author Simon Wang <sooh_simon@163.com> 
 */
interface All 
{
	/////////////////////////////////////////////////////////////////////////////////////////////sample usage
	//function getRank($tb, $fieldScore, $fieldKey, $keyVal,$reverse=false)
	
	///////////////////////////////////////////////////////////////////////////////////////////// standard
	/**
	 * @return autoid if has
	 */
	public function addRecord($obj, $fields, $fieldAuto=null);
	public function addLog($obj, $fields);
	public function host();
	public function port();
	/**
	 * get or set current database
	 * @return All
	 */
	public function dbCurrent($dbTo);
	/**
	 * @return int count of deleted rows
	 */
	public function delRecords($obj,$where=null);
	/**
	 * disconnect and free resources
	 */
	public function free();
	/**
	 * update when record exists
	 */
	public function ensureRecord($obj, $fields, $fieldupdIfExist, $fieldAuto=null);
	public function getAssoc($obj, $key,$otherfields, $where=null, $orderby=null,$pagesize=null,$rsfrom=0);
	public function getCol($obj, $col, $where=null, $orderby=null,$pagesize=null,$rsfrom=0);
	public function getOne($obj, $field, $where=null, $orderby=null);
	public function getPair($obj, $key,$val, $where=null, $orderby=null,$pagesize=null,$rsfrom=0);
	public function getRecord($obj, $fields, $where=null, $orderby=null);
	public function getRecords($obj, $fields, $where=null, $orderby=null,$pagesize=null,$rsfrom=0);
	public function getRecordCount ($obj, $where=null, $orderby=null);
	/**
	 * random select several, 注意：不是所有的数据库都支持
	 */
	public function getRecordsRand($obj, $fields, $where=null, $orderby=null,$num=null);
	
	/**
	 * database status
	 */
	public function status();
	public function resetAutoIncrement($obj, $newstart=1);
	/**
	 * @return int count of deleted rows
	 */	
	public function updRecords($obj, $fields, $where=null, $other=null);
	/**
	 * @return Where
	 */
	public function newWhereBuilder();
	
	/**
	 * exec special query on special db-type
	 *		like array('sql'=>'select * from test');
	 * 
	 * @param array $arr
	 */
	public function execCustom($arr);

	/**
	 * @return array
	 */
	public function fetchAssocThenFree($result);
	
	/**
	 * transaction 注意：不是所有的数据库都支持
	 */
	public function trans_begin();
	/**
	 * transaction 注意：不是所有的数据库都支持
	 */	
	public function trans_commit();
	/**
	 * transaction 注意：不是所有的数据库都支持
	 */	
	public function trans_cancel();	
	/////////////////////////////////////////////////////////////////////////////////////////////KVObj
	public function kvoFieldSupport();//true or false: if must load all
	public function kvoLoad($obj,$fields,$arrPkey);
	/**
	 * @param array $verChk false | array(verid=>val)
	 * @param boolean $override if verid++
	 */
	public function kvoUpdate($obj,$fields,$arrPkey,$verCurrent,$override=false);
	public function kvoNew($obj,$fields, $arrPkey,$verCurrent, $fieldAuto=null);
	public function kvoDelete($obj,$arrPkey);
			
	/////////////////////////////////////////////////////////////////////////////////////////////extending
	public function getTables($dbname,$like=null,$addDBNameWhenReturn=false);
	public function setFieldDef ($obj, $old, $new, $def=null, $after=null);
	public function ensureDB($dbname);
	public function dropTable ($obj);
	public function ensureObj($obj,$fields,$pkey=null,$keys=null,$ukey=null);
//  implodeFields ($arrFields, $seperator='-', $newname=null) 
//
//  duplicateTbl ($newName, $oldName, $where=false, $moreSetting='') 
//  mvRecords ($tbFrom, $tbTo, $where=null, $moreSetting='') 
//  duplicateObj ($newName, $oldName, $where=false, $moreSetting='') 
//  
//  setIndex ($tb, $k, $r, $isUnique=false) 
//  
//  getStruct ($tbname) 
//	getDesc ($obj) 	
}

