<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Sooh\DB\Base;

/**
 * Description of SQLDefine
 *
 * @author wang.ning
 */



class SQLDefine {
	public $server='';
	public $dowhat='select';
	public $field='*';
	public $tablenamefull='dbname.tbname';
	public $where=null;
	public $orderby;//rsort
	public $fromAndSize=array(0,10000);
	public $pkey=null;
	public $join='';
	public $result;//????
	public $strTrace;//'select * from tb...';
	
	public function resetForNext()
	{
		//$this->server=null;
		$this->join='';
		$this->pkey=null;
		$this->dowhat=null;
		$this->field=null;
		$this->tablenamefull=null;
		$this->where=null;
		$this->orderby=null;
		$this->fromAndSize=array(0,10000);
	}
}
