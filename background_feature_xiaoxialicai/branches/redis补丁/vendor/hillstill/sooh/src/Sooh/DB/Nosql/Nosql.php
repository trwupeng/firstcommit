<?php
namespace Sooh\DB\Nosql;

use \Sooh\DB\Error as sooh_dbErr;
use \Sooh\DB\Base\SQLDefine as sooh_sql;

class NoSqlList
{
	private $_db = null;
	private $_name = null;
	public function __construct( $db, $name )
	{
		$_db = $db;
		$_name = $name;
	}
	
	
}

class Nosql
{
	static protected $_instance;
	static protected $_conf;
	static protected $_db = null;
	static public function getInstance( $conf )
	{
		if ( null != self::$_instance )
		{
			return self::$_instance;
		}
		if ( 'redis' == $conf['type'] )
		{
			self::$_instance = new KRedis();
		}
		else 
		{
			self::doThrow(sooh_dbErr::otherError, 'unknown nosql type:' . $conf['type'] );
		}
		self::$_conf = $conf;
		$ret = self::$_instance->connect();
		if ( !$ret )
		{
			self::doThrow(sooh_dbErr::connectError);
		}
		return self::$_instance;
	}
	
	static protected function doThrow( $code,  $msg = '' )
	{
		$err= new sooh_dbErr( $code, $msg, new sooh_sql() );
		throw $err;
	}
	
	/*
	 * 连接
	 */
	public function connect( $force = false ) {}
	
	/*
	 * 添加或更新记录
	 * $key: 键
	 * $value: 值, 如为null，则表示自增
	 * $timeout: 健的超时时间(秒)，0表示永久
	 * $arr: 多个键值对
	 * $exData: 扩展数据
	 * 
	 * 返回值：成功true， 失败false
	 */
	public function set( $key, $value, $timeout, $exData ) {}
	public function setM( $arr, $timeout, $exData ) {}
	
	/*
	 * 获取键值
	 * $key: 健，可以是数组
	 * $exData: 扩展数据
	 * 
	 * 返回值：成功 对应的值，失败false
	 */
	public function get( $key, $exData ) {}
	
	/*
	 * 删除键值
	 * $key: 健，可以是数组
	 * $exData: 扩展数据
	 * 
	 * 返回值：成功true， 失败false
	 */
	public function del( $key, $exData ) {}
	
	/*
	 * 清除过期的key
	 */
	public function gc() {}
	
	/*
	 * key是否存在
	 * 
	 * $key: 健
	 * $exData: 扩展数据
	 * 
	 * 返回值：存在true， 不存在false 
	 */
	public function exist( $key, $exData ) {}
	
	/*
	 * 选择一个数据库
	 * 
	 * $name: 数据库名字
	 */
	public function select( $name ) {}
	
	/*
	 * list压入头
	 * 
	 * $key: list的名字
	 * $value: 值
	 */
	public function list_pushHead( $key, $value ) {}
	
	/*
	 * list压入尾
	 * 
	 * $key: list的名字
	 * $value: 值
	 */
	
	public function list_pushBack( $key, $value ) {}
	
	/*
	 * list头的弹出
	 * 
	 * $key: list的名字
	 */
	public function list_popHead( $key ) {}
	
	/*
	 * list尾的弹出
	 * 
	 * $key: list的名字
	 */
	public function list_popBack( $key ) {}
	
	/*
	 * list的大小
	 * 
	 * $key: list名字
	 */
	public function list_size( $key ) {}
	
	
}
