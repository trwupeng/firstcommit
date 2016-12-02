<?php
//ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . '/root/ice-3.6.1/php/lib');
//require 'Ice.php';
require __DIR__.'/../ice-phplibs/ice/Ice.php';

require __DIR__.'/../ice-phplibs/service-interfaces/samples.php';
//echo "start...\n";	 
$ic = null;
try{
	

	$data = new Ice_InitializationData;
    $data->properties = Ice_createProperties();
	$data->properties->setProperty("Ice.Default.Locator","SzcIceGrid/Locator:tcp -h 127.0.0.1 -p 4061");
	$ic = Ice_initialize($data);
	$base = $ic->stringToProxy("sample0000");

	$oneway = $base->ice_oneway();
	$printer = sooh_services_samples_sample0000PrxHelper::uncheckedCast($oneway);

	//$printer = sooh_services_logcenter_logerPrxHelper::checkedCast($base);

	if(!$printer){
		echo ("[ice-ret] Error Invalid proxy\n");
		error_log("[ice-ret] Error Invalid proxy");
	}else{
		$returnedStr = $printer->sayhi('异步&\\/\'"123');
		echo "[ice-ret] request pushed into que\n";
		error_log("[ice-ret] request pushed into que");
	}
	echo "\n";
 
	if($ic){
		try  {
			$ic->destroy();
		}  catch(Exception $ex)  {
			echo $ex->getMessage();
			error_log("[ice-free][error ".get_class($ex)."".$ex->getCode()."]".$ex->getMessage()."");
		}
	}
}catch(Exception $ex){
	echo "[ice-ret] [error ".get_class($ex)."".$ex->getCode()."]".$ex->getMessage()."<<<<<<<<<<<<<<<<<\n";
	error_log("[ice-ret] [error ".get_class($ex)."".$ex->getCode()."]".$ex->getMessage()."<<<<<<<<<<<<<<<<<\n");
}

