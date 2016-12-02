<?php
namespace Sooh\Base;
/**
 * 通用工具类
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class Tools {
	public static function uri($args=null,$actNew=null,$ctrlNew=null,$modNew=null)
	{
		$ini = \Sooh\Base\Ini::getInstance ();
		if($actNew==null)$actNew= $ini->get ('request.action');
		if($ctrlNew==null)$ctrlNew=$ini->get ('request.controller');
		if($modNew==null)$modNew= $ini->get ('request.module');
		if(empty($args))$uri='';
		elseif(is_array($args))$uri=  http_build_query ($args);
		elseif(is_string($args))$uri=$args;
		else throw new ErrorException('unsupport args for sooh_uri');
		if(!defined('SOOH_INDEX_FILE'))define ('SOOH_INDEX_FILE', 'index.php');
		if(!defined('SOOH_ROUTE_VAR') || defined('SOOH_USE_REWRITE')){//!empty($_REQUEST[SOOH_ROUTE_VAR]) && count(explode('/', $_REQUEST[SOOH_ROUTE_VAR]))>1
			$uri = $ini->get ('request.baseUri').'/'."$modNew/$ctrlNew/$actNew?$uri";
		}else{
			$uri = $ini->get ('request.baseUri').'/'.SOOH_INDEX_FILE.'?'.SOOH_ROUTE_VAR."=$modNew/$ctrlNew/$actNew&$uri";
		}
		return $uri;
	}
	public static function uriTpl($args=null,$actNew=null,$ctrlNew=null,$modNew=null)
	{
		$uri = self::uri($args,$actNew,$ctrlNew,$modNew);
		return str_replace(array('%7B','%7D'),array('{','}'),$uri);
	}
	public static function remoteIP()
	{
		$proxyIP = \Sooh\Base\Ini::getInstance()->get('inner_nat');
		if(!empty($proxyIP) && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
			return $_SERVER['HTTP_X_FORWARDED_FOR'];
		}else{
			return $_SERVER['REMOTE_ADDR'];
		}
	}
	public static function httpGet($url,$arrHeaders=null,$arrCookies=null ,$timeOut = 5)
	{
		$ch = curl_init();
		if($ch){
			curl_setopt($ch, CURLOPT_URL, $url);
			if(is_array($arrHeaders) && !empty($arrHeaders)){
				curl_setopt($ch, CURLOPT_HTTPHEADER, $arrHeaders);
			}
			if(is_array($arrCookies) && !empty($arrCookies)){
				$arrCookies = str_replace('&', '; ', http_build_query($arrCookies));
				curl_setopt($ch, CURLOPT_COOKIE,$arrCookies);
			}
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeOut ); //--tgh 160415
			$output = curl_exec($ch);
			$err=curl_error($ch);
			if(!empty($err)){
				error_log('[errorFailed:'.$err.']'.$url);
			}
			self::$httpCodeLast = curl_getinfo($ch,CURLINFO_HTTP_CODE);
			curl_close($ch);
			
			return $output.$err;
		}else{
			return "curl init failed";
		}
	}
	protected static $httpCodeLast=0; 
	public static function httpCodeLast()
	{
		return self::$httpCodeLast;
	}
	public static function httpPost($url,$args,$arrHeaders=null,$arrCookies=null,$timeOut=5)
	{
		$ch = curl_init();
		if($ch){
			curl_setopt($ch, CURLOPT_URL, $url);
			
			if(is_array($arrHeaders) && !empty($arrHeaders)){
				curl_setopt($ch, CURLOPT_HTTPHEADER, $arrHeaders);
			}
			if(is_array($arrCookies) && !empty($arrCookies)){
				$arrCookies = str_replace('&', '; ', http_build_query($arrCookies));
				curl_setopt($ch, CURLOPT_COOKIE,$arrCookies);
			}
			if(is_array($args)){
				$tmp= http_build_query($args);
				curl_setopt($ch, CURLOPT_POST, 1);
				if(strlen($tmp)<1000){
					curl_setopt($ch, CURLOPT_POSTFIELDS, $tmp);
				}else{
					curl_setopt($ch, CURLOPT_POSTFIELDS, $args);
				}
			}else{
				curl_setopt($ch, CURLOPT_POSTFIELDS, $args);
			}
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_TIMEOUT, $timeOut );
			$output = curl_exec($ch);
			$err=curl_error($ch);
			self::$httpCodeLast = curl_getinfo($ch,CURLINFO_HTTP_CODE);
			curl_close($ch);
			
			return $output.$err;
		}else{
			return "curl init failed";
		}
	}
	public static function runBackground($strArgs)
	{
		$ini = \Sooh\Base\Ini::getInstance();
		if(DIRECTORY_SEPARATOR =='/'){//unix
			$cmd =$ini->get('path_php').' '.$ini->get('path_console')." \"$strArgs\" &"; 
		}else{//win
			$cmd ='start /b '.$ini->get('path_php').' '.$ini->get('path_console')." \"$strArgs\""; 
		}
		pclose(popen($cmd, 'r'));
		return $cmd;
	}

	/*
	 * 获取redis的表名
	 */
	private static $_curRedisListKey = 1;
	public static function getRedisListKey()
	{
		return 'xxLog_' . self::$_curRedisListKey;
	}
}
