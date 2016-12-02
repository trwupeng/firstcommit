<?php
namespace Lib\Misc;
class ZerocIce
{
	protected static $_instance=null;
	/**
	 * 
	 * @param string $conf
	 * @return \Lib\Misc\ZerocIce
	 */
	public static function getInstance($conf=null)
	{
		if(self::$_instance==null){
			if(empty($conf)){
				$conf = \Sooh\Base\Ini::getInstance()->get('IceCenter4Evt',"SzcIceGrid/Locator:tcp -h 127.0.0.1 -p 4061");
			}
			$tmp = get_called_class();
			$obj = new $tmp;
			$obj->iceRegServer=$conf;
			self::$_instance=$obj;
		}
		
		return self::$_instance;
	}
	protected $iceRegServer;
	/**
	 * twoway - 同步调用
	 */
	public function syn($pak='logcenter',$classname='loger',$funcname='writeSyn',$arg1=null,$arg2=null,$arg3=null,$arg4=null,$arg5=null)
	{
//error_log(">>>>>>>>>>>>>>>>>>>>>>>>>>$pak='logcenter',$classname='loger',$funcname='writeAsy'");
		$this->classname = $classname = lcfirst($classname);
		$tmp = $this->prepareIce($pak,$classname);

		try{
			$data = new \Ice_InitializationData;
			$data->properties = \Ice_createProperties();
			$data->properties->setProperty("Ice.Default.Locator",$this->iceRegServer);
			//echo $data->properties->__toString()."\n\n";
			$ic = Ice_initialize($data);
			$base = $ic->stringToProxy($classname);
			$printer = $tmp::checkedCast($base);
			//$printer = $tmp::checkedCast($base);
			//error_log("[icerpc-log trace] ice-two-way: $classname - > $funcname");
			if(!$printer){
				//error_log('[icerpc-log error] Error Invalid proxy for '.$classname);
			}else{
				$returnedStr = $this->finalCall($printer,$funcname,$arg1,$arg2,$arg3,$arg4,$arg5);
			}
		}catch(\Exception $ex){
			error_log("[icerpc-log error] $classname [error ".get_class($ex)."#".$ex->getCode()."]".$ex->getMessage()."<<<<<<<<<<<<<<<<<".$ex->getTraceAsString());
		}
		if($ic){
			try  {
				$ic->destroy();
			}  catch(Exception $ex)  {
				error_log("[icerpc-log close failed] $classname [error ".get_class($ex)."".$ex->getCode()."]".$ex->getMessage()."");
			}
		}
		//error_log("[icerpc-log trace] ice-two-way-done: $classname - > $funcname");
		return $returnedStr;
	}
	private $classname;
	private function sentlog($funcname,$arg1=null,$arg2=null,$arg3=null,$arg4=null,$arg5=null)
	{
		$dt = \Sooh\Base\Time::getInstance()->timestamp();
		$str = "cmd ".date("m-d H:i:s",$dt)." ".$this->classname." ".$funcname."\n";
		if($arg1!=null){
			$str.="arg ".$arg1."\n";
			if($arg2!=null){
				$str.="arg ".$arg2."\n";
				if($arg3!=null){
					$str.="arg ".$arg3."\n";
					if($arg4!=null){
						$str.="arg ".$arg4."\n";
						if($arg5!=null){
							$str.="arg ".$arg5."\n";
						}
					}
				}
			}
		}
		$filename = dirname(ini_get('error_log'));
		$filename = $filename.'/icelog.'.date("Y-m-d",$dt).'.log';
		file_put_contents($filename, $str."\n",FILE_APPEND);
	}
	/**
	 * oneway - 异步调用
	 */
	public function asy($pak='logcenter',$classname='loger',$funcname='writeAsy',$arg1=null,$arg2=null,$arg3=null,$arg4=null,$arg5=null)
	{
//error_log(">>>>>>>>>>>>>>>>>>>>>>>>>>$pak='logcenter',$classname='loger',$funcname='writeAsy'");
		$this->classname = $classname = lcfirst($classname);
		$tmp = $this->prepareIce($pak,$classname);
		try{
			$data = new \Ice_InitializationData;
			$data->properties = \Ice_createProperties();
			$data->properties->setProperty("Ice.Default.Locator",$this->iceRegServer);
			$ic = Ice_initialize($data);
			$base = $ic->stringToProxy($classname);
			$printer = $tmp::uncheckedCast($base->ice_datagram());//ice_oneway
			//$printer = $tmp::checkedCast($base);
			//error_log("[icerpc-log trace] ice-one-way-start: $classname - > $funcname");
			if(!$printer){
				error_log("[icerpc-log error] $classname Error Invalid proxy for ".$classname);
			}else{
				$this->finalCall($printer,$funcname,$arg1,$arg2,$arg3,$arg4,$arg5);
			}
		}catch(\Exception $ex){
			error_log("[icerpc-log error] $classname [error ".get_class($ex)."".$ex->getCode()."]".$ex->getMessage()."<<<<<<<<<<<<<<<<<\n".$ex->getTraceAsString());
		}
		//error_log("[icerpc-log trace] ice-one-way-send: $classname - > $funcname");
		if($ic){
			try  {
				$ic->destroy();
			}  catch(Exception $ex)  {
				error_log("[icerpc-log close failed] $classname [error ".get_class($ex)."".$ex->getCode()."]".$ex->getMessage()."");
			}
		}
		//error_log("[icerpc-log trace] ice-one-way-done: $classname - > $funcname");
	}
	private function prepareIce($pak,$classname)
	{
		if(!class_exists('\\Ice_LocalObject',false)){
			include __DIR__.'/Ice_LocalObject.php';
		}
		if(!class_exists('\\Ice_InitializationData',false)){
			include APP_PATH.'/../SoohIce/ice-phplibs/ice/Ice.php';
		}
		if(!class_exists('\\sooh_services_'.$pak.'_'.$classname.'PrxHelper',false)){
			include APP_PATH.'/../SoohIce/ice-phplibs/service-interfaces/'.$pak.'.php';
		
		}
		if(!class_exists('\\Ice_InitializationData',false)){
			error_log('[icerpc-log error]ice-missing:'. $pak.'_'.$classname);
		}
		return '\\sooh_services_'.$pak.'_'.$classname.'PrxHelper';
	}
	private function finalCall($printer,$funcname,$arg1=null,$arg2=null,$arg3=null,$arg4=null,$arg5=null)
	{
		$this->sentlog($funcname,$arg1,$arg2,$arg3,$arg4,$arg5);
		if($arg1!=null){
			if($arg2!=null){
				if($arg3!=null){
					if($arg4!=null){
						if($arg5!=null){
							$returnedStr = $printer->$funcname($arg1,$arg2,$arg3,$arg4,$arg5);
						}else{
							$returnedStr = $printer->$funcname($arg1,$arg2,$arg3,$arg4);
						}
					}else{
						$returnedStr = $printer->$funcname($arg1,$arg2,$arg3);
					}
				}else{
					$returnedStr = $printer->$funcname($arg1,$arg2);
				}
			}else{
				$returnedStr = $printer->$funcname($arg1);
			}
		}else{
			$returnedStr = $printer->$funcname();
		}
		return $returnedStr;
	}
}