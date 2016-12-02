<?php
namespace Sooh\DB\Interfaces;

/**
 * usage $strWhere = [new where()] -> init('AND','db.tablename')->append()->append()->end()
 * 
 * append('id!',3)
 * append(array('id!'=>3))
 * append(null,dbWhereObj)
 * 
 * append('field','skipThis',false)
 * append('field','appendThis',true)
 * append('skip',array(),'SkipEmptyArray')
 * append('not-skip',array(1,2),'SkipEmptyArray')
 * 
 * @param mixed $k
 * @param mixed $v
 * @param mixed $ifRealAppend bool or 'throwError'
 * @return tea_dbinterfaceWhere
 * @throws \ErrorException
 * @author Simon Wang <sooh_simon@163.com> 
 */
interface Where 
{
	/**
	 * @param string $method and | or
	 * @param string $tablename
	 * @return Where
	 */
	public function init($method='AND',$tablename='db.tb');
	
	/**
	 * @return Where
	 */
	public function append($k,$v=null,$flg='throwError');
	public function end();
	public function abandon();
}
