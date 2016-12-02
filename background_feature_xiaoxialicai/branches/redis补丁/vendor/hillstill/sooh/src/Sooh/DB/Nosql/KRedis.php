<?php

namespace Sooh\DB\Nosql;
use \Sooh\DB\Error as sooh_dbErr;
use \Redis;

class KRedis extends Nosql
{
	/*
	 * 连接
	*/
	public function connect( $force = false )
	{
		if ( !$force && null != self::$_db )
		{
			return self::$_db;
		}
		if ( !isset( self::$_conf ) || !isset(self::$_conf['hosts']) )
		{
			self::doThrow(sooh_dbErr::otherError);
		}
		
		foreach ( self::$_conf['hosts'] as $k=>$v )
		{
			$r = $this->doConnect( $v['host'], $v['port'], self::$_conf['pass'] );
			$info = $r->info();
			if ( !$info )
			{
				self::doThrow(sooh_dbErr::otherError, "redis get info failed" );
			}
			if ( 'master' != $info['role'] )
			{
				//连上的是从库
				$r->close();
				
				//连主库
				$r = $this->doConnect( $info['master_host'], $info['master_port'], self::$_conf['pass'] );
			}
			self::$_db = $r;
			return $r;
		}
		return false;
	}
	protected function doConnect( $host, $port, $pwd )
	{
		$r = new Redis();
		$ret = $r->pconnect( $host, $port );
		if ( !$ret )
		{
			self::doThrow(sooh_dbErr::connectError);
		}
		if ( null != $pwd )
		{
			$ret = $r->auth( $pwd );
			if ( !$ret )
			{
				self::doThrow(sooh_dbErr::connectError, "redis auth failed" );
			}
		}
		
		return $r;
	}
	
	/*
	 * 添加或更新记录
	* $key: 键
	* $value: 值, 如为null，则表示自增
	* $timeout: 健的超时时间(秒)，0表示永久
	* $arr: 多个键值对
	* $exData: 扩展数据(hash值)
	*/
	public function set( $key, $value, $timeout, $exData )
	{
		$r = $this->connect();
		if ( null == $value )
		{
			$value = $r->incr($key);
		}
		
		$ret = true;
		if ( null == $exData )
		{
			if ( null == $timeout || 0 == $timeout )
			{
				$ret = $r->set( $key, $value );
			}
			else
			{
				$ret = $r->setex( $key, $timeout, $value );
			}
		}
		else if ( is_array($exData) && isset($exData['h']) ) 
		{
			$ret = $r->hset( $exData['h'], $key, $value );
		}
		else 
		{
			$ret = false;
		}
		
		$r->close();

		return $ret;
	}
	public function setM( $arr, $timeout, $exData )
	{
		$r = $this->connect();
		$ret = true;
		if ( null == $exData )
		{
			if ( null == $timeout || 0 == $timeout )
			{
				$ret = $r->mset( $arr );
			}
			else
			{
				foreach ( $arr as $k=>$v )
				{
					$ret = $r->setex( $k, $timeout, $v );
					if ( !$ret )
					{
						break;
					}
				}
			}
		}
		else if ( is_array($exData) && isset($exData['h']) )
		{
			$ret = $r->hMset( $exData['h'], $arr );
		}
		else
		{
			$ret = false;
		}
		
		$r->close();
		
		return $ret;
	}
	
	/*
	 * 获取键值
	* $key: 健，可以是数组
	* $exData: 扩展数据(hash值)
	*/
	public function get( $key, $exData )
	{
		$r = $this->connect();
		$ret = null;
		if ( !is_array($key) )
		{
			if ( null == $exData )
			{
				$ret = $r->get( $key );
			}
			else if ( is_array($exData) && isset($exData['h']) )
			{
				$ret = $r->hGet( $exData['h'], $key );
			}
			else 
			{
				$ret = false;
			}
		}
		else
		{
			if ( null == $exData )
			{
				$ret = $r->getMuliple( $key );
			}
			else if ( is_array($exData) && isset($exData['h']) )
			{
				$ret = $r->hMget( $exData['h'], $key );
			}
			else 
			{
				$ret = false;
			}
		}
		
		$r->close();
		
		return $ret;
	}
	
	
	/*
	 * 删除键值
	* $key: 健，可以是数组
	* $exData: 扩展数据(hash值)
	*/
	public function del( $key, $exData )
	{
		$r = $this->connect();
		if ( null == $exData )
		{
			$ret = $r->delete( $key );
		}
		else 
		{
			$ret = $r->hDel( $exData['h'], $key );
		}
		$r->close();
		return $ret;
	}
	
	/*
	 * 清除过期的key
	*/
	public function gc()
	{
		$r = $this->connect();
		$r->persist();
		$r->close();
	}
	
	/*
	 * key是否存在
	 * 
	 * $key: 健
	 * $exData: 扩展数据(hash值)
	*/
	public function exist( $key, $exData )
	{
		$r = $this->connect();
		if ( null == $exData )
		{
			$ret = $r->exists( $key );
		}
		else 
		{
			$ret = $r->hDel( $exData['h'], $key );
		}
		$r->close();
		return $ret;
	}
	
	/*
	 * 选择一个数据库
	*
	* $name: 数据库名字
	*/
	public function select( $name )
	{
		$r = $this->connect();
		$ret = $r->select( $name );
		$r->close();
		
		return $ret;
	}
	
	/*
	 * list压入头
	*
	* $key: list的名字
	* $value: 值
	*/
	public function list_pushHead( $key, $value )
	{
		$r = $this->connect();
		$ret = $r->lPush( $key, $value );
		$r->close();
		
		return $ret;
	}
	
	/*
	 * list压入尾
	*
	* $key: list的名字
	* $value: 值
	*/
	
	public function list_pushBack( $key, $value )
	{
		$r = $this->connect();
		$ret = $r->rPush( $key, $value );
		$r->close();
		
		return $ret;
	}
			
	
	/*
	 * list头的弹出
	*
	* $key: list的名字
	*/
	public function list_popHead( $key )
	{
		$r = $this->connect();
		$ret = $r->lPop();
		$r->close();
		
		return $ret;
	}
	
	/*
	 * list尾的弹出
	*
	* $key: list的名字
	*/
	public function list_popBack( $key )
	{
		$r = $this->connect();
		$ret = $r->rPop();
		$r->close();
		
		return $ret;
	}
	
	/*
	 * list的大小
	*
	* $key: list名字
	*/
	public function list_size( $key )
	{
		$r = $this->connect();
		$ret = $r->lSize();
		$r->close();
		
		return $ret;
	}
}
