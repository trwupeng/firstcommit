<?php
namespace Sooh\DB\Base;

//use \Sooh\DB\Error as sooh_dbField;
use \Sooh\Base\Ini as sooh_ini;
use \Sooh\DB\Broker as sooh_dbBroker;
/**
	public function kvoFieldSupport();//true or false: if must load all
	public function kvoLoad($obj,$fields,$arrPkey);
	public function kvoUpdate($obj,$fields,$arrPkey);
	public function kvoNew($obj,$fields, $arrPkey);
 */

/**
 * jsonencode for array field
 * 数组字段会调用jsonencde
 * @author Simon Wang <sooh_simon@163.com> 
 */
abstract class KVObj
{
	const onAfterLoaded='onAfterLoad';
	const onBeforeSave ='onBeforeSave';
	const onAfterSave = 'onAfterSave';
	protected $fieldName_verid='iRecordVerID';
	protected $fieldName_lockmsg='sLockData';//建议100字节长的字符串(默认'')，54个字节的基本长度，剩下的给lock的说明
	protected $listener=array();
	protected $chged=array();
	protected $pkey;
	protected $tbname;
	protected $r=array();
	protected $loads=null;
	protected $cacheWhenVerIDIs=0;
	protected static $_copies=array();
	
//	public static function searchAll($fields,$where,$tableByPkey=null)
//	{
//		if($tableByPkey!==null){
//			if(is_numeric($tableByPkey)){
//				$id = $tableByPkey%static::numToSplit();
//			}else{
//				$id = static::indexForSplit($tableByPkey)%static::numToSplit();
//			}
//			$tbFullName = null;
//			$db = static::getDBAndTbName($tbFullName,$id,null);
//		}else{
//			$max = static::numToSplit();
//			for($id=0;$id<$max;$id++){
//				$db = static::getDBAndTbName($tbFullName,$id,null);
//			}
//		}
//	}

