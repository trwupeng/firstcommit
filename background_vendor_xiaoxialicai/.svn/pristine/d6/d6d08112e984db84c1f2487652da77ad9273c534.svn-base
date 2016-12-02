<?php
namespace Sooh\DB\Types;

use \Sooh\DB\Error as sooh_dbErr;
use \Sooh\DB\Base\Field as sooh_dbField;
use \Sooh\DB\Base\Table as sooh_dbTable;
use \Sooh\DB\Base\SQLDefine as sooh_sql;
use \Sooh\DB\Broker as sooh_broker;

/**
 * TODO: _fmtObj() _tmpObj() getRank()
 * not support : trans_begin() trans_commit() trans_cancel()
 * continue: addRecord()
 * @author Simon Wang <sooh_simon@163.com> 
 */
class Redis implements \Sooh\DB\Interfaces\All
{
	public function kvoFieldSupport(){return true;}
	public function kvoLoad($obj, $fields,$arrPkey)
	{
		return $this->getRecord($obj, $fields,$arrPkey);
	}
	public function kvoUpdate($obj, $fields, $arrPkey, $verCurrent, $override=false)
	{
		$k = key($verCurrent);
		$v = current($verCurrent);
		$where = $arrPkey;
		if($override){
			$fields[$k] = $v;
			$where[$k.'<']=$v;
			$ret = $this->updRecords($obj, $fields,$arrPkey);
			if(!$ret){
				throw new \Sooh\Base\ErrException('update failed, pkey error?');
			}
		}else{
			if($v>99999999){
				$fields[$k]=substr($v,-8);
			}elseif($v==99999999){
				$fields[$k]=1;
			}else{
				$fields[$k]=$v+1;
			}
			$where[$k]=$v;
			$ret = $this->updRecords($obj, $fields,$where);
			if($ret!==1){
				throw new \Sooh\Base\ErrException('update failed, verid changed?');
			}
		}
	}
	public function kvoNew($obj,$fields, $arrPkey,$verCurrent, $fieldAuto=null)
	{
		foreach ($arrPkey as $k=>$v){
			$fields[$k]=$v;
		}
		if(is_array($verCurrent)){
			$fields[key($verCurrent)]=  current($verCurrent);
		}
		$autoid = $this->addRecord($obj, $fields,false,$fieldAuto);
		if($fieldAuto!==null) {
			$arrPkey[$fieldAuto]=$autoid;
		}
		return $arrPkey;
	}
	public function kvoDelete($obj,$arrPkey)
	{
		return $this->delRecords($obj,$arrPkey);
	}
	public function getRank($obj,$whereForWho,$fieldScore,$rsort=true)
	{
		throw new \Sooh\Base\ErrException(__FUNCTION__.' not support in '.__CLASS__." obj:$obj.$fieldScore more:".  json_encode($whereForWho).' '.($rsort?'true':'false'));
	}
	/**
	 *
	 * @var sooh_sql
	 */
	private $_lastCmd=null;
	private $_connection=null;
	public $dbConf;
	public function __construct($conf) 
	{
		$this->_connection = $conf;
	}
	/**
	 * where用的一个函数，使用中无视
	 * @param type $tmp
	 * @return type
	 */	
	public function _tmpObj($tmp)
	{
		$bak = $this->objForCreateWhere;
		$this->objForCreateWhere=$tmp;
		return $bak;
	}
	/**
	 * @return sooh_dbTable
	 */
	private function _fmtObj($obj)
	{
		if(is_string($obj)){
			if(is_array($this->_connection)){
				$this->connect();
			}

			$obj = trim($obj);
			$tmp = explode(' ', $obj);
			if(sizeof($tmp)==1){
				$tmp = explode('.', $obj);
				$n = sizeof($tmp);
				switch($n){
					case 1:
						$this->objForCreateWhere = new sooh_dbTable;
						$this->objForCreateWhere->db = $this->_lastDB;
						$this->objForCreateWhere->name = $obj;
						$this->objForCreateWhere->fullname = $this->objForCreateWhere->db.'.'.$this->objForCreateWhere->name;
						return $this->objForCreateWhere;
					case 2:
						//$this->dbCurrent($tmp[0]);
						$this->objForCreateWhere = new sooh_dbTable;
						$this->objForCreateWhere->db = $tmp[0];
						$this->objForCreateWhere->name = $tmp[1];
						$this->objForCreateWhere->fullname = $this->objForCreateWhere->db.'.'.$this->objForCreateWhere->name;
						return $this->objForCreateWhere;
					default :
						throw new \Sooh\Base\ErrException('objname format not support:'. var_export($obj,true));
					}
				}else{
			return $obj;
			}
		}else{
			return $this->objForCreateWhere = $obj;
		}
	}
	protected $objForCreateWhere=null; 
	private $_lastDB;
	private $_host_port=array();
	public function host()
	{
		return $this->_host_port['host'];
	}
	public function port()
	{
		return $this->_host_port['port'];
	}
	protected function connect()
	{
		$conf= $this->_connection;
		if(empty($conf)){
			throw new \Sooh\Base\ErrException('disconnection called already?');
		}
		$this->_lastCmd = new sooh_sql();
		$this->_host_port = array('host'=>$conf['host'],'port'=>$conf['port']);
		$this->_lastCmd->server = $conf['host'].':'.$conf['port'];
		$defaultDB = $conf['name'];
		unset($conf['dbEnums']);
		$this->_connection = new redis();
		$ret = $this->_connection->pconnect($conf['host'], $conf['port']);
		if($ret && !empty($conf['pass'])){
			$ret = $this->_connection->auth($conf['pass']);
		}
		if(!$ret){
			$err= sooh_dbErr::connectError;
			$skip = sooh_dbErr::$maskSkipTheseError;
			if(empty($skip) || !isset($skip[$err])){
				$lastCmd = sooh_broker::lastCmd();
				$err=new sooh_dbErr($err,'['.$err.']connect failed', '');
				error_log("[".$err->getCode()."]".$err->getMessage()."\n". $lastCmd."\n".$err->getTraceAsString());
				throw $err;
			}elseif($skip[$err]===false){
				$lastCmd = sooh_broker::lastCmd();
				$err=new sooh_dbErr($err,'['.$err.']connect failed', '');
				throw $err;
			}
		}
		$this->_chkErr();
		$this->dbCurrent($defaultDB);
		return $this->_connection;
	}
	protected function disconnect()
	{
		if(is_array($this->_connection)){
			$this->_connection=null;
		}else {
			$this->_connection->close();
			$this->_connection=array();
		}
		$this->_lastCmd=null;
	}
	protected $flgTransaction=null;
	protected $oldAutoCommit=null;
	public function trans_begin() {
		throw new \Sooh\Base\ErrException(__FUNCTION__.' not support in '.__CLASS__);
	}
	public function trans_cancel() {
		throw new \Sooh\Base\ErrException(__FUNCTION__.' not support in '.__CLASS__);
	}
	public function trans_commit() {
		throw new \Sooh\Base\ErrException(__FUNCTION__.' not support in '.__CLASS__);
	}
	public function addRecord($obj, $fields, $fieldAuto=null)
	{
		$r = explode('.', $obj);
		if(sizeof($r)===2){
			$db = $r[0];
			$tb = $r[1];
		}else{
			$db = $this->dbCurrent();
		}
		
		//db.user. [uid=sfsdfasdf,level=21]
		
		/**
$redis->LPUSH('user_uid', sfsdfasdf); //(integer) 4
$redis->SET('user_name_sfsdfasdf', 'hacker');
$redis->SET('user_level_sfsdfasdf', 21);

$redis_sort_option=array('BY'=>'user_level_*',
'SORT'=>'DESC'
);
var_dump($redis->SORT('user_id',$redis_sort_option)); //array(4) { [0]=> string(3) "222" [1]=> string(1) "1" [2]=> string(1) "2" [3]=> string(5) "59230" }

		 * 
		 */
		$obj = $this->_fmtObj($obj);
		$obj->autoInc = $fieldAuto;
		$this->_lastCmd->dowhat = "insert";
		$this->_lastCmd->tablenamefull = $obj->fullname;
		
		if($fieldAuto!==null && isset($fields[$fieldAuto]) && empty($fields[$fieldAuto])) {
			unset($fields[$fieldAuto]);
		}
		$this->_lastCmd->field = $fields;

		$this->_query(null);
		return $this->getLastInsertId($obj);
	}
	public function addLog($obj, $fields)
	{
		$obj = $this->_fmtObj($obj);
		$this->_lastCmd->dowhat = "addlog";
		$this->_lastCmd->tablenamefull = $obj->fullname;
		$this->_lastCmd->field = $fields;
		$this->_query(null);
	}
	/**
	 * 
	 * @param sooh_dbTable $obj
	 */
	protected function getLastInsertId($obj)
	{
		$newid = mysqli_insert_id($this->_connection);
		return empty($newid)?true:$newid;
	}
	public function updRecords($obj, $fields, $where=null, $other=null)
	{
		$obj = $this->_fmtObj($obj);
		$this->_lastCmd->dowhat = 'update';
		$this->_lastCmd->tablenamefull = $obj->fullname;
		$this->_lastCmd->field =$fields;
		$this->_lastCmd->where = $this->_fmtWhere($where);
		$this->_lastCmd->orderby = $other;
		$this->_query(null);
		$this->_chkErr();
		$rs = mysqli_affected_rows($this->_connection);
		return $rs==0?true:$rs;
	}
	
