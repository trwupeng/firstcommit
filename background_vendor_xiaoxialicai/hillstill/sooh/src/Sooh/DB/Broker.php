<?php

namespace Sooh\DB;

use \Sooh\Base\Trace as sooh_trace;
use \Sooh\Base\Ini as sooh_ini;
use \Sooh\DB\Base\SQLDefine as sooh_sql;
/**
 * usage:
 * $GLOBALS['CONF']['dbConf']=array(
 *		'default'=>array('host'=>'127.0.0.1','user'=>'root','pass'=>'hy1302','type'=>'mysql','port'=>'3306',
 *						'dbEnums'=>array('default'=>'db_0',)),
 * );
 * Broker::getInstance('default');
 * 
 * reason of adding default and dbEnums, see KVObj
 * @author Simon Wang <sooh_simon@163.com> 
 */

class Broker {
	private static function idOfConnection($conf)
	{
		return $conf['user'].'@'.$conf['host'].':'.(empty($conf['port'])?"":$conf['port']);
	}
	/**
	 * @return \Sooh\DB\Interfaces\All
	 */
	public static function getInstance($arrConf_or_Index=null,$modName=null)
	{
		if(is_array($arrConf_or_Index))$conf = $arrConf_or_Index;
		else {
			$ini = sooh_ini::getInstance();
			
			if($arrConf_or_Index===null){
				$conf = $ini->get('dbConf');
				if(isset($conf['default']))$conf = $conf['default'];
				elseif(is_array($conf))$conf = current ($conf);
				else throw new \ErrorException('default dbConf not found');
			}else{
				$conf = $ini->get('dbConf.'.$arrConf_or_Index);
			}
		}
		if(!isset($conf['name']) || $modName!==null){
			if (isset($conf['dbEnums'][$modName]))$conf['name'] = $conf['dbEnums'][$modName];
			else $conf['name'] = $conf['dbEnums']['default'];
		}
		$id = self::idOfConnection($conf);
		if (empty(self::$_instances[$id])) {
			$type = $conf['type'];
			if(empty($type)){
				$ttmp = $ini->get('dbConf');
				if(is_array($ttmp)){
					$ttmp = implode(',', array_keys($ttmp));
				}else {
					$ttmp='EMPTY';
				}
				$err=new \ErrorException('db-config missing:'.  json_encode($arrConf_or_Index) .' current:'.$ttmp);
				error_log($err->getMessage()."\n".$err->getTraceAsString());
				throw $err;
			}
			$class = '\\Sooh\\DB\\Types\\'.ucfirst($type);
//			if (!class_exists($class, false))
//				include __DIR__ . '/' . $class . '.php';
			self::$_instances[$id] =  new $class($conf);
			self::$_instances[$id]->dbConf=$conf;
			if(sooh_trace::needsWrite(__CLASS__))sooh_trace::str('create new connection['.$id.'] of '.  json_encode($conf));
		}else{
			if(sooh_trace::needsWrite(__CLASS__))sooh_trace::str('exists connection['.$id.'] of '.  json_encode($conf));
		}
		return self::$_instances[$id];
	}

	public static function free($arrConf_or_strModName=null,$subid = null)
	{
		if ($arrConf_or_strModName == null)
			$ks = array_keys(self::$_instances);
		elseif(is_array($arrConf_or_strModName)){
			$ks = array(self::idOfConnection($arrConf_or_strModName));
		}else{
			$ini = sooh_ini::getInstance();
			$dbid = $ini->get('dbByObj.'.$arrConf_or_strModName);
			if($dbid===null)$dbid = $ini->get('dbByObj.default');
			if(is_array($dbid)){
				if($subid===null){
					$ks = array();
					foreach($dbid as $subid)
						$ks[]=self::idOfConnection(self::confOfMod($arrConf_or_strModName,$subid));
				}else{
					$ks = array(self::idOfConnection(self::confOfMod($arrConf_or_strModName,$subid)));
				}
			}else{
				$ks = array(self::idOfConnection(self::confOfMod($arrConf_or_strModName,null)));
			}
		}
		if(sooh_trace::needsWrite(__CLASS__))sooh_trace::obj('final free these db(s):',$ks);
		foreach ($ks as $id) {
			if (isset(self::$_instances[$id])) {
				self::$_instances[$id]->free();
				unset(self::$_instances[$id]);
			}
		}
	}

	private static $_instances = array();
	/**
	 *
	 * @var array 记录最近执行的sqlCmd的堆栈
	 */
	private static $lastSQLs=array();
	public static $maxLastSqls=3;
	/**
	 * 
	 * @param sooh_sql $cmd
	 */
	public static function pushCmd($cmd)
	{
		if(sizeof(self::$lastSQLs)>self::$maxLastSqls)array_pop (self::$lastSQLs);
		array_unshift(self::$lastSQLs, $cmd->strTrace);
	}
	/**
	 * 
	 * @return string 
	 */
	public static function lastCmd($topOne=true)
	{
		if($topOne){
			if(self::$lastSQLs[0]){
				return self::$lastSQLs[0];
			}else{
				return null;
			}
		}else{
			return self::$lastSQLs;
		}
	}
	/**
	 * 标记忽略特定的数据库错误
	 * @param string $err default is duplicateKey
	 * @param bool $totalSkip true:no log no Exception, false: Exception only
	 */
	public static function errorMarkSkip($err= \Sooh\DB\Error::duplicateKey,$totalSkip=false)
	{
		if(!is_array(\Sooh\DB\Error::$maskSkipTheseError)){
			\Sooh\DB\Error::$maskSkipTheseError = array($err=>$totalSkip);
		}else{
			\Sooh\DB\Error::$maskSkipTheseError[$err]=$totalSkip;
		}
	}
	/**
	 * 检查是否是指定的数据库错误类型
	 * @param \ErrorException $e
	 * @param string $type default is duplicateKey
	 * @return boolean 
	 */
	public static function errorIs( $e, $type=\Sooh\DB\Error::duplicateKey)
	{
		$n = $e->getCode()-0;
		return is_a($e,'\Sooh\DB\Error') && (($n&$type)>0);
	}
}