	/**
	 * @param array $pkey
	 * @return \Sooh\DB\Base\KVObj
	 */	
	public static function getCopy($pkey)
	{
		$class = get_called_class();
		$md5 = md5(json_encode($pkey));
		if(!isset(self::$_copies[$class][$md5])){
			$o = new $class;
			self::$_copies[$class][$md5] = $o->initConstruct()->initPkey($pkey);
		}
		return self::$_copies[$class][$md5];
	}
	/**
	 * 遍历所有分库
	 * @param type $callback_db_tb
	 */
	public static function loop($callback_db_tb)
	{
		$total = static::numToSplit();
		$tbName=null;
		if(is_array($callback_db_tb) || is_string($callback_db_tb))	{
			
			for($i=0;$i<$total;$i++){
				$db = static::getDBAndTbNameById($tbName, $i,false);
				call_user_func ($callback_db_tb, $db, $tbName);
			}
		}else{ 
			for($i=0;$i<$total;$i++){
				$db = static::getDBAndTbNameById($tbName, $i,false);
				$callback_db_tb($this, $db, $tbName);
			}
		}
	}
	
//	public static function query($where)
//	{
//		$db = self::getDB($pkey);
//		$db->dbCurrent(null);
//	}
	public static function freeAll($pkey=null)
	{
		$class = get_called_class();
		if($pkey){
			$md5 = md5(json_encode($pkey));
			self::$_copies[$class][$md5]->free(false);
			unset(self::$_copies[$class][$md5]);
			if(empty(self::$_copies[$class])){
				unset(self::$_copies[$class]);
			}
		}else{
			foreach(self::$_copies as $class=>$rs){
				foreach($rs as $k=>$o){
					$o->free(false);
					unset($rs[$k]);
					unset(self::$_copies[$class][$k]);
				}
				if(!empty(self::$_copies[$class])){
					unset(self::$_copies[$class]);
				}
			}
		}
		
	}
	public function free($removeGlobal=true)
	{
		$pkey = $this->pkey;
		$this->r=array();
		$this->pkey=null;
		$this->chged=array();
		if(!empty($this->customData)){
			$ks = array_keys($this->customData);
			foreach($ks as $k){
				unset($this->customData[$k]);
			}
		}
		$this->tbname=null;
		$ks = array_keys($this->listener);
		foreach($ks as $k){
			unset($this->listener[$k]);
		}
		$this->listener=array();
		if($removeGlobal){
			static::freeAll ($pkey);
		}
	}
	public $customData=array();
	/**
	 * 根据pkey计算分表用的id值（默认使用完整的pkey,得出0-99分布）
	 * @param type $pkey
	 * @return int
	 */
	protected static function indexForSplit($pkey)
	{
		if(sizeof($pkey)==1){
			$n = current($pkey);
			if(is_numeric($n) && !strpos($n, '.')){
				return $n%10000;
			}
		}
		$s = md5(json_encode($pkey));
		$n1 = base_convert(substr($s,-3), 16, 10);
		$n2 = base_convert(substr($s,-6,3), 16, 10);
		$n = $n2*100+($n1%100);
		return $n%10000;
	}
	/**
	 * 拆分成几个表
	 * @return int
	 */
	protected static function numToSplit(){
		$dbByObj = static::idFor_dbByObj_InConf(false);
		return \Sooh\Base\Ini::getInstance()->get('dbByObj.'.$dbByObj.'.0',1);
	}
	/**
	 * 根据拆分id，确认实际的表名
	 * @param int $n
	 * @param bool $isCache 
	 * @return string
	 */
	protected static function splitedTbName($n,$isCache)
	{
		if($isCache){
			return 'redis_test_'.($n % static::numToSplit());
		}else{
			return 'mysql_test_'.($n % static::numToSplit());
		}
	}
	/**
	 * @param array $pkey
	 * @return \Sooh\DB\Interfaces\All
	 */
	protected static function getDBAndTbName(&$tbnameToSet,$pkey,$isCache=false)
	{
		$splitedId = static::indexForSplit($pkey);
		self::$tmpId=$splitedId;
		$ret = static::getDBAndTbNameById($tbnameToSet,$splitedId, $isCache);
		if($ret===null){
			throw new \ErrorException('can NOT find dbConf for '.get_called_class().($isCache?"Cache":'').':'.  json_encode($pkey).' $splitedId='.$splitedId);
		}
		return $ret;
	}
	protected static $tmpId=0;
	protected static function idFor_dbByObj_InConf($isCache)
	{
		return 'default'.($isCache?"cache":'');
	}
	protected static function getDBAndTbNameById(&$tbnameToSet,$splitedId,$isCache=false)
	{
		$dbByObj = static::idFor_dbByObj_InConf($isCache);
		$ini = sooh_ini::getInstance();
		$dbId = $ini->get('dbByObj.'.$dbByObj);
		if(is_array($dbId)){
			$i = $splitedId % (sizeof($dbId)-1);
			$confIDStr = 'dbConf.'.$dbId[$i+1];
		}elseif(!empty($dbId)){
			$confIDStr = 'dbConf.'.$dbId;
		}else{
			$tmp = $ini->get('dbByObj.default');
			if(empty($tmp)){
				$confIDStr = 'dbConf.default';
			}else{
				if(is_array($tmp)){
					$i = $splitedId % (sizeof($tmp)-1);
					$confIDStr = 'dbConf.'.$tmp[$i+1];
				}else{
					$confIDStr = 'dbConf.'.$tmp;
				}
			}
		}
		$conf = $ini->get($confIDStr);
		if(empty($conf)){
			error_log('try find '.$confIDStr.' in dbConf failed for '. $dbByObj);
			return null;
		}
		
		$db = sooh_dbBroker::getInstance($conf,$dbByObj);
		if (isset($conf['dbEnums'][$dbByObj])){
			$dbname = $conf['dbEnums'][$dbByObj];
		}else{
			$dbname = $conf['dbEnums']['default'];
		}
		$tbnameToSet = $dbname.'.'.static::splitedTbName($splitedId,$isCache);
		return $db;
	}
	