	public function getRecordsRand($obj, $fields, $where=null, $orderby=null,$num=null)
	{
		$obj = $this->_fmtObj($obj);
		$this->_lastCmd->dowhat='select';
		$this->_lastCmd->field=$fields;
		$this->_lastCmd->tablenamefull = $obj->fullname;
		$this->_lastCmd->where = $this->_fmtWhere($where);
		$this->_lastCmd->orderby = $orderby.' sort RAND()';
		$this->_lastCmd->fromAndSize=array(0,$num);

		$rs0 = $this->_query(null);
		$rs=array();
		while (null!==($r = mysqli_fetch_assoc($rs0))){
			//$this->_chkErr();
			$rs[]=$r;
		}
		mysqli_free_result($rs0);
		return $rs;
	}
	public function getRecords($obj, $fields, $where=null, $orderby=null,$pagesize=null,$rsfrom=0)
	{
		$obj = $this->_fmtObj($obj);
		$this->_lastCmd->dowhat='select';
		$this->_lastCmd->field=$fields;
		$this->_lastCmd->tablenamefull = $obj->fullname;
		$this->_lastCmd->where = $this->_fmtWhere($where);
		$this->_lastCmd->orderby = $orderby;
		$this->_lastCmd->fromAndSize=array($rsfrom,$pagesize);

		$rs0 = $this->_query(null);
		$rs=array();
		while (null!==($r = mysqli_fetch_assoc($rs0))){
			//$this->_chkErr();
			$rs[]=$r;
		}
		mysqli_free_result($rs0);
		return $rs;
	}
	
