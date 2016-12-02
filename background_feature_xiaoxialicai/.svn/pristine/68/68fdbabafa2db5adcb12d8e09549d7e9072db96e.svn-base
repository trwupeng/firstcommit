<?php
//ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . '/root/ice-3.6.1/php/lib');
//require 'Ice.php';
require __DIR__.'/../ice-phplibs/ice/Ice.php';

require __DIR__.'/../ice-phplibs/service-interfaces/samples.php';

$ic = null;
$iceRegServer = "SzcIceGrid/Locator:tcp -h 127.0.0.1 -p 4061";
try{
	$data = new Ice_InitializationData;
    $data->properties = Ice_createProperties();
	$data->properties->setProperty("Ice.Default.Locator",$iceRegServer);
	echo $data->properties->__toString()."\n\n";
	$ic = Ice_initialize($data);
	$base = $ic->stringToProxy("sample0000");
	$fs = get_class_methods($base);
//	echo implode(', ',$fs)."\n";
	$printer = sooh_services_samples_sample0000PrxHelper::checkedCast($base);
	if(!$printer){
		echo ("[ice-ret] Error Invalid proxy\n\n");
		error_log('[ice-ret] Error Invalid proxy');
	}else{
		$returnedStr = $printer->echohi("同步&\\/'\"123");
		echo "[ice-ret]$returnedStr\n\n";
		error_log("[ice-ret]$returnedStr");
	}
 

}catch(Exception $ex){
	echo "[ice-ret] [error ".get_class($ex)."".$ex->getCode()."]".$ex->getMessage()."\n".$ex->getTraceAsString()."<<<<<<<<<<<<<<<<<\n";
	error_log("[ice-ret] [error ".get_class($ex)."".$ex->getCode()."]".$ex->getMessage()."<<<<<<<<<<<<<<<<<");
}

if($ic){
	try  {
		$ic->destroy();
	}  catch(Exception $ex)  {
		echo $ex->getMessage();
		error_log("[ice-free][error ".get_class($ex)."".$ex->getCode()."]".$ex->getMessage()."");
	}
}

