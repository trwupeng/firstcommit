<?php
namespace Sooh\DB\Types;

use \Sooh\DB\Error as sooh_dbErr;
use \Sooh\DB\Base\Field as sooh_dbField;
use \Sooh\DB\Base\Table as sooh_dbTable;
use \Sooh\DB\Base\SQLDefine as sooh_sql;
use \Sooh\DB\Broker as sooh_broker;
use \Sooh\Base\Trace as sooh_trace;

/**
 * @todo ensureObj（） setFieldDef（） getRecordsRand（） 等多处尚未实现
 * @author Simon Wang <sooh_simon@163.com> 
 */
class Sqlsrv implements \Sooh\DB\Interfaces\All
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
			if(!$ret)throw new \ErrorException('update failed, pkey error?');
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
			if($ret!==1)throw new \ErrorException('update failed, verid changed?');
		}
	}
	public function kvoNew($obj,$fields, $arrPkey,$verCurrent, $fieldAuto=null)
	{
		$auto = null;
		foreach ($arrPkey as $k=>$v){
			$fields[$k]=$v;
		}
		if(is_array($verCurrent))foreach ($verCurrent as $k=>$v){
			$fields[$k]=$v;
		}
		$autoid = $this->addRecord($obj, $fields,false,$fieldAuto);
		if($auto!==null) $arrPkey[$auto]=$autoid;
		return $arrPkey;
	}
	public function kvoDelete($obj,$arrPkey)
	{
		return $this->delRecords($obj,$arrPkey);
	}
	public function getRank($obj,$whereForWho,$fieldScore,$rsort=true)
	{
		$score = $this->getOne($obj, $fieldScore,$whereForWho)-0;
		if($rsort) $where = array($fieldScore.'>'=>$score);
		else $where = array($fieldScore.'<'=>$score);
		return $this->getRecordCount($obj, $where, $rsort?"sort by $fieldScore desc":"sort by $fieldScore");
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
			if(is_array($this->_connection))$this->connect();

			$obj = trim($obj);
			$tmp = explode(' ', $obj);
			if(sizeof($tmp)==1){
				$tmp = explode('.', $obj);
				$n = sizeof($tmp);
				switch($n){
					case 1:
						$this->objForCreateWhere = new sooh_dbTable;
						$this->objForCreateWhere->db = trim($this->_lastDB,'.dbo').'.dbo';
						$this->objForCreateWhere->name = $obj;
						$this->objForCreateWhere->fullname = $this->objForCreateWhere->db.'.'.$this->objForCreateWhere->name;
						return $this->objForCreateWhere;
					case 2:
						//$this->dbCurrent($tmp[0]);
						$this->objForCreateWhere = new sooh_dbTable;
						$this->_lastDB=$this->objForCreateWhere->db = trim($tmp[0],'.dbo').'.dbo';
						$this->objForCreateWhere->name = $tmp[1];
						$this->objForCreateWhere->fullname = $this->objForCreateWhere->db.'.'.$this->objForCreateWhere->name;
						return $this->objForCreateWhere;
					case 3:
						$this->objForCreateWhere = new sooh_dbTable;
						$this->_lastDB=$this->objForCreateWhere->db = $tmp[0].'.'.$tmp[1];
						$this->objForCreateWhere->name = $tmp[2];
						$this->objForCreateWhere->fullname = $this->objForCreateWhere->db.'.'.$this->objForCreateWhere->name;
						return $this->objForCreateWhere;
					default :
						throw new \ErrorException('objname format not support'. var_export($obj,true));
				}
				
			}else{
				return $obj;
			}
		}else return $this->objForCreateWhere = $obj;
	}
		protected $objForCreateWhere=null; 
	private $_lastDB;
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
		if(empty($conf))throw new \ErrorException('disconnection called already?');
		$this->_lastCmd = new sooh_sql();
		$this->_host_port = array('host'=>$conf['host'],'port'=>$conf['port']);
		$this->_lastCmd->server = $conf['host'];//.':'.$conf['port'];
		$defaultDB = $conf['name'];
		unset($conf['dbEnums']);
		if(sooh_trace::needsWrite('Sooh\DB\Broker'))sooh_trace::str("db-connect:".json_encode($conf));
		$this->_connection = sqlsrv_connect($conf['host'],array("UID"=>$conf['user'],"PWD"=>$conf['pass']) );
		
		$this->_chkErr();
		$this->dbCurrent($defaultDB);
		return $this->_connection;
	}
	protected function disconnect()
	{
		if(is_array($this->_connection))$this->_connection=null;
		else {
			sqlsrv_close($this->_connection);
			$this->_connection=array();
		}
		$this->_lastCmd=null;
	}
	protected $flgTransaction=null;
	protected $oldAutoCommit=null;
	public function trans_begin() {
		if($this->oldAutoCommit===null){
			$rs0 = $this->_query('SELECT @@AUTOCOMMIT');
			$r = sqlsrv_fetch_array($rs0,SQLSRV_FETCH_NUMERIC);
			$this->_chkErr();
			sqlsrv_free_stmt($rs0);
			if(is_array($r)){
				$this->oldAutoCommit= $r[0]-0;
				$this->flgTransaction=true;
				
			}else throw new sooh_dbErr(sooh_dbErr::otherError,'get AutoCommit failed');
		}
		if($this->oldAutoCommit==1)
			$this->_query('SET AUTOCOMMIT = 0');
		$this->_query('START TRANSACTION');
	}
	public function trans_cancel() {
		$this->_query('ROLLBACK');
		if($this->oldAutoCommit==1)
			$this->_query('SET AUTOCOMMIT = 1');
		$this->flgTransaction=null;
	}
	public function trans_commit() {
		$this->_query('COMMIT');
		if($this->oldAutoCommit==1)
			$this->_query('SET AUTOCOMMIT = 1');
		$this->flgTransaction=null;
	}
	public function addRecord($obj, $fields,  $fieldAuto=null)
	{
		$obj = $this->_fmtObj($obj);
		$obj->autoInc = $fieldAuto;
		
		$this->_lastCmd->dowhat = "insert";
		$this->_lastCmd->tablenamefull = $obj->fullname;
		
		if($fieldAuto!==null && isset($fields[$fieldAuto]) && empty($fields[$fieldAuto])) unset($fields[$fieldAuto]);
		$this->_lastCmd->field = $fields;
		
		$rset=$this->_query(null);
		$rs = sqlsrv_rows_affected($rset);
		if($rs>0)return $this->getLastInsertId($obj);
		else return false;
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
		$rs0 = $this->_query("select SCOPE_IDENTITY()");//'{$obj->fullname}'
		$r = sqlsrv_fetch_array($rs0,SQLSRV_FETCH_NUMERIC);
		$this->_chkErr();
		sqlsrv_free_stmt($rs0);
		if(is_array($r) && $r[0]!==null)return $r[0];
		else return true;
	}
	public function updRecords($obj, $fields, $where=null, $other=null)
	{
		$obj = $this->_fmtObj($obj);
		$this->_lastCmd->dowhat = 'update';
		$this->_lastCmd->tablenamefull = $obj->fullname;
		$this->_lastCmd->field =$fields;
		$this->_lastCmd->where = $this->_fmtWhere($where);
		$this->_lastCmd->orderby = $other;
		$rset=$this->_query(null);
		$this->_chkErr();
		$rs = sqlsrv_rows_affected($rset);
		if ($rs ==0) return true;
		else return $rs;
	}
	
	public function getRecordsRand($obj, $fields, $where=null, $orderby=null,$num=null)
	{
		//$obj = $this->_fmtObj($obj);
		//if(is_array($fields))$fields=  implode (',', $fields);
		//$sql = "select $fields from ".$obj->fullname." " . $this->_fmtWhere($where);
		//$sql.=$this->_fmtFinalPart($orderby.' order by RAND()',$num);
		throw new \Exception('todo:Rand() not support yet');
//		$rs0 = $this->_query(null);
//		$rs=array();
//		while (null!==($r = sqlsrv_fetch_array($rs0,SQLSRV_FETCH_ASSOC))){
//			$this->_chkErr();
//			$rs[]=$r;
//		}
//		sqlsrv_free_stmt($rs0);
//		return $rs;
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
		while (null!==($r = sqlsrv_fetch_array($rs0,SQLSRV_FETCH_ASSOC))){
			//$this->_chkErr();
			$rs[]=$r;
		}
		sqlsrv_free_stmt($rs0);
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

		$r = sqlsrv_fetch_array($rs0,SQLSRV_FETCH_ASSOC);
		$this->_chkErr();
		sqlsrv_free_stmt($rs0);
		return $r;
	}
	public function getAssoc($obj, $key,$otherfields, $where=null, $orderby=null,$pagesize=null,$rsfrom=0)
	{
		$obj = $this->_fmtObj($obj);
		if(is_array($otherfields))$otherfields[]=$key;
		else $otherfields.=",".$key;
		$this->_lastCmd->dowhat='select';
		$this->_lastCmd->field=$otherfields;
		$this->_lastCmd->tablenamefull = $obj->fullname;
		$this->_lastCmd->where = $this->_fmtWhere($where);
		$this->_lastCmd->orderby = $orderby;
		$this->_lastCmd->fromAndSize=array($rsfrom,$pagesize);
		
		$rs0 = $this->_query(null);
		$rs = array();
		while (null!==($r = sqlsrv_fetch_array($rs0,SQLSRV_FETCH_ASSOC))){
			//$this->_chkErr();
			$rs[$r[$key]] = $r;
		}
		sqlsrv_free_stmt($rs0);
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
		while (null!==($r = sqlsrv_fetch_array($rs0,SQLSRV_FETCH_NUMERIC))){
			//$this->_chkErr();
			$rs[] = $r[0];
		}
		sqlsrv_free_stmt($rs0);
		if(is_array($rs))return $rs;
		else return null;
	}
	public function status()
	{
		return array('status'=>'todo');
	}
	public function resetAutoIncrement($obj, $newstart=1)
	{
		$obj = $this->_fmtObj($obj);
		$this->_query("dbcc checkident($obj->fullname,RESEED,$newstart);");
	}
	public function execCustom($arr)
	{
		if(is_array($this->_connection))$this->connect();
		if(is_array($arr)){
			$rs0 = $this->_query(current($arr));
		}elseif(!is_scalar ($arr) && !empty($arr)){
			$this->_lastCmd=$arr;
			$rs0 = $this->_query(null);
		}else{
			throw new \ErrorException("arg not support for ".__FUNCTION__.":". serialize($arr));
		}
		$this->_chkErr();
		return $rs0;
	}
	public function fetchAssocThenFree($result)
	{
		$rs=array();
		while (null!==($r = sqlsrv_fetch_array($result,SQLSRV_FETCH_ASSOC))){
			$this->_chkErr();
			$rs[]=$r;
		}
		sqlsrv_free_stmt($result);
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
		$r = sqlsrv_fetch_array($rs0,SQLSRV_FETCH_NUMERIC);
		$this->_chkErr();
		sqlsrv_free_stmt($rs0);
		if(is_array($r))return $r[0];
		else return null;
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
		while (null!==($r = sqlsrv_fetch_array($rs0,SQLSRV_FETCH_NUMERIC))){
			//$this->_chkErr();
			$rs[$r[0]] = $r[1];
		}
		sqlsrv_free_stmt($rs0);
		return $rs;
	}
	/**
	 * 
	 * @param array $fields
	 * @param sooh_dbField $_ignore_
	 * @return type
	 */
	protected function _fmtField($fields,$_ignore_=null)
	{
		if (is_array($fields)){
			$buf = "";
			foreach ($fields as $k=>$v){
				if (is_int($k)) {
					$buf .= ",$v";
				}
				else {
					if(!is_scalar($v) && $v!==null){
						$_ignore_=$v;
						switch($_ignore_->mathMethod){
							case '+':
							case '-':
							case '*':
							case '/':
								$tmp = ",$k=$k".$_ignore_->mathMethod.$this->_safe($k,$_ignore_->val);
								break;
							default:
								$err= new \ErrorException('unsupport sooh_dbField::method '.var_export($v,true));
								error_log($err->getMessage()."\n".$err->getTraceAsString());
								throw $err;
						}
						
						$buf .= $tmp;
					}else{
						$tmp = ",$k=".$this->_safe($k,$v);
						$buf .= $tmp;
					}
				}
			}
			$buf=substr($buf,1);

		}else $buf=$fields;
		return $buf;
	}
	
	public function ensureRecord($obj, $fields, $fieldupdIfExist, $where=null, $fieldAuto=null)
	{
		$this->_fmtField($fields,$fieldupdIfExist);
		$upded = $this->updRecords($obj, $this->fieldForOnDup, $where);
		if(is_int($upded) && $upded>0)return true;
		else{
			return $this->addRecord($obj, $fields,true,$fieldAuto);
		}
	}
	public function delRecords($obj,$where=null)
	{
		$obj = $this->_fmtObj($obj);
		$this->_lastCmd->dowhat='delete';
		$this->_lastCmd->tablenamefull = $obj->fullname;
		$this->_lastCmd->where = $this->_fmtWhere($where);
		
		$rset = $this->_query(null);
		return sqlsrv_rows_affected($rset);
	}
	protected function _chkErr($skip=0)
	{
		$message = sqlsrv_errors();

		if (is_array($message) && 5701!=$message[0]['code']) {
			switch ($message[0]['code']){
				//case :$err=sooh_dbErr::connectError;break;
				case 18456:$err=sooh_dbErr::connectError;break;
					
				case 2714:$err=sooh_dbErr::tableExists;break;
				case 208:$err=sooh_dbErr::tableNotExists;break;
				case 2705:$err=sooh_dbErr::fieldExists;break;
				case 207:$err=sooh_dbErr::fieldNotExists;break;
				case 2627://ODBC Driver 11 for SQL Server][SQL Server]Violation of PRIMARY KEY constraint 'PK__test1231__3BD0198EF085A699'. Cannot insert duplicate key in object 'dbo.test12314'. The duplicate key value is (7).done
					//Violation of UNIQUE KEY constraint 'b'. Cannot insert duplicate key in object 'dbo.test12314'. The duplicate key
					$dupKey = explode(' KEY constraint \'', $message[0]['message']);
					if(substr($dupKey[0],-7)=='PRIMARY')$dupKey='PRIMARY_KEY';
					else{
						$dupKey = explode('\'. Cannot insert',$dupKey[1]);
						$dupKey = array_shift($dupKey);
					}
					$err=sooh_dbErr::duplicateKey;
					break;
				default:$err=sooh_dbErr::otherError; $message[0]['message']="(err:".$message[0]['code'].")".$message[0]['message'];break;
			}
			if(empty($skip) || !isset($skip[$err])){
				$lastCmd =sooh_broker::lastCmd();
				$err=new sooh_dbErr($err, $message[0]['message'], $lastCmd);
				if(!empty($dupKey))$err->keyDuplicated=$dupKey;
				error_log("[".$err->getCode()."]".$err->getMessage()."\n". $lastCmd."\n".$err->getTraceAsString());
				throw $err;
			}elseif($skip[$err]===false){
				$lastCmd =sooh_broker::lastCmd();
				$err=new sooh_dbErr($err, $message[0]['message'], $lastCmd);
				if(!empty($dupKey))$err->keyDuplicated=$dupKey;
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
						$err = new \ErrorException('unsupport:'.$orderby);
						sooh_trace::exception($err);
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
			if(empty($this->_lastCmd))throw new \ErrorException('empty sql given');
			$orderby=array();
			$groupby=array();
			if(!empty($this->_lastCmd->orderby)){
				$arr = explode(' ',trim($this->_lastCmd->orderby));
				$mx = count($arr);
				
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
							$err = new \ErrorException('unsupport:'.$orderby);
							sooh_trace::exception($err);
							throw $err;
					}
				}
			}
			
			$this->_lastCmd->dowhat=strtolower($this->_lastCmd->dowhat);
			switch ($this->_lastCmd->dowhat){
				case 'select':
					$sql = 'select ';
					if(is_array($this->_lastCmd->fromAndSize) && $this->_lastCmd->fromAndSize[1]>0){
						$sql.=' top '.$this->_lastCmd->fromAndSize[1].' ';
						if($this->_lastCmd->fromAndSize[0]!==0){
							if(is_array($this->_lastCmd->pkey) && sizeof($this->_lastCmd->pkey)>1){
								throw new \ErrorException("multi-pkey not support in mssql for limit");
							}
							if(is_array($this->_lastCmd->pkey)){
								$pkey = key($this->_lastCmd->pkey);
								if(is_int($pkey))$pkey=  current ($this->_lastCmd->pkey);
							}else{
								if(empty($this->_lastCmd->pkey))$pkey = 'Id';
								else{
									if(is_string($this->_lastCmd->pkey))$pkey=$this->_lastCmd->pkey;
									else throw new \ErrorException("invalid pkey  in mssql found for limit");
								}
							}
							$limit = "$pkey NOT IN (SELECT TOP {$this->_lastCmd->fromAndSize[0]} $pkey FROM {$this->_lastCmd->tablenamefull} __WHERE__";
							if(!empty($orderby))$limit.=' order by '.implode (',', $orderby);
							$limit.=")";
							//throw new \ErrorException('todo: 获取并缓存主键');//SELECT TOP 10 * FROM sql WHERE ( code NOT IN (SELECT TOP 20 code FROM TestTable ORDER BY id))
						}
					}
					$sql .=  $this->_fmtField($this->_lastCmd->field).' from '.$this->_lastCmd->tablenamefull;
					break;
				case 'addlog':
				case 'insert':
					$sql ='insert into '.$this->_lastCmd->tablenamefull ;
					$sql.=" (".implode(',', array_keys($this->_lastCmd->field)).") ";
					$sql.="values (";
					foreach($this->_lastCmd->field as $k=>$v){
						$sql.=$this->_safe($k,$v).',';
					}
					$sql = substr($sql,0,-1).")";
					break;
				case 'update':
					//update FE_temp.dbo.tb_user set tb_user.timeLastBought = tb_bought.lastBought  
					////	from FE_temp.dbo.tb_user left join FE_temp.dbo.tb_bought on tb_bought.userIdentifier=tb_user.userIdentifier 
					$sql='update '.$this->_lastCmd->tablenamefull .' set '.$this->_fmtField ($this->_lastCmd->field);
					break;
				case 'delete':
					$sql='delete from '.$this->_lastCmd->tablenamefull;
					break;
				default:
					throw new \ErrorException('unsupport sql cmd:'.$this->_lastCmd->dowhat);
			}
			
			if(!empty($limit)){
				if(!empty($this->_lastCmd->where))	{
					$where = substr(trim($this->_lastCmd->where),5);
					$limit = str_replace('__WHERE__', ' where '.$where, $limit);
					$sql.= ' where ('.$where.') and ('.$limit.')';
				}else $sql.= ' where '.$limit = str_replace('__WHERE__', '', $limit);
			}elseif(!empty($this->_lastCmd->where))	$sql.= ' '.$this->_lastCmd->where;
			
			
			if(!empty($this->_lastCmd->orderby)){
				if(!empty($groupby))$sort_group.=' group by '.implode (',', $groupby);
				if(!empty($orderby))$sort_group.=' order by '.implode (',', $orderby);
				$sql.= ' '.$sort_group;
			}
			
			
			
			$this->_lastCmd->resetForNext();
			$this->_lastCmd->strTrace='['.$this->_lastCmd->server.']'.$sql;
		}else{
			if(is_string($sql)){
				$this->_lastCmd->resetForNext();
				$this->_lastCmd->strTrace='['.$this->_lastCmd->server.']'.$sql;
			}else {
				$err=new \ErrorException('sql gived is not a string');
				error_log($err->getMessage()."\n".$err->getTraceAsString());
				throw $err;
			}
		}

		sooh_broker::pushCmd($this->_lastCmd);
		
		$skip = sooh_dbErr::$maskSkipTheseError;
		sooh_dbErr::$maskSkipTheseError=array();
		//throw new \ErrorException($sql);
		$rs = sqlsrv_query($this->_connection,$sql);
		$this->_chkErr($skip);
		return $rs;
	}
	private function _fmtWhere($where)
	{
		if(!empty($where)){
			if(is_array($where)){
				if(count($where)==1 && strtoupper(key($where))=='OR'){
					$where = current($where);
					if(count($where)==1 && is_array($where[0]))	$where= current($where);
					$where=$this->newWhereBuilder()->init('OR',$this->objForCreateWhere)->append($where, null)->end();
				}else $where=$this->newWhereBuilder()->init('AND',$this->objForCreateWhere)->append($where, null)->end();
			}elseif(is_scalar ($where))throw new \ErrorException('where struct not support');
			else $where = $where->end();
			return $where;
		}else return '';
	}
	public function free()
	{
		$this->disconnect();
		$n = sizeof($this->wheresCreated);
		if($n>1){
			foreach($this->wheresCreated as $i=>$o){
				if($i)$o->end();
			}
			if(sooh_trace::needsWrite(__CLASS__))sooh_trace::str (__CLASS__.':'.$n.' Where(s) created but not end.better check you code');
		}else if(sooh_trace::needsWrite(__CLASS__))sooh_trace::str (__CLASS__.':all cleared');
	}
	/**
	 * @return \Sooh\DB\Interfaces\Where
	 */
	public function newWhereBuilder()
	{
		if(is_array($this->_connection))$this->connect();
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
		if(empty($fieldname))throw new \ErrorException('fieldname not given for safe string');
		if(empty($this->_connection) || is_array($this->_connection)) throw new \ErrorException('connection-handle invalid');
		if(is_null($str))return 'NULL';
		if(is_bool($str))return $str ? 1 : 0;
		if(is_int($str))return (int)$str;
		if(is_float($str))return (float)$str;

		//if(@get_magic_quotes_gpc())$str = stripslashes($str);
		$str = str_replace("'","''",$str);
		$str = str_replace("\0","[NULL]",$str);
		return "'$str'";
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
		if(is_array($this->_connection))$this->connect();
		if($dbTo==null)return $this->_lastDB;
		if($this->_lastDB!==$dbTo){
			//sqlsrv_select_db($this->_connection, $this->_realDBName($dbTo));
			$this->_chkErr();
			$this->_lastDB=$dbTo;
		}
		return $this;
	}

	///////////////////////////////////////////////////////follower is extInterface
	public function getTables($dbname,$like=null,$addDBNameWhenReturn=false)
	{
		if(is_array($this->_connection))$this->connect();
		//SELECT TABLE_NAME FROM information_schema.tables where TABLE_TYPE = 'BASE TABLE' ;
		
		$this->_lastCmd->dowhat='select';
		$this->_lastCmd->field='TABLE_NAME';
		$this->_lastCmd->tablenamefull = 'information_schema.tables';
		if(is_string($like))$this->_lastCmd->where = $this->_fmtWhere(array('TABLE_TYPE'=>'BASE TABLE','TABLE_NAME*'=>"%$like%"));
		else $this->_lastCmd->where = $this->_fmtWhere(array('TABLE_TYPE'=>'BASE TABLE'));
		
		$rs = $this->query(null);
		$tables = array();
		if($addDBNameWhenReturn)while (null!==($row = sqlsrv_fetch_array($rs,SQLSRV_FETCH_NUMERIC))) $tables[] = $dbname.'.'.$row[0];
		else while (null!==($row = sqlsrv_fetch_array($rs,SQLSRV_FETCH_NUMERIC))) $tables[] = $row[0];
		mysql_free_stmt($rs);
		return $tables;
	}
	public function setFieldDef ($obj, $old, $new, $def=null, $after=null)
	{
		throw new \ErrorException("todo:not support yet");
//		$obj = $this->_fmtObj($obj);
//		sooh_dbErr::$maskSkipTheseError = array(sooh_dbErr::fieldNotExists=>true, sooh_dbErr::fieldExists=>true);
//		$after = empty($after)?'':' after '.$after;
//		if($new==null)$this->_query('alter table '.$obj->fullname.' drop '.$old);
//		elseif ($old==null)$this->_query('alter table '.$obj->fullname.' add '.$new.' '.$def.$after);
//		else $this->_query('alter table '.$obj->fullname.' change '.$old.' '.$new.' '.$def.$after);
	}
	public function dropTable ($obj)
	{
		$obj = $this->_fmtObj($obj);
		$sql = "IF EXISTS(SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = '$obj->name') DROP TABLE $obj->name;";
		$this->_query($sql);
	}
	public function ensureDB ($dbname)
	{
		$sql = "if not exists（select * from sys.databases where name = '$dbname'）create database $dbname ";
		$this->_query($sql);
	}
	/**
	 * @param sooh_dbField $_ignore_ 
	 */
	public function ensureObj($obj,$fields,$pkey=null,$keys=null,$ukeys=null,$_ignore_=null)
	{
		if (empty($fields)) throw new \ErrorException('empty fields given');
		$obj = $this->_fmtObj($obj);
		$parts=array();
		foreach($fields as $k=>$_ignore_){
			if(is_string($_ignore_)){
				$parts[]="$k $_ignore_";
			}else{
				switch($_ignore_->fieldType){
					case sooh_dbField::int32:
						$parts[]= $k.' int not null default '.($_ignore_->val-0);
						break;
					case sooh_dbField::int64:
						$parts[]= $k.' bigint unsigned not null default '.($_ignore_->val?:0);
						break;
					case sooh_dbField::string:
						if($_ignore_->fieldLen>2000)	$parts[] = $k.' text';
						else $parts[] = $k.' varchar($def->fieldLen) not null default \''.($_ignore_->val?:'').'\'';
						break;
					case sooh_dbField::float:
						$parts[]= $k.' FLOAT not null default '.($_ignore_->val?:0);
						break;
					case sooh_dbField::blob:
						$parts[]= $k.' blob';
						break;
				}
			}
		}
		$tmpSysTb = explode('.', $obj->fullname);
		$findName = array_pop($tmpSysTb);
		$tmpSysTb[]='sysobjects';
		
		$sql = "if not exists (select * from ".  implode('.', $tmpSysTb)." where id = object_id('$findName') and OBJECTPROPERTY(id, 'IsUserTable') = 1)"
					."create table {$obj->fullname} (" .implode(',', $parts);
		if (!empty($pkey)) {
			if (is_array($pkey))$sql .= ", PRIMARY KEY  (".implode(",",$pkey).")";
			else $sql .= ", PRIMARY KEY  ($pkey)";
		}
		
		if (is_string($keys)) $keys = explode(",",$keys);
		if (is_array($keys))
			foreach ($keys as $k=>$key){
				if (is_array($key)) $key = implode(",",$key);
				if (is_int($k))$k = str_replace(",","_",$key);
				$sql .= ",KEY $k ($key)";
			}
		if (is_string($ukeys)) $ukeys = explode(",",$ukeys);
		if (is_array($ukeys)){
			foreach ($ukeys as $k=> $key){
				if (is_array($key)) $key = implode(",",$key);
				if (is_int($k))$k = str_replace(",","_",$key);
				$sql .= ",UNIQUE KEY $k ($key)";
			}
		}
		$sql .=  ")";
		$this->execCustom(array('sql'=>$sql));
		return $this;
	}
}