	public function getRecord($obj, $fields, $where=null, $orderby=null)
	{
		$obj = $this->_fmtObj($obj);
		$this->_lastCmd->dowhat='select';
		$this->_lastCmd->field=$fields;
		$this->_lastCmd->tablenamefull = $obj->fullname;
		$this->_lastCmd->where = $this->_fmtWhere($where);
		$this->_lastCmd->orderby = $orderby;
		$this->_lastCmd->fromAndSize=array(0,1);

		$rs0 = $this->_query(null);
		$r = mysqli_fetch_assoc($rs0);
		$this->_chkErr();
		mysqli_free_result($rs0);
		return $r;
	}
	public function getAssoc($obj, $key,$otherfields, $where=null, $orderby=null,$pagesize=null,$rsfrom=0)
	{
		$obj = $this->_fmtObj($obj);
		if(is_array($otherfields)){
			$otherfields[]=$key;
		}else{
			$otherfields.=",".$key;
		}
		$this->_lastCmd->dowhat='select';
		$this->_lastCmd->field=$otherfields;
		$this->_lastCmd->tablenamefull = $obj->fullname;
		$this->_lastCmd->where = $this->_fmtWhere($where);
		$this->_lastCmd->orderby = $orderby;
		$this->_lastCmd->fromAndSize=array($rsfrom,$pagesize);
		
		$rs0 = $this->_query(null);
		$rs = array();
		while (null!==($r = mysqli_fetch_assoc($rs0))){
			//$this->_chkErr();
			$rs[$r[$key]] = $r;
		}
		mysqli_free_result($rs0);
		return $rs;
	}