	protected function initConstruct($cacheSetting=0,$fieldVer='iRecordVerID')
	{
		$this->cacheWhenVerIDIs = $cacheSetting;
		$this->fieldName_verid = $fieldVer;
		return $this;
	}
	/**
	 * 
	 * @param array $pkey
	 * @return \Sooh\DB\Base\KVObj
	 */
	protected function initPkey($pkey)
	{
		$this->pkey=$pkey;
		return $this;
	}
	/**
	 * 
	 * @param type $fields
	 * @return pkey | null
	 */
	public function load($fields='*')//---------------------------------------------load 指定字段的情况
	{
		if($this->exists()===false){
			if($this->cacheWhenVerIDIs){
				$fields = '*';//-----------------------------有 cache
			}
			$this->loads=$fields;
			return $this->reload();
		}else{
			return $this->pkey;
		}
		
	}
	public function tbname()
	{
		if($this->tbname!==null){
			return $this->tbname;
		}else{
			static::getDBAndTbName($this->tbname, $this->pkey,false);
		}
		return $this->tbname;
	}
	/**
	 * 
	 * @return \Sooh\DB\Interfaces\All
	 */
	public function db()
	{
		return static::getDBAndTbName($this->tbname, $this->pkey,false);
	}
	public $idForSplit=0;
	/**
	 * 
	 * @return pkey | null
	 */
	public function reload()
	{
		if(!empty($this->pkey) && !empty($this->loads)){
			//deal with cache
			if($this->cacheWhenVerIDIs){
				$tbCache=null;
				$dbCache = static::getDBAndTbName($tbCache, $this->pkey,true);
				$this->r = $dbCache->kvoLoad($tbCache, $this->loads,$this->pkey);
				if(empty($this->r)){
					$db = static::getDBAndTbName($this->tbname, $this->pkey,false);
					$this->idForSplit = self::$tmpId;
					$this->r = $db->kvoLoad($this->tbname, '*',$this->pkey);
					if(!empty($this->r)){
						$dbCache->kvoNew($tbCache, $this->r, $this->pkey,array($this->fieldName_verid=>$this->r[$this->fieldName_verid]),$this->_fieldAutoInc);
					}else{
						$this->r=array();
					}
				}
			}else{
				$db = static::getDBAndTbName($this->tbname, $this->pkey,false);
				$this->idForSplit = self::$tmpId;
				$this->r = $db->kvoLoad($this->tbname, $this->loads,$this->pkey);
			}

			if(!empty($this->r)){
				//try jsondecode
				foreach($this->r as $k=>$v){
					if(isset($this->fieldsSimple[$k])){
						continue;
					}elseif($v!==null && !is_scalar($v)){
						if(!isset($this->fieldsDatetime[$k]) &&is_a($v,'Datetime')){
							$this->fieldsDatetime[$k]=$k;
						}
					}elseif(is_string($v)){
						if($v[0]=='{' && substr($v,-1)=='}'){
							$this->r[$k] = json_decode($v,true);
							if(empty($this->r[$k])){
								$this->r[$k]=$v;
							}
						}elseif($v[0]=='[' && substr($v,-1)==']'){
							if($v=='[]'){
								$this->r[$k] = array();
							}else{
								$this->r[$k] = json_decode($v,true);
								if(empty($this->r[$k])){
									$this->r[$k]=$v;
								}
							}
						}
					}
				}
				foreach($this->fieldsDatetime as $k){
					$this->r[$k] = $this->r[$k]->getTimestamp();
				}

				$this->chged=array();
				$this->callbackOn(self::onAfterLoaded);
				return $this->pkey;
			}
		}else{
			throw new \ErrorException('known what to load(pkey, fields all empty)');
		}
		return null;
	}
	/**
	 * 符合条件的记录的条数
	 * (注，函数复用了，loop回调的还是这个函数，此时arrWhere是db,$_ignore_是 tbName
	 * 
	 * @param \Sooh\DB\Interfaces\All $where
	 */
	public static function loopGetRecordsCount($arrWhere,$_ignore_=null)
	{
		if(!is_array($arrWhere)){
			static::$tmpVar['counts']+=$arrWhere->getRecordCount($_ignore_,static::$tmpVar['where']);
		}else{
			static::$tmpVar = array('where'=>$arrWhere);
			$func = get_called_class().'::'.__FUNCTION__;
			self::loop($func);
			return static::$tmpVar['counts'];
		}
	}
	protected static $tmpVar;
	/**
	 * 符合条件的记录
	 * (注，函数复用了，loop回调的还是这个函数，此时arrWhere是db,$_ignore_是 tbName
	 * 
	 * @param \Sooh\DB\Interfaces\All $where
	 */
	public static function loopFindRecords($arrWhere,$_ignore_=null)
	{
		if(!is_array($arrWhere)){
			static::$tmpVar['rs'] = array_merge(static::$tmpVar['rs'],$arrWhere->getRecords($_ignore_,'*',static::$tmpVar['where']));
		}else{
			static::$tmpVar = array('where'=>$arrWhere,'rs'=>array());
			$func = get_called_class().'::'.__FUNCTION__;
			self::loop($func);
			return static::$tmpVar['rs'];
		}
	}

	public static function loopByFields($callback_db_tb, $fields, $dbFunc, $order)
	{
		$total = static::numToSplit();
		$tbName=null;
		if(is_array($callback_db_tb) || is_string($callback_db_tb))	{

			for($i=0;$i<$total;$i++){
				$db = static::getDBAndTbNameById($tbName, $i,false);
				call_user_func ($callback_db_tb, $db, $tbName, $fields, $dbFunc, $order);
			}
		}else{
			for($i=0;$i<$total;$i++){
				$db = static::getDBAndTbNameById($tbName, $i,false);
				$callback_db_tb($this, $db, $tbName, $fields, $dbFunc, $order);
			}
		}
	}
	/**
	 * 根据字段，数据库函数遍历 有些方法不支持 getPair就是不支持的典型
	 * @param unknown $arrWhere
	 * @param string $_ignore_
	 * @param string $fields
	 * @param string $dbFunc
	 */
	public static function loopFindRecordsByFields($arrWhere, $_ignore_ = null, $fields='*', $dbFunc='getRecords', $orderBy=null)
	{
		if(!is_array($arrWhere)){
			$rs = $arrWhere->$dbFunc($_ignore_,$fields,static::$tmpVar['where'], $orderBy);
			static::$tmpVar['rs'] = array_merge(static::$tmpVar['rs'],$rs);
		}else{
			static::$tmpVar = array('where'=>$arrWhere,'rs'=>array(), 'dbFunc'=>$dbFunc, 'order'=>$orderBy);
			$func = get_called_class().'::'.__FUNCTION__;
			self::loopByFields($func, $fields, $dbFunc, $orderBy);
			return static::$tmpVar['rs'];
		}
	}



