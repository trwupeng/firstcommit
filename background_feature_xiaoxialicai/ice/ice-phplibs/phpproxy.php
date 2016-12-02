<?php
$runMode = 'test';
$runMode = 'licai_php';

$baseDir = realpath(__DIR__.'/../..');
$tmp = explode('/',$_REQUEST['cmd']);
if(sizeof($tmp)==2){
	array_unshift($tmp,'services');
}
list($m,$c,$a) = $tmp;
$classname = "\\Lib\\Services\\".ucfirst($c);

if($runMode==='test'){
	define('MyIceServiceDir',$baseDir.'/ice-phplibs/sample-service/');//sample
	include_once MyIceServiceDir.'/conf.php';//sample	
}else{
	define(APP_PATH,$baseDir.'/'.$runMode);
	include_once $baseDir.'/'.$runMode.'/conf/globals.php';//yaf
	define('MyIceServiceDir',$baseDir.'/'.$runMode.'/application/library/');//yaf	
}

define('MySoohInVendor','/var/www/vendor/hillstill/sooh/src/');
function myloader($class)
{
	global $r44;
	$r = explode('\\', $class);
	if($r[0]=='\\')array_shift($r);
	if(sizeof($r)==1){
		if(file_exists($r[0].'.php')) {
			if(file_exists($r[0].'.php')){
				include $r[0].'.php';
				return true;
			}
		}
		return false;

	}else{
		if($r[0]=='Sooh'){
			$chk =  MySoohInVendor.implode('/', $r).'.php';
		}else{
			$chk =  MyIceServiceDir.implode('/', $r).'.php';
		}
		if(file_exists($chk)){
			include $chk;
			return true;
		}else{
			return false;
		}
	}
}
spl_autoload_register('myloader');
//error_log(">>>>>>>>>>>>>>>>>>>>>>>>>>[ICE-PHPPROXY start]$classname -> $a");
try{
	$service = $classname::getInstance(null);
	$ret = call_user_func_array([$service,$a],$_REQUEST['arg']);
	echo $ret;
}catch(\ErrorException $ex){
	error_log("error catch on $classname -> $a ():".$ex->getMessage()."\n".$ex->getTraceAsString());
	echo $ex->getMessage();
}
\Sooh\Base\Ini::registerShutdown(null, null);
//error_log(">>>>>>>>>>>>>>>>>>>>>>>>>>[ICE-PHPPROXY ENDED]$classname -> $a");