	public function getCol($obj, $col, $where=null, $orderby=null,$pagesize=null,$rsfrom=0)
	{
		$obj = $this->_fmtObj($obj);
		$this->_lastCmd->dowhat='select';
		$this->_lastCmd->field=$col;
		$this->_lastCmd->tablenamefull = $obj->fullname;
		$this->_lastCmd->where = $this->_fmtWhere($where);
		$this->_lastCmd->orderby = $orderby;
		$this->_lastCmd->fromAndSize=array($rsfrom,$pagesize);

		$rs0 = $this->_query(null);
		$rs = array();
		while (null!==($r = mysqli_fetch_row($rs0))){
			//$this->_chkErr();
			$rs[] = $r[0];
		}
		mysqli_free_result($rs0);
		return is_array($rs)?$rs:null;
	}
	public function status()
	{
		if(is_array($this->_connection)){
			$this->connect();
		}
		$rs = mysqli_info($this->_connection);
		$rs0 = $this->_query("show variables like '%char%'");
		while (null!==($r = mysqli_fetch_row($rs0))){
			$rs[$r[0]] = $r[1];
		}
		return $rs;
	}
	public function resetAutoIncrement($obj, $newstart=1)
	{
		$obj = $this->_fmtObj($obj);
		$this->_query("alter table $obj AUTO_INCREMENT = $newstart");
	}
	public function execCustom($arr)
	{
		if(is_array($this->_connection)){
			$this->connect();
		}
		if(is_array($arr)){
			$rs0 = $this->_query(current($arr));
		}elseif(!is_scalar ($arr) && !empty($arr)){
			$this->_lastCmd=$arr;
			$rs0 = $this->_query(null);
		}else{
			throw new \Sooh\Base\ErrException("arg not support for ".__FUNCTION__.":". serialize($arr));
		}
		$this->_chkErr();
		return $rs0;
	}
	public function fetchAssocThenFree($result)
	{
		$rs=array();
		while (null!==($r = mysqli_fetch_assoc($result))){
			//$this->_chkErr();
			$rs[]=$r;
		}
		mysqli_free_result($result);
		return $rs;
	}
	
