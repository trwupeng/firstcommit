<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Prj\Data;

/**
 * Description of TbConfigLimit
 *
 * @author wang.ning
 */
class TbConfigLimit {
	/**
	 * 
	 * @return \Prj\Data\TbConfigLimit
	 */
	public static function parseStr($str)
	{
		return new TbConfigLimit;
	}

	public static function createByTimeLimit($dtStart,$dtEnd)
	{
		
	}
	
	public static function createByScopeLimit()
	{
		
	}
	
	public function getIndex($chk)
	{
		return 0;
	}
	public function toString()
	{
		return "";
	}
}
