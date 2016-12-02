<?php
namespace Prj\Misc;

/**
 * 各类许可协议相关接口
 *
 * @author simon.wang
 */
class Licence {
	public static  function version($name=	'invest')
	{
		$obj = \Prj\Data\Agreement::getCopy(['verName'=>$name,'verId'=>0]);
		$r = $obj->db()->getOne($obj->tbname(), 'max(verId)',['verName'=>$name,'status'=>1])-0;
		return $r;
	}
	
	public static function register($ver=null)
	{
		return $tmp = self::getTpl(__FUNCTION__,$ver);
	}

    public static function diya($ver=null)
    {
        return $tmp = self::getTpl(__FUNCTION__,$ver);
    }

    public static function jiekuan($ver=null)
    {
        return $tmp = self::getTpl(__FUNCTION__,$ver);
    }

    public static function zhaiquan($ver=null)
    {
        return $tmp = self::getTpl(__FUNCTION__,$ver);
    }

	public static function binding($ver=null)
	{
		return $tmp = self::getTpl(__FUNCTION__,$ver);;
	}
	public static function invest($arr,$ver=null)
	{
		$find=[];
		$rep=[];
		foreach($arr as $k=>$v){
			$find[]='{$'.$k.'}';
			$rep[]=$v;
		}
		$tmp = self::getTpl(__FUNCTION__,$ver);
		return str_replace($find, $rep, $tmp);
	}
	
	public static function recharges($arr,$ver=null)
	{
	    
		$find=[];
		$rep=[];
		foreach($arr as $k=>$v){
			$find[]='{$'.$k.'}';
			$rep[]=$v;
		}
		$tmp = self::getTpl(__FUNCTION__,$ver);
		return str_replace($find, $rep, $tmp);
	}
	
	protected static function getTpl($name,$ver)
	{
	    if($ver==1){
		$obj = \Prj\Data\Agreement::getCopy(['verName'=>$name,'verId'=>$ver]);
		$r = $obj->db()->getOne($obj->tbname(), 'content',['verName'=>$name,'status'=>1,'verId'=>$ver],'rsort verId');
	    }else{
	        $obj = \Prj\Data\Agreement::getCopy(['verName'=>$name,'verId'=>0]);
	        $r = $obj->db()->getOne($obj->tbname(), 'content',['verName'=>$name,'status'=>1],'rsort verId');
	    }
		return $r;
	}
}