	public function getOne($obj, $field, $where=null, $orderby=null)
	{
		$obj = $this->_fmtObj($obj);
		$this->_lastCmd->dowhat='select';
		$this->_lastCmd->field=$field;
		$this->_lastCmd->tablenamefull = $obj->fullname;
		$this->_lastCmd->where = $this->_fmtWhere($where);
		$this->_lastCmd->orderby = $orderby;
		$this->_lastCmd->fromAndSize=array(0,1);
		
		$rs0 = $this->_query(null);
		$r = mysqli_fetch_row($rs0);
		$this->_chkErr();
		mysqli_free_result($rs0);
		return is_array($r)?$r[0]:null;
	}
	public function getPair($obj, $key,$val, $where=null, $orderby=null,$pagesize=null,$rsfrom=0)
	{
		$obj = $this->_fmtObj($obj);
		$this->_lastCmd->dowhat='select';
		$this->_lastCmd->field=array($key,$val);
		$this->_lastCmd->tablenamefull = $obj->fullname;
		$this->_lastCmd->where = $this->_fmtWhere($where);
		$this->_lastCmd->orderby = $orderby;
		$this->_lastCmd->fromAndSize=array($rsfrom,$pagesize);
		
		$rs0 = $this->_query(null);
		$rs = array();
		while (null!==($r = mysqli_fetch_row($rs0))){
			//$this->_chkErr();
			$rs[$r[0]] = $r[1];
		}
		mysqli_free_result($rs0);
		return $rs;
	}
	private $fieldForOnDup=null;
	/**
	 * 
	 * @param array $fields
	 * @param sooh_dbField $_onDup_
	 * @return type
	 */
	protected function _fmtField($fields,$_onDup_=null)
	{
		if (is_array($fields)){
			$fieldForOnDup='';
			$buf = "";
			$fieldsOnDup = $_onDup_;
			foreach ($fields as $k=>$v){
				if (is_int($k)) {
					$buf .= ",$v";
				}
				else {
					
					if(!is_scalar($v) && $v!==null){
						$_onDup_=$v;
						switch($_onDup_->mathMethod){
							case '+':
							case '-':
							case '*':
							case '/':
								$tmp = ",$k=$k".$_onDup_->mathMethod.$this->_safe($k,$_onDup_->val);
								break;
							default:
								$err= new \Sooh\Base\ErrException('unsupport sooh_dbField::method '.var_export($v,true));
								error_log($err->getMessage()."\n".$err->getTraceAsString());
								throw $err;
						}
						
						$buf .= $tmp;
						if(in_array($k, $fieldsOnDup)){
							$fieldForOnDup .= $tmp;
						}
					}else{
						$tmp = ",$k=".$this->_safe($k,$v);
						$buf .= $tmp;
						if(is_array($fieldsOnDup) && in_array($k, $fieldsOnDup)){
							$fieldForOnDup .= $tmp;
						}
					}
				}
			}
			$buf=substr($buf,1);
			if($fieldForOnDup!==''){
				$this->fieldForOnDup = substr($fieldForOnDup,1);
			}
		}else{
			$buf=$fields;
		}
		return $buf;
	}
	public function ensureRecord($obj, $fields, $fieldupdIfExist, $fieldAuto=null)
	{
		if (is_string($fieldupdIfExist)){
			$fieldupdIfExist = explode(",",$fieldupdIfExist);
		}
		$obj = $this->_fmtObj($obj);
		$sql = "insert into $obj->fullname set "
				.$this->_fmtField($fields,$fieldupdIfExist)
				.' ON DUPLICATE KEY UPDATE ';
		$sql .= $this->fieldForOnDup;
		
		$fieldAuto = $this->_query($sql);
		$fieldAuto = mysqli_insert_id($this->_connection);
		return $fieldAuto==0?true:$fieldAuto;
	}