	/**
	 * 分表后，查询结果分页很复杂，提供这个函数解决部分情况：
	 * a)以唯一索引（1个或2个字段，更多的暂不支持：2项效率高些，代码也好写，呵呵）作为排序条件，可以完美实现分页
	 * b)以非唯一索引作为排序条件，有问题，
	 * 举例（autoid,subkey）：
	 * 传递过来的lastPage是空：array('autoid'=>'sort','subkey'=>'sort'),array('where'=>array(),['unique'=>1(默认),]),pager
	 * 传递过来的lastPage非空：array('autoid'=>'sort','subkey'=>'sort'),decodedArr_lastPage,pager
	 * TODO:目前的代码只支持下一页，连上一页都待开发
	 * @param array $sort_field_type
	 * @param array $lastPage with extraWhere
	 * @param \Sooh\DB\Pager $pager 
	 * @return array (lastPage=>array(), records=array())
	 */
	public static function loopGetRecordsPage($sort_field_type,$lastPage,$pager=null)
	{
		if($pager===null){//loop callback
			static::loopGetRecordsPage_getRecords($sort_field_type, $lastPage);
		}else{
			
			//lastPage: ['_pageid_'=>0,'_autoid_'=>array(from,to),'_subkey_'=>array(from,to), 此函数返回值中lastPage
			//			'unique'=>1(默认),'fieldSearch1'=>'val','fieldSearch2'=>'val',] 当前 搜索表单条件
			$strSort = ' ';
			
			$revertSort=array('sort'=>'rsort','rsort'=>'sort');
			$pageIdOld = $lastPage['_last_']['_pageid_']-0;
			if($pager->page_id_zeroBegin==$pageIdOld){//刷新
				//echo "-------------------------refresh--------\n";
//				var_dump($lastPage);
//				echo "-------\n";
				$pageForward=true;
				if(isset($lastPage['_pre_'])){
					$lastPage['_last_'] = $lastPage['_pre_'];
				}
				foreach($sort_field_type as $field=>$type){
					$strSort.="$type $field ";
				}
				return static::loopGetRecordsPage_oneStep($sort_field_type, $lastPage, $pager,$pageForward,$strSort);
			}elseif($pager->page_id_zeroBegin>$pageIdOld){//正向翻页
				//echo "-------------------------forward from $pageIdOld to {$pager->page_id_zeroBegin}--------\n";
//				var_dump($lastPage);
//				echo "-------\n";
				$pageForward=true;
				foreach($sort_field_type as $field=>$type){
					$strSort.="$type $field ";
				}
				$steps = $pager->page_id_zeroBegin-$pageIdOld;
				$pager->init($pager->total, $pager->pageid()+$pageIdOld-$pager->page_id_zeroBegin);
				for($i=0;$i<$steps;$i++){
					$pager->init($pager->total, $pager->pageid()+1);
					$ret = static::loopGetRecordsPage_oneStep($sort_field_type, $lastPage, $pager,$pageForward,$strSort);
					$lastPage = $ret['lastPage'];
				}
				return $ret;
			}else{//反向翻页
				//echo "-------------------------backward from $pageIdOld to {$pager->page_id_zeroBegin}--------\n";
//				var_dump($lastPage);
//				echo "-------\n";
				$pageForward=false;
				foreach($sort_field_type as $field=>$type){
					$strSort.=$revertSort[$type]." $field ";
				}
				$steps = $pageIdOld-$pager->page_id_zeroBegin;
				$pager->init($pager->total, $pager->pageid()+$pageIdOld-$pager->page_id_zeroBegin);
				for($i=0;$i<$steps;$i++){
					$pager->init($pager->total, $pager->pageid()-1);
					$ret = static::loopGetRecordsPage_oneStep($sort_field_type, $lastPage, $pager,$pageForward,$strSort);
					$lastPage = $ret['lastPage'];
				}
				return $ret;
			}
			
		}
	}
	/**
	 * @param array $sort_field_type
	 * @param array $lastPageReal
	 * @param \Sooh\DB\Pager $pager 额外的where条件
	 * @return array (lastPage=>array(), records=array())
	 */	
	protected static function loopGetRecordsPage_oneStep($sort_field_type,$lastPage,$pager,$pageForward,$strSort)
	{
		//lastPage: ['_pageid_'=>0,'_autoid_'=>array(from,to),'_subkey_'=>array(from,to), 此函数返回值中lastPage
		//			'unique'=>1(默认),'fieldSearch1'=>'val','fieldSearch2'=>'val',] 当前 搜索表单条件
		$where = static::loopGetRecordsPage_buildWhere($sort_field_type, $lastPage,$pageForward);
		static::$tmpVar = array('where'=>$where,'sort'=>$strSort,'pagesize'=>$pager->page_size);
		$func = get_called_class().'::loopGetRecordsPage';
		self::loop($func);
		$records = static::loopGetRecordsPage_sortGetPage($sort_field_type,$pageForward);

		$news = array();
		$news['_pageid_'] =$pager->page_id_zeroBegin;
		if(!empty($records)){
			reset($records);
			$firstRow =  current($records);
			$endRow = end($records);
			foreach ($sort_field_type as $k=>$r){
				$news['_'.$k.'_'] = array($firstRow[$k],$endRow[$k]);
			}
		}else{
			foreach ($sort_field_type as $k=>$r){
				$news['_'.$k.'_'] = array();
			}
		}
		foreach($news as $k=>$v){
			$lastPage['_pre_'][$k] = $lastPage['_last_'][$k];
			$lastPage['_last_'][$k]=$v;
		}
		//$lastPage['_fields_'] = $sort_field_type;
		return array('lastPage'=>$lastPage,'records'=>$records);
	}
	
	protected static function loopGetRecordsPage_buildWhere($sort_field_type,$lastPage,$pageForward){
		$where=array();
		$sortMethod = array(
			array('sort'=>'<','rsort'=>'>'),//反向翻页
			array('sort'=>'>','rsort'=>'<'),//正向翻页
		);
		if($pageForward){//正向翻页
			$pageForward=1;
		}else{//反向翻页
			$pageForward=0;
		}
		if(sizeof($sort_field_type)==1){//单键
			$w = array();
			if(isset($lastPage['_last_']['_pageid_'])){
				$k = key($sort_field_type);
				$sort = current($sort_field_type);
				$w[$k.$sortMethod[$pageForward][$sort]]=$lastPage['_last_']['_'.$k.'_'][$pageForward];
				//echo ">>$k $sort $pageForward ".$sortMethod[$pageForward][$sort]." ".$lastPage['_'.$k.'_'][$pageForward]."\n";
				//echo ">>1>>".json_encode($w)."\n";
			}
			if(is_array($lastPage['where'])){
				$w = static::loopGetRecordsPage_mergeWhere($w,$lastPage['where']);
				//echo ">>2>>".json_encode($w)."\n";
			}
			//echo ">>3>>".json_encode($w)."\n";
			$where[]=$w;
		}else{//双键, 【=，>】，【>,null】
			$wEq = array();
			$wCmp = array();
			if(isset($lastPage['_last_']['_pageid_'])){
				$k1 = key($sort_field_type);
				$sort1 = current($sort_field_type);
				array_shift($sort_field_type);
				$k2 = key($sort_field_type);
				$sort2 = current($sort_field_type);
				$wEq[$k1.'=']=$lastPage['_last_']['_'.$k1.'_'][$pageForward];
				$wEq[$k2.$sortMethod[$pageForward][$sort2]]=$lastPage['_last_']['_'.$k2.'_'][$pageForward];
				
				$wCmp[$k1.$sortMethod[$pageForward][$sort1]]=$lastPage['_last_']['_'.$k1.'_'][$pageForward];
				if(is_array($lastPage['where'])){
					$wEq = static::loopGetRecordsPage_mergeWhere($wEq,$lastPage['where']);
					$wCmp = static::loopGetRecordsPage_mergeWhere($wCmp,$lastPage['where']);
				}
				$where[]=$wEq;
				$where[]=$wCmp;
			}else{
				if(is_array($lastPage['where'])){
					$where[] = $lastPage['where'];
				}else{
					$where[]=$wEq;
				}
			}
		}
		//echo "WHERE=";
		//var_dump($where);
		return $where;
	}
	protected static function loopGetRecordsPage_mergeWhere($sys,$usr)
	{
		foreach($usr as $k=>$v){
			if(isset($sys[$k])){
				$cmp = substr($k,-1);
				if($v>$sys[$k]){
					$max=$v;
					$min=$sys[$k];
				}else{
					$max=$sys[$k];
					$min=$v;
				}
				if($cmp=='['){
					$sys[$k]=$min;
				}elseif($cmp=='<'){
					$sys[$k]=$min;
				}elseif($cmp=='>'){
					$sys[$k]=$max;
				}elseif($cmp==']'){
					$sys[$k]=$max;
				}else{
					throw new \ErrorException('where merge conflict:'.$k);
				}
			}else{
				$sys[$k]=$v;
			}
		}
		return $sys;
	}
	protected static function loopGetRecordsPage_sortGetPage($sortField_sortType,$pageForward)
	{
		$all=array();
		switch (sizeof($sortField_sortType)){
			case 1:
				$tmp=array();
				$k = key($sortField_sortType);
				foreach(static::$tmpVar['rs'] as $rs){
					foreach($rs as $r){
						$tmp[ $r[ $k ] ][] = $r;
					}
				}
				if(current($sortField_sortType)==='sort'){
					ksort ($tmp);
				}else{
					krsort ($tmp);
				}
				foreach($tmp as $r){
					$all = array_merge($all,$r);
				}
				break;
			case 2:
				$tmp=array();
				$sort_key=  array_keys($sortField_sortType);
				foreach(static::$tmpVar['rs'] as $rs){
					foreach($rs as $r){
						$tmp[ $r[ $sort_key[0] ] ][ $r[ $sort_key[1] ] ] = $r;
					}
				}
				if(current($sortField_sortType)==='sort'){
					ksort ($tmp);
				}else{
					krsort ($tmp);
				}

				$cmp = $sortField_sortType[$sort_key[1]];
				if($cmp==='sort'){
					foreach($tmp as $rs1){
						ksort ($rs1);
						$all = array_merge($all,$rs1);
					}
				}
				else {
					foreach($tmp as $rs1){
						krsort ($rs1);
						$all = array_merge($all,$rs1);
					}
				}
				break;
			default:
				throw new \ErrorException('sort field needs to be 1-2, given:'.  json_encode($sortField_sortType));
		}
		if($pageForward){
			while(sizeof($all)>static::$tmpVar['pagesize']){
				array_pop ($all);
			}
		}else{
			while(sizeof($all)>static::$tmpVar['pagesize']){
				array_shift ($all);
			}
		}
		return $all;		
	}
	/**
	 * loopGetRecords调用，获取符合条件的记录
	 * @param \Sooh\DB\Interfaces\All $db
	 * @param string $tb
	 */
	protected static function loopGetRecordsPage_getRecords($db,$tb)
	{
		
		foreach(static::$tmpVar['where'] as $realWhere){
			static::$tmpVar['rs'][] = $db->getRecords($tb,'*',$realWhere,static::$tmpVar['sort'],static::$tmpVar['pagesize']);
		}
	}
	protected $fieldsDatetime=array();
	protected $fieldsSimple=array();
	protected $lockedByMe=false;
	/**
	 * 锁定一条记录(TODO: 分散设计后，应该没有很多的冲突几率，考虑加个冲突日志并酌情报警)
	 * @param string $msg msg describe the reason
	 * @param int $secExpire default 3year
	 * @return boolean 
	 * @throws ErrorException when record is locked already
	 */
	public function lock($msg,$secExpire=94608000)
	{
		$dt = \Sooh\Base\Time::getInstance();
		if(''!==($lockMsg=$this->isLocked())){
			error_log('locked already:'.  get_called_class().' '.  json_encode($this->pkey));
			return false;
		}else{
//			$err= new \ErrorException('record lock:'.$this->r[$this->fieldName_verid]);
//			error_log($err->getMessage()."\n".$err->getTraceAsString());
			$tmp='expire='.($dt->timestamp()+$secExpire).'&msg='.$msg.'&ymd='.$dt->YmdFull.'&ip='. \Sooh\Base\Tools::remoteIP();

			$where = $this->pkey;
			$where[$this->fieldName_verid] = $this->r[$this->fieldName_verid];
			$ret = $this->db()->updRecords($this->tbname(), array($this->fieldName_verid=> $this->r[$this->fieldName_verid]+1,$this->fieldName_lockmsg=>$tmp), $where);
			
			if($ret===1){
				$this->r[$this->fieldName_verid]++;
				$this->r[$this->fieldName_lockmsg]=$tmp;
				$this->lockedByMe=true;
				return true;
			}else{
				error_log('locked failed');
				return false;
			}
		}
	}
	/**
	 * 检查当前是否已经锁定
	 * @return  string '' means not locked, otherwise lock-reason returned 
	 */
	public function isLocked()
	{
		if(!empty($this->r[$this->fieldName_lockmsg])){
			$tmp = null;
			parse_str($this->r[$this->fieldName_lockmsg], $tmp);
			var_export($tmp,true);
			if($tmp['expire']>\Sooh\Base\Time::getInstance()->timestamp()){
				return $tmp['msg'];
			}else{
				return '';
			}
		}else {
			return '';
		}
	}
	public function isLockedByThisProcess()
	{
		return $this->lockedByMe;
	}
	/**
	 * 解锁记录。（上锁后成功的update，就被解锁了，不用专门执行unlock）
	 * @return  Boolean 
	 */	
	public function unlock()
	{
		$where = $this->pkey;
		$where[$this->fieldName_verid] = $this->r[$this->fieldName_verid];
		$ret = $this->db()->updRecords($this->tbname(), array($this->fieldName_verid=>$this->r[$this->fieldName_verid]+1,$this->fieldName_lockmsg=>''), $where);
		if($ret===1){
			$this->r[$this->fieldName_verid]++;
			$this->r[$this->fieldName_lockmsg]='';
			return true;
		}else{
			return false;
		}
	}
	/**
	 * 获取当前的主键
	 * @return array
	 */
	public function getPKey()
	{
		return $this->pkey;
	}
	/**
	 * 获取某个字段的值
	 * @param string $field 字段名
	 * @param boolean $nullAccepted 当取得的值是null的时候，是否应该丢出异常
	 */	
	public function getField($field,$nullAccepted=false)
	{
		if(!isset($this->r[$field])){
			if($nullAccepted==false){
				$err = new \ErrorException("fieldGet of $field not loaded or is NULL \nwhen request:"
													.$_SERVER["REQUEST_URI"]
											."\n check code of load(cur loaded:"
													.(is_array($this->r)?implode(',',array_keys($this->r)):"NULL")
											.")\npkey=". json_encode($this->pkey));
				error_log($err->getMessage()."\n".$err->getTraceAsString());
				throw $err;
			}else{
				return null;
			}
		}
		return $this->r[$field];
	}
	protected $_fieldAutoInc=null;
	public function autoIncrementUsed($fieldName)
	{
		$this->_fieldAutoInc=$fieldName;
	}
	/**
	 * 检查某个字段是否存在  或  对象是否成功加载
	 * @param type $field
	 * @return type
	 */
	public function existsField($field=null)
	{
		if($field!==null){
			return isset($this->r[$field]);
		}else{
			return !empty($this->r);
		}
	}
	/**
	 * 检查某个字段是否存在  或  对象是否成功加载
	 * @param type $field
	 * @return type
	 */
	public function exists($field=null)
	{
		if($field!==null){
			return isset($this->r[$field]);
		}else{
			return !empty($this->r);
		}
	}
	/**
	 * 设置某个字段的值
	 * @param string $field 字段名
	 * @param mixed $val  值
	 */
	public function setField($field,$val)//--------------------val 是 FIeld + 的情况
	{
		$this->chged[$field]=$field;
		$this->r[$field]=$val;
	}
	