	public function delRecords($obj,$where=null)
	{
		$obj = $this->_fmtObj($obj);
		$this->_lastCmd->dowhat='delete';
		$this->_lastCmd->tablenamefull = $obj->fullname;
		$this->_lastCmd->where = $this->_fmtWhere($where);
		
		$this->_query(null);
		return mysqli_affected_rows($this->_connection);
	}
	protected function _chkErr($skip=0)
	{
		$errno = mysqli_errno($this->_connection);

		if ($errno) {
			$message = mysqli_error($this->_connection);

			switch ($errno){
				case 1054:$err=sooh_dbErr::fieldNotExists;break;
				case 1045:$err=sooh_dbErr::connectError;break;
				case 1049:$err=sooh_dbErr::connectError;break;
					
				case 1050:$err=sooh_dbErr::tableExists;break;
				case 1146:$err=sooh_dbErr::tableNotExists;break;
				case 1060:$err=sooh_dbErr::fieldExists;break;
				case 1062:
				case 1022:
				case 1069:
					//[1062]Duplicate entry '2' for key 'PRIMARY''
					$dupKey = explode('for key ', $message);
					$dupKey = trim(array_pop($dupKey),'\'');
					$err=sooh_dbErr::duplicateKey;
					break;
				default:$err=sooh_dbErr::otherError; break;
			}
			if(empty($skip) || !isset($skip[$err])){
				$lastCmd = sooh_broker::lastCmd();
				$err=new sooh_dbErr($err,'['.$errno.']'.$message, $lastCmd);
				if(!empty($dupKey)){
					$err->keyDuplicated=$dupKey;
				}
				error_log("[".$err->getCode()."]".$err->getMessage()."\n". $lastCmd."\n".$err->getTraceAsString());
				throw $err;
			}elseif($skip[$err]===false){
				$lastCmd = sooh_broker::lastCmd();
				$err=new sooh_dbErr($err,'['.$errno.']'.$message, $lastCmd);
				if(!empty($dupKey)){
					$err->keyDuplicated=$dupKey;
				}
				throw $err;
			}
		}
	}
/*
	protected function _fmtFinalPart($orderby, $pagesize=null,$rsfrom=null)
	{
		if ($pagesize){
			if ($rsfrom) $limit = " limit $rsfrom,$pagesize";
			else $limit = " limit $pagesize";
		}else $limit = '';
		
		$sort_group='';
		if(!empty($orderby)){
			$arr = explode(' ',trim($orderby));
			$mx = count($arr);
			$orderby=array();
			$groupby=array();
			for($i=0;$i<$mx;$i+=2){
				$k = $arr[$i];
				$v = $arr[$i+1];
				switch($k){
					case 'rsort':
						$orderby[]= $v.' desc';
						break;
					case 'sort':
						$orderby[]= $v;
						break;
					case 'groupby':
					case 'group':
						$groupby[]= $v;
						break;
					default:
						$err = new \Sooh\Base\ErrException('unsupport:'.$orderby);
						throw $err;
				}
			}
			if(!empty($groupby))$sort_group.=' group by '.implode (',', $groupby);
			if(!empty($orderby))$sort_group.=' order by '.implode (',', $orderby);
		}
		return $sort_group.$limit;
	}
*/
	protected function _query($sql)
	{
		if($sql == null){
			if(empty($this->_lastCmd)){
				throw new \Sooh\Base\ErrException('empty sql given');
			}
			$this->_lastCmd->dowhat = strtolower($this->_lastCmd->dowhat);
			switch($this->_lastCmd->dowhat){
				case 'insert':
					$sql='insert into '.$this->_lastCmd->tablenamefull.' set '.$this->_fmtField ($this->_lastCmd->field);
					break;
				case 'addlog':
					$sql='insert delayed into '.$this->_lastCmd->tablenamefull.' set '.$this->_fmtField ($this->_lastCmd->field);
					break;
				case 'insert':
					$sql='insert into '.$this->_lastCmd->tablenamefull.' set '.$this->_fmtField ($this->_lastCmd->field);
					break;
				case 'update':
					$sql = 'update '.$this->_lastCmd->tablenamefull.' set '. $this->_fmtField ($this->_lastCmd->field);
					break;
				case 'delete':
					$sql = 'delete from '.$this->_lastCmd->tablenamefull;
					break;
				case 'select':
					$sql = 'select '. $this->_fmtField ($this->_lastCmd->field).' from '.$this->_lastCmd->tablenamefull;
					break;
				default:
					throw new \Sooh\Base\ErrException("unsupport sql cmd:".$this->_lastCmd->dowhat);
			}
			
			
			if(!empty($this->_lastCmd->where)){
				$sql.= ' '.$this->_lastCmd->where;
			}

			if(!empty($this->_lastCmd->orderby)){
				$arr = explode(' ',trim($this->_lastCmd->orderby));
				$mx = count($arr);
				$orderby=array();
				$groupby=array();
				for($i=0;$i<$mx;$i+=2){
					$k = $arr[$i];
					$v = $arr[$i+1];
					switch($k){
						case 'rsort':
							$orderby[]= $v.' desc';
							break;
						case 'sort':
							$orderby[]= $v;
							break;
						case 'groupby':
						case 'group':
							$groupby[]= $v;
							break;
						default:
							$err = new \Sooh\Base\ErrException('unsupport:'.$orderby);
							throw $err;
					}
				}
				if(!empty($groupby)){
					$sort_group.=' group by '.implode (',', $groupby);
				}
				if(!empty($orderby)){
					$sort_group.=' order by '.implode (',', $orderby);
				}
				$sql.= ' '.$sort_group;
			}
			
			if($this->_lastCmd->dowhat=='select' && is_array($this->_lastCmd->fromAndSize) && $this->_lastCmd->fromAndSize[1]>0){
				$sql.=' limit '.$this->_lastCmd->fromAndSize[0].','.$this->_lastCmd->fromAndSize[1];
			}
			
			$this->_lastCmd->resetForNext();
			$this->_lastCmd->strTrace='['.$this->_lastCmd->server.']'.$sql;
		}else{
			if(is_string($sql)){
				$this->_lastCmd->resetForNext();
				$this->_lastCmd->strTrace='['.$this->_lastCmd->server.']'.$sql;
			}else {
				$err=new \Sooh\Base\ErrException('sql gived is not a string');
				error_log($err->getMessage()."\n".$err->getTraceAsString());
				throw $err;
			}
		}

		sooh_broker::pushCmd($this->_lastCmd);
		
		$skip = sooh_dbErr::$maskSkipTheseError;
		sooh_dbErr::$maskSkipTheseError=array();
		
		$rs = mysqli_query($this->_connection, $sql);
		
		$this->_chkErr($skip);
		return $rs;
	}
	private function _fmtWhere($where)
	{
		if(!empty($where)){
			if(is_array($where)){
				if(count($where)==1 && strtoupper(key($where))=='OR'){
					$where = current($where);
					if(count($where)==1 && is_array($where[0]))	{
						$where= current($where);
					}
					$where=$this->newWhereBuilder()->init('OR',$this->objForCreateWhere)->append($where, null)->end();
				}else {
					$where=$this->newWhereBuilder()->init('AND',$this->objForCreateWhere)->append($where, null)->end();
				}
			}elseif(is_scalar ($where)){
				throw new \Sooh\Base\ErrException('where struct not support');
			}else{
				$where = $where->end();
			}
			return $where;
		}else{
			return '';
		}
	}
	public function free()
	{
		$this->disconnect();
		$n = sizeof($this->wheresCreated);
		if($n>1){
			foreach($this->wheresCreated as $i=>$o){
				if($i){
					$o->end();
				}
			}
		}
	}
	/**
	 * @return \Sooh\DB\Interfaces\Where
	 */
	public function newWhereBuilder()
	{
		if(is_array($this->_connection)){
			$this->connect();
		}
		$sn = $this->wheresCreated[0];
		$o = new \Sooh\DB\Base\Where($sn);
		$this->wheresCreated[$sn]=$o;
		$this->wheresCreated[0]++;
		$o->_HandleAutoSet($this);
		return $o;
	}
	public function _whereDone($snOfWhere)
	{
		unset($this->wheresCreated[$snOfWhere]);
	}
	protected $wheresCreated=array(1,);
	protected $h=null;
	