	public function delete($skipLock=true)
	{
		try{
			$where =  $this->pkey;
			if($skipLock==false){
				$where[$this->fieldName_lockmsg]='';
			}
			$tbCache=null;
			if($this->cacheWhenVerIDIs){
				$dbCache = static::getDBAndTbName($tbCache, $where,true);
				$dbCache->kvoDelete($tbCache, $where);
			}
			$db = static::getDBAndTbName($this->tbname, $where,false);
			$db->kvoDelete($this->tbname, $where);
			if($this->cacheWhenVerIDIs){
				$dbCache->kvoDelete($tbCache, $where);
			}
			
			$this->chged=array();
			$this->r=array();
		} catch (\Exception $ex) {
			error_log($ex->getMessage().$ex->getTraceAsString());
		}
	}
	
	protected function fieldsUpds($ks)
	{
		$tmp = array();
		foreach($ks as $k){
			if(!is_array($this->r[$k])){
				$tmp[$k]=$this->r[$k];
			}else {
				$tmp[$k] = json_encode($this->r[$k]);
			}
		}
		foreach($this->fieldsDatetime as $k){
			$tmp[$k] = date('Y-m-d H:i:s',$tmp[$k]);
		}
		$tmp[$this->fieldName_verid] = self::nextVerId($this->r[$this->fieldName_verid]);
		return $tmp;
	}
	public static function nextVerId($curId)
	{
		return ($curId>=99999999)?1:$curId+1;
	}
	protected function trySave()
	{
		$tbCache=null;
		$db = static::getDBAndTbName($this->tbname, $this->pkey,false);
		if($this->cacheWhenVerIDIs){
			$dbCache = static::getDBAndTbName($tbCache, $this->pkey,true);
		}
		$class = get_called_class();
		if(empty($this->chged)){
			$err = new \ErrorException(get_called_class().':nothing needs to do');
			throw $err;
		}
		
		try{
			if($this->lockedByMe===false && $this->isLocked()) {
				throw new \ErrorException("Can not update as record is locked");
			}
			if(!isset($this->r[$this->fieldName_verid])){
				
				$verCurrent = array($this->fieldName_verid=>1);
				$pkeyBak=$this->pkey;
				$this->pkey = $db->kvoNew($this->tbname, $this->fieldsUpds($this->chged), $this->pkey,$verCurrent,$this->_fieldAutoInc);
				$this->r[$this->fieldName_verid]=1;
				foreach($this->pkey as $k=>$v){
					$this->r[$k]=$v;
				}
//				if($this->cacheWhenVerIDIs){
//					$dbCache->kvoNew($tbCache, $this->r, $this->pkey,$verCurrent,$this->_fieldAutoInc);
//				}
				if(json_encode($pkeyBak) !== json_encode($this->pkey)){
					$sOld=json_encode($pkeyBak);
					$md5 = md5($sOld);
					$sNew = json_encode($this->pkey);
					$md5New = md5($sNew);
					unset(self::$_copies[$class][$md5]);
					self::$_copies[$class][$md5New] = $this;
					$class = get_class();
				}
				return 1;
			}else{
				$verCurrent = array($this->fieldName_verid=>$this->r[$this->fieldName_verid]);
				$whereForUpdate = $this->pkey;
				if(isset($this->r[$this->fieldName_lockmsg])){
					if(!empty($this->r[$this->fieldName_lockmsg]) && !$this->lockedByMe){
						throw new \Sooh\Base\ErrException(\Sooh\Base\ErrException::msgLocked);
					}
					
					if($this->lockedByMe){
						$this->setField($this->fieldName_lockmsg, '');
					}else{
						$whereForUpdate[$this->fieldName_lockmsg]='';
					}
				}

				if($this->cacheWhenVerIDIs<=1){
					if($db->kvoFieldSupport()){
						$fields = $this->fieldsUpds($this->chged);
						$nextVerId = $fields[$this->fieldName_verid];
						$_ret = $db->kvoUpdate($this->tbname, $fields, $whereForUpdate, $verCurrent);
					}else{
						$fieldsAll = $this->fieldsUpds(array_keys($this->r));
						$nextVerId = $fieldsAll[$this->fieldName_verid];
						$_ret = $db->kvoUpdate($this->tbname, $fieldsAll, $whereForUpdate, $verCurrent);
					}
                    if($this->cacheWhenVerIDIs){
						if($dbCache->kvoFieldSupport()){
							if(empty($fields)){
								$fields = $this->fieldsUpds($this->chged);
								$nextVerId = $fields[$this->fieldName_verid];
							}
							$dbCache->kvoUpdate($tbCache, $fields, $whereForUpdate, $verCurrent,true);
						}else{
							if(empty($fieldsAll)){
								$fieldsAll=$this->fieldsUpds(array_keys($this->r));
								$nextVerId = $fieldsAll[$this->fieldName_verid];
							}
							$dbCache->kvoUpdate($tbCache, $fieldsAll, $whereForUpdate, $verCurrent,true);
						}
                    }
					$this->r[$this->fieldName_verid]= $fields[$this->fieldName_verid];
                }else{
					if($dbCache->kvoFieldSupport()){
						$fields = $this->fieldsUpds($this->chged);
						$nextVerId = $fields[$this->fieldName_verid];
						$_ret = $dbCache->kvoUpdate($tbCache, $fields, $whereForUpdate, $verCurrent);
					}else{
						$fieldsAll = $this->fieldsUpds($this->chged);
						$nextVerId = $fieldsAll[$this->fieldName_verid];
						$_ret = $dbCache->kvoUpdate($tbCache, $fieldsAll, $whereForUpdate, $verCurrent);
					}
					if($this->r[$this->fieldName_verid]%$this->cacheWhenVerIDIs==0){
						try{
							if($db->kvoFieldSupport()){
						        if(empty($fields)){
						            $fields = $this->fieldsForSqlUpds($this->chged);
						            $nextVerId = $fields[$this->fieldName_verid];
						        }
						        $_ret = $db->kvoUpdate($this->tbname, $fields, $whereForUpdate, $verCurrent,true);
						    }else{
						        if(empty($fieldsAll)){
						            $fieldsAll=$this->fieldsForSqlUpds(array_keys($this->r));
						            $nextVerId = $fieldsAll[$this->fieldName_verid];
						        }
						        $_ret = $db->kvoUpdate($this->tbname, $fieldsAll, $whereForUpdate, $verCurrent,true);
						    }
						}catch(\ErrorException $e){
							error_log("fatal error: $class : update disk failed after cache updated");
							throw $e;
						}
					}
					$this->r[$this->fieldName_verid] = $nextVerId;
				}
			}
			$this->lockedByMe=false;
			return $_ret;
		}catch(\ErrorException $e){//key duplicate -> add failed
			throw $e;
		}

	}
	protected $lastErrCmd;
	function update($callback=null)
	{
		$retry = 0;
		while($retry<=2){
			if($callback!==null){
				if(is_array($callback)){
					$class = $callback[0];
					$func = $callback[1];
					$class->$func($this,$retry);
				}elseif(is_callable($callback)){
					$callback($this,$retry);
				}else{
					throw new \ErrorException('invalid callback given');
				}
			}

			try{
				$this->callbackOn(self::onBeforeSave);
				$_ret = $this->trySave();
				$this->callbackOn(self::onAfterSave);
				return $_ret;
			}catch(\ErrorException $e){
				if($callback!==null){
					$retry++;
					$this->reload();
				}
				else {
					throw $e;
				}
			}
		}
		throw $e;
	}
	
	public function registerOn($callback,$evt)
	{
		$this->listener[$evt][]=$callback;
	}
	protected function callbackOn($type)
	{
		if(!empty($this->listener[$type])){
			foreach($this->listener[$type] as $listener){
				if(is_array($listener)) {
					call_user_func ($listener, $this);
				}elseif(is_string($listener)){
					call_user_func ($listener, $this);
				}else {
					$listener($this);
				}
			}
		}
	}
	public function dump()
	{
		return $this->r;
	}

}