	public function _safe($fieldname,$str)
	{
		if(empty($fieldname)){
			throw new \Sooh\Base\ErrException('fieldname not given for safe string');
		}
		if(empty($this->_connection)) {
			throw new \Sooh\Base\ErrException('connection-handle not inited');
		}elseif(is_array($this->_connection)){
			throw new \Sooh\Base\ErrException('connection-handle invalid');
		}
		return '\''.mysqli_real_escape_string($this->_connection,$str).'\'';
	}
	public function getRecordCount ($obj, $where=null, $orderby=null)
	{
		return $this->getOne($obj,'count(*)', $where,$orderby);
	}
	protected function _realDBName($dbid)
	{
		return $dbid;
	}
	/**
	 * @return \Sooh\DB\Interfaces\All
	 */
	public function dbCurrent($dbTo)
	{
		if(is_array($this->_connection)){
			$this->connect();
		}
		if($dbTo==null){
			return $this->_lastDB;
		}
		if($this->_lastDB!=$dbTo){
			mysqli_select_db($this->_connection, $this->_realDBName($dbTo));
			$this->_chkErr();
			$this->_lastDB=$dbTo;
		}
		return $this;
	}

	///////////////////////////////////////////////////////follower is extInterface
	public function getTables($dbname,$like=null,$addDBNameWhenReturn=false)
	{
		if(is_array($this->_connection)){
			$this->connect();
		}
		$rs = $this->_query("SHOW TABLES ". ($dbname?" FROM ".$this->_realDBName($dbname):'').($like!==null?" like '$like'":''));
		$tables = array();
		if($addDBNameWhenReturn){
			while (null!=($row = mysqli_fetch_row($rs))) {
				$tables[] = $dbname.'.'.$row[0];
			}
		}else{ 
			while (null!=($row = mysqli_fetch_row($rs))){ 
				$tables[] = $row[0];
			}
		}
		
		mysqli_free_result($rs);
		return $tables;
	}
	public function setFieldDef ($obj, $old, $new, $def=null, $after=null)
	{
		$obj = $this->_fmtObj($obj);
		sooh_dbErr::$maskSkipTheseError = array(sooh_dbErr::fieldNotExists=>true, sooh_dbErr::fieldExists=>true);
		$after = empty($after)?'':' after '.$after;
		if($new==null){
			$this->_query('alter table '.$obj->fullname.' drop '.$old);
		}elseif ($old==null){$this->_query('alter table '.$obj->fullname.' add '.$new.' '.$def.$after);
		}else{
			$this->_query('alter table '.$obj->fullname.' change '.$old.' '.$new.' '.$def.$after);
		}
	}
	public function dropTable ($obj)
	{
		$obj = $this->_fmtObj($obj);
		$this->_query('drop table if exists '.$obj);
	}
	public function ensureDB ($dbname)
	{
		$this->_query('create database if not exists '.$this->_realDBName($dbname).' CHARACTER SET utf8');
	}
	/**
	 * @param sooh_dbField $_ignore_ 
	 */
	public function ensureObj($obj,$fields,$pkey=null,$keys=null,$ukeys=null,$_ignore_=null)
	{
		$type = 'MyISAM';
		if (empty($fields)){
			throw new \Sooh\Base\ErrException('empty fields given');
		}
		$obj = $this->_fmtObj($obj);
		$parts=array();
		foreach($fields as $k=>$_ignore_){
			if(is_string($_ignore_)){
				$parts[]="$k $_ignore_";
			}else{
				switch($_ignore_->fieldType){
					case sooh_dbField::int32:
						$sql .= $k.' int not null default '.($_ignore_->val-0).',';
						break;
					case sooh_dbField::int64:
						$sql .= $k.' bigint unsigned not null default '.($_ignore_->val?:0).',';
						break;
					case sooh_dbField::string:
						if($_ignore_->fieldLen>16777215){
							$sql .= $k.' LONGTEXT,';
						}elseif($_ignore_->fieldLen>65535){
							$sql .= $k.' MEDIUMTEXT,';
						}elseif($_ignore_->fieldLen>1000){
							$sql .= $k.' text,';							
						}else{
							$sql .= $k.' varchar($def->fieldLen) not null default \''.($_ignore_->val?:'').'\',';
						}
						break;
					case sooh_dbField::float:
						$sql .= $k.' FLOAT not null default '.($_ignore_->val?:0).',';
						break;
					case sooh_dbField::blob:
						if($_ignore_->fieldLen>16777215) {
							$sql .= $k.' LONGBLOB,';
						}elseif($_ignore_->fieldLen>65535){
							$sql .= $k.' MEDIUMBLOB,';
						}elseif($_ignore_->fieldLen>255){
							$sql .= $k.' blob,';
						}else{
							$sql .= $k.' tinyblob,';
						}
						break;
				}
			}
		}
		
		$sql = "create table if not exists {$obj->fullname} (" . implode(',', $parts) ;
		if (!empty($pkey)) {
			if (is_array($pkey)){
				$sql .= ", PRIMARY KEY  (".implode(",",$pkey).")";
			}else{
				$sql .= ", PRIMARY KEY  ($pkey)";
			}
		}
		
		if (is_string($keys)){
			$keys = explode(",",$keys);
		}
		if (is_array($keys)){
			foreach ($keys as $k=>$key){
				if (is_array($key)){
					$key = implode(",",$key);
				}elseif (is_int($k)){
					$k = str_replace(",","_",$key);
				}
				$sql .= ",KEY $k ($key)";
			}
		}
		if (is_string($ukeys)){
			$ukeys = explode(",",$ukeys);
		}
		if (is_array($ukeys)){
			foreach ($ukeys as $k=> $key){
				if (is_array($key)){
					$key = implode(",",$key);
				}elseif (is_int($k)){
					$k = str_replace(",","_",$key);
				}
				$sql .= ",UNIQUE KEY $k ($key)";
			}
		}
		$sql .=  ") ENGINE=". $type.' DEFAULT CHARSET=UTF8';
		$this->execCustom(array('sql'=>$sql));
		
		return $this;
	}
}
